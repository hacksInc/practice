<?php
/**
 *  Adhoc/Point20141010.php
 *
 *  ポイント管理サーバへのHTTPリクエスト再送信
 *  2014年10月10日不具合対応
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  adhoc_point20141010 Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_AdhocPoint20141010 extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  adhoc_point20141010 action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_AdhocPoint20141010 extends Pp_CliActionClass
{
    /**
     *  preprocess of adhoc_point20141010 Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  adhoc_point20141010 action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
//    function perform()
//    {
//		$date_from = '2014-10-09 04:10:00';
//		$date_to   = date('Y-m-d H:i:s');
//		
//		echo "adhoc_point20141010\n";
//		echo "start[" . date('Y-m-d H:i:s') . "]\n";
//		echo "\n";
//		echo "date_from[" . $date_from . "]\n";
//		echo "date_to[" . $date_to . "]\n";
//		echo "\n";
//		
//		$admin_m =& $this->backend->getManager('Admin');
//		
//		$admin_m->setSessionSqlBigSelectsOn();
//	
//		$db =& $this->backend->getDB('logex');
//		
//		// ポイント管理サーバの最後のバックアップ時点以降のログを取り出す
//		$param = array($date_from, $date_to);
//		$sql = "SELECT *"
//		     . " FROM log_point_request"
//		     . " WHERE ? <= date_created AND date_created < ?"
//		     . " ORDER BY id";
//		
//		$adodb_countrecs_old = $admin_m->setAdodbCountrecs(false);
//		$result =& $db->query($sql, $param);
//
//		$cnt = 0;
//		while ($row = $result->FetchRow()) {
//
//			$log = array(
//				'err_msgs' => array(),
//			);
//			
//			$log['origin'] = $row;
//			
//			$request_flg = true; // HTTPリクエストするか
//			
//			$id = $row['id'];
//			echo "id[$id]\n";
//			
//
//			if (empty($row['opts'])) {
//				$request_flg = false;
//				$log['err_msgs'][] = 'Empty opts.';
//			}
//			
//			$opts = json_decode($row['opts'], true);
//			if (empty($opts)) {
//				$request_flg = false;
//				$log['err_msgs'][] = 'Invalid opts.';
//			}
//			
//
//			if (empty($row['info'])) {
//				$request_flg = false;
//				$log['err_msgs'][] = 'Empty info.';
//			}
//			
//			$info = json_decode($row['info'], true);
//			if (empty($opts)) {
//				$request_flg = false;
//				$log['err_msgs'][] = 'Invalid info.';
//			}
//
//			// HTTPリクエストを再送する
//			if ($request_flg) {
//				$log['retry'] = $this->request($opts);
//			}
//			
//			$this->log($id, $log);
//			
//			$cnt++;
//			if (($cnt % 100) == 0) {
//				sleep(2);
//			}
//		}
//		
//		$admin_m->setAdodbCountrecs($adodb_countrecs_old);
//
//		echo "end[" . date('Y-m-d H:i:s') . "]\n";
//		
//        return null;
//    }
	
//	protected function request($opts)
//	{
//		// 初期化
//		$ch = curl_init();
//		
//		// オプションを設定
//		curl_setopt_array($ch, $opts);
//		
//		// 実行
//		$result = curl_exec($ch);
//
//		// 取得結果判定
//		$info = curl_getinfo($ch);
//
//		$last_curl = array(
//			'opts'   => $opts,
//			'info'   => $info,
//			'result' => $result,
//			'errno'  => null,
//			'error'  => null,
//		);
//		
//		if ($result === false) {
//			$last_curl['errno'] = curl_errno($ch);
//			$last_curl['error'] = curl_error($ch);
//			
//			echo "ERROR:curl error.\n";
//		}
//
//		// 終了
//		curl_close($ch);
//		
//		return $last_curl;
//	}
//	
//	protected function log($id, $log)
//	{
//		static $dir = null;
//		if (!$dir) {
//			$dir = BASE . '/log/point20141010_' . uniqid();
//		}
//		
//		if (!is_dir($dir)) {
//			$ret = mkdir($dir);
//			if (!$ret) {
//				echo "ERROR:mkdir failed.\n";
//				return;
//			}
//		}
//		
//		$json = json_encode($log);
//		
//		$filename = "$dir/id$id.json";
//		
//		$ret = file_put_contents($filename, $json);
//		if (!$ret) {
//			echo "ERROR:file_put_contents failed.\n";
//		}
//	}
}

?>