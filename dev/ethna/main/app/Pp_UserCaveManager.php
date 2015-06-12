<?php
/**
 *  Pp_UserCaveManager.php
 *
 *  ケイブの決済サーバと直接通信する認証・決済処理を、このクラスに定義する
 *  @author     {$author}
 *  @package    Igps
 *  @version    $Id$
 *  @see        Pp_UserManager
 */

require_once 'classes/InterfacePlatform.php';

/**
 *  Pp_UserCaveManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Igps
 */
class Pp_UserCaveManager extends Ethna_AppManager implements InterfacePlatform
{
	private $app_id;
	private $api_url;
	
	/**
	 *  コンストラクタ
	 *
	 *  @access public
	 *  @param  object  Ethna_Backend   $backend    backendオブジェクト
	 */
	function __construct( &$backend )
	{
		parent::__construct( $backend );

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

		// cURL設定
		$this->curl_opts = array(
			CURLOPT_POST			=> true,
			CURLOPT_CONNECTTIMEOUT	=> 10,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_BINARYTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 60,
//			CURLOPT_CAINFO			=> '../cacert.pem',
//			CURLOPT_USERAGENT		=> '',
		);
	}

	/**
	 * トランザクションIDを生成する
	 */
	public function genTransId()
	{
		return md5(uniqid(mt_rand(), true));
	}
	
	/**
	 * @brief app_idを設定する
	*/
/*
	function setAppId($os){
		$user_m =& $this->backend->getManager('User');
		$app_id = $this->config->get('app_id');
		switch($os){
			case Cf_UserManager::OS_IPHONE:
				$this->app_id = $app_id[Cf_UserManager::OS_IPHONE]; break;
			case Cf_UserManager::OS_ANDROID:
				$this->app_id = $app_id[Cf_UserManager::OS_ANDROID]; break;
			default:
				throw new DataLackedException('Undefined OS ' . $os);
		}
	}
*/
	
	/** app_idを直接設定する */
	function setAppIdDirect($app_id)
	{
		$this->app_id = $app_id;
	}
	
	/**
	 * @brief app_idを取得する
	 */
	function getAppId(){
		return $this->app_id;
	}

	/**
	 * Paymentサーバへのリクエスト
	 *
	 *  @access private
	 *  @param  string  $url  接続先
	 *  @param  array  $params  送信パラメータ
	 *  @return array  取得結果
	 */
	private function _request($url, $params)
	{
		// アプリIDをパラメータに追加
		$params['app_id'] = $this->app_id;

		// 決済サーバーのユーザーIDを直接指定することはできない。puidとして扱う
//		if(isset($params['user_id'])) unset($params['user_id']);
//		if(isset($params['puid'])){
//			$params['user_id'] = $params['puid'];
//			unset($params['puid']);
//		}

		// 初期化
		$ch = curl_init();

		// オプションを設定
		$this->curl_opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
		$this->curl_opts[CURLOPT_URL] = $this->api_url . $url;

		if ($this->config->get('is_test_site')) {
			// 開発だった場合
			$this->curl_opts[CURLOPT_SSL_VERIFYPEER] = false;
			$this->curl_opts[CURLOPT_SSL_VERIFYHOST] = false;
		}

		// 'Expect: 100-continue'を無効にする。
		// CURLはこのヘッダをサポートしないサーバだとtimeoutするまで処理を中断(2秒程度)する為
		if (isset($this->curl_opts[CURLOPT_HTTPHEADER])) {
			$existing_headers = $this->curl_opts[CURLOPT_HTTPHEADER];
			$existing_headers[] = 'Expect:';
			$this->curl_opts[CURLOPT_HTTPHEADER] = $existing_headers;
		} else {
			$this->curl_opts[CURLOPT_HTTPHEADER] = array('Expect:');
		}

		// 設定を反映
		curl_setopt_array($ch, $this->curl_opts);

		// 実行
		$result = curl_exec($ch);

		// 取得結果判定
		$info = curl_getinfo( $ch );
		$log_level = LOG_DEBUG;
		if ( substr($info['http_code'], 0, 1) != '2' ) {
			$log_level = LOG_WARNING;
		}
        $this->backend->logger->log( $log_level, "curl_getinfo:" . var_export( $info, true ) );
        $this->backend->logger->log( $log_level, "result:" . var_export( $result, true ) );

		if ($result === false) {
			// 実行エラー
			//$result['sts'] = "ERROR";
			//$result['msg'] = "curl_exec error";
			//return $result;
//			throw new CurlException(curl_errno($ch) .':'. curl_error($ch));
			throw new Exception(curl_errno($ch) .':'. curl_error($ch));
		}

		// 終了
		curl_close($ch);

		return json_decode($result, true);
	}

