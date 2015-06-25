<?php
/**
 *  Admin/Developer/Master/Sync/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Pp_Form_AdminDeveloperMasterSync.php';

/**
 *  admin_developer_master_sync_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterSyncExec extends Pp_Form_AdminDeveloperMasterSync
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'mode' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,                 // Required Option(true/false)
            'min'         => null,                 // Minimum value
            'max'         => 16,                   // Maximum value
            'regexp'      => '/^deploy|refresh|standby$/', // String by Regexp
        ),
        'table' => array(
            'required'    => true,                // Required Option(true/false)
        ),
		'algorithms',
    );
}

/**
 *  admin_developer_master_sync_exec action implementation.
 *
 *  ※admin_developer_master_sync_multi_exec経由でこのアクションが実行される場合もある
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterSyncExec extends Pp_AdminActionClass
{
	/** 
	 * diff結果を表示する際の最大行数（1回のdiffごとの最大値）
	 */
	const DIFF_MAX_LINES = 100;
	
	/** 
	 * diff結果を表示する際の最大バイト数（複数回のdiffの合計値） 
	 */
	const DIFF_TOTAL_BUF_SIZE = 1048576;
	
    /**
     *  preprocess of admin_developer_master_sync_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
/*
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }
*/

    /**
     *  admin_developer_master_sync_exec action implementation.
     *
	 *  @see http://www.percona.com/doc/percona-toolkit/2.2/pt-table-sync.html
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$mode  = $this->af->get('mode');
		$table = $this->af->get('table');
		
		if ($table) {
			$tables = array($table);
		} else {
			// admin_developer_master_sync_multi_exec経由の場合
			$tables = $this->af->get('tables');
		}
		
		if (count($tables) == 0) {
			return 'admin_error_500';
		}
		
		$algorithms = $this->af->get('algorithms');
		if ((strlen($algorithms) == 0) || ($algorithms == 'default')) {
			$algorithms = null;
		}
        
        $function = null;
        if (!$algorithms) {
            $function = 'SHA1';
        }

		$developer_m =& $this->backend->getManager('Developer');
		
		$ssh = null; // ポートフォワード用SSHコマンド

		if ($mode == 'deploy') {
			$src_dsn = $this->config->get('dsn_src');
			$dest_dsn = $this->config->get('dsn');
			
		} else if ($mode == 'refresh') {
			$src_dsn = $this->config->get('dsn');
			$dest_dsn = $this->config->get('dsn_src');
			
		} else if ($mode == 'standby') {
			$src_dsn = $this->config->get('dsn');
			$dest_dsn = $this->config->get('dsn_dest');
			
			$ssh = $this->config->get('ssh_dest');
			
		} else {
			$this->af->ae->add(null, "この環境では実行できません。", E_ERROR_DEFAULT);
			return 'admin_error_400';
		}
		
		$src  = $developer_m->getPtDsn($src_dsn);
		$dest = $developer_m->getPtDsn($dest_dsn);
		
		$mysqldump_opt = array(
			'src' => $this->getMysqldumpOptions($src_dsn),
			'dest' => $this->getMysqldumpOptions($dest_dsn),
		);
		
		if (!$src || !$dest || !$mysqldump_opt['src'] || !$mysqldump_opt['dest']) {
			$this->af->ae->add(null, "この環境では実行できません。", E_ERROR_DEFAULT);
			return 'admin_error_400';
		}
		
		// SSHポートフォワード
		if ($ssh) {
			$command = $ssh . ' > /dev/null 2>&1 &';
			// 注意:
			// プログラムがこの関数で始まる場合、バックグラウンドで処理を続けさせるには、
			// プログラムの出力をファイルや別の出力ストリームにリダイレクトする必要があります。
			// そうしないと、プログラムが実行を終えるまでPHPはハングしてしまいます。
			// http://php.net/manual/ja/function.exec.php
			
			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->logger->log(LOG_WARNING, 'command:' . $command);
			$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
			
			if ($return_var) {
		        return 'admin_error_500';
			}
			
			sleep(5);
		}
		
		// 同期する
		// http://www.percona.com/doc/percona-toolkit/2.2/pt-table-sync.html#options
		$sync_result_assoc = array();
		foreach ($tables as $table) {
//			$command = "pt-table-sync --dry-run"
			$command = "pt-table-sync --execute --verbose"
					 . " --nocheck-slave --charset=utf8"
					 . ($algorithms ? " --algorithms=$algorithms" : "")
					 . ($function ? " --function=$function" : "")
					 . " $src,t=$table $dest";

			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->logger->log(LOG_WARNING, 'command:' . $command);
			$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
			
			$sync_result = $return_var; // 後で判定するので同期結果を保持しておく
			$sync_result_assoc[$table] = $sync_result;
		}
		
		// 差異が無いことを確認する
		$verify_result_assoc = array();
		$verify_info_assoc = array();
		$total_output_len = 0;
		$date = date('YmdHis', $_SERVER['REQUEST_TIME']);
		foreach ($sync_result_assoc as $table => $sync_result) {
			if ($developer_m->getPtTableSyncExitStatusType($sync_result) !== 'OK') {
				continue;
			}
			
			$filename = array();
			foreach (array('src', 'dest') as $mysqldump_key) {
				$mysqldump_command = 'mysqldump ' . $mysqldump_opt[$mysqldump_key] . ' ' . $table;
				$filename[$mysqldump_key] = BASE . '/tmp/master_sync_' . $date . '_' . $table . '_' . $mysqldump_key . '.sql';
				
				$command = $mysqldump_command . ' > ' . $filename[$mysqldump_key];
				$output = null;
				$return_var = null;
				exec($command, $output, $return_var);
				$this->logger->log(LOG_WARNING, 'command:' . $command);
				$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
				$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
			}
			
			$command = 'diff ' . $filename['src'] . ' ' . $filename['dest'] . '';
			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->logger->log(LOG_WARNING, 'command:' . $command);
			//$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->logger->log(LOG_WARNING, 'output:' . implode("\n", array_slice($output, 0, 10)));
			$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
			
			// 返り値
			// diff は以下の値のどれかで終了する:
			// 0      全く変更がなかった。
			// 1      変更があった。
			// 2      何らかのエラーが起こった。
			if ($return_var == 0) {
				$verify_result = Pp_DeveloperManager::MASTER_SYNC_VERIFY_OK;
			} else {
				$verify_result = Pp_DeveloperManager::MASTER_SYNC_VERIFY_NG;
			}
			
			$verify_result_assoc[$table] = $verify_result;
			
			// diffからの出力を変数に保持（エラー画面で使用するので）
			if ($total_output_len > self::DIFF_TOTAL_BUF_SIZE) {
				$output_tmp = "...(差分が多過ぎます)";
			} else {
				$output_tmp = implode("\n", array_slice($output, 0, self::DIFF_MAX_LINES));
				if (isset($output[self::DIFF_MAX_LINES])) {
					$output_tmp .= "\n...(差分が多過ぎます)";
				}

				$total_output_len += strlen($output_tmp);
			}
			
			$verify_info_assoc[$table] = array(
				'command'    => $command,
				'output'     => $output_tmp,
				'return_var' => $return_var,
			);
			
			if ($verify_result == Pp_DeveloperManager::MASTER_SYNC_VERIFY_OK) {
				unlink($filename['src']);
				unlink($filename['dest']);
			}
		}

		// SSHポートフォワード終了
		if ($ssh) {
			$command = "pkill -u `whoami` -f \"$ssh\"";
			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->logger->log(LOG_WARNING, 'command:' . $command);
			$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
		}
		
		// ログ
		$lid = $this->session->get('lid');
		$action = $this->backend->ctl->getCurrentActionName();
		foreach ($sync_result_assoc as $table => $sync_result) {
			if (isset($verify_result_assoc[$table])) {
				$verify_result = $verify_result_assoc[$table];
			} else {
				$verify_result = null;
			}
			
			$developer_m->logMasterSync(array(
				'table_name'  => $table,
				'mode'        => $mode,
				'sync_result' => $sync_result,
				'verify_result' => $verify_result,
				'action'      => $action,
				'account_reg' => $lid,
			));
		}

		// 結果を判定
		$app_errors = array();
		foreach ($sync_result_assoc as $table => $sync_result) {
			if ($developer_m->getPtTableSyncExitStatusType($sync_result) !== 'OK') {
				$message = "pt-table-syncコマンドでエラーが起きました。同期に失敗した可能性があります。table=[$table]";
				$app_errors[] = array(
					'message' => $message,
				);
			
				$this->ae->add(null, $message);
			} else if ($verify_result_assoc[$table] != Pp_DeveloperManager::MASTER_SYNC_VERIFY_OK) {
				$message = "diffコマンドで差異が検出されました。同期に失敗した可能性があります。table=[$table]";
				$app_errors[] = array(
					'message' => $message,
					'verify_info' => $verify_info_assoc[$table],
				);
			
				$this->ae->add(null, $message);
			}
		}
		
		if ($this->ae->count() > 0) {
			$this->af->setApp('app_errors', $app_errors);
		
//	        return 'admin_error_500';
	        return 'admin_developer_master_sync_error';
		}		
		
		return 'admin_developer_master_sync_exec';
    }
	
	// このアクション内で実行するmysqldump用のオプション指定文字列を取得する
	protected function getMysqldumpOptions($dsn)
	{
		$db =& $this->backend->getDB();
		$parsed = $db->parseDSN($dsn);
		
		$options = '--lock-tables=0 --add-locks=0 --extended-insert=0 --skip-comments --order-by-primary'
		         . ' -u ' . $parsed['username']
		         . ' --password=' . $parsed['password']
		         . ' -h ' . $parsed['hostspec'];
		
		if (isset($parsed['port']) && $parsed['port']) {
			$options .= ' -P ' . $parsed['port'];
		}
		
		$options .= ' ' . $parsed['database'];
		
		return $options;
	}
}
?>