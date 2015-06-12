<?php
/**
 *  Pp_AdminUserManager.php
 *
 *  @author	 {$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_UserManager.php';

/**
 *  Pp_AdminUserManager
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_AdminUserManager extends Pp_UserManager
{

	protected $metadata = null;

	var $GAME_MAIN_UNIT_INDEX_TABLES = array(
		'ut_user_base',
		'ut_user_game',
		'ut_user_achievement_count',
		'ut_user_tutorial',
		'ut_transaction',
		'ut_user_device_info',
		'ut_user_mission',
		'ut_portal_user_base',
		'ut_user_item',
		'ut_user_photo',
		'ut_user_character',
	);

	protected function getEditableGridDatatypeFromMysqlType($type)
	{
		$map = array(
			'number' => array('int', 'smallint', 'tinyint', 'mediumint', 'bigint', 'bit'),
			'string' => array('varchar', 'char', 'text', 'date', 'datetime', 'timestamp'),
		);

		foreach ($map as $datatype => $heads)
		{
			foreach ($heads as $head)
			{
				if (strncmp($type, $head, strlen($head)) === 0)
				{
					return $datatype;
				}
			}
		}
	}

	protected function loadMetadata()
	{
		$mixed_metadata = array();
		foreach (array('GAME_MAIN_UNIT_INDEX_TABLES') as $varname)
		{
			foreach ($this->$varname as $table)
			{
				if (!isset($mixed_metadata[$table]))
				{
					$mixed_metadata[$table] = null;
				}
			}
		}

		// MySQLのテーブル定義から設定を生成する
		foreach ($mixed_metadata as $table => $data)
		{
			if (!$data) $data = array();

			$mysql_table_status = $this->db->GetRow("SHOW TABLE STATUS LIKE ?", $table);
			$mysql_columns_list = $this->db->GetAll("SHOW FULL COLUMNS FROM $table");

			// table_labelを生成する
			if (!isset($data['table_label']) || !$data['table_label'])
			{
				$table_label = $mysql_table_status['Comment'];

				if (!$table_label) $table_label = $table;

				$mixed_metadata[$table]['table_label'] = $table_label;
			}

			// columns_label, hidden_columnsを生成する
			$hidden_columns = array();
			$account_columns = array();
			if (!isset($data['columns_label']))
			{
				$columns_label = array();
				foreach ($mysql_columns_list as $mysql_columns)
				{
					$field = $mysql_columns['Field'];

					if (($field == 'date_modified') ||
						($field == 'modify_date') ||
						($field == 'account_created') ||
						($field == 'account_modified')
					) {
						$account_columns[] = $field;
						continue;
					}

					if (($field == 'date_created') ||
						($field == 'created_date') ||
						($field == 'uipw_hash') ||
						($field == 'dmpw_hash')
					)
					{
						$hidden_columns[] = $field;
						continue;
					}

					if (isset($mysql_columns['Comment']) && (strlen($mysql_columns['Comment']) > 0))
					{
						$columns_label[$field] = $mysql_columns['Comment'];
					}
					else
					{
						$columns_label[$field] = $field;
					}
				}

				$mixed_metadata[$table]['columns_label'] = $columns_label;
			}

			if (!isset($data['hidden_columns']) && (count($hidden_columns) > 0))
			{
				$mixed_metadata[$table]['hidden_columns'] = $hidden_columns;
			}

			if (!isset($data['account_columns']) && (count($account_columns) > 0))
			{
				$mixed_metadata[$table]['account_columns'] = $account_columns;
			}

			// editablegrid_datatypeを生成する
			if (isset($data['editablegrid_datatype']))
			{
				$editablegrid_datatype = $data['editablegrid_datatype'];
			}
			else
			{
				$editablegrid_datatype = array();
			}

			foreach ($mysql_columns_list as $mysql_columns)
			{
				$field = $mysql_columns['Field'];

				if (isset($editablegrid_datatype[$field])) continue;

				if (!isset($mixed_metadata[$table]['columns_label'][$field])) continue;

				$datatype = $this->getEditableGridDatatypeFromMysqlType($mysql_columns['Type']);
				if ($datatype) $editablegrid_datatype[$field] = $datatype;
			}

			if (count($editablegrid_datatype) > 0)
			{
				$mixed_metadata[$table]['editablegrid_datatype'] = $editablegrid_datatype;
			}

			// primary_keysを生成する
			if (!isset($data['primary_keys']))
			{
				$primary_keys = array();
				foreach ($mysql_columns_list as $mysql_columns)
				{
					if (in_array('PRI', explode(',', $mysql_columns['Key'])))
					{
						$primary_keys[] = $mysql_columns['Field'];
					}
				}

				$mixed_metadata[$table]['primary_keys'] = $primary_keys;
			}
		}

		$this->metadata = $mixed_metadata;
	}

	public function getMasterColumnsLabel($table)
	{
		if (is_null($this->metadata)) $this->loadMetadata();

		return $this->metadata[$table]['columns_label'];
	}

	public function getMasterTableLabel($table)
	{
		if (is_null($this->metadata)) $this->loadMetadata();

		return $this->metadata[$table]['table_label'];
	}

	public function getPrimaryKeys($table)
	{
		if (is_null($this->metadata)) $this->loadMetadata();

		return $this->metadata[$table]['primary_keys'];
	}

	public function isDateColumnName($name)
	{
		switch ($name)
		{
			case 'date_start':
			case 'date_end':
			case 'start_date':
			case 'end_date':
			case 'date_bonus':
				return true;
		}

		return false;
	}

	public function isStringColumnName($name)
	{
		switch ($name)
		{
			case 'ai_id':
				return true;
		}

		return false;
	}

	public function getEditableGridMetadata($table, $is_primary_key_editable = false)
	{
		if (is_null($this->metadata)) $this->loadMetadata();

		$full_metadata = $this->metadata[$table];

		$metadata = array();
		foreach ($full_metadata['columns_label'] as $name => $label)
		{
			$metadata_row = array('name' => $name, 'label' => $label);
			$metadata_row['datatype'] = $full_metadata['editablegrid_datatype'][$name];

			if ($is_primary_key_editable)
			{
				$editable = true;
			}
			else if (!in_array($name, $full_metadata['primary_keys']))
			{
				$editable = true;
			}
			else
			{
				$editable = false;
			}

			$metadata_row['editable'] = $editable;

			if (isset($full_metadata['editablegrid_values'][$name]))
			{
				$metadata_row['values'] = $full_metadata['editablegrid_values'][$name];
			}

			$metadata[] = $metadata_row;
		}

		$metadata[] = array(
			'name' => 'action',
			'label' => ' ',
			'datatype' => 'html',
			'editable' => false,
		);

		return $metadata;
	}

	/**
	 * ユーザー基本情報の取得 ※最終アクセス日時などを取得するためにユーザーゲームと結合する。オーバーロードする。
	 *
	 * @param int $pp_id サイコパスID
	 *
	 * @return array:ユーザー基本情報 | null:取得エラー
	 */
	public function getUserBase($pp_id)
	{
		if (empty($pp_id))
		{	// サイコパスIDがなければ取得エラー
			return null;
		}

		// memcacheから取得してみる
		$cache_key = "ut_user_base__$pp_id";
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 1 );
		if ($cache_data && !Ethna::isError($cache_data))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// 取得できない場合はDBから取得
		$param = array($pp_id);
		$sql = "
SELECT
  ut1.*
  , ut2.last_login
FROM
  ut_user_base ut1
  INNER JOIN ut_user_game ut2
    ON ut1.pp_id = ut2.pp_id
WHERE
  ut1.pp_id = ?
";
		$data = $this->db->GetRow($sql, $param);

		if (!empty($data))
		{	// 取得したデータをキャッシュする
			$cache_m->set($cache_key, $data);
		}

		return $data;
	}

	public function getUserBaseDetail($pp_id)
	{
		if (empty($pp_id))
		{	// サイコパスIDがなければ取得エラー
			return null;
		}

		$param = array($pp_id, $pp_id);
		$sql = "
SELECT
  ut1.*
  , ut2.last_login
  , ut2.cont_login
  , ut2.today_login
  , ut2.crime_coef
  , ut2.body_coef
  , ut2.intelli_coef
  , ut2.mental_coef
  , ut2.ex_stress_care
  , ut3.login
  ,  CONVERT(ut4.flag0, SIGNED) flag0
  , ut5.point
  , ut6.api_transaction_id
  , ut7.content
  , (SELECT MAX(mission_id) FROM ut_user_mission WHERE pp_id = ?) mission_id
FROM
  ut_user_base ut1
  INNER JOIN ut_user_game ut2
    ON ut1.pp_id = ut2.pp_id
  INNER JOIN ut_user_achievement_count ut3
    ON ut1.pp_id = ut3.pp_id
  INNER JOIN ut_user_tutorial ut4
    ON ut1.pp_id = ut4.pp_id
  INNER JOIN ut_portal_user_base ut5
    ON ut1.pp_id = ut5.pp_id
  LEFT JOIN ut_transaction ut6
    ON ut1.pp_id = ut6.pp_id
  LEFT JOIN ut_user_device_info ut7
    ON ut1.pp_id = ut7.pp_id
WHERE
  ut1.pp_id = ?;
";
		$data = $this->db->GetRow($sql, $param);

		return $data;
	}

	/**
	 * ユーザ基本データをニックネームから取得する
	 *
	 * ニックネームと完全一致するユーザ基本データを取得できる
	 * @param string $name ニックネーム
	 * @return array ユーザ基本データ
	 */
	function getUserBaseFromName($name)
	{
		$param = array($name);
		$sql = "SELECT * FROM ut_user_base WHERE name = ?";

		return $this->db->getRow($sql, $param);
	}

	/**
	 * ユーザ基本データをニックネームからLIKE表現で取得する
	 *
	 * ニックネームと部分一致するユーザ基本データを取得できる
	 * @param string $name ニックネーム
	 * @return array ユーザ基本データの配列
	 */
	function getUserBaseFromNameLike($name)
	{
		if (empty($name)) {
			$sql = "
SELECT
  ut1.*
  , ut2.last_login
FROM
  ut_user_base ut1
  INNER JOIN ut_user_game ut2
	ON ut1.pp_id = ut2.pp_id
LIMIT
  0, 100
";

			return $this->db->getAll($sql);

		} else {
			$like_expr = '%' . str_replace(array('%', '_', "\\"), '', $name) . '%';
			$param = array($like_expr);
			$sql = "
SELECT
  ut1.*
  , ut2.last_login
FROM
  ut_user_base ut1
  INNER JOIN ut_user_game ut2
	ON ut1.pp_id = ut2.pp_id
WHERE ut1.name LIKE ?
";

			return $this->db->getAll($sql, $param);
		}
	}

	/**
	 * ユーザ基本データをログイン時間から取得する
	 *
	 * @param string $date_start 対象開始日時
	 * @param string $date_end 対象終了日時
	 * @return array ユーザ基本データの配列
	 */
	function getUserBaseFromLogindate($date_start, $date_end)
	{
		$param = array($date_start, $date_end);

		$sql = "
SELECT
  ut1.pp_id
  , ut1.name
  , ut1.device_type
  , ut1.date_created
  , ut2.last_login
FROM
  ut_user_base ut1
  INNER JOIN ut_user_game ut2
	ON ut1.pp_id = ut2.pp_id
WHERE ut2.last_login BETWEEN ? AND ?
";

		return $this->db->getAll($sql, $param);
	}

	/**
	 * ユーザ基本情報をDBから直接取得する
	 *
	 * @param int $pp_id
	 * @return array ユーザ基本情報1件の連想配列
	 */
	function getUserBaseDirect($pp_id)
	{
		$param = array($pp_id);
		$sql = "SELECT * FROM ut_user_base WHERE pp_id = ?";

		return $this->db->getRow($sql, $param);
	}

	/**
	 * ユーザ基本情報をDBへ直接セットする
	 *
	 * @param int $pp_id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserBaseDirect($pp_id, $columns)
	{
		// UPDATE実行
		$param = array_values($columns);
		$param[] = $pp_id;
		$sql = "UPDATE ut_user_base SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE pp_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		return true;
	}

	/**
	 * プラットフォームからケイブ決済サーバのアプリケーションIDを取得する
	 *
	 * @param string $platform プラットフォーム（"apple" or "google"）
	 * @return int ケイブ決済サーバのアプリケーションID
	 */
	function getPaymentServerAppIdFromPlatform($platform)
	{
		switch ($platform) {
			case 'apple':
				$ua = self::OS_IPHONE;
				break;

			case 'google':
				$ua = self::OS_ANDROID;
				break;
		}

		return $this->getPaymentServerAppIdFromUserAgent($ua);
	}

	/**
	 * プラットフォームからUser-Agent種別を取得する
	 *
	 * @param string $platform プラットフォーム（"apple" or "google"）
	 * @return int User-Agent種別（Pp_UserManager::OS_～）
	 */
	function getUaFromPlatform($platform)
	{
		switch ($platform) {
			case 'apple':
				return self::OS_IPHONE;

			case 'google':
				return self::OS_ANDROID;
		}
	}

	/**
	 * ユーザ初期状態管理データを取得する
	 *
	 * @param int $pp_id ユーザID
	 * @return array ユーザ初期状態管理データ（t_user_initialのカラム名がキー）
	 */
	function getUserInitial($pp_id)
	{
		$param = array($pp_id);
		$sql = "SELECT * FROM t_user_initial WHERE pp_id = ?";

		return $this->db_r->getRow($sql, $param);
	}

	/**
	 * ユーザのモンスター初期登録フラグ設定日時を取得する
	 *
	 * @param int $pp_id ユーザID
	 * @return string|null ユーザのモンスター初期登録フラグ設定日時(Y-m-d H:i:s)
	 */
	function getUserInitialMonsterDate($pp_id)
	{
		$row = $this->getUserInitial($pp_id);
		if (!is_array($row) || !isset($row['monster_flg']) || !$row['monster_flg']) {
			return null;
		}

		if (!isset($row['date_monster_flg']) || !$row['date_monster_flg']) {
			return null;
		}

		return $row['date_monster_flg'];
	}

	/**
	 * ユーザのモンスター初期登録フラグ設定日時をKPIタグ用の年月表記で取得する
	 *
	 * @param int $pp_id ユーザID
	 * @return string|null ユーザのモンスター初期登録フラグ設定日時(ym)
	 */
	function getUserInitialMonsterKpiYm($pp_id)
	{
		$initial_monster_time = strtotime($this->getUserInitialMonsterDate($pp_id));
		if (!$initial_monster_time) {
			return null;
		}

		return date('ym', $initial_monster_time);
	}

	/**
	 * データ移行パスワードをハッシュする
	 *
	 * このクラス（Pp_AdminUserManager）経由だとprotectedではないのでEthnaアクション等から呼べる
	 */
	function hashDmpw($pp_id, $dmpw)
	{
		return parent::hashDmpw($pp_id, $dmpw);
	}

	/**
	 * データ移行パスワードを更新する（管理画面用）
	 *
	 * @param int $pp_id ジャグモン内ユーザID
	 * @param string $account アカウント名
	 * @param string $new_dmpw 新データ移行パスワード（省略可）
	 * @return array|boolean|object array:更新成功時の新パスワード情報, false:更新失敗, Ethna_Error:エラー
	 */
	function updateDmpwForAdmin($pp_id, $account, $new_dmpw = null)
	{
		// 新データ移行パスワードが無指定の場合はランダム生成
		if ($new_dmpw === null) {
			$new_dmpw = $this->getRandomDmpw();
		}

		// 新データ移行パスワードの書式チェック
		if (!$this->isValidDmpwFormat($new_dmpw)) {
			return Ethna::raiseError("Weak password. account[%s]", E_USER_ERROR, $account);
		}

		// ユーザ情報を取得
		$base = $this->getUserBaseDirect($pp_id);

		if (!is_array($base) || ($base['pp_id'] != $pp_id)) {
			return Ethna::raiseError("Invalid pp_id. pp_id[%s]", E_USER_ERROR, $pp_id);
		}

		if ($base['account'] != $account) {
			return Ethna::raiseError("Invalid account. account[%s]", E_USER_ERROR, $account);
		}

		// データ移行パスワードハッシュを生成
		$new_dmpw_hash = $this->hashDmpw($pp_id, $new_dmpw);

		$this->backend->logger->log(LOG_DEBUG,
			"updateDmpwForAdmin [%s] [%s] [%s]", $account, $base['dmpw_hash'], $new_dmpw_hash
		);

		// DB更新
		$param = array($new_dmpw_hash, $pp_id, $account);
		$sql = "UPDATE ut_user_base SET dmpw_hash = ? WHERE pp_id = ? AND account = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d] FILE[%s] LINE[%d]", E_USER_ERROR,
				$affected_rows, __FILE__, __LINE__);
		}

		// ログ(DB)
		$this->logUser($pp_id, array(
			'func' => __METHOD__,
			'account' => $account,
			'old_dmpw_hash' => $base['dmpw_hash'],
			'new_dmpw_hash' => $new_dmpw_hash,
		));

		return array(
			'new_dmpw' => $new_dmpw,
			'new_dmpw_hash' => $new_dmpw_hash,
			'old_dmpw_hash' => $base['dmpw_hash']
		);
	}

	/**
	 * ユーザ基本データをクリーンアップ処理用にエクスポートする
	 *
	 * ut_user_baseテーブルのデータの内、
	 * ・ユーザーID下1桁が引数で指定された値
	 * ・最近ログインしている
	 * のユーザーについて、
	 * ・バックアップファイルへの出力（全カラム。ファイルが既に存在したら追記）
	 * ・テンポラリファイルへの出力（一部カラムのみ。ファイルが既に存在したら上書き）
	 * を行う。
	 * @param int $digit ユーザーID下1桁
	 * @param string $backup_dir バックアップ先ディレクトリ
	 * @param string $cleanup_uniq クリーンアップ処理のユニーク値（ファイル名に使用）
	 * @return string|boolean テンポラリファイル名（フルパス） 失敗時はfalse
	 */
	function exportUserBaseForTransactionCleanup($digit, $backup_dir, $cleanup_uniq)
	{
		// 引数チェック
		if (!preg_match('/^[0-9]$/', $digit)) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$admin_m = $this->backend->getManager('Admin');
		$present_m = $this->backend->getManager('AdminPresent');

		// 候補とする最終ログイン日時の最小値
		$login_date_min = date('Y-m-d',
			$_SERVER['REQUEST_TIME'] - (self::NON_ACTIVE_DAYS + Pp_AdminPresentManager::SOFT_DELETE_RETENTION_PERIOD) * 86400
		) . ' 00:00:00';

		$table = 'ut_user_base';
		$datafile = "{$backup_dir}/{$table}_{$cleanup_uniq}.csv";
		$datafile_exists = file_exists($datafile);
		$tmpfile = BASE . "/tmp/transaction_cleanup_{$table}_{$cleanup_uniq}";

		// ファイルオープン
		$fp_tmp = fopen($tmpfile, 'w');
		if (!$fp_tmp) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$fp_data = fopen($datafile, 'w');
		if (!$fp_data) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		if (!$datafile_exists) {
			// BOM書き込み（MS-ExcelでUTF-8のCSVファイルを開くための対応）
 			fwrite($fp_data, pack('C*', 0xEF, 0xBB, 0xBF));
		}

		// カラム名を取得
		$colnames = $admin_m->getFieldsFromTableDefinition($table);
		$colnums = array_flip($colnames);

		// カラム名をファイル出力
		fputcsv($fp_data, $colnames);

		// データ取得
		$param = array($digit, $login_date_min);
		$sql = "SELECT " . implode(',', $colnames)
			 . " FROM ut_user_base"
			 . " WHERE pp_id % 10 = ?"
			 . " AND login_date >= ?";

		$adodb_fetch_mode_old = $this->db_r->db->SetFetchMode(ADODB_FETCH_NUM);
		$adodb_countrecs_old = $admin_m->setAdodbCountrecs(false);

		$result =& $this->db_r->query($sql, $param);
		while ($row = $result->FetchRow()) {
			// ファイル出力
			$line = $row[$colnums['pp_id']] . PHP_EOL;
			if (!fwrite($fp_tmp, $line)) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}

			if (!fputcsv($fp_data, $row)) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
		}

		$admin_m->setAdodbCountrecs($adodb_countrecs_old);
		$this->db_r->db->SetFetchMode($adodb_fetch_mode_old);

		fclose($fp_tmp);
		fclose($fp_data);

		return $tmpfile;
	}
}
