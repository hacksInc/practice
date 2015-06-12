<?php
/**
 *  ポイント管理サーバ（旧称：ペイメントサーバ）マネージャ
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Base/Client.php';

/**
 *  Pp_PointManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PointManager extends Ethna_AppManager
{
	/** ポイント管理サーバーへの入力項目として使用するアイテムID */
	const ITEM_ID = 'medal';

	/** JSONのカラム名 */
	protected $json_colnames = array('game_arg', 'point_input', 'point_output', 'game_ret');
	
	/** 現在日時(Y-m-d H:i:s) */
	protected $now = null;

	/** 排他制御情報 */
	protected $exclusive = array(
		'user_id' => null,
		'key'     => null,
		'value'   => null,
	);
	
	/** cURL設定 */
	protected $curl_opts = array(
		CURLOPT_POST			=> true,
		CURLOPT_CONNECTTIMEOUT	=> 5,//10,
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_BINARYTRANSFER	=> true,
		CURLOPT_TIMEOUT			=> 5,//60,
//		CURLOPT_CAINFO			=> '../cacert.pem',
//		CURLOPT_USERAGENT		=> '',
	);

	/** 最後にcURL実行した際の各種情報 */
	protected $last_curl = array(
		'opts'   => null,
		'info'   => null,
		'result' => null,
		'errno'  => null,
		'error'  => null,
	);

	/**
	 * トランザクション更新準備情報
	 * updateTransactionへの引数と同じ書式で
	 */
	protected $update_transaction_columns = null;
	
