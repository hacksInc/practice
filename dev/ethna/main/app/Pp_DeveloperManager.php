<?php
/**
 *	開発者向け機能マネージャ
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once 'classes/Util.php';

/**
 *	Pp_DeveloperManager
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_DeveloperManager extends Ethna_AppManager
{
	/**
	 * マスターデータのアップロード時ログCSVファイルの保存先サブディレクトリ
	 *
	 * サブディレクトリはBASE以下の部分
	 */
	const MASTER_UPLOAD_LOG_SUBDIR = '/log/developer/master/upload';

	/**
	 * マスターデータのアップロード時ログCSVファイルの最大保持セット数
	 *
	 * 1セットは2ファイル（～_base.csv, ～_update.csvの2つ）
	 */
	const MAXTER_UPLOAD_LOG_MAX = 120;

	/** マスターデータ同期機能の実行後の検証結果：OK */
	const MASTER_SYNC_VERIFY_OK = 0;

	/** マスターデータ同期機能の実行後の検証結果：NG */
	const MASTER_SYNC_VERIFY_NG = 1;

	/**
	 * マスターテーブル一覧ページ(/admin/developer/master/index)の表示対象テーブル
	 */
	var $MASTER_INDEX_TABLES = array(
		'm_stage',
		'm_area',
		'm_mission',
		'm_mission_enemy',
		'm_sp_area_release',
		'm_area_stress_avg_correction',
		'm_photo',
		'm_photo_gacha',
		'm_photo_gacha_lineup',
		'm_enemy_ai',
		'm_shop',
		'm_item',
		'm_sell_item',
		'm_sell_list',
		'm_character',
		'm_achievement_group',
		'm_achievement_condition',
		'm_asset_bundle',
		'm_client',
		'm_serial',
		'm_speech',
	);

	/**
	 * マスターデータ同期機能（複数テーブル対応）の対象テーブル
	 */
	var $MASTER_SYNC_MULTI_TABLES = array(
		'm_stage',
		'm_area',
		'm_mission',
		'm_mission_enemy',
		'm_sp_area_release',
		'm_area_stress_avg_correction',
		'm_photo',
		'm_photo_gacha',
		'm_photo_gacha_lineup',
		'm_enemy_ai',
		'm_shop',
		'm_item',
		'm_sell_item',
		'm_sell_list',
		'm_character',
		'm_achievement_group',
		'm_achievement_condition',
		'm_asset_bundle',
		'm_client',
		'm_serial',
		'm_speech',
	);

	/**
	 * マスターデータ同期機能（複数テーブル対応）の表示ラベル情報
	 */
	var $MASTER_SYNC_MULTI_LABEL = array(
		'deploy'  => array('mode' => 'デプロイ', 'last_synced' => 'マスター反映日時'),
		'standby' => array('mode' => '商用同期', 'last_synced' => '前回同期日時'),
		'unitsync'=> array('mode' => 'ユニット間同期', 'last_synced' => 'マスター反映日時'),
	);

	/**
	 * pt-table-sync の Exit Status
	 *
	 * @see http://www.percona.com/doc/percona-toolkit/2.2/pt-table-sync.html#exit-status
	 */
	var $PT_TABLE_SYNC_EXIT_STATUS = array(
		0 => array('type' => 'OK', 'meaning' => 'Success.'),
		1 => array('type' => 'NG', 'meaning' => 'Internal error.'),
		2 => array('type' => 'OK', 'meaning' => 'At least one table differed on the destination.'),
		3 => array('type' => 'NG', 'meaning' => 'Combination of 1 and 2.'),
	);

	/**
	 * テーブル関連のメタデータ
	 *
	 * loadMetadata関数で生成する。
	 * $metadata[テーブル名] = array(
	 *	 'table_label' => テーブルの表示用ラベル,
	 *	 'columns_label' => array(
	 *	   カラム名 => カラムの表示用ラベル, ...
	 *	 ),
	 *	 'editablegrid_datatype' => array(
	 *	   カラム名 => EditableGridのdatatype, ...
	 *	 ),
	 *	 'editablegrid_values' => array(
	 *	   カラム名 => EditableGridのvalues, ...
	 *	 ),
	 *	 'primary_keys' => array(主キーのカラム名, ...),
	 *	 'hidden_columns' => array(隠しカラム名, ...)  …CSV上で扱わない隠しカラムがある場合に使用する。指定可能なのは'date_created'または'created_date'のみ。
	 *	 'account_columns' => array(管理画面アカウント関連カラム名, ...)  …最終更新アカウントや最終更新日時などの、編集不可だが表示したい場合はあるカラム
	 * )
	 * @see http://editablegrid.net
	 */
	protected $metadata = null;

	/**
	 * テーブル関連のメタデータ（追加分）
	 *
	 * loadMetadataEx関数で生成する。
	 * $metadata_ex[テーブル名] = array(
	 *	 'summary' => 概要,
	 * )
	 */
	protected $metadata_ex = null;

	/** マスタの更新処理でのエラーメッセージ */
	protected $master_error_msg = null;

	/** マスタの更新処理でのエラー番号 */
	protected $master_error_no  = null;

	// コンストラクタで取得されないDBのインスタンス
	protected $db_m = null;
	protected $db_m_r = null;
	protected $db_logex = null;

	/** コンストラクタ */
	function __construct(&$backend) {
		parent::__construct($backend);

		if( is_null( $this->db_m ))
		{	// インスタンスを取得していないなら取得
			$this->db_m =& $this->backend->getDB( 'm' );
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		if( is_null( $this->db_logex ))
		{	// インスタンスを取得していないなら取得
			$this->db_logex =& $this->backend->getDB( 'logex' );
		}

		$this->loadMetadata();
		//$this->loadMetadataEx();
	}

	/** メタデータをロードする */
	protected function loadMetadata()
	{
		// $this->metadata と同じ書式で設定を書く
		// 省略するとDBのテーブル定義から自動判別する。
		$specified_metadata = array(
			'm_area' => array(
				'editablegrid_datatype' => array(
					'boss_flag' => 'boolean',
				),
				'editablegrid_values' => array(
					'use_type' => array(
						'1' => 'スタミナ',
						'2' => '鍵',
						'3' => 'フレンド鍵',
						'-1' => '不明',
					),
				),
			),
			//'t_user_base'		   => null,
			//'t_user_item'		   => null,
			//'t_user_badge'		   => null,
			//'t_user_badge_material' => null,
			//'t_user_friend' 	   => null,
			//'t_user_monster_book'  => array(
			//	'columns_label' => array(
			//		'monster_id'   => 'モンスターID',
			//		'status'	   => '入手',
			//		'date_met'	   => '遭遇日時',
			//		'date_got'	   => '入手日時',
			//	),
			//	'editablegrid_values' => array(
			//		'status' => array(
			//			'1' => '　',
			//			'2' => '○',
			//		),
			//	),
			//),
		);
		// ↑※注意※
		// 管理画面からのJSON吐き出しで文字列カラムかどうかを自動判定させたい場合は、
		// ここの設定だけでなく、Pp_View_AdminDeveloperMasterDownloadJson にも
		// テーブル名を記述する必要がある。

		// ロード対象テーブルを確定する
		$mixed_metadata = $specified_metadata;
		foreach (array('MASTER_INDEX_TABLES') as $varname) {
			foreach ($this->$varname as $table) {
				if (!isset($mixed_metadata[$table])) {
					$mixed_metadata[$table] = null;
				}
			}
		}

		// MySQLのテーブル定義から設定を生成する
		foreach ($mixed_metadata as $table => $data) {
			if (!$data) $data = array();

			$mysql_table_status = $this->db_m_r->GetRow("SHOW TABLE STATUS LIKE ?", $table);
			$mysql_columns_list = $this->db_m_r->GetAll("SHOW FULL COLUMNS FROM $table");

			// table_labelを生成する
			if (!isset($data['table_label']) || !$data['table_label']) {
				$table_label = $mysql_table_status['Comment'];

				if (!$table_label) {
					$table_label = $table;
				}

				$mixed_metadata[$table]['table_label'] = $table_label;
			}

			// columns_label, hidden_columnsを生成する
			$hidden_columns = array();
			$account_columns = array();
			if (!isset($data['columns_label'])) {
				$columns_label = array();
				foreach ($mysql_columns_list as $mysql_columns) {
					$field = $mysql_columns['Field'];

					// ※ここのフィールド名の記述はジャグモン以外のDBだと異なるはずなので、移植する際は注意
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
					) {
						$hidden_columns[] = $field;
						continue;
					}

					if (isset($mysql_columns['Comment']) && (strlen($mysql_columns['Comment']) > 0)) {
						$columns_label[$field] = $mysql_columns['Comment'];
					} else {
						$columns_label[$field] = $field;
					}
				}

				$mixed_metadata[$table]['columns_label'] = $columns_label;
			}

			if (!isset($data['hidden_columns']) && (count($hidden_columns) > 0)) {
				$mixed_metadata[$table]['hidden_columns'] = $hidden_columns;
			}

			if (!isset($data['account_columns']) && (count($account_columns) > 0)) {
				$mixed_metadata[$table]['account_columns'] = $account_columns;
			}

			// editablegrid_datatypeを生成する
			if (isset($data['editablegrid_datatype'])) {
				$editablegrid_datatype = $data['editablegrid_datatype'];
			} else {
				$editablegrid_datatype = array();
			}

			foreach ($mysql_columns_list as $mysql_columns) {
				$field = $mysql_columns['Field'];

				if (isset($editablegrid_datatype[$field])) {
					continue;
				}

				if (!isset($mixed_metadata[$table]['columns_label'][$field])) {
					continue;
				}

				$datatype = $this->getEditableGridDatatypeFromMysqlType($mysql_columns['Type']);
				if ($datatype) {
					$editablegrid_datatype[$field] = $datatype;
				}
			}

			if (count($editablegrid_datatype) > 0) {
				$mixed_metadata[$table]['editablegrid_datatype'] = $editablegrid_datatype;
			}

			// primary_keysを生成する
			if (!isset($data['primary_keys'])) {
				$primary_keys = array();

				foreach ($mysql_columns_list as $mysql_columns) {
					if (in_array('PRI', explode(',', $mysql_columns['Key']))) {
						$primary_keys[] = $mysql_columns['Field'];
					}
				}

				$mixed_metadata[$table]['primary_keys'] = $primary_keys;
			}
		}

		$this->metadata = $mixed_metadata;
	}

	/** メタデータ（追加分）をロードする */
	protected function loadMetadataEx()
	{
		$sql = "SELECT table_name AS id, table_name, summary FROM m_master";
		$this->metadata_ex = $this->db_r->db->GetAssoc($sql);
	}

	/**
	 * テーブル関連のメタデータを取得する
	 *
	 * @param string $table テーブル名
	 * @param bool $ex_flg 追加分も取得するか
	 * @return array メタデータの連想配列
	 */
	function getMetadata($table, $ex_flg = true)
	{
		$metadata = $this->metadata[$table];

		if ($ex_flg && isset($this->metadata_ex[$table])) {
			$metadata = array_merge($metadata, $this->metadata_ex[$table]);
		}

		return $metadata;
	}

	/**
	 * テーブル関連のメタデータをステータス（最終同期日時など）付きで取得する
	 *
	 * @param string $table テーブル名
	 * @return array メタデータの連想配列
	 */
	function getMetadataWithStatus($table)
	{
		$metadata = $this->getMetadata($table);
		if (!is_array($metadata)) {
			return $metadata;
		}

//		$last_modified = $this->getMasterLastModified($table);
//		if (!Ethna::isError($last_modified) && $last_modified) {
//			$metadata['last_modified'] = $last_modified;
//		}

		$metadata['last_synced'] = array();
		foreach ($this->MASTER_SYNC_MULTI_LABEL as $mode => $label) {
			$last = $this->getMasterLastSynced($table, $mode);
			if (!Ethna::isError($last) && $last) {
				$metadata['last_synced'][$mode] = $last;
			}
		}

		$metadata['last_modified'] = $this->getMasterLastModified($table);

		return $metadata;
	}

	/**
	 * MySQL型に対応するEditableGridのdatatypeを取得する
	 *
	 * @param string $type MySQLの型
	 * @return string EditatbleGridのdatatype
	 */
	protected function getEditableGridDatatypeFromMysqlType($type)
	{
		$map = array(
			'number' => array('int', 'smallint', 'tinyint', 'mediumint', 'bigint'),
			'string' => array('varchar', 'char', 'text', 'date', 'datetime', 'timestamp'),
		);

		foreach ($map as $datatype => $heads) {
			foreach ($heads as $head) {
				if (strncmp($type, $head, strlen($head)) === 0) {
					return $datatype;
				}
			}
		}
	}

	/**
	 * EditableGridのmetadataを取得する
	 *
	 * 戻り値をjson_encodeすればEditableGridで使用可能
	 * @param string $table テーブル名
	 * @param bool $is_primary_key_editable 主キーもeditableにするか
	 * @return array metadata
	 */
	function getEditableGridMetadata($table, $is_primary_key_editable = false)
	{
		if (!isset($this->metadata[$table])) {
			return false;
		}

		$full_metadata = $this->metadata[$table];

		$metadata = array();
		foreach ($full_metadata['columns_label'] as $name => $label) {
			$metadata_row = array('name' => $name, 'label' => $label);
			$metadata_row['datatype'] = $full_metadata['editablegrid_datatype'][$name];

			if ($is_primary_key_editable) {
				$editable = true;
			} else if (!in_array($name, $full_metadata['primary_keys'])) {
				$editable = true;
			} else {
				$editable = false;
			}

			$metadata_row['editable'] = $editable;

			if (isset($full_metadata['editablegrid_values'][$name])) {
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
	 * 主キーが有効な書式の値かチェックする
	 *
	 * @param string $name カラム名
	 * @param int|string $value 値
	 * @return boolean 正否
	 */
	protected function isValidPrimaryKeyFormat($name, $value)
	{
		if ($this->isDateColumnName($name)) {
			return strtotime($value) ? true : false;
		} elseif ($this->isStringColumnName($name)) {
			return strlen($value) > 0 ? true : false;
		} else {
			return is_numeric($value);
		}
	}

	/** 日付型のカラム名か */
	function isDateColumnName($name)
	{
		switch ($name) {
			case 'date_start':
			case 'date_end':
			case 'start_date':
			case 'end_date':
			case 'date_bonus':
				return true;
		}

		return false;
	}

	/** 文字列型のカラム名か */
	function isStringColumnName($name)
	{
		switch ($name) {
			case 'ai_id':
				return true;
		}

		return false;
	}

	/**
	 * マスターテーブルのデータ一覧を取得する
	 *
	 * @param string $table テーブル名
	 * @param bool $all 全カラムを取得するか（true: 全カラムを取得する, false: $this->metadataでcolumns_labelが定義されているカラムのみ取得する）
	 * @param string $where 取得条件とするWHERE句の内容（先頭の"WHERE "は不要）※SQLインジェクションチェックはこの関数内では行わないので注意！　呼び元で確認すること
	 * @return array データ一覧の連想配列。連想配列のキーは、主キー情報を元に作成した文字列。
	 */
	function getMasterList($table, $all = true, $where = null)
	{
		if (!isset($this->metadata[$table])) {
			return false;
		}

		if ($all) {
			$keys = array('*');
		} else {
			$label = $this->getMasterColumnsLabel($table);
			$keys = array_keys($label);
		}

		$sql = "SELECT " . implode(',', $keys) . " FROM $table ";
		if ($where !== null) {
			$sql .= ' WHERE ' . $where . ' ';
		}
		$sql .= " ORDER BY " . implode(',', $this->metadata[$table]['primary_keys']);

		$list = array();
		foreach ($this->db_m_r->GetAll($sql) as $row) {
			$id = $this->getRowIdFromAssoc($table, $row);

			$list[$id] = $row;
		}

		return $list;
	}

	function getUserList($table, $user_id)
	{
		if (!isset($this->metadata[$table])) {
			return false;
		}

		$sql = "SELECT * FROM $table";

		if ($table == 't_user_friend') {
			$sql .= " WHERE user_id = ? OR friend_id = ?";
			$param = array($user_id, $user_id);
		} else {
			$sql .= " WHERE user_id = ?";
			$param = array($user_id);
		}

		$sql .= " ORDER BY " . implode(',', $this->metadata[$table]['primary_keys']);

		$list = array();
		foreach ($this->db_r->db->GetAll($sql, $param) as $row) {
			$id = $this->getRowIdFromAssoc($table, $row);

			$list[$id] = $row;
		}

		return $list;
	}

	/**
	 * マスターテーブルの表示用ラベルを取得する
	 *
	 * @param string $table テーブル名
	 * @return string ラベル
	 */
	function getMasterTableLabel($table)
	{
		return $this->metadata[$table]['table_label'];
	}

	/**
	 * マスターテーブルの表示用ラベルを連想配列で取得する
	 *
	 * @return array ラベル情報（キーがテーブル名、値がラベル）
	 */
	function getMasterTableLabelAssoc()
	{
		return $this->getTableLabelAssoc('m_');
	}

	/**
	 * ユーザーテーブルの表示用ラベルを連想配列で取得する
	 *
	 * @return array ラベル情報（キーがテーブル名、値がラベル）
	 */
	function getUserTableLabelAssoc()
	{
		return $this->getTableLabelAssoc('ut_');
	}

	/**
	 * テーブルの表示用ラベルを連想配列で取得する
	 *
	 * @param string $prefix 取得対象テーブル名prefix（省略可。省略すると全テーブルを取得）
	 * @return array ラベル情報（キーがテーブル名、値がラベル）
	 */
	protected function getTableLabelAssoc($prefix = null)
	{
		$prefix_len = $prefix ? strlen($prefix) : 0;

		$assoc = array();
		foreach ($this->metadata as $table => $columns) {
			if ($prefix && (strncmp($table, $prefix, $prefix_len) !== 0)) {
				continue;
			}

			$assoc[$table] = $columns['table_label'];
		}

		return $assoc;
	}

	/**
	 * マスターテーブルのカラムの表示用ラベルを取得する
	 *
	 * @param string $table テーブル名
	 * @return array ラベルの配列
	 */
	function getMasterColumnsLabel($table)
	{
		return $this->metadata[$table]['columns_label'];
	}

	/**
	 * 主キーの位置（メタデータのcolumns_labelの定義上で何番目か）を取得する
	 *
	 * @param string $table テーブル名
	 * @return array 位置の配列
	 */
	function getPrimaryKeyPositions($table)
	{
		$positions = array();
		$primary_keys = $this->metadata[$table]['primary_keys'];
		$i = 0;
		foreach ($this->metadata[$table]['columns_label'] as $field => $label) {
			if (in_array($field, $primary_keys)) {
				$positions[] = $i;
			}

			$i++;
		}

		return $positions;
	}

	function getPrimaryKeys($table)
	{
		return $this->metadata[$table]['primary_keys'];
	}

	/**
	 * 行の内容をあらわす連想配列から行IDを取得する
	 *
	 * 行IDとは、主キーをdelimiterでつなげた文字列。使用するのは管理画面でのみ。
	 * @param string $table テーブル名
	 * @param array $row  カラム内容の連想配列
	 * @param string $delimiter 区切り文字
	 * @return string 行ID
	 */
	function getRowIdFromAssoc($table, $row, $delimiter = '_')
	{
		$tmp = array();
		foreach ($this->metadata[$table]['primary_keys'] as $key) {
			$value = $row[$key];
			if ($this->isDateColumnName($key)) {
				$value = strtotime($value);
			}

			$tmp[] = $value;
		}
		$id = implode($delimiter, $tmp);

		return $id;
	}

	/**
	 * 行の内容をあらわす配列から行IDを取得する
	 *
	 * 行IDとは、主キーをdelimiterでつなげた文字列。使用するのは管理画面でのみ。
	 * @param string $table テーブル名
	 * @param array $row  カラム内容の配列。配列の順番はメタデータのcolumns_labelと同じである必要がある。
	 * @param string $delimiter 区切り文字
	 * @return string 行ID
	 */
	function getRowIdFromArray($table, $row, $delimiter = '_')
	{
		$label = $this->getMasterColumnsLabel($table);

		$tmp = array();
		foreach ($this->metadata[$table]['primary_keys'] as $key) {
			$i = 0;
			foreach ($label as $colname => $dummy) {
				if ($colname == $key) {
					break;
				}

				$i++;
			}

			$value = $row[$i];
			if ($this->isDateColumnName($key)) {
				$value = strtotime($value);
			}

			$tmp[] = $value;
		}

		$id = implode($delimiter, $tmp);

		return $id;
	}

	/**
	 * マスタのレコードを全削除
	 *
	 * @param  string  $table  テーブル名
	 */
	function truncateMaster($table)
	{
		// 全行削除
		$sql = "TRUNCATE TABLE ".$table;
		$ret = $this->db_m->db->execute($sql);

		if (!$ret) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $table);
			return false;
		}
		return true;
	}

	/**
	 * マスタの更新
	 *
	 * @param  string  $table  テーブル名
	 * @param	array	$row	行の内容（カラムの値を、getMasterColumnsLabel関数での定義と同じ順に並べたもの）
	 * @param  string  $crud   CRUD種別('c' or 'u')
	 * @see  http://phplens.com/lens/adodb/docs-adodb.htm#autoexecute
	 */
	function updMaster($table, $row, $crud)
	{
		$this->master_error_msg = null;
		$this->master_error_no  = null;

		$primary_keys = $this->metadata[$table]['primary_keys'];

		$record = array(); // ADODB AutoExecuteに渡すレコード情報
		foreach ($this->getMasterColumnsLabel($table) as $colname => $dummy) {
			$record[$colname] = array_shift($row);
		}

		foreach ($primary_keys as $pkname) {
			if (!$this->isValidPrimaryKeyFormat($pkname, $record[$pkname])) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
		}

////DEBUG
//$debug_not_ignore_tables = array('m_asset_bundle', 'm_monster'); // デバッグ用に無視しない（本当に更新実行する）テーブル名の配列

		$ret = null;
		if ($crud == 'c') {
			// 隠しカラム対応
			if (isset($this->metadata[$table]['hidden_columns'])) {
				foreach ($this->metadata[$table]['hidden_columns'] as $hidden) {
					// ※ここのフィールド名の記述はジャグモン以外のDBだと異なるはずなので、移植する際は注意
					if (($hidden == 'date_created') || ($hidden == 'created_date')) {
						$record[$hidden] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
					}
				}
			}

//if (in_array($table, $debug_not_ignore_tables)) {
			$ret = $this->db_m->db->AutoExecute($table, $record, 'INSERT');
//} else {
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export(array($table, $record, 'INSERT'), true));
//$ret = true;//DEBUG
//}
		} else if ($crud == 'u') {
			$tmp = array();
			foreach ($primary_keys as $pkname) {
				$pkvalue = $record[$pkname];
				unset($record[$pkname]);

				if ($this->isDateColumnName($pkname)) {
					$tmp[] = "$pkname = '$pkvalue'";
				} elseif ($this->isStringColumnName($pkname)) {
					$tmp[] = "$pkname = '$pkvalue'";
				} else {
					$tmp[] = "$pkname = $pkvalue";
				}
			}
			$where = implode(' AND ', $tmp);

//if (in_array($table, $debug_not_ignore_tables)) {
			$ret = $this->db_m->db->AutoExecute($table, $record, 'UPDATE', $where);
//} else {
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export(array($table, $record, 'UPDATE', $where), true));
//$ret = true;//DEBUG
//}
		}

		if (!$ret) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $crud . ':' . $table . ':' . var_export($record, true));

			$this->master_error_msg = $this->db_m->db->ErrorMsg();
			$this->master_error_no  = $this->db_m->db->ErrorNo();

			return false;
		}

