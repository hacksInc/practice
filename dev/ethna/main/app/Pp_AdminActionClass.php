<?php
// vim: foldmethod=marker
/**
 *  Pp_AdminActionClass.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Base/Client.php';
require_once 'classes/Util.php';
require_once 'Pp_AdminActionForm.php';

// {{{ Pp_AdminActionClass
/**
 *  管理画面用 action execution class
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_AdminActionClass extends Ethna_ActionClass
{
	/** LANからのアクセスが必須か */
	protected $must_lan = true;

	protected $must_login = true;

	/** アクセス制御パーミッションが必須か */
	protected $must_permission = true;

	/**
	 *  バリデーションエラー時の遷移先ビュー名
	 *
	 *  アクション毎の個別エラーページを表示したい場合、派生クラス（各アクション）でこの変数を定義しなおす事
	 */
	protected $validation_error_forward_name = 'admin_error_400';

	/**
	 *  コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);

		$admin_m =& $this->backend->getManager('Admin');
		$admin_m->setSessionSqlBigSelectsOn();
	}

	/**
	 *  authenticate before executing action.
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (null if no errors. false if we have something wrong.)
	 */
	function authenticate()
	{
//		return parent::authenticate();
		$ret = parent::authenticate();
		if ($ret) {
			return $ret;
		}

		$ret = $this->authenticateUnit();
		if ($ret) {
			return $ret;
		}

		if ($this->must_lan) {
			$remote_addr = Base_Client::getRemoteAddr();
			if (!in_array($remote_addr, array(
//				'192.168.131.248', // CAVE Nakameguro office to Nihonbashi IDC via VPN
				'124.39.138.2',    // CAVE Nakameguro office's global IP address
				'192.168.56.1',    // VirtualBox (Local PC only)
				'153.142.59.17',   // デジタルハーツ
				'58.80.29.146',    // デジタルハーツ
//				'157.14.146.6',    // CS外注シフォン様
//				'157.14.145.138',  // CS外注シフォン様
//				'157.14.146.113',  // CS外注シフォン様
//				'182.171.236.104', // CS外注シフォン様（引越し先）
				'219.105.33.242',  // 「株式会社クリエーション」様
				'202.241.130.140', // 「株式会社クリエーション」様
				'157.14.149.199', // 「株式会社クリエーション」様
				'157.14.149.209', // 「株式会社クリエーション」様
				'119.106.35.48',
				'118.238.221.138',
			))) {
				$this->backend->logger->log(LOG_DEBUG, 'Access denied. [' . $remote_addr . ']');
				exit;
			}
		}

		if ($_SERVER['SERVER_NAME'] == 'dev.jmja.cave.co.jp') {
			return 'admin_error_devmoved';
		}

		if ($this->must_login) {
			if (!$this->session->isStart()) {
				return 'admin_login';
			}
		}
	}

	private function authenticateUnit()
	{
		$unit = $this->config->get('unit_id');
		if ($unit) {
			//OK
			return;
		}

		$action = $this->backend->ctl->getCurrentActionName();
		$unit_m = $this->backend->getManager('Unit');
		$unit_all = $this->config->get('unit_all');

		$unit = null;
		if ($action == 'admin_login') {
			$unit = $this->af->get('unit');
		} else if ($this->session->isStart()) {
			$unit = $this->session->get('unit');
		} else {
			$unit_default = $this->config->get('unit_default');
			if (is_array($unit_default) && isset($unit_default['admin'])) {
				$unit = $unit_default['admin'];
			}
		}

		if ($unit && is_array($unit_all) && isset($unit_all[$unit])) {
			$unit_m->resetUnit($unit);
			$this->backend->logger->log(LOG_DEBUG, 'Unit found. unit=[' . $unit . ']');
		} else {
			$this->backend->logger->log(LOG_WARNING, 'Unit not found.');
		}

		return;
	}

	/**
	 *  Preparation for executing action. (Form input check, etc.)
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (null if no errors. false if we have something wrong.)
	 */
	function prepare()
	{
//		return parent::prepare();
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 必ずバリデートする
		// （アクセス制御でアクションフォームを参照するので）
		if ($this->af->validate() > 0) {
			return $this->validation_error_forward_name;
		}

		// アクセス制御
		if ($this->must_login && $this->must_permission) {
			$ret = $this->permit();
			if ($ret) {
				return $ret;
			}
		}
	}

	/**
	 *  execute action.
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (we does not forward if returns null.)
	 */
	function perform()
	{
		return parent::perform();
	}

	/**
	 * アクセス制御設定に基づいて認可する
	 *
	 * @return string|null エラーの場合はEthnaビュー名。OKの場合は戻り値なし。
	 */
	protected function permit()
	{
		$lid = $this->session->get('lid');
		$action_name = $this->backend->ctl->action_name;

		$admin_m =& $this->backend->getManager('Admin');

		$user = $admin_m->getAdminUser($lid);
		$role = $user['role'];
		$unit = $this->config->get('unit_id');

		//if (!$admin_m->hasAccessControlPermission($role, $action_name, Util::getEnv(), $this->getAccessControlQueries(), $unit)) {
		if (false) {
			$this->af->ae->add(null, "アクセス権限がありません。", E_ERROR_DEFAULT);
			return 'admin_error_403';
		}
	}

	/**
	 * アクセス制御クエリを取得する
	 *
	 * アクションフォームへのクエリの内、アクセス制御に使用する可能性がある全項目を取得する
	 * @return array $queries[クエリ名] = 値
	 */
	protected function getAccessControlQueries()
	{
		$admin_m =& $this->backend->getManager('Admin');
		$names = $admin_m->getAccessControlQueryNameAll();

		$queries = array();
		foreach ($names as $name) {
			$queries[$name] = $this->af->get($name);
		}

		return $queries;
	}

	/**
	 * マスターデータCSVアップロード確認アクションの共通perform処理
	 */
	protected function performMasterUploadConfirm()
	{
		$xml = $this->af->get( 'xml' );
		$table = $this->af->get('table');

		$conditions = $this->af->getApp('conditions');

		$developer_m =& $this->backend->getManager('Developer');

		$area = $developer_m->getMasterList($table);
		$label = $developer_m->getMasterColumnsLabel($table);
		$label_cnt = count($label);
		$colnames = array_keys($label);

		$table_label = $developer_m->getMasterTableLabel($table);

		$buf = mb_convert_encoding( file_get_contents( $xml['tmp_name'] ), 'utf-8', 'sjis-win' );

		$fp = tmpfile();
		fwrite( $fp, $buf );
		rewind( $fp );

		$list = array();

		$cnt = 0;

		setlocale(LC_ALL, 'ja_JP.UTF-8');
		while ( !feof( $fp ) ) {
			$row = fgetcsv( $fp, 10240 );
			$cnt++;

			// 一行目は破棄
			if ( $cnt == 1 ) {
				// 要素数が足りなければ処理を終了する
				if ( count( $row ) < $label_cnt ) {
					$this->af->ae->add(null, "カラム数が足りません");
					return 'admin_error_400';
				}

				continue;
			}

			// 要素数を調整する
			$row = Pp_AdminActionForm::adjustCsvRow($row, $label_cnt);
			if ($row === false) {
				continue;
			}

			// 日付の書式を正規化
			for ($i = 0; $i < $label_cnt; $i++) {
				if ($developer_m->isDateColumnName($colnames[$i])) {
					$row[$i] = Pp_AdminActionForm::normalizeDateString($row[$i]);
				}
			}

			// 呼び出し元アクションによる条件が指定されていたらチェック
			if ($conditions) foreach ($conditions as $position => $value) {
				if ($row[$position] != $value) {
					$this->af->ae->add(null, "列{$position}の値が不正です");
					return 'admin_error_400';
				}
			}

			$id = $developer_m->getRowIdFromArray($table, $row);
			$list[$id] = $row;
		}

		fclose( $fp );

		// ファイルの位置を変更
		$fname = explode( "/", $xml['tmp_name'] );
		$fname[count( $fname ) - 1] = "tmp_" . $table . "_upd.csv";
		$fname = implode( "/", $fname );

		move_uploaded_file( $xml['tmp_name'], $fname );

		// 追加・変更箇所を判別
		$row_crud = array(); // 行ごとのCRUD種別  $row_crud[エリアID] = タイプ（新規追加の場合は'c', 更新の場合は'u', 削除の場合は'd'）
		$cell_update = array(); // セルごとの更新箇所　更新ある箇所は  $cell_update[ID][index値] = true  （index値はCSVで何列目かの値。0から数える）
		foreach ($list as $key => $row) {
			if (isset($area[$key])) {
				$cell_update[$key] = array();
				for ($i = 0; $i < $label_cnt; $i++) {
					if ($row[$i] != $area[$key][$colnames[$i]]) {
						$cell_update[$key][$i] = true;
						$row_crud[$key] = 'u';
					}
				}
			} else {
				$row_crud[$key] = 'c';
			}
		}
		// 削除箇所を判別
		foreach($area as $key => $row)
		{
			if (!isset($list[$key])) {
				$row_crud[$key] = 'd';
				$list[$key] = $area[$key];
			}
		}
		ksort($list);

		$this->af->setApp( "list", $list );
		$this->af->setApp('label', $label);
		$this->af->setApp( "cell_update", $cell_update );
		$this->af->setApp( "row_crud", $row_crud );
		$this->af->setApp( "fname", $fname );
		$this->af->setApp('table_label', $table_label);

        return 'admin_developer_master_upload_confirm';
	}

	/**
	 * マスターデータCSVアップロード登録アクションの共通perform処理
	 */
	function performMasterUploadRegist()
	{
		$file = $this->af->get( "file" );
		$crudlist = $this->af->get( "crudlist" );
		$table = $this->af->get('table');

		$developer_m =& $this->backend->getManager('Developer');

		$log_subdir = $this->af->getApp('log_subdir');
		if (!$log_subdir) {
			$log_subdir = Pp_DeveloperManager::MASTER_UPLOAD_LOG_SUBDIR;
		}

		$log_dir = BASE . $log_subdir;
		if (!is_dir($log_dir)) {
			mkdir($log_dir, 0775, true);
		}

		if (!is_writable($log_dir)) {
			$this->ae->add(null, "ログ書き込みエラー [" . $log_dir . "]");
			return 'admin_error_500';
		}

		$conditions = $this->af->getApp('conditions');

		$label = $developer_m->getMasterColumnsLabel($table);
		$label_cnt = count($label);
		$colnames = array_keys($label);

		// 更新前のログ
		$base_csv = $developer_m->getMasterCsv($table);
		$base_dest = $log_dir . '/'
		           . $developer_m->assembleUploadLogFilename($table, $this->session->get('lid'), 'base');
		if (!file_put_contents($base_dest, $base_csv)) {
			$this->backend->logger->log(LOG_WARNING, 'Logging failed. ' . $base_dest);
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $base_dest);
			return 'admin_error_500';
		}

		// 更新する
		$buf = mb_convert_encoding( file_get_contents( $file ), 'utf-8', 'sjis-win' );

		$fp = tmpfile();
		fwrite( $fp, $buf );
		rewind( $fp );

		$list = array();

		$cnt = 0;

		if ( !$developer_m->truncateMaster($table) ) {
			$this->ae->add(null, "テーブルのクリアに失敗しました [" . $table . "]");
			return 'admin_error_500';
		}

		setlocale(LC_ALL, 'ja_JP.UTF-8');

		while ( !feof( $fp ) ) {
			$row = fgetcsv( $fp, 10240 );
			$cnt++;

			// 一行目は破棄
			if ( $cnt == 1 ) {
				// 要素数が足りなければ処理を終了する
				if ( count( $row ) < $label_cnt ) {
					error_log('ERROR:' . __FILE__ . ':' . __LINE__ );
					break;
				}

				continue;
			}

			// 要素数を調整する
			$row = Pp_AdminActionForm::adjustCsvRow($row, $label_cnt);
			if ($row === false) {
				continue;
			}

			$id = $developer_m->getRowIdFromArray($table, $row);

			// 日付の書式を正規化
			for ($i = 0; $i < $label_cnt; $i++) {
				if ($developer_m->isDateColumnName($colnames[$i])) {
					$row[$i] = Pp_AdminActionForm::normalizeDateString($row[$i]);
				}
			}

			// 呼び出し元アクションによる条件が指定されていたらチェック
			if ($conditions) foreach ($conditions as $position => $value) {
				if ($row[$position] != $value) {
					$this->af->ae->add(null, "列{$position}の値が不正です");
					return 'admin_error_400';
				}
			}

			// 更新実行
			//if ( !$developer_m->updMaster($table, $row, $crudlist[$id]) ) {
			if ( !$developer_m->updMaster($table, $row, 'c') ) {	// マスタデータは「DV全レコード削除→CSVレコード全インサート」という処理に変更
				$this->ae->add(null, "DB書き込み時にエラーが発生しました。CSVファイルの内容を再確認して下さい。");

				$error_no = $developer_m->getMasterErrorNo();
				if ($error_no) {
					$this->ae->add(null, 'ErrorNo:' . $error_no . ', ErrorMsg:' . $developer_m->getMasterErrorMsg());
				}

				$this->ae->add(null, implode(",", array_values($row)));
				break;
			}
		}

		fclose( $fp );

		// ログ
		$dest = $log_dir . '/'
		      . $developer_m->assembleUploadLogFilename($table, $this->session->get('lid'), 'update');
		if (!copy($file, $dest)) {
			$this->backend->logger->log(LOG_WARNING, 'Logging failed. ' . $dest);
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $dest);
		}

		$developer_m ->logMasterModify(array(
			'account_reg' => $this->session->get('lid'),
			'table_name'  => $table,
			'action'      => $this->backend->ctl->getCurrentActionName(),
		));

        return 'admin_developer_master_upload_regist';
	}

	/**
	 * マスターデータCSVアップロードログ一覧アクションの共通perform処理
	 */
	function performMasterLogList()
	{
		$developer_m =& $this->backend->getManager('Developer');
		$table = $this->af->get('table');

		$dir = $this->af->getApp('dir');
		if (!$dir) {
			$dir = BASE . Pp_DeveloperManager::MASTER_UPLOAD_LOG_SUBDIR;
		}

		$list = $developer_m->findUploadLog($table, $dir);

		if ($list) {
			$files_per_set = 2;
			if (count($list) > Pp_DeveloperManager::MAXTER_UPLOAD_LOG_MAX * $files_per_set) {
				for ($i = 0; $i < $files_per_set; $i++) {
					$item = array_shift($list);
					unlink($dir . '/' . $item['filename']);
				}
			}

			$this->af->setApp('list', $list);
		}

        return 'admin_developer_master_log_list';
	}
}
// }}}

?>
