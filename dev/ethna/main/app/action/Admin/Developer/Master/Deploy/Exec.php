<?php
/**
 *  Admin/Developer/Master/Deploy/Exec.php
 *  DBの商用デプロイ
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_master_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterDeployExec extends Pp_AdminActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'mode' => array(
			'type'        => VAR_TYPE_STRING, // Input type
			'required'    => true,                 // Required Option(true/false)
			'min'         => null,                 // Minimum value
			'max'         => 16,                   // Maximum value
			'regexp'      => '/^deploy|refresh|standby$/', // String by Regexp
		),

		/*
		 *  TODO: Write form definition which this action uses.
		 *  @see http://ethna.jp/ethna-document-dev_guide-form.html
		 *
		 *  Example(You can omit all elements except for "type" one) :
		 *
		 *  'sample' => array(
		 *      // Form definition
		 *      'type'        => VAR_TYPE_INT,    // Input type
		 *      'form_type'   => FORM_TYPE_TEXT,  // Form type
		 *      'name'        => 'Sample',        // Display name
		 *
		 *      //  Validator (executes Validator by written order.)
		 *      'required'    => true,            // Required Option(true/false)
		 *      'min'         => null,            // Minimum value
		 *      'max'         => null,            // Maximum value
		 *      'regexp'      => null,            // String by Regexp
		 *      'mbregexp'    => null,            // Multibype string by Regexp
		 *      'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp
		 *
		 *      //  Filter
		 *      'filter'      => 'sample',        // Optional Input filter to convert input
		 *      'custom'      => null,            // Optional method name which
		 *                                        // is defined in this(parent) class.
		 *  ),
		 */
	);

	/**
	 *  Form input value convert filter : sample
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	/*
	function _filter_sample($value)
	{
		//  convert to upper case.
		return strtoupper($value);
	}
	 */

	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);

		$developer_m =& $this->backend->getManager('Developer');
		$tables = $developer_m->MASTER_INDEX_TABLES;

		foreach ($tables as $table) {
			$this->form[$table] = array(
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_HIDDEN,   // Form type
				//'name'        => $table, // Display name
				'required'    => false,             // Required Option(true/false)
			);
		}

	}

}