	/**
	 *  入金
	 *
	 *  @param  integer  $puid  Paymentサーバ側が発行したユーザーID
	 *  @param  string   $transaction  google発行のレシートjsonをbase64エンコード ※androidのみ
	 *  @param  string   $signature  google発行のレシート検証用コード　google発行値そのまま ※androidのみ
	 *  @param  string   $receipt  apple発行レシート　※iphoneのみ
	 *  @param  integer  $coin  コイン額
	 *  @param  integer  $service サービス額　コインのみのときは0を指定
	 *  @return array  結果
	 *  @throws UserCaveException
	 */
/*
	public function requestPaymentRegist($puid, $transaction, $signature, $receipt, $coin, $service)
	{
		// 送信パラメータ
		$param = array(
			'user_id'			=> $puid,
			'transaction'		=> $transaction,
			'signature'			=> $signature,
			'receipt'			=> $receipt,
			'coin'				=> $coin,
			'service'			=> $service,
			'env'				=> $this->config->get('is_test_site') ? 3 : 1,	//環境 1：本番、2：STG、3：DEV iphoneアプリで3を指定した場合、sandbox用の検証サーバを使用
//			'regist_date'		=> date("Y-m-d H:i:s"),
			'regist_date'		=> date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']),
		);
$this->backend->logger->log(LOG_INFO, "LOG: param = ".print_r($param, true));

		$result = $this->_request("/payment/regist", $param);
//printLog("LOG: app_id: $this->app_id result = ".print_r($result, true));
$this->backend->logger->log(LOG_INFO, "LOG: app_id: $this->app_id result = ".print_r($result, true));
		if ($result['sts'] === 'NG') {
//			throw new UserCaveException($result['msg']);
			throw new Exception($result['msg']);
		}
		
		//format
		$ret = $result['param'];
		//puid
		//$ret['puid'] = $ret['user_id'];
		//unset($ret['user_id']);
		return $ret;
	}
*/

	/**
	 *  出金
	 *
	 *  @access public
	 *  @param  string   $transaction_id 購入ID
	 *  @param  integer  $puid  Paymentサーバ側が発行したユーザーID
	 *  @param  integer  $item_id  アイテムID
	 *  @param  integer  $price  価格
	 *  @return array  結果
	 *  @throws UserCaveException
	 */
	public function requestPaymentUse($transaction_id, $puid, $item_id, $price)
	{
		// 送信パラメータ
		$param = array(
			'transaction_id'	=> $transaction_id,
			'user_id'			=> $puid,
			'item_id'			=> $item_id,
			'coin'				=> $price,
			'regist_date'		=> date("Y-m-d H:i:s"),
		);

		$result = $this->_request("/payment/use", $param);
//printLog("LOG: app_id: $this->app_id result = ".print_r($result, true));
$this->backend->logger->log(LOG_INFO, "LOG: app_id: $this->app_id result = ".print_r($result, true));
		if ($result['sts'] === 'NG') {
//			throw new UserCaveException($result['msg']);
			throw new Exception($result['msg']);
		}

		// ログ用
		$result['transaction_id'] = $transaction_id;

		//format
		$ret = $result['param'];
		//puid
		//$ret['puid'] = $ret['user_id'];
		//unset($ret['user_id']);
		return $ret;
	}
	
	/**
	 *  残高参照
	 *
	 *  @access public
	 *  @param  integer  $puid  Paymentサーバ側が発行したユーザーID
	 *  @return array  残高情報
	 *  @throws UserCaveException
	 */
	public function requestPaymentCheck($puid)
	{
		// 送信パラメータ
		$param = array(
			'user_id'	=> $puid,
		);

		$result = $this->_request("/payment/check", $param);
//printLog("LOG: app_id: $this->app_id result = ".print_r($result, true));
$this->backend->logger->log(LOG_INFO, "LOG: app_id: $this->app_id result = ".print_r($result, true));
		if ($result['sts'] === 'NG') {
			throw new UserCaveException($result['msg']);
		}

		//format
		$ret = $result['param'];
		//puid
		//s$ret['puid'] = $ret['user_id'];
		//unset($ret['user_id']);
		return $ret;
	}
	
	/**
	 *  アカウント登録
	 *
	 *  @access public
	 *  @param  integer  $account  アカウント
	 *  @param  integer  $password  パスワード
	 *  @return array  登録結果
	 *  @throws UserCaveException
	 */
	public function requestUserRegist($account, $password)
	{
/*
		// 送信パラメータ
		$param = array(
			'account'	=> $account,
			'password'	=> $password,
		);

		$result = $this->_request("/user/regist", $param);
printLog("LOG: app_id: $this->app_id result = ".print_r($result, true));

		if ($result['sts'] === 'NG') {
			throw new UserCaveException($result['msg']);
		}
		//format
		$ret = $result['param'];
		//puid
		$ret['puid'] = $ret['user_id'];
		unset($ret['user_id']);
		return $ret;
*/
	}
	
