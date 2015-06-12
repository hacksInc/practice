<?php
/**
 *  Pp_LogdataViewPointManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';
require_once 'array_column.php';

/**
 *  Pp_LogdataViewPointManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewPointManager extends Pp_LogdataViewManager
{
	/** 検索種別：ユーザーIDによる検索 */
	const SEARCH_TYPE_USER_ID = 1;
	
	/** 検索種別：アクセスエラー検索 */
	const SEARCH_TYPE_STS_NG = 2;
	
	/**
	 * ダウンロード用ファイルbasename
	 * 
	 * ・サーバ内でのテンポラリファイル生成時のファイル名、
	 * ・HTTPレスポンスヘッダで使用するブラウザが認識するファイル名
	 * の両方の用途
	 */
	const DOWNLOAD_BASENAME = 'point_log_data';
	
	/**
	 * 不正レシート
	 */
	protected $MALFORMED_RECEIPT_LIST = array(
		'ewoJInNpZ25hdHVyZSIgPSAiQXBkeEpkdE53UFUyckE1L2NuM2tJTzFPVGsyNWZlREthMGFhZ3l5UnZlV2xjRmxnbHY2UkY2em5raUJTM3VtOVVjN3BWb2IrUHFaUjJUOHd5VnJITnBsb2YzRFgzSXFET2xXcSs5MGE3WWwrcXJSN0E3ald3dml3NzA4UFMrNjdQeUhSbmhPL0c3YlZxZ1JwRXI2RXVGeWJpVTFGWEFpWEpjNmxzMVlBc3NReEFBQURWekNDQTFNd2dnSTdvQU1DQVFJQ0NHVVVrVTNaV0FTMU1BMEdDU3FHU0liM0RRRUJCUVVBTUg4eEN6QUpCZ05WQkFZVEFsVlRNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURXpNREVHQTFVRUF3d3FRWEJ3YkdVZ2FWUjFibVZ6SUZOMGIzSmxJRU5sY25ScFptbGpZWFJwYjI0Z1FYVjBhRzl5YVhSNU1CNFhEVEE1TURZeE5USXlNRFUxTmxvWERURTBNRFl4TkRJeU1EVTFObG93WkRFak1DRUdBMVVFQXd3YVVIVnlZMmhoYzJWU1pXTmxhWEIwUTJWeWRHbG1hV05oZEdVeEd6QVpCZ05WQkFzTUVrRndjR3hsSUdsVWRXNWxjeUJUZEc5eVpURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd2daOHdEUVlKS29aSWh2Y05BUUVCQlFBRGdZMEFNSUdKQW9HQkFNclJqRjJjdDRJclNkaVRDaGFPPGc4cHd2L2NtSHM4cC9Sd1YvcnQvOTFYS1ZoTmw0WElCaW1LalFRTmZnSHNEczZ5anUrK0RyS0pFN3VLc3BoTWRkS1lmRkU1ckdYc0FkQkVqQndSSXhleFRldngzSExFRkdBdDFtb0t4NTA5ZGh4dGlJZERnSnYyWWFWczQ5QjB1SnZOZHk2U01xTk5MSHNETHpEUzlvWkhBZ01CQUFHamNqQndNQXdHQTFVZEV3RUIvd1FDTUFBd0h3WURWUjBqQkJnd0ZvQVVOaDNvNHAyQzBnRVl0VEpyRHRkREM1RllRem93RGdZRFZSMFBBUUgvQkFRREFnZUFNQjBHQTFVZERnUVdCQlNwZzRQeUdVakZQaEpYQ0JUTXphTittVjhrOVRBUUJnb3Foa2lHOTJOa0JnVUJCQUlGQURBTkJna3Foa2lHOXcwQkFRVUZBQU9DQVFFQUVhU2JQanRtTjRDL0lCM1FFcEszMlJ4YWNDRFhkVlhBZVZSZVM1RmFaeGMrdDg4cFFQOTNCaUF4dmRXLzNlVFNNR1k1RmJlQVlMM2V0cVA1Z204d3JGb2pYMGlreVZSU3RRKy9BUTBLRWp0cUIwN2tMczlRVWU4Y3pSOFVHZmRNMUV1bVYvVWd2RGQ0TndOWXhMUU1nNFdUUWZna1FRVnk4R1had1ZIZ2JFL1VDNlk3MDUzcEdYQms1MU5QTTN3b3hoZDNnU1PPdlhqK2xvSHNTdGNURXFlOXBCRHBtRzUrc2s0dHcrR0szR01lRU41LytlMVFUOW5wL0tsMW5qK2FCdzdDMHhzeTBiRm5hQWQxY1NTNnhkb3J5L0NVdk02Z3RLc21uT09kcVRlc2JwMGJzOHNuNldxczBDOWRnY3hSSHVPTVoydG04bnBMVW03YXJnT1N6UT09IjsKCSJwdXJjaGFzZS1pbmZvIiA9ICJld29KSW05eWFXZHBibUZzTFhCMWNtTm9ZWE5sTFdSaGRHVXRjSE4wSWlBOUlDSXlNREV5TFRBM0xURXlJREExT2pVME9qTTFJRUZ0WlhKcFkyRXZURzl6WDBGdVoyVnNaWE1pT3dvSkluQjFjbU5vWVhObExXUmhkR1V0YlhNaUlEMGdJakV6TkRJd09UYzJOelU0T0RJaU93b0pJbTl5YVdkcGJtRnNMWFJ5WVc1ellXTjBhVzl1TFdsa0lpQTlJQ0l4TnpBd01EQXdNamswTkRrME1qQWlPd29KSW1KMmNuTWlJRDBnSWpFdU5DSTdDZ2tpWVhCd0xXbDBaVzB0YVdRaUlEMGdJalExTURVME1qSXpNeUk3Q2draWRISmhibk5oWTNScGIyNHRhV1FpSUQwZ0lqRTNNREF3TURBeU9UUTBPVFF5TUNJN0Nna2ljWFZoYm5ScGRIa2lJRDBnSWpFaU93b0pJbTl5YVdkcGJtRnNMWEIxY21Ob1lYTmxMV1JoZEdVdGJYTWlJRDBnSWpFek5ESXdPVGMyTnpVNE9ESWlPd29KSW1sMFpXMHRhV1FpSUQwZ0lqVXpOREU0TlRBME1pSTdDZ2tpZG1WeWMybHZiaTFsZUhSbGNtNWhiQzFwWkdWdWRHbG1hV1Z5SWlBOUlDSTVNRFV4TWpNMklqc0tDU0p3Y205a2RXTjBMV2xrSWlBOUlDSmpiMjB1ZW1Wd2RHOXNZV0l1WTNSeVltOXVkWE11YzNWd1pYSndiM2RsY2pFaU93b0pJbkIxY21Ob1lYTmxMV1JoZEdVaUlEMGdJakl3TVRJdE1EY3RNVElnTVRJNk5UUTZNelVnUlhSakwwZE5WQ0k3Q2draWIzSnBaMmx1WVd3dGNIVnlZMmhoYzJVdFpHRjBaU0lnUFNBaU1qQXhNaTB3TnkweE1pQXhNam8xTkRvek5TQkZkR012UjAxVUlqc0tDU0ppYVdRaUlEMGdJbU52YlM1NlpYQjBiMnhoWWk1amRISmxlSEJsY21sdFpXNTBjeUk3Q2draWNIVnlZMmhoYzJVdFpHRjBaUzF3YzNRaUlEMGdJakl3TVRJdE1EY3RNVElnTURVNk5UUTZNelVnUVcxbGNtbGpZUzlNYjNOZlFXNW5aV3hsY3lJN0NuMD0iOwoJInBvZCIgPSAiMTciOwoJInNpZ25pbmctc3RhdHVzIiA9ICIwIjsKfQ',
	);
	
	/**
	 * DB接続(pp-ini.phpの'dsn_logex'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_logex = null;
	
	/**
	 * DB接続(pp-ini.phpの'dsn_logex_r'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_logex_r = null;
	
	/**
	 * ポイント管理通信リクエストログIDリスト取得用のSQLクエリ情報
	 * 
	 * @see function queryLogPointRequest
	 */
	protected $log_point_request_query = array();
	
	/**
	 * ポイント管理通信リクエストログ参照用の共通のSQL句を取得する
	 * 
	 * 検索パラメータについては、queryLogPointRequest関数と同じなのでそちらを参照
	 * @param array $search_params 検索パラメータ
	 * @return array array($sql, $param)
	 */
	protected function getLogPointRequestCommonSqlClause($search_params)
	{
		$date_from = $search_params['date_from'];
		$date_to   = $search_params['date_to'];
		$user_id   = isset($search_params['user_id']) ? $search_params['user_id'] : null;
		$sts       = isset($search_params['sts'])     ? $search_params['sts']     : null;
		
		$param = array($date_from, $date_to);
		$sql = " FROM log_point_request"
		     . " WHERE ? <= date_created AND date_created < ?";

		if ($user_id) {
			$param[] = $user_id;
			$sql .= " AND user_id = ?";
		}
		
		if ($sts) {
			$param[] = $sts;
			$sql .= " AND result_sts = ?";
		}
		
		return array($sql, $param);
	}
	
	
	/**
	 * ポイント管理通信リクエストログを数える
	 * 
	 * 検索パラメータについては、queryLogPointRequest関数と同じなのでそちらを参照
	 * @param array $search_params 検索パラメータ
	 * @return int 行数
	 */
	function countLogPointRequest($search_params)
	{
		if (!$this->db_logex_r) {
			$this->db_logex_r =& $this->backend->getDB('logex_r');
		}
		
		list($sql, $param) = $this->getLogPointRequestCommonSqlClause($search_params);
		
		$sql = "SELECT COUNT(*) " . $sql;
		
		return $this->db_logex_r->GetOne($sql, $param);
	}
	
	/**
	 * ポイント管理通信リクエストログを数える（セッションキャッシュ使用）
	 * 
	 * 引数・戻り値はcountLogPointRequest関数と同じ
	 */
	function cacheCountLogPointRequest($search_params)
	{
		$tmp_arr = array_map('strval', $search_params);
		ksort($tmp_arr);
		$session_key = serialize($tmp_arr);

		$count = $this->session->get($session_key);
		if (!is_numeric($count)) {
			$count = $this->countLogPointRequest($search_params);
			$this->session->set($session_key, $count);
		}
		
		return $count;
	}
	
	/**
	 * ポイント管理通信リクエストログ一覧を取得する
	 * 
	 * 検索パラメータについては、queryLogPointRequest関数と同じなのでそちらを参照
	 * @param array $search_params 検索パラメータ
	 * @param int $limit 最大何件取得するか ※必須なので注意
	 * @param int $offset 何件目から取得するか
	 * @return array ポイント管理通信リクエストログの連想配列の配列 各行のキーは、log_point_requestテーブルのカラムと同名
	 */
	function getLogPointRequestList($search_params, $limit, $offset = null)
	{
		if (!$this->db_logex_r) {
			$this->db_logex_r =& $this->backend->getDB('logex_r');
		}

		$select_clause = "SELECT id, game_transaction_id, user_id, remote_addr, action, game_arg, result, result_sts, date_created ";
		$order_clause = " ORDER BY date_created DESC, id DESC";

		list($common_clause, $param) = $this->getLogPointRequestCommonSqlClause($search_params);
		
		// 以下の2段階に分けて処理する方が短時間で終わる。
		// 1. まずidのみ取得
		$sql = "SELECT id " . $common_clause . $order_clause;
		$param[] = $offset ? intval($offset) : 0;
		$param[] = intval($limit);
		$sql .= " LIMIT ?, ?";

		$id_list = $this->db_logex_r->GetCol($sql, $param);

		// 2. id指定で各種カラムを取得
		$sql2 = $select_clause
		      . " FROM log_point_request WHERE id IN(". implode(",", $id_list) . ")"
		      . $order_clause;
		
		return $this->db_logex_r->GetAll($sql2);
	}
	
	/**
	 * ポイント管理通信リクエストログ一覧を付加情報付きで取得する
	 * 
	 * 検索パラメータについては、queryLogPointRequest関数と同じなのでそちらを参照
	 * @param array $search_params 検索パラメータ
	 * @param int $limit 最大何件取得するか ※必須なので注意
	 * @param int $offset 何件目から取得するか
	 * @return array ポイント管理通信リクエストログの連想配列の配列 各行のキーは、log_point_requestテーブルのカラムと同名のものに加えて、"receipt_check", "result_short"が付加される。
	 */
	function getLogPointRequestListEx($search_params, $limit, $offset)
	{
		$rows = $this->getLogPointRequestList($search_params, $limit, $offset);
		if (is_array($rows)) foreach ($rows as $i => $row) {
			$rows[$i]['receipt_check'] = $this->checkLogPointRequestReceipt(
				$row['game_arg'], $row['result']
			);
			
			$rows[$i]['result_short'] = substr($row['result'], 0, 12);
		}
		
		return $rows;
	}
	
	/**
	 * ポイント管理通信リクエストログについてレシートチェックを行なう
	 * 
	 * @param string|array $game_arg アプリ→ゲームサーバ引数(JSON文字列 or 連想配列)
	 * @param string|array $result cURLセッション実行結果(JSON文字列 or 連想配列)
	 * @return string|bool チェック結果を表すラベル文字列（"不正","OK"等） エラー時はfalse
	 */
	protected function checkLogPointRequestReceipt($game_arg, $result)
	{
		// 引数チェック
		foreach (array('game_arg', 'result') as $varname) {
			if (is_string($$varname)) {
				$$varname = json_decode($$varname, true);
			}
			
			if (!is_array($$varname)) {
				//error
				return false;
			}
		}
		
		if (!isset($game_arg['google_transaction_or_apple_receipt']) ||
		    (strlen($game_arg['google_transaction_or_apple_receipt']) == 0)
		) {
			// レシートが関係ない場合、"-"
			return '-';
		}
		
		// 特定の不正レシートであれば、"不正"
		foreach ($this->MALFORMED_RECEIPT_LIST as $malformed_receipt) {
			if (strcmp($game_arg['google_transaction_or_apple_receipt'], $malformed_receipt) === 0) {
				return '不正';
			}
		}
		
		if (isset($result['sts'])) {
			if ($result['sts'] == 'NG') {
				// 実行結果NGで判別できない場合、"不明" 
				return '不明';
			} else if ($result['sts'] == 'OK') {
				// 実行結果がOKであれば、"OK" 
				return 'OK';
			}
		}
	}
	
	/**
	 * ポイント管理通信リクエストログCSVファイルを作成する
	 * 
	 * 検索パラメータについては、queryLogPointRequest関数と同じなのでそちらを参照
	 * @param array $search_params 検索パラメータ
     * @return mixed $file_name 正常時
     *                false エラー時
	 */
    public function createCsvFileLogPointRequest($search_params)
    {
		// CSV書式関連の情報
		$info = array(
			array('id',                  'ID'),
			array('user_id',             'ユーザーID'),
			array('remote_addr',         'アクセス元IP'),
			array('game_transaction_id', 'ゲームトランザクションID'),
			array('action',              '実行アクション'),
			array('result_short',        '実行結果'),
			array('date_created',        '登録日'),
			array('receipt_check',       'レシート'),
			array('game_arg',            'アプリデータ'),
			array('result',              '送信データ'),
		);
		
		$keys   = array_column($info, 0);
		$labels = array_column($info, 1);
		$keys_cnt = count($keys);
		
//		$params_csv_rows = array();
//		foreach ($search_params as $k => $v) {
//			$params_csv_rows[] = array($k, $v);
//		}
		
		// ファイルオープン
		$file_name = $this->createCsvFileName(self::DOWNLOAD_BASENAME);
		$tmp_dir = $this->backend->ctl->getDirectory('tmp');
		$fp = fopen("$tmp_dir/$file_name", 'w');
		if ($fp === false) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
		
		// 検索条件を出力
//		$ret = fwrite($fp, Util::assembleCsv($params_csv_rows));
//		if ($ret === false) {
//			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
//			return false;
//		}
		
		// ヘッダ行を出力
		$ret = fwrite($fp, Util::assembleCsv(array($labels)));
		if ($ret === false) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		// データをDBから取得＆出力
		$partial_limit = 100;
		$cnt = 0;
		while (1) {
			$rows = $this->getLogPointRequestListEx($search_params, $partial_limit, $partial_limit * $cnt);
			if (!is_array($rows) || empty($rows)) {
				break;
			}
			
			$csv_rows = array();
			foreach ($rows as $row) {
				$arr = array();
				for ($i = 0; $i < $keys_cnt; $i++) {
					$arr[] = $row[$keys[$i]];
				}
				
				$csv_rows[] = $arr;
			}
			
			$ret = fwrite($fp, Util::assembleCsv($csv_rows));
			if ($ret === false) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
			
			$cnt++;
			if ($cnt > 999) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
		}

		fclose($fp);
		
		return $file_name;
    }
	
	/**
	 * レプリケーション遅延をチェックする
	 * 
	 * @param string $date_created チェック対象とする登録日時の上限（端点含まない）
	 * @return bool チェックOKか
	 */
	function checkLogPointRequestReplica($date_created)
	{
		if (!$this->db_logex) {
			$this->db_logex =& $this->backend->getDB('logex');
		}

		if (!$this->db_logex_r) {
			$this->db_logex_r =& $this->backend->getDB('logex_r');
		}
		
		$param = array($date_created);
		$sql = "SELECT MAX(id) FROM log_point_request WHERE date_created < ?";

		$master_max_id = $this->db_logex->GetOne($sql, $param);
		$slave_max_id  = $this->db_logex_r->GetOne($sql, $param);
		
		return ($master_max_id == $slave_max_id);
	}
}