/**
 *  admin_developer_master_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterDeployExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_developer_master_exec Action.
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
	 *  admin_developer_master_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$mode  = $this->af->get('mode');

		$db =& $this->backend->getDB();
		$developer_m =& $this->backend->getManager('Developer');

		$port_forwarding = false;

		switch($mode) {
		case 'deploy':
			$src_dsn = $this->config->get('dsn_src');
			$dest_dsn = $this->config->get('dsn_m');
			break;
		case 'refresh':
			$src_dsn = $this->config->get('dsn_m');
			$dest_dsn = $this->config->get('dsn_src');
			break;
		case 'standby':
			$src_dsn = $this->config->get('dsn_m');
			$dest_dsn = $this->config->get('dsn_dest');
			$port_forwarding = true;
			break;
		default:
			$this->af->ae->add(null, "この環境では実行できません。", E_ERROR_DEFAULT);
			return 'admin_error_400';
			break;
		}

		//		$labels = $developer_m->getMasterTableLabelAssoc();
		$tables = $developer_m->MASTER_INDEX_TABLES;
		$table_list = array();
		//		foreach ($labels as $table => $label) {
		foreach ($tables as $table) {
			$form_table = $this->af->get($table);
			if (!empty($form_table)) {
				$table_list[] = $table;
			}
		}

		$src_parse = $db->parseDSN($src_dsn);
		$dest_parse = $db->parseDSN($dest_dsn);

		/** dump file */
		$tmp_dir = $this->backend->ctl->getDirectory('tmp');
		$src_filename = $tmp_dir."/deploy/dump_src_".date('YmdHis').".sql";
		$dest_filename = $tmp_dir."/deploy/dump_dest_".date('YmdHis').".sql";
		$src_dir = dirname($src_filename);
		$dest_dir = dirname($dest_filename);
		if (!file_exists($src_dir)) mkdir($src_dir, 0755, true);
		if (!file_exists($dest_dir)) mkdir($dest_dir, 0755, true);

		/** dump */
		$dump_command_path = exec('which mysqldump');
		if (empty($dump_command_path)) {
			// mysqldumpがない場合の処理
			$this->af->ae->add(null, "mysqldumpが見つかりません。", E_ERROR_DEFAULT);
			return 'admin_error_400';
		}
		$command = $this->getMysqlDumpCommand($dump_command_path, $src_parse, $table_list, $src_filename);
		$output = null;
		$return_var = null;
		exec($command, $output, $return_var);
		$this->logger->log(LOG_WARNING, 'command:' . $command);
		$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
		$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);

		if ($return_var) {
			return 'admin_error_500';
		}


		/** リストア */
		$mysql_command_path = exec('which mysql');
		if (empty($dump_command_path)) {
			// mysqldumpがない場合の処理
			$this->af->ae->add(null, "mysqlが見つかりません。", E_ERROR_DEFAULT);
			return 'admin_error_400';
		}

		// デプロイ処理
		if ( $port_forwarding ) {
			// STG→スタンバイへの転送はポートフォワーディングを使用する
			// 下記をPHPで実行すると固まるので、BGで動いているポートフォワーディングを利用する
//			shell_exec( "ssh ptpyco@157.7.233.88 -p 10022 -L 10222:game_master_db:3306 -N -f" );
			
			// mysql -u xxx -h xxx --password=xxx[ -p xxx ] database > src_filename
			$option = " --port=10000";
//			if (!empty($dest_parse['port'])) $option .= " -P ".$dest_parse['port'];
			$command = sprintf(
				$mysql_command_path." -u %s -h %s --password=%s %s %s < %s",
				$dest_parse['username'],
				'127.0.0.1', //$dest_parse['hostspec'],
				$dest_parse['password'],
				$option,
				$dest_parse['database'],
				$src_filename
			);

			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->logger->log(LOG_WARNING, 'command:' . $command);
			$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
			$sync_result = $return_var;

			if ($return_var) {
				return 'admin_error_500';
			}
		} else {
			// mysql -u xxx -h xxx --password=xxx[ -p xxx ] database > src_filename
			$option = "";
			if (!empty($dest_parse['port'])) $option .= " -P ".$dest_parse['port'];
			$command = sprintf(
				$mysql_command_path." -u %s -h %s --password=%s %s %s < %s",
				$dest_parse['username'],
				$dest_parse['hostspec'],
				$dest_parse['password'],
				$option,
				$dest_parse['database'],
				$src_filename
			);

			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->logger->log(LOG_WARNING, 'command:' . $command);
			$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
			$sync_result = $return_var;

			if ($return_var) {
				return 'admin_error_500';
			}
		}

		/** 差分確認 */
		$command = $this->getMysqlDumpCommand($dump_command_path, $dest_parse, $table_list, $dest_filename);
		$output = null;
		$return_var = null;
		exec($command, $output, $return_var);
		$this->logger->log(LOG_WARNING, 'command:' . $command);
		$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
		$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);

		if ($return_var) {
			return 'admin_error_500';
		}

		$command = 'diff ' . $src_filename . ' ' . $dest_filename . '';
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


		/** ログ */
		$lid = $this->session->get('lid');
		$action = $this->backend->ctl->getCurrentActionName();
		foreach($table_list as $table) {
			$developer_m->logMasterSync(array(
				'table_name'  => $table,
				'mode'        => $mode,
				'sync_result' => $sync_result,
				'verify_result' => $verify_result,
				'action'      => $action,
				'account_reg' => $lid,
			));
		}


		$this->af->setApp('table_list', $table_list);

		return 'admin_developer_master_deploy_exec';
	}

	protected function getMysqlDumpCommand($dump_command_path, $parse, $table_list, $filename) {
		// mysqldump --skip-lock-tables -u xxx -h xxx --password=xxx[ -p xxx ] database tables tables ... > src_filename
		$command = sprintf($dump_command_path." --lock-tables=0 --add-locks=0 --extended-insert=0 --skip-comments --order-by-primary -u %s -h %s --password=%s", $parse['username'], $parse['hostspec'], $parse['password']);
		if (!empty($parse['port'])) $command .= " -P ".$parse['port'];
		$command .= " ".$parse['database'];
		$command .= " ".implode(" ", $table_list);
		$command .= " > ".$filename;

		return $command;
	}
}

?>