	/**
	 *  ユーザー情報参照
	 *
	 *  @access public
	 *  @param  integer  $account  アカウント
	 *  @return array  登録情報
	 */
	public function requestUserCheck($account)
	{
/*
		// 送信パラメータ
		$param = array(
			'account'	=> $account,
		);

		$result = $this->_request("/user/check", $param);
//printLog("LOG: app_id: $this->app_id result = ".print_r($result, true));
$this->backend->logger->log(LOG_INFO, "LOG: app_id: $this->app_id result = ".print_r($result, true));

		if ($result['sts'] === 'NG') {
			return false;
		}
		//format
		$ret = $result['param'];
		//puid
		$ret['puid'] = $ret['user_id'];
		unset($ret['user_id']);
		return $ret;
*/
	}
	
	/**
	 *  ユーザー情報変更
	 *
	 *  @access public
	 *  @param  integer  $puid  Paymentサーバ側が発行したユーザーID
	 *  @param  integer  $type  変更種別(1:パスワード、2:アカウント、3:メールアドレス、4:UIID)
	 *  @param  integer  $value  変更値
	 *  @return array  変更結果
	 *  @throws UserCaveException
	 */
	public function requestUserEdit($puid, $type, $value)
	{
/*
		// 送信パラメータ
		$param = array(
			'user_id'		=> $puid,
			'edit_type'		=> $type,
			'edit_value'	=> $value,
		);

		$result = $this->_request("/user/check", $param);
printLog("LOG: app_id: $this->app_id result = ".print_r($result, true));
		if ($result['sts'] === 'NG') {
			throw new UserCaveException($result['msg']);
		}

		//format
		$ret = $result['param'];
		//puid
		$ret['puid'] = $ret['user_id'];
		unset($ret['user_id']);
		return $ret;
*/
	}
	
	/**
	 *  ユーザーのパラメータを取得
	 *
	 *  @access public
	 *  @param  string  $user_id  ユーザーID
	 *  @return array  DBデータ
	 *
	 */
	public function dbGetUser($user_id)
	{
/*
		$this->createDB($this->needMasterDataBase('t_user_cave'));
		$param = array($user_id);
		$sql = "SELECT * FROM t_user_cave WHERE user_id = ?";
		$result =& $this->db->query($sql, $param);
		if (Ethna::isError($result)) {
			throw new DbQueryException($sql, $this->db->ErrorMsg(), $param);
		}
		return $result->FetchRow();
*/
	}
	
	/**
	 *  ユーザーのパラメータを取得
	 *
	 *  @access public
	 *  @param  integer  $puid  PlatformのユーザーID
	 *  @return array  DBデータ
	 *
	 */
	public function dbGetUserByPuid($puid)
	{
/*
		$this->createDB($this->needMasterDataBase('t_user_cave'));
		$param = array($puid);
		$sql = "SELECT * FROM t_user_cave WHERE puid = ?";
		$result =& $this->db->query($sql, $param);
		if (Ethna::isError($result)) {
			throw new DbQueryException($sql, $this->db->ErrorMsg(), $param);
		}
		return $result->FetchRow();
*/
	}
	
	/**
	 *  ユーザーのパラメータを取得
	 *
	 *  @access public
	 *  @param  integer  $cave_id  ケイブID
	 *  @return array  DBデータ
	 *
	 */
/*
	public function dbGetUserByCaveId($cave_id)
	{
		$this->createDB($this->needMasterDataBase('t_user_cave'));
		$param = array($cave_id);
		$sql = "SELECT * FROM t_user_cave WHERE cave_id = ?";
		$result =& $this->db->query($sql, $param);
		if (Ethna::isError($result)) {
			throw new DbQueryException($sql, $this->db->ErrorMsg(), $param);
		}
		return $result->FetchRow();
	}
*/
	
	/**
	 *  ユーザーのパラメータを取得
	 *
	 *  @access public
	 *  @param  integer  $device_id  端末ID
	 *  @return array  DBデータ
	 *
	 */
/*
	public function dbGetUserByDeviceId($device_id)
	{
		$mem = Cf_Memcache::getInstance( $this->config );
		$ret = $mem->get( 't_user_cave_device_' . $device_id );
		if( $ret !== false ){
			return $ret;
		} else if( $this->needMasterDataBase( 't_user_cave' )){
			$this->createDB();
		} else {
			$this->createDB(false);
		}

		$param = array($device_id);
		$sql = "SELECT * FROM t_user_cave WHERE device_id = ?";
		$result =& $this->db->query($sql, $param);
		if (Ethna::isError($result)) {
			throw new DbQueryException($sql, $this->db->ErrorMsg(), $param);
		}
		$data = $result->FetchRow();
		$mem->set( 't_user_cave_device_' . $device_id, $data );
		return $data;
	}
*/
	
