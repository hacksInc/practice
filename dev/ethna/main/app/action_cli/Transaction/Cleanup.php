<?php
/**
 *  トランザクションデータをクリーンアップする
 *
 *  t_～～テーブルの不要なデータを消去する
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  transaction_cleanup Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_TransactionCleanup extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  transaction_cleanup action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_TransactionCleanup extends Pp_CliActionClass
{
    /**
     *  preprocess of transaction_cleanup Action.
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
     *  transaction_cleanup action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// 引数取得
		// タイムアウトまでの時間（分）
		if (isset($GLOBALS['argv'][2])) {
			$timeout_minutes = $GLOBALS['argv'][2];
		} else {
			$timeout_minutes = 60;
		}

		if (!preg_match('/^[0-9]+$/', $timeout_minutes) ||
			($timeout_minutes <= 0) || ($timeout_minutes > 360)
		) {
			error_log('ERROR:Invalid timeout_minutes[' . $timeout_minutes . ']');
			exit(1);
		}

		// 何件DELETEするごとにスリープするか
		// 0だとスリープしない
		if (isset($GLOBALS['argv'][3])) {
			$sleep_threshold = $GLOBALS['argv'][3];
		} else {
			$sleep_threshold = 100;
		}

		if (!preg_match('/^[0-9]+$/', $sleep_threshold) ||
			($sleep_threshold < 0) || ($sleep_threshold > 1000)
		) {
			error_log('ERROR:Invalid sleep_threshold[' . $sleep_threshold . ']');
			exit(1);
		}
		
		// 対象とするユーザーID下1桁
		if (isset($GLOBALS['argv'][4])) {
			$digit = $GLOBALS['argv'][4];
		} else {
			$digit = floor($_SERVER['REQUEST_TIME'] / 86400) % 10; 
		}
		
		if (!preg_match('/^[0-9]$/', $digit)) {
			error_log('ERROR:Invalid digit[' . $digit . ']');
			exit(1);
		}

		// 各種リソース初期化
		$admin_m   = $this->backend->getManager('Admin');
		$user_m    = $this->backend->getManager('AdminUser');
		$present_m = $this->backend->getManager('AdminPresent');
		$point_m   = $this->backend->getManager('AdminPoint');
		
//		$db =& $this->backend->getDB();

		// タイムアウトまでの時間（秒）
		$timeout = $timeout_minutes * 60;
		
		// タイムアウトする日時（UNIXタイムスタンプ）
		$time_limit = $_SERVER['REQUEST_TIME'] + $timeout;
		
		// このクリーンアップ処理のユニーク値（テンポラリファイル作成時に使用）
		$cleanup_uniq = uniqid($this->config->get('unit_id'));
		
		// 処理開始
		echo "●transaction_cleanup開始 [" . date('Y-m-d H:i:s') . "]\n";
		echo "timeout_minutes:" . $timeout_minutes . "\n";
		echo "sleep_threshold:" . $sleep_threshold . "\n";
		echo "digit:" . $digit . "\n";
		echo "unit:" . $this->config->get('unit_id') . "\n";

		$admin_m->offSessionQueryCache();
		$admin_m->setSessionSqlBigSelectsOn(array('r'));
		
		// バックアップ用ディレクトリを初期化する
		$backup_dir = $admin_m->initTransactionCleanupBackupDir();

		// 対象ユーザーを抽出する
		$tmpfile = $user_m->exportUserBaseForTransactionCleanup($digit, $backup_dir, $cleanup_uniq);
		if (!$tmpfile) {
			echo "ERROR: exportUserBaseForTransactionCleanup failed.\n";
			return false;
		}

		// ユーザー毎に処理
		$cnt = 0;
		$delete_cnt = 0;
		$fp = fopen($tmpfile, 'r');
		while ($line = fgets($fp)) {
			$user_id = rtrim($line);
			echo "check [" . $user_id . "]\n";

			// 対象プレゼントを抽出する
			$tmpfile2 = $present_m->exportUserPresentForTransactionCleanup($user_id, $backup_dir, $cleanup_uniq);
			if (!$tmpfile2) {
				echo "ERROR: exportUserPresentForTransactionCleanup failed.\n";
				continue;
			}
			
			$fp2 = fopen($tmpfile2, 'r');
			while ($line = fgets($fp2)) {
				list($present_id, $user_id, $game_transaction_id) = explode(',', rtrim($line));
				
				// ポイント管理サーバ対応
				if ($game_transaction_id) {
					$point_m->exportPointTransactionForTransactionCleanup($game_transaction_id, $user_id, $backup_dir, $cleanup_uniq);
					
//					$db->begin();
					echo "deleteTransaction [" . $user_id . "] [" . $game_transaction_id . "]\n";
					$ret = $point_m->deleteTransaction($game_transaction_id, $user_id);
					if ($ret !== true) {
						echo "ERROR: deleteTransaction failed.\n";
//						$db->rollback();
						continue;
					}
					
					$delete_cnt++;
//				} else {
//					$db->begin();
				}

				// 対象プレゼントを削除する
				echo "deleteUserPresent [" . $user_id . "] [" . $present_id . "]\n";
				$ret = $present_m->deleteUserPresent($present_id, $user_id);
				if ($ret !== true) {
					echo "ERROR: deleteUserPresent failed.\n";
//					$db->rollback();
					continue;
				}

				$delete_cnt++;
				
//				$db->commit();
				
				if ($sleep_threshold && ($delete_cnt >= $sleep_threshold)) {
					echo "sleep [" . date('Y-m-d H:i:s') . "]\n";
					sleep(1);
					$delete_cnt -= $sleep_threshold;
				}
			}
			
			fclose($fp2);
			unlink($tmpfile2);
			
			$cnt++;
			if ($cnt % 10 != 0) {
				continue;
			}

			$time_cur = time();
			if ($time_cur >= $time_limit) {
				echo "タイムアウト [" . date('Y-m-d H:i:s') . "]\n";
				break;
			}
		}
		
		fclose($fp);
		unlink($tmpfile);
		
		$admin_m->compressTransactionCleanupBackupDir();
		
		echo "終了 [" . date('Y-m-d H:i:s') . "]\n\n";

        return null;
    }
}

?>