//	private $app_id;
	private $api_url;
	
	/**
	 * コンストラクタ
	 */
	function __construct ( &$backend )
	{
		parent::__construct( $backend );
		
		$this->now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		
		$this->_init();
	}

	/**
	 * マネージャの初期化処理
	 *
	 *  @access private
	 */
	private function _init()
	{
		// アプリ固有ID
		//$this->app_id	= $this->config->get('app_id');	// Paymentサーバ側から指定されたID

		// APIを使用するためのURL
		$this->api_url	= $this->config->get('api_url');
	}
	
	/**
	 * 21007対応の為のリクエストパラメータ書き換え
	 * 
	 * @param array $params ポイント管理サーバーへの入力項目
	 * @return array ポイント管理サーバーへの入力項目
	 */
	protected function rewriteParamsFor21007($params)
	{
		if (!is_array($params) || !isset($params['game_user_id']) || !isset($params['app_id'])) {
			$this->backend->logger->log(LOG_WARNING, 'Invalid params.');
			return $params;
		}
		
		$user_m =& $this->backend->getManager('User');
		$app_id_map = $this->config->get('app_id_21007');
		
		foreach ($app_id_map as $app_id_from => $app_id_to) {
			if ($params['app_id'] == $app_id_from) {
				$user_base = $user_m->getUserBase($params['game_user_id']);
				if (is_array($user_base) && ($user_base['attribute'] == Pp_UserManager::USER_ATTRIBUTE_APPLE_REVIEW)) {
					$params['app_id'] = $app_id_to;
				}

				break;
			}
		}
		
		return $params;
	}
	
	/**
	 * ポイント管理サーバーAPIへリクエストする
	 * 
	 * @param string $url リクエストURLのパス部分（https://FQDNを除いた部分）
	 * @param array $params ポイント管理サーバーへの入力項目
	 * @return array ポイント管理サーバーからの出力項目
	 * @throws Exeption
	 */
	function request($url, $params)
	{
		// アプリIDをパラメータに追加
//		$params['app_id'] = $this->app_id;

		// 決済サーバーのユーザーIDを直接指定することはできない。puidとして扱う
//		if(isset($params['user_id'])) unset($params['user_id']);
//		if(isset($params['puid'])){
//			$params['user_id'] = $params['puid'];
//			unset($params['puid']);
//		}
		
		// 21007対応
		$params = $this->rewriteParamsFor21007($params);
		
		// 初期化
		$ch = curl_init();

		// オプションを設定
		$curl_opts = $this->curl_opts;
		$curl_opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
		$curl_opts[CURLOPT_URL] = $this->api_url . $url;

		if ($this->config->get('is_test_site')) {
			// 開発だった場合
			$curl_opts[CURLOPT_SSL_VERIFYPEER] = false;
			$curl_opts[CURLOPT_SSL_VERIFYHOST] = false;
		}

		// HTTPリクエストヘッダ準備
		// 既にセットされていたら取り出す
		if (isset($curl_opts[CURLOPT_HTTPHEADER])) {
			$http_request_headers = $curl_opts[CURLOPT_HTTPHEADER];
		} else {
			$http_request_headers = array();
		}
		
		// 'Expect: 100-continue'を無効にする。
		// CURLはこのヘッダをサポートしないサーバだとtimeoutするまで処理を中断(2秒程度)する為
		$http_request_headers[] = 'Expect:';

		// 'Host: FQDN'を指定する。
		// DNSやルーティングの都合でURLに http://IPアドレス/～ を使用する必要がある場合用
		$api_host = $this->config->get('api_host');
		if ($api_host) {
			$http_request_headers[] = 'Host: ' . $api_host;
		}
		
		$curl_opts[CURLOPT_HTTPHEADER] = $http_request_headers;
		
		// 設定を反映
		curl_setopt_array($ch, $curl_opts);

		// 実行
		$result = curl_exec($ch);

		// 取得結果判定
		$info = curl_getinfo( $ch );
		$log_level = LOG_DEBUG;
		if ( substr($info['http_code'], 0, 1) != '2' ) {
			$log_level = LOG_WARNING;
		}
        $this->backend->logger->log( $log_level, "curl_opts:" . var_export( $curl_opts, true ) );
        $this->backend->logger->log( $log_level, "curl_getinfo:" . var_export( $info, true ) );
        $this->backend->logger->log( $log_level, "result:" . var_export( $result, true ) );

		$this->last_curl = array(
			'opts'   => $curl_opts,
			'info'   => $info,
			'result' => $result,
			'errno'  => null,
			'error'  => null,
		);
		
		if ($result === false) {
			$this->last_curl['errno'] = curl_errno($ch);
			$this->last_curl['error'] = curl_error($ch);
			return Ethna::raiseError($this->last_curl['error'], E_USER_ERROR);
		}

		// 終了
		curl_close($ch);
		
		return json_decode($result, true);
	}

	/**
	 * ポイント管理サーバへの入力項目を調整して取得する（購入関連）
	 * 
	 * @param array $params ポイント管理サーバーへの入力項目（調整前）
	 * 　　　　　　　　　　　　$params['app_id'], $params['regist_date']は省略可（省略すると、この関数内で付加）
	 * @param array $params ポイント管理サーバーへの入力項目（調整後）　エラー時はEthnaエラーオブジェクト
	 */
	function getPurchaseRequestParams($params)
	{
		$user_m =& $this->backend->getManager('User');
		
		if (!isset($params['game_user_id']) || !$params['game_user_id']) {
			return Ethna::raiseError('Empty game_user_id.', E_USER_ERROR);
		}

		$user_base = $user_m->getUserBase($params['game_user_id']);
		if (!is_array($user_base)) {
			return Ethna::raiseError('Invalid game_user_id. [' . $params['game_user_id'] . ']', E_USER_ERROR);
		}
		
		if (isset($params['app_id']) && $params['app_id']) {
			if (!$this->isValidAppId($user_base['ua'], $params['app_id'])) {
				return Ethna::raiseError('Invalid app_id. [' . $params['app_id'] . ']', E_USER_ERROR);
			}
		} else {
			$params['app_id'] = $this->getDefaultAppId($user_base['ua']);
		}
		
		if (!isset($params['regist_date']) || !$params['regist_date']) {
			$params['regist_date'] = $this->now;
		}
		
		return $params;
	}
	
	/**
	 * ポイント管理サーバへの通信リクエストを記録する
	 * 
	 * @param string $game_transaction_id ゲームトランザクションID
	 * @param int    $user_id             ユーザID
	 * @param string $remote_addr         アクセス元IPアドレス
	 * @param string $action              ゲームサーバでの実行アクション
	 * @param array  $game_arg            アプリ→ゲームサーバ引数（JSON文字列またはPHP連想配列）
	 * @param array  $last_curl           最後にcURL実行した際の各種情報（省略可）
	 * @return bool|object 成功時:true, 失敗時:Ethnaエラーオブジェクト
	 */
	function logRequest($game_transaction_id, $user_id, $remote_addr, $action, $game_arg, $last_curl = null)
	{
		if ($last_curl === null) {
			$last_curl = $this->last_curl;
		}
		
        $log_db = $this->backend->getDB('logex');
		
		$result_json = $this->jsonEncodeIfArray($last_curl['result']);
		$result_assoc = json_decode($result_json, true);
		if (is_array($result_assoc) && isset($result_assoc['sts'])) {
			$result_sts = $result_assoc['sts'];
		} else {
			$result_sts = null;
		}
		
		$param = array(
			$game_transaction_id, 
			$user_id, 
			$remote_addr, 
			$action, 
			$this->jsonEncodeIfArray($game_arg),
			$this->jsonEncodeIfArray($last_curl['opts']), 
			$this->jsonEncodeIfArray($last_curl['info']), 
			$result_json,
			$result_sts,
			$last_curl['errno'], 
			$last_curl['error'], 
			$this->now
		);
		$sql = "INSERT INTO log_point_request(game_transaction_id, user_id, remote_addr, action, game_arg, opts, info, result, result_sts, errno, error, date_created)"
		     . " VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		if (!$log_db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$log_db->db->ErrorNo(), $log_db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		return true;
	}
	
	/** 配列ならjson_encodeする */
	protected function jsonEncodeIfArray($data)
	{
		return is_array($data) ? json_encode($data) : $data;
	}
	
	/**
	 * デフォルトのアプリケーションIDを取得する
	 * 
	 * @param int $ua User-Agent種別
	 * @return string アプリケーションID
	 */
	function getDefaultAppId($ua)
	{
		$config_app_id = $this->config->get('app_id');
		if (!isset($config_app_id[$ua])) {
			return false;
		}
		
		if (is_array($config_app_id[$ua])) {
			return $config_app_id[$ua][0];
		} else {
			return $config_app_id[$ua];
		}
	}
	
	/**
	 * 有効なアプリケーションIDか
	 * 
	 * @param int $ua User-Agent種別
	 * @param string $app_id アプリケーションID
	 * @return boolean 真否
	 */
	function isValidAppId($ua, $app_id)
	{
		$config_app_id = $this->config->get('app_id');
		if (!isset($config_app_id[$ua])) {
			return false;
		}
		
		if (is_array($config_app_id[$ua])) {
			return in_array($app_id, $config_app_id[$ua]);
		} else {
			return ($app_id == $config_app_id[$ua]);
		}
	}
	
	/**
	 * 最新のトランザクション情報を取得する
	 * 
	 * @param string $game_transaction_id ゲームトランザクションID
	 * @param int $user_id ユーザID
	 * @param bool $json_decode_flg JSONデコードするか
	 * @return array トランザクション情報（t_point_transactionテーブルのカラム名がキー） 情報が存在しないまたはエラー時はfalse
	 */
	function getTransactionResult($game_transaction_id, $user_id, $json_decode_flg = false)
	{
		$param = array($game_transaction_id);
		$sql = "SELECT * FROM t_point_transaction WHERE game_transaction_id = ?";
		
		$row = $this->db->GetRow($sql, $param);
		if (is_array($row) && isset($row['user_id']) && ($row['user_id'] != $user_id)) {
			$this->backend->logger->log(LOG_INFO, 'Different user_id. [' . $row['user_id'] . '] [' . $user_id . ']');
			return false;
		}
		
		if ($json_decode_flg) {
			foreach ($this->json_colnames as $colname) {
				if (isset($row[$colname]) && (strlen($row[$colname]) > 0)) {
					$row[$colname] = json_decode($row[$colname], true);
				}
			}
		}

		return $row;
	}
	
	/**
	 * 同じ引数か
	 * 
	 * @param array  $last_transaction 最新のトランザクション情報（getTransactionResultの戻り値）
	 * @param string $action           ゲームサーバでの実行アクション
	 * @param array  $game_arg         アプリ→ゲームサーバ引数（PHP連想配列）
	 * @return boolean 真偽
	 */
	function isSameArg($last_transaction, $action, $game_arg)
	{
		if (!is_array($last_transaction)) {
			$this->backend->logger->log(LOG_WARNING, 'last_transaction is not array.');
			return false;
		}
		
		if (!$action) {
			$this->backend->logger->log(LOG_WARNING, 'action is false.');
			return false;
		}
		
		if (!is_array($game_arg)) {
			$this->backend->logger->log(LOG_WARNING, 'game_arg is not array.');
			return false;
		}
		
		if (!isset($last_transaction['action']) || 
		    (strcmp($last_transaction['action'], $action) !== 0)
		) {
			$this->backend->logger->log(LOG_WARNING, 'action is not same.');
			return false;
		}

		if (!isset($last_transaction['game_arg'])) {
			$this->backend->logger->log(LOG_WARNING, 'game_arg of last_transaction is not set.');
			return false;
		}
		
		if (is_array($last_transaction['game_arg'])) {
			$last_game_arg = $last_transaction['game_arg'];
		} else {
			$last_game_arg = json_decode($last_transaction['game_arg'], true);
		}
		
		foreach ($game_arg as $key => $value) {
			if (strlen($value) == 0) {
				continue;
			}
			
			if (!isset($last_game_arg[$key]) ||
			    (strcmp($last_game_arg[$key], $value) !== 0)
			) {
				$this->backend->logger->log(LOG_WARNING, 'game_arg is not same. [' . $last_transaction['game_arg'][$key] . '] [' . $value . ']');
				return false;
			}
		}

		return true;
	}
	
	/** 
	 * 使用可能なトランザクションか
	 * 
	 * 下記の項目全てを満たすと使用可能と判定する
	 * ・指定されたゲームトランザクションIDがジャグモンDBに存在する
	 * ・指定されたゲームトランザクションIDが指定されたユーザIDのものである
	 * ・指定されたゲームトランザクションIDについて、ポイント管理サーバからの出力がジャグモンDBに記録されていない
	 * @param string $game_transaction_id ゲームトランザクションID
	 * @param int $user_id ユーザID
	 * @return bool 真偽
	 */
	function isTransactionAvailable($game_transaction_id, $user_id)
	{
		$previous_result = $this->getTransactionResult($game_transaction_id, $user_id);
		if (!is_array($previous_result) || !array_key_exists('point_output_sts', $previous_result)) {
			return false;
		} else if (strlen($previous_result['point_output_sts']) > 0) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * 存在するトランザクションか
	 * 
	 * 下記の項目全てを満たすと存在すると判定する
	 * ・指定されたゲームトランザクションIDがジャグモンDBに存在する
	 * ・指定されたゲームトランザクションIDが指定されたユーザIDのものである
	 * @param string $game_transaction_id ゲームトランザクションID
	 * @param int $user_id ユーザID
	 * @return bool 真偽
	 */
	function isTransactionExists($game_transaction_id, $user_id)
	{
		$previous_result = $this->getTransactionResult($game_transaction_id, $user_id);
		if (!is_array($previous_result) || !array_key_exists('point_output_sts', $previous_result)) {
			return false;
		}
		
		return true;
	}

	/**
	 * トランザクションを生成する
	 * 
	 * @param int $user_id ユーザID
	 * @param string $prefix プリフィックス（呼び出し元Ethnaアクションがapi_*の場合は"api", admin_*の場合は"admin"を指定すること）
	 * @return string ゲームトランザクションID ※エラーの場合はEthnaエラーオブジェクト
	 */
	function createTransaction($user_id, $prefix = 'api')
	{
		$game_transaction_id = $prefix . $user_id . uniqid();
		
		$sql = "INSERT INTO t_point_transaction (game_transaction_id, user_id, date_created, date_modified)"
		     . " VALUES (?, ?, ?, ?)";
		$param = array($game_transaction_id, $user_id, $this->now, $this->now);
		
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return $game_transaction_id;
	}
	function createTransactionCutUniqid($user_id, $prefix = 'api')
	{
		$game_transaction_id = $prefix . $user_id;
		
		$sql = "INSERT INTO t_point_transaction (game_transaction_id, user_id, date_created, date_modified)"
		     . " VALUES (?, ?, ?, ?)";
		$param = array($game_transaction_id, $user_id, $this->now, $this->now);
		
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return $game_transaction_id;
	}
	
	/**
	 * DB保存用ゲームサーバ→アプリ戻り値を準備する
	 * 
	 * @param array|string $game_ret ゲームサーバ→アプリ戻り値 ※JSON文字列またはPHP連想配列
	 * @return boolean|object 成否（成功時:true, 失敗時:Ethnaエラーオブジェクト）
	 */
	function prepareGameRet($game_ret)
	{
		if (!$this->update_transaction_columns) {
			return Ethna::raiseError("Not prepared.");
		}
		
		$this->update_transaction_columns['game_ret'] = $game_ret;
		
		return true;
	}
	
	/**
	 * トランザクション更新を準備する
	 * 
	 * @param array $columns updateTransactionへの引数と同じ書式
	 * @return boolean|object 成否　成功時:true, 失敗時:Ethnaエラーオブジェクト
	 */
	protected function prepareUpdateTransaction($columns)
	{
		if ($this->update_transaction_columns) {
			return Ethna::raiseError("Already prepared.");
		}
		
		$this->update_transaction_columns = $columns;
		return true;
	}
	
	/**
	 * 準備されたトランザクション更新を行う
	 * 
	 * @return boolead|object updateTransactionからの戻り値に同じ
	 */
	function updatePreparedTransaction()
	{
//		if (!count($this->update_transaction_columns)) {
		if (!$this->update_transaction_columns) {
			return Ethna::raiseError("Not prepared.");
		}
		
//		return $this->updateTransaction(array_pop($this->update_transaction_columns));
		
		$ret = $this->updateTransaction($this->update_transaction_columns);
		$this->update_transaction_columns = null;
		return $ret;
	}
	
	/**
	 * トランザクションを更新する
	 * 
	 * @param array $columns array(
	 *                          'game_transaction_id' => ゲームトランザクションID, ※必須
	 *                          'user_id'             => ユーザID,                ※必須
	 *                          'remote_addr'         => アクセス元IPアドレス,
	 *                          'action'              => ゲームサーバでの実行アクション,
	 *                          'game_arg'            => アプリ→ゲームサーバ引数,            ※JSON文字列またはPHP連想配列
	 *                          'point_path'          => ポイント管理サーバURLパス
	 *                          'point_input'         => ゲームサーバ→ポイント管理サーバ入力, ※JSON文字列またはPHP連想配列
	 *                          'point_output'        => ポイント管理サーバ→ゲームサーバ出力, ※JSON文字列またはPHP連想配列
	 *                          'point_output_sts'    => ポイント管理サーバ→ゲームサーバ出力(処理結果), ※省略するとpoint_outputから自動取得
	 *                          'game_ret'            => ゲームサーバ→アプリ戻り値,          ※JSON文字列またはPHP連想配列
	 *                        )
	 * @return boolean|object 成否（成功時:true, 失敗時:Ethnaエラーオブジェクト）
	 */
	function updateTransaction($columns)
	{
		// 引数のフォーマットを調整
		if (isset($columns['point_output'])) {
			if (is_array($columns['point_output'])) {
				$point_output = $columns['point_output'];
			} else {
				$point_output = json_decode($columns['point_output'], true);
			}
			
			if (!isset($columns['point_output_sts']) && isset($point_output['sts'])) {
				$columns['point_output_sts'] = $point_output['sts'];
			}
		}
		
		foreach ($this->json_colnames as $colname) {
			if (isset($columns[$colname]) && is_array($columns[$colname])) {
				$columns[$colname] = json_encode($columns[$colname]);
			}
		}
		
		// 最新情報を更新
		$sql = "UPDATE t_point_transaction"
		     . " SET remote_addr = ?, action = ?, game_arg = ?, point_path = ?, point_input = ?, point_output = ?, point_output_sts = ?, game_ret = ?, date_modified = ?"
		     . " WHERE game_transaction_id = ? AND user_id = ?";
		
		$param = array(
			isset($columns['remote_addr'])      ? $columns['remote_addr']      : null,
			isset($columns['action'])           ? $columns['action']           : null,
			isset($columns['game_arg'])         ? $columns['game_arg']         : null,
			isset($columns['point_path'])       ? $columns['point_path']       : null,
			isset($columns['point_input'])      ? $columns['point_input']      : null,
			isset($columns['point_output'])     ? $columns['point_output']     : null,
			isset($columns['point_output_sts']) ? $columns['point_output_sts'] : null,
			isset($columns['game_ret'])         ? $columns['game_ret']         : null,
			$this->now,
			$columns['game_transaction_id'],
			$columns['user_id'],
		);

		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		$affected_rows = $this->db->db->affected_rows();
//		if ($affected_rows > 1) {
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		return true;
	}
	
	/**
	 * ユーザー毎のゲームトランザクションIDを調整する
	 * 
	 * 既存のt_user_base.game_transaction_idが使用できない状態だったら、新規発行する。
	 * ゲームトランザクションIDは/api/user/createと/api/shop/point/purchaseで新規発行しているので、
	 * この関数は不要なはずだが、念のためチェックする。
	 * @param int $user_id ユーザID
	 * @return boolean|object 成否（成功時:true, 失敗時:Ethnaエラーオブジェクト）
	 */
	function adjustUserGameTransactionId($user_id)
	{
		$user_m =& $this->backend->getManager('User');
		$base = $user_m->getUserBase($user_id);
		if (!$base || Ethna::isError($base)) {
			return $base;
		}
		
		$flg = false; // ゲームトランザクションID新規発行するかのフラグ
		if (!$base['game_transaction_id']) {
			$flg = true;
		} else {
			$transaction = $this->getTransactionResult($base['game_transaction_id'], $user_id);
			if (Ethna::isError($transaction)) {
				return $transaction;
			}
			
			if (!$transaction) {
				$flg = true;
			} else if  (strlen($transaction['point_output_sts']) > 0) {
				$flg = true;
			}
		}
		
		$this->backend->logger->log(LOG_DEBUG, 'adjustUserGameTransactionId. user_id=[' . $user_id . '] flg=[' . $flg . ']');
		
		if ($flg) {
			$this->backend->logger->log(LOG_WARNING, 'Invalid game_transaction_id. user_id=[' . $user_id . ']');
			
			$new_game_transaction_id = $this->createTransaction($user_id);
			$columns = array(
				'game_transaction_id' => $new_game_transaction_id,
			);

			$ret = $user_m->setUserBase($user_id, $columns);
			if (!$ret || Ethna::isError($ret)) {
				return $ret;
			}
		}

		return true;
	}
	
	/**
	 * 年齢認証を期限切れにする
	 * 
	 * @param int $user_id ユーザID
	 * @return boolean|object 成否（成功時:true, 失敗時:Ethnaエラーオブジェクト）
	 */
	function expireMonthlyAgeVerification($user_id)
	{
		$user_m =& $this->backend->getManager('User');
		$base = $user_m->getUserBase($user_id);
		if (!$base || Ethna::isError($base)) {
			return $base;
		}
		
		$now_ym = date('Y-m', $_SERVER['REQUEST_TIME']);
		$save_ym = substr($base['date_purchase'], 0, 7);//YYYY-MMだけにする
		
		// t_user_baseのage_verificationが0でなく、
		// かつ、t_user_baseのdate_purchaseが今月でなかったら、
		// ・age_verificationを-1
		// ・ma_purchasedを0
		// ・date_purchaseを今月
		// ・ma_purchased_mixを0
		// にする
		if (($base['age_verification'] != 0) &&
		    (strcmp($now_ym, $save_ym) != 0)
		) {
			$columns = array(
				'age_verification' => -1,
				'ma_purchased' => 0,
				'date_purchase' => $now_ym . '-01 00:00:00',
				'ma_purchased_mix' => 0,
			);
			
			$ret = $user_m->setUserBase($user_id, $columns);
			if (!$ret || Ethna::isError($ret)) {
				return $ret;
			}
		}
		
		return true;
	}
	
	/**
	 * 課金アイテム残高照会（サービス付）
	 * 
	 * @param int $user_id ユーザID
	 * @return array ポイント管理サーバからの出力項目
	 */
	function inquiry($user_id)
	{
		$user_m =& $this->backend->getManager('User');
		
		$base = $user_m->getUserBase($user_id);
		if (!$base || Ethna::isError($base)) {
			return $base;
		}

//		$game_transaction_id = $this->createTransaction($user_id);
		$game_transaction_id = 'inq' . $user_id . uniqid();
		
		$params = array(
			'app_id'              => $this->getDefaultAppId($base['ua']),
			'game_user_id'        => $user_id, 
			'game_transaction_id' => $game_transaction_id,
			'item_id'             => self::ITEM_ID,
		);

		$point_path = '/Payment/Inquiry';
		$point_output = $this->request($point_path, $params);
//		$this->logRequest($game_transaction_id, $user_id, null, __METHOD__, $params);
//		if ($point_output && !Ethna::isError($point_output)) {
//			$ret = $this->updateTransaction(array(
//				'game_transaction_id' => $game_transaction_id,
//				'user_id'             => $user_id,
//				'point_path'          => $point_path,
//				'point_input'         => $params,
//				'point_output'        => $point_output,
//			));
//			if ($ret !== true) {
//				return Ethna::raiseError('updateTransaction failed.', E_USER_ERROR);
//			}
//		}
		
		return $point_output;
	}
	
	/**
	 * ゲーム内サービス付与
	 * 
	 * ポイント管理サーバへの通信とlog_point_request, t_point_transactionテーブルへの記録を行う。
	 * t_user_baseの更新は行わないので、この関数呼出し後に行うこと。
	 * この関数はMySQLのトランザクション開始前に呼ぶこと。
	 * ※引数$game_argは、consume関数にはあるが、この関数には無い。クライアント側アプリを起点とするゲーム内サービス付与が無い為。
	 * @param string $game_transaction_id ゲームトランザクションID
	 * @param int $user_id ユーザID
	 * @param int $service_count サービスアイテム数
	 * @param string $remote_addr アクセス元IPアドレス
	 * @param string $action ゲームサーバでの実行アクション
	 * @param bool $kpi_flg KPI処理フラグ(true:処理する, false:処理しない)
	 * @return array|object ポイント管理サーバからの出力項目 （エラー時はEthnaエラーオブジェクト）
	 */
	function gamebonus($game_transaction_id, $user_id, $service_count, $remote_addr, $action, $kpi_flg = true)
	{
		$user_m =& $this->backend->getManager('User');
		
		$base = $user_m->getUserBase($user_id);
		if (!$base || Ethna::isError($base)) {
			return $base;
		}

		if (!$this->isTransactionAvailable($game_transaction_id, $user_id)) {
			return Ethna::raiseError('Unavailable game_transaction_id. game_transaction_id=[' . $game_transaction_id . '] user_id=[' . $user_id . ']', E_USER_ERROR);
		}
		
		$params = array(
			'app_id'              => $this->getDefaultAppId($base['ua']),
			'game_user_id'        => $user_id, 
			'regist_date'         => $this->now, 
			'game_transaction_id' => $game_transaction_id,
			'item_id'             => self::ITEM_ID,
			'service_count'       => $service_count, 
		);

		$point_path = '/Payment/Gamebonus';
		$point_output = $this->request($point_path, $params);
		$this->logRequest($game_transaction_id, $user_id, $remote_addr, $action, $params);
		if ($point_output && !Ethna::isError($point_output)) {
			$ret = $this->prepareUpdateTransaction(array(
				'game_transaction_id' => $game_transaction_id,
				'user_id'             => $user_id,
				'remote_addr'         => $remote_addr,
				'action'              => $action,
				'point_path'          => $point_path,
				'point_input'         => $params,
				'point_output'        => $point_output,
			));
			
			if ($ret !== true) {
				return Ethna::raiseError('updateTransaction failed.', E_USER_ERROR);
			}
		}
		
		// KPI
		if ($kpi_flg && is_array($point_output) && isset($point_output['sts']) && ($point_output['sts'] == 'OK')) {
			$kpi_m = $this->backend->getManager('Kpi');
			$kpi_platform = $kpi_m->getPlatform($user_id);
			$kpi_m->log($kpi_platform."-jgm-magicalmedal_free_distribution",5,1,"",$user_id,$service_count,"","");
		}
		
		return $point_output;
	}

	/**
	 * ゲーム内サービス付与して出力項目変換する
	 * 
	 * @see Pp_PointManager::gamebonus
	 * @see Pp_PointManager::convertPointOutputToPaymentService
	 */
	function requestGamebonusAndConvertOutput($game_transaction_id, $user_id, $service_count, $remote_addr, $action)
	{
		$point_output = $this->gamebonus($game_transaction_id, $user_id, $service_count, $remote_addr, $action);
		list($payment, $service) = $this->convertPointOutputToPaymentService($point_output);
		
		return array($payment, $service);
	}
	
	/**
	 * 消費
	 * 
	 * ポイント管理サーバへの通信とlog_point_request, t_point_transactionテーブルへの記録を行う。
	 * t_user_baseの更新は行わないので、この関数呼出し後に行うこと。
	 * この関数はMySQLのトランザクション開始前に呼ぶこと。
	 * @param string $game_transaction_id ゲームトランザクションID
	 * @param int $user_id ユーザID
	 * @param int $item_count アイテム数
	 * @param string $remote_addr アクセス元IPアドレス
	 * @param string $action ゲームサーバでの実行アクション
	 * @param array  $game_arg アプリ→ゲームサーバ引数（PHP連想配列）
	 * @param bool $kpi_flg KPI処理フラグ(true:処理する, false:処理しない)
	 * @return array|object ポイント管理サーバからの出力項目 （エラー時はEthnaエラーオブジェクト）
	 */
	function consume($game_transaction_id, $user_id, $item_count, $remote_addr, $action, $game_arg = null, $kpi_flg = true)
	{
		$user_m =& $this->backend->getManager('User');
		
		$base = $user_m->getUserBase($user_id);
		if (!$base || Ethna::isError($base)) {
			return $base;
		}

		if (!$this->isTransactionAvailable($game_transaction_id, $user_id)) {
			return Ethna::raiseError('Unavailable game_transaction_id. game_transaction_id=[' . $game_transaction_id . '] user_id=[' . $user_id . ']', E_USER_ERROR);
		}
		
		$params = array(
			'app_id'              => $this->getDefaultAppId($base['ua']),
			'game_user_id'        => $user_id, 
			'regist_date'         => $this->now, 
			'game_transaction_id' => $game_transaction_id,
			'item_id'             => self::ITEM_ID,
			'item_count'          => $item_count, 
		);

		$point_path = '/Payment/Consume/';
		$point_output = $this->request($point_path, $params);
		$this->logRequest($game_transaction_id, $user_id, $remote_addr, $action, $params);
		if ($point_output && !Ethna::isError($point_output)) {
			$ret = $this->prepareUpdateTransaction(array(
				'game_transaction_id' => $game_transaction_id,
				'user_id'             => $user_id,
				'remote_addr'         => $remote_addr,
				'action'              => $action,
				'game_arg'            => $game_arg,
				'point_path'          => $point_path,
				'point_input'         => $params,
				'point_output'        => $point_output,
			));
			
			if ($ret !== true) {
				return Ethna::raiseError('updateTransaction failed.', E_USER_ERROR);
			}
		}

		// KPI
		if ($kpi_flg && is_array($point_output) && isset($point_output['sts']) && ($point_output['sts'] == 'OK')) {
			$kpi_m = $this->backend->getManager('Kpi');
			$kpi_platform = $kpi_m->getPlatform($user_id);
			$kpi_m->log($kpi_platform."-jgm-mt_paid_dau",2,1,"",$user_id,"","","");
			$kpi_m->log($kpi_platform."-jgm-mt_sales",1,1,"",$user_id,$item_count,"","");
			
			list($payment, $service) = $this->convertPointOutputToPaymentService($point_output);

			if ($payment !== null) {
				$kpi_count = $base['medal'] - $payment;
				if ($kpi_count > 0) {
					$kpi_m->log($kpi_platform."-jgm-magicalmedal_count",1,$kpi_count,"",$user_id,"","","");
				}
			}
			
			if ($service !== null) {
				$kpi_free_count = $base['service_point'] - $service;
				if ($kpi_free_count > 0) {
					$kpi_m->log($kpi_platform."-jgm-magicalmedal_free_count",5,1,"",$user_id,$kpi_free_count,"","");
				}
			}
			
			$periodlog_m = $this->backend->getManager('Periodlog');
			$periodlog_m->logPeriodUserAccumu(
				$user_id, null,
				Pp_PeriodlogManager::ACTION_TYPE_PAYMENT_USE, 
				Pp_PeriodlogManager::PERIOD_TYPE_MONTHLY,
				$item_count
			);
			
			$periodlog_m->logPeriodUserAccumu(
				$user_id, null,
				Pp_PeriodlogManager::ACTION_TYPE_PAYMENT_USE, 
				Pp_PeriodlogManager::PERIOD_TYPE_WEEKLY,
				$item_count
			);
			
			$periodlog_m->logPeriodUserAccumu(
				$user_id, null,
				Pp_PeriodlogManager::ACTION_TYPE_PAYMENT_USE_NUM, 
				Pp_PeriodlogManager::PERIOD_TYPE_WEEKLY,
				1
			);
		}
		
		return $point_output;
	}
	
	/**
	 * 消費して出力項目変換する
	 * 
	 * @see Pp_PointManager::consume
	 * @see Pp_PointManager::convertPointOutputToPaymentService
	 */
	function requestConsumeAndConvertOutput($game_transaction_id, $user_id, $item_count, $remote_addr, $action, $game_arg = null)
	{
		$point_output = $this->consume($game_transaction_id, $user_id, $item_count, $remote_addr, $action, $game_arg);
		list($payment, $service) = $this->convertPointOutputToPaymentService($point_output);
		
		return array($payment, $service);
	}
	
	/**
	 * ポイント管理サーバ出力項目を課金アイテムアイテム残数とサービスアイテムアイテム残数に変換して返す
	 * 
	 * @param array $point_output ポイント管理サーバ出力項目
	 * @return array array(課金アイテムアイテム残数, サービスアイテムアイテム残数) ※変換できなかった場合はarray(null, null)
	 */
	function convertPointOutputToPaymentService($point_output)
	{
		$payment = null;
		$service = null;
		
		if (is_array($point_output) && 
		    isset($point_output['sts']) && ($point_output['sts'] == 'OK')
		) {
			if (isset($point_output['pay']) && isset($point_output['pay']['payment'])) {
				$payment = $point_output['pay']['payment'];
			}
			
			if (isset($point_output['service']) && isset($point_output['service']['service'])) {
				$service = $point_output['service']['service'];
			}
		}
		
		return array($payment, $service);
	}
	
	/**
	 * 排他制御情報を初期化する
	 * 
	 * @param int $user_id ユーザID
	 */
	function initExclusive($user_id)
	{
		$this->exclusive['user_id'] = $user_id;
		$this->exclusive['key']     = $user_id . '_point_uniq';
		$this->exclusive['value']   = uniqid(mt_rand(0, 65535));
		
		$cache =& Ethna_CacheManager::getInstance('memcache');
		$cache->set($this->exclusive['key'], $this->exclusive['value']);
	}
	
	/**
	 * 排他制御情報をチェックする
	 * 
	 * @param int $user_id ユーザID
	 * @return boolean true:正常,false:不正
	 */
	function checkExclusive($user_id)
	{
		if ($user_id != $this->exclusive['user_id']) {
			$this->backend->logger->log(LOG_WARNING, 'Exclusive user_id has changed. [' . $user_id . '] [' . $this->exclusive['user_id'] . ']');
			return false;
		}

		$cache =& Ethna_CacheManager::getInstance('memcache');
		$value = $cache->get($this->exclusive['key']);
		if ($value != $this->exclusive['value']) {
			$this->backend->logger->log(LOG_WARNING, 'Exclusive value has changed. [' . $value . '] [' . $this->exclusive['value'] . ']');
			return false;
		}

		return true;
	}
}
?>