	/**
	 *  ユーザーのパラメータを取得
	 *
	 *  @access public
	 *  @param  string  $account  アカウント
	 *  @param  string  $password  パスワード
	 *  @return array  DBデータ
	 *
	 */
/*
	public function dbGetUserByAccountGlobal($account)
	{
		$this->createDB($this->needMasterDataBase('t_user_cave'));
		$param = array($account);
		$sql = "SELECT * FROM t_user_cave WHERE account = ?";
		$result =& $this->db->query($sql, $param);
		if (Ethna::isError($result)) {
			throw new DbQueryException($sql, $this->db->ErrorMsg(), $param);
		}
		return $result->FetchRow();
	}
*/
	
	/**
	 *  ユーザーのパラメータを取得
	 *
	 *  @access public
	 *  @param  string  $account  アカウント
	 *  @param  string  $password  パスワード
	 *  @return array  DBデータ
	 *
	 */
/*
	public function dbGetUserByAccountAndPassword($account, $password)
	{
		$this->createDB($this->needMasterDataBase('t_user_cave'));
		$param = array($this->app_id, $account, sha1($password));
		$sql = "SELECT * FROM t_user_cave WHERE app_id = ? AND account = ? AND password_hash = ?";
		$result =& $this->db->query($sql, $param);
		if (Ethna::isError($result)) {
			throw new DbQueryException($sql, $this->db->ErrorMsg(), $param);
		}
		return $result->FetchRow();
	}
*/
	
	/**
	 *  ユーザーのパラメータを取得
	 *
	 *  @access public
	 *  @param  string  $account  アカウント
	 *  @param  string  $password  パスワード
	 *  @return array  DBデータ
	 *
	 */
/*
	public function dbGetUserByAccountAndPasswordGlobal($account, $password)
	{
		$this->createDB($this->needMasterDataBase('t_user_cave'));
		$param = array($account, sha1($password));
		$sql = "SELECT * FROM t_user_cave WHERE account = ? AND password_hash = ?";
		$result =& $this->db->query($sql, $param);
		if (Ethna::isError($result)) {
			throw new DbQueryException($sql, $this->db->ErrorMsg(), $param);
		}
		return $result->FetchRow();
	}
*/
	
	/**
	 *  ユーザーのパラメータを更新
	 *
	 *  @access public
	 *  @param  string  $user_id  ユーザーID
	 *  @param  array  $columns  カラム
	 *  @return boolean  true :成功, false: 失敗
	 *
	 */
	public function dbUpdateUser($user_id, $columns)
	{
/*
		$this->createDB();
		$set = array();
		$param = array();
		foreach ($columns as $key => $row) {
			$set[]= $key . " = ?";
			$param[] = $row;
		}
		$set[] = 'date_modified = now()';
		$param[] = sprintf("%s", $user_id);

		$sql = "UPDATE t_user_cave SET " . implode(",", $set) . " WHERE user_id = ?";
		if (!$this->db->execute($sql, $param)) {
			throw new DbQueryException($sql, $this->db->ErrorMsg(), $param);
		}
		// 更新テーブルを記録
		$this->setUpdateTables('t_user_cave');
		Cf_Memcache::getInstance( $this->config )->delete('t_user_cave_device_' . $columns['device_id']);
		return true;
*/
	}
	
	/**
	 *  ユーザーのパラメータを作成
	 *
	 *  @access public
	 *  @param  string  $user_id  ユーザーID
	 *  @param  array  $columns  カラム
	 *  @return boolean  true :成功, false: 失敗
	 *
	 */
	public function dbInsertUser($user_id, $columns)
	{
/*
		$this->createDB();
		$param = array($user_id, $columns['puid'], $columns['cave_id'], $columns['device_id'], $this->app_id, $columns['account'], $columns['password_hash']);
		$sql = "INSERT INTO t_user_cave (user_id, puid, cave_id, device_id, app_id, account, password_hash, date_created, date_modified) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
		if (!$this->db->execute($sql, $param)) {
			throw new DbQueryException($sql, $this->db->ErrorMsg(), $param);
		}
		// 更新テーブルを記録
		$this->setUpdateTables('t_user_cave');
		return true;
*/
	}
}
?>