//if (in_array($table, $debug_not_ignore_tables)) {
		if ( $this->db_m->db->affected_rows() != 1 ) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
//}

		return true;
	}

	/**
	 * マスタの更新処理でのエラーメッセージを取得する
	 *
	 * エラーが無かった場合はnullが返る
	 * @return string エラーメッセージ
	 */
	function getMasterErrorMsg()
	{
		return $this->master_error_msg;
	}

	/**
	 * マスタの更新処理でのエラー番号を取得する
	 *
	 * エラーが無かった場合はnullが返る
	 * @return int エラー番号
	 */
	function getMasterErrorNo()
	{
		return $this->master_error_no;
	}

	/**
	 * マスターテーブルのCSV用グリッドデータを取得する
	 *
	 * @param string $table テーブル名
	 * @param string $where 取得条件とするWHERE句の内容（先頭の"WHERE "は不要）※SQLインジェクションチェックはこの関数内では行わないので注意！　呼び元で確認すること
	 * @return array グリッドデータ（Pp_AdminViewClass::outputCsv()の引数として使える）
	 */
	function getMasterCsvGrid($table, $where = null)
	{
		$list  = $this->getMasterList($table, true, $where);
		$label = $this->getMasterColumnsLabel($table);
		$keys = array_keys($label);

		$grid = array();
		$grid[] = array_values($label);
		foreach ($list as $row) {
			$tmp = array();
			foreach ($keys as $key) {
				$tmp[] = $row[$key];
			}

			$grid[] = $tmp;
		}

		return $grid;
	}

	/**
	 * マスターテーブルのCSVデータを取得する
	 *
	 * @param string $table テーブル名
	 * @return string CSVデータ
	 */
	function getMasterCsv($table)
	{
		$grid = $this->getMasterCsvGrid($table);
		$csv = Util::assembleCsv($grid);

		return $csv;
	}

	/**
	 * Percona Toolkit用のDSNを取得する
	 *
	 * @param string $dsn Ethnaの書式でのDSN
	 * @return string Percona Toolkitの書式でのDSN
	 */
	function getPtDsn($dsn)
	{
		if (!$dsn) {
			return false;
		}

		$parsed = $this->db->parseDSN($dsn);

		$assoc = array();

//		$hostspec = explode(':', $parsed['hostspec']);
//		$assoc['h'] = $hostspec[0]; // hostname
//		if (count($hostspec) > 1) {
//			$assoc['P'] = $hostspec[1]; // port
//		}

		$assoc['h'] = $parsed['hostspec'];

		if (isset($parsed['port']) && $parsed['port']) {
			$assoc['P'] = $parsed['port'];
		}

		$assoc['D'] = $parsed['database'];
		$assoc['u'] = $parsed['username'];
		$assoc['p'] = $parsed['password'];

		$pt_dsn = http_build_query($assoc, '', ',');

		return $pt_dsn;
	}

	/**
	 * マスターデータのアップロード時ログCSVファイルを探す
	 *
	 * @param string $table テーブル名
	 * @param string $dir ディレクトリ
	 * @return ファイル情報　$list[index]['filename] = ファイル名（ディレクトリ含まず）
	 *									 ['table']	 = テーブル名
	 *									 ['date']	 = 日付(YmdHis)
	 *									 ['user']	 = 管理画面ユーザ名
	 *									 ['type']	 = 種別('base' or 'update')
	 */
	function findUploadLog($table, $dir = null)
	{
		if (!$dir) {
			$dir = BASE . self::MASTER_UPLOAD_LOG_SUBDIR;
		}

		if (!is_dir($dir)) {
			return null;
		}

		$pattern = 'pp-' . $table . '-20*.csv';
		$items = &File_Find::glob($pattern, $dir, 'shell');

		if (!$items) {
			return null;
		}

		sort($items);

		$list = array();
		foreach ($items as $item) {
			$parts = explode('-', $item);
			$list[] = array(
				'filename' => $item,
				'table'  => $parts[1],
				'date'	 => $parts[2],
				'user'	 => $parts[3],
				'type'	 => $parts[4],
			);
		}

		return $list;
	}

	/**
	 * マスターデータのアップロード時ログCSVファイル名を組み立てる
	 *
	 * @param string $table テーブル名
	 * @param string $user 管理画面ユーザ名
	 * @param string $type 種別('base' or 'update')
	 * @param int $time タイムスタンプ値
	 * @return string ファイル名（ディレクトリ含まず）
	 */
	function assembleUploadLogFilename($table, $user, $type, $time = null)
	{
		assert(($type == 'base') || ($type == 'update'));

		if (!$time) {
			$time = $_SERVER['REQUEST_TIME'];
		}

		$date = date('YmdHis', $time);
		$_user = str_replace('.', '_', $user);

		$filename = 'pp-' . $table . '-' . $date . '-' . $_user . '-' . $type
				  . '.csv';

		return $filename;
	}

	/**
	 * マスターテーブルのデータ一覧を、アプリ用データ（JSON変換用）のフォーマットで取得する
	 */
	function getMasterListForAppliData($table)
	{
		if ($table == 'm_skill') {
			$skill_m =& $this->backend->getManager('AdminSkill');
			$list = $skill_m->getMasterSkillListForAppliData();
		} else if ($table == 'm_skill_effect') {
			$skill_m =& $this->backend->getManager('AdminSkill');
			$list = $skill_m->getMasterSkillEffectListForAppliData();
		} else if ($table == 'm_leader_skill') {
			$skill_m =& $this->backend->getManager('AdminSkill');
			$list = $skill_m->getMasterLeaderSkillListForAppliData();
		} else if ($table == 'm_leader_skill_effect') {
			$skill_m =& $this->backend->getManager('AdminSkill');
			$list = $skill_m->getMasterLeaderSkillEffectListForAppliData();
		} else {
			$list = $this->getMasterList($table, false);
			$list = array_values($list);
		}

		$list = $this->array_intval($list);

		// m_monster.evolution_materialはDB上ではカンマ区切り文字列だが、管理画面でのJSON出力時に数値の配列に変換する
		// 2013/7/4 久保さんからの要望
		if ($table == 'm_monster') {
			$i = count($list);
			while ($i--) {
				if (strlen($list[$i]['evolution_material']) > 0) {
					$evolution_material = array_map('intval', explode(',', $list[$i]['evolution_material']));
				} else {
					$evolution_material = array();
				}

				$list[$i]['evolution_material'] = $evolution_material;
			}
		}
		//m_monster_action_id、m_monster_action_tblも同様
		//特定のカラムのみ数値の配列に変換して書き換える
		if ($table == 'm_monster_action_id' || $table == 'm_monster_action_tbl') {
			$clm = array('act_param', 'ref_id', 'act_rate', 'act_seq');
			$i = count($list);
			while ($i--) {
				foreach ($clm as $val) {
					if (isset($list[$i][$val])) {
						if (strlen($list[$i][$val]) > 0) {
							$tmp = array_map('intval', explode(',', $list[$i][$val]));
						} else {
							$tmp = array();
						}
						$list[$i][$val] = $tmp;
					}
				}
			}
		}

	}

	/**
	 * マスターデータの最終更新日時を取得する
	 *
	 * @param string $table テーブル名
	 * @return array array('date_modified' => 最終更新日時(Y-m-d H:i:s), 'account_upd' => 更新アカウント)
	 */
	protected function getMasterLastModified($table)
	{
		if (!isset($this->metadata[$table])) {
			return Ethna::raiseError('Invalid table.', E_USER_ERROR);
		}

		// 各所から最終更新日時情報を取得
		$rows = array();
		$rows[] = $this->getMasterLastModifiedFromLogTable($table);
		$rows[] = $this->getMasterLastModifiedFromMasterTable($table);

		// 取得した最終更新日時情報の中で、最新のものを求める
		$last_row = array('date_modified' => null, 'account_upd' => null);
		foreach ($rows as $row) {
			if (!is_array($row) || !isset($row['date_modified'])) {
				continue;
			}

			if (!$last_row || ($last_row['date_modified'] < $row['date_modified'])) {
				$last_row = $row;
			}
		}

		return $last_row;
	}

	/**
	 * マスターデータの最終更新日時をマスターテーブルから取得する
	 *
	 * @param string $table テーブル名
	 * @return array array('date_modified' => 最終更新日時(Y-m-d H:i:s), 'account_upd' => 更新アカウント)
	 */
	protected function getMasterLastModifiedFromMasterTable($table)
	{
		if (!isset($this->metadata[$table])) {
			return Ethna::raiseError('Invalid table.', E_USER_ERROR);
		}

		// カラム名候補
		// $candidates[カラム名] => array(カラム別名, ...)
		$candidates = array(
			'date_modified' => array('date_modified', 'modify_date'),
			'account_upd'	=> array('account_upd', 'account_modified'),
		);

		// 検出したカラム名
		// $detected[カラム名] = カラム名または別名
		$detected = array(
			'date_modified' => null,
			'account_upd'	=> null,
		);

		// カラムがあるか判別する
		foreach ($candidates as $name => $aliases) foreach ($aliases as $alias) {
			if (isset($this->metadata[$table]) &&
				isset($this->metadata[$table]['account_columns']) &&
				in_array($alias, $this->metadata[$table]['account_columns'])
			) {
				$detected[$name] = $alias;
			}
		}

		if (!$detected['date_modified']) {
			// OK. カラム無し
			return null;
		}

		// 最終更新日時とアカウントを求める
		$sql = "SELECT " . $detected['date_modified'] . " AS date_modified";
		if ($detected['account_upd']) {
			$sql .= ", " . $detected['account_upd'] . " AS account_upd";
		}
		$sql .= " FROM " . $table
			 .	" ORDER BY date_modified DESC LIMIT 1";

		return $this->db_m_r->GetRow($sql);
	}

	/**
	 * マスターデータの最終更新日時をログテーブルから取得する
	 *
	 * @param string $table テーブル名
	 * @return array array('date_modified' => 最終更新日時(Y-m-d H:i:s), 'account_upd' => 更新アカウント)
	 */
	protected function getMasterLastModifiedFromLogTable($table)
	{
		if (!isset($this->metadata[$table])) {
			return Ethna::raiseError('Invalid table.', E_USER_ERROR);
		}

		$param = array($table);
		$sql = "SELECT date_created AS date_modified, account_reg AS account_upd"
			 . " FROM log_master_modify"
			 . " WHERE table_name = ?"
			 . " ORDER BY date_created DESC LIMIT 1";

		return	$this->db_logex->GetRow($sql, $param);
	}

	/**
	 * マスタテーブル更新ログを記録する
	 *
	 * @param array $columns ログ内容（カラム名 => 値 の連想配列。idとdate_createdは含めなくてよい）
	 * @return bool 成否
	 */
	function logMasterModify($columns)
	{
		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_logex->db->AutoExecute('log_master_modify', $columns, 'INSERT');
	}

	/**
	 * マスターデータの最終同期日時を取得する
	 *
	 * sync_resultが
	 * 0: Success.
	 * 2: At least one table differed on the destination.
	 * のいずれかであるログを取得対象とする。
	 * @see http://www.percona.com/doc/percona-toolkit/2.2/pt-table-sync.html#exit-status
	 * @param string $table テーブル名
	 * @param string $mode 動作モード('standby' or 'deploy')
	 * @return string 最終同期日時(Y-m-d H:i:s)
	 */
	protected function getMasterLastSynced($table, $mode)
	{
		$ok_statuses = array();
		foreach ($this->PT_TABLE_SYNC_EXIT_STATUS as $status => $status_info) {
			if ($status_info['type'] == 'OK') {
				$ok_statuses[] = $status;
			}
		}

		$param = array($table, $mode);
		$sql = "SELECT date_created FROM log_master_sync WHERE table_name = ? AND mode = ?"
			 . " AND sync_result IN (" . implode(',', $ok_statuses) . ")"
			 . " AND verify_result = " . self::MASTER_SYNC_VERIFY_OK
			 . " ORDER BY date_created DESC LIMIT 1";

//		return $this->db_r->GetOne($sql, $param);
		$row = $this->db_logex->GetRow($sql, $param);
		if (is_array($row) && isset($row['date_created'])) {
			return $row['date_created'];
		}
	}

	/**
	 * マスタテーブル同期ログを記録する
	 *
	 * @param array $columns ログ内容（カラム名 => 値 の連想配列。idとdate_createdは含めなくてよい）
	 * @return bool 成否
	 */
	function logMasterSync($columns)
	{
		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_logex->db->AutoExecute('log_master_sync', $columns, 'INSERT');
	}

	/**
	 * マスタテーブル同期ログ一覧を記録する
	 *
	 * @param string $mode 動作モード('standby' or 'deploy')
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	function getMasterSyncLogList($mode, $offset = 0, $limit = 100000)
	{
		$param = array($mode, $offset, $limit);
		$sql = "SELECT *"
			. " FROM log_master_sync"
			. " WHERE mode = ?"
			. " ORDER BY id DESC"
			. " LIMIT ?, ?";

		return $this->db_logex->GetAll($sql, $param);
	}

	/**
	 * マスターデータの最終同期したログテーブルを取得する
	 *
	 * @param string $table テーブル名
	 * @return array メタデータの連想配列
	 */
	function getLastLogMasterSync($table)
	{
		$param = array($table);
		$sql = "SELECT date_created AS date_modified, account_reg AS account_upd"
			 . " FROM log_master_sync"
			 . " WHERE table_name = ? AND sync_result = 0 AND verify_result = 0"
			 . " ORDER BY date_created DESC LIMIT 1";

		return	$this->db_logex->GetRow($sql, $param);
	}



	/**
	 * ガチャカテゴリマスタのガチャID指定アップロード時ログCSVファイルの保存先サブディレクトリを取得する
	 */
	function getGachaWeightCategoryUploadLogSubdir($gacha_id)
	{
		return self::MASTER_UPLOAD_LOG_SUBDIR . '/' . 'm_gacha_category/' . $gacha_id;
	}

	/**
	 * ガチャアイテムリストマスタのガチャID指定アップロード時ログCSVファイルの保存先サブディレクトリを取得する
	 */
	function getGachaWeightItemUploadLogSubdir($gacha_id)
	{
		return self::MASTER_UPLOAD_LOG_SUBDIR . '/' . 'm_gacha_itemlist/' . $gacha_id;
	}

	/**
	 * pt-table-syncのExit Statusの種別を取得する
	 *
	 * @param int $status
	 * @return string|null	'OK' or 'NG' or null
	 */
	function getPtTableSyncExitStatusType($status)
	{
		if (isset($this->PT_TABLE_SYNC_EXIT_STATUS[$status])) {
			return $this->PT_TABLE_SYNC_EXIT_STATUS[$status]['type'];
		} else {
			return null;
		}
	}

	/**
	 * おまけガチャカテゴリマスタのガチャID指定アップロード時ログCSVファイルの保存先サブディレクトリを取得する
	 */
	function getGachaWeightExtraCategoryUploadLogSubdir($gacha_id)
	{
		return self::MASTER_UPLOAD_LOG_SUBDIR . '/' . 'm_gacha_extra_category/' . $gacha_id;
	}

	/**
	 * おまけガチャアイテムリストマスタのガチャID指定アップロード時ログCSVファイルの保存先サブディレクトリを取得する
	 */
	function getGachaWeightExtraItemUploadLogSubdir($gacha_id)
	{
		return self::MASTER_UPLOAD_LOG_SUBDIR . '/' . 'm_gacha_extra_itemlist/' . $gacha_id;
	}

	/**
	 * svn checkoutコマンドを取得する
	 *
	 * 設定ファイルにあるレポジトリ情報を基にsvn checkoutコマンドを組み立てる
	 * @param string $path SVNパス("/trunk/web"等)
	 * @param string $revision リビジョン番号
	 * @param boolean $export checkoutの代わりにexportを実行するか
	 * @return string|boolean コマンド文字列（失敗時はfalse)
	 */
	function getSvnCheckoutCommand($path, $revision = null, $export = false)
	{
		if (!preg_match('/^\/[\/a-zA-Z0-9_-]+$/', $path)) {
			$this->logger->log(LOG_WARNING, 'Invalid path.');
			return false;
		}

		$config_svn = $this->config->get('svn');
		if (!is_array($config_svn) ||
			!isset($config_svn['root']) || (strlen($config_svn['root']) == 0)
		) {
			$this->logger->log(LOG_WARNING, 'Invalid config_svn.');
			return false;
		}

		$root = $config_svn['root'];

		$url = $root . $path;

		$svn_command = "svn " . ($export ? "export" : "checkout");
		if ($revision > 0) {
			$svn_command .= " -r $revision";
		}
		$svn_command .= " $url";

		return $svn_command;
	}

	/**
	 * テンポラリのSVN作業コピールートディレクトリを取得する
	 *
	 * @param string $uniqid 一意なID（チェックアウト毎に一意）
	 * @param string $session_id セッションID（管理ページログイン毎のセッションID）
	 * @return string ディレクトリ名（フルパス）
	 */
	function getTmpSvnWcroot($uniqid, $session_id = null)
	{
		$wcroot = BASE . '/tmp/svn' . $uniqid;
		if ($session_id !== null) {
			$wcroot .= $session_id;
		}

		return $wcroot;
	}

	/**
	 * ローカルホストへのSSH経由のコマンドを取得する
	 *
	 * @param string $command コマンド文字列
	 * @return string|boolean コマンド文字列（失敗時はfalse)
	 */
	function getCommandViaSshLocalhost($command)
	{
		$ssh_localhost = $this->config->get('ssh_localhost');
		if (!$ssh_localhost) {
			$this->backend->logger->log(LOG_WARNING, 'Invalid config ssh_localhost.');
			return false;
		}

		$command = $ssh_localhost . ' "' . $command . '"';

		return $command;
	}
}
