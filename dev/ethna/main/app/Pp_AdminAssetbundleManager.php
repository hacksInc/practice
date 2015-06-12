<?php
/**
 *  Pp_AdminAssetbundleManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AssetbundleManager.php';

/**
 *  Pp_AdminAssetbundleManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminAssetbundleManager extends Pp_AssetbundleManager
{
	/**
	 * リソースバージョンマスタのキャッシュクリアフラグのSELECTボックス用オプション値
	 */
	var $RES_VER_CLEAR_OPTIONS = array(
		0 => 'OFF',
		1 => 'ON',
	); 

	/**
	 * リソースバージョンマスタのリソース関連のキー名
	 */
	static protected $RES_VER_KEYS = array(
		'res_ver',
        'mon_ver',
        'mon_image_ver',
        'skilldata_ver',
        'skilleffect_ver',
        'bgmodel_ver',
        'sound_ver',
        'map_ver',
		'worldmap_ver',
        'mon_exp_ver',
        'player_rank_ver',
        'ach_ver',
        'mon_act_ver',
        'boost_ver',
        'badge_ver',
        'badge_material_ver',
        'badge_skill_ver',
	);
	
	/**
	 * デバイス毎のビルド名
	 * AssetBundle(monster)の命名規則に基づく
	 * @see リソースアップデート実装仕様20130520.xls
	 */
	protected $BuildTarget = array(
		'PC'      => '',
		'Android' => 'Android',
		'iPhone'  => 'iPhone',
	);
	
	/** 許可するディレクトリの先頭部分 */
	protected $dir_allow = array('monster', 'others', 'effect', 'bgmodel', 'sound', 'map', 'worldmap');
	
	/**
	 * ファイル名を連結する
	 * 
	 * @see class Pp_Action_ResourceAssetbundle
	 * @param string $file_name  ファイル名
	 * @param string $version  ヴァージョン
	 * @param string $device_name 'Android' or 'iPhone' or ''
	 * @return string ファイル名
	 */
	static function jointFileName($file_name, $version, $device_name)
	{
		$joint_file_name = $file_name.".".$version;
		if (!empty($device_name)) {
			$joint_file_name .= ".".$device_name;
		}
		$joint_file_name .= ".unity3d";

		return $joint_file_name;
	}
	
	/**
	 * ファイル名を分割する
	 * 
	 * @param string $joint_file_name ファイル名
	 * @return array キーが'file_name','version','device_name'の連想配列
	 */
	static function splitFileName($joint_file_name)
	{
		$arr = explode('.', $joint_file_name);
		$file_name = $arr[0];
		$version   = $arr[1];
		
		if (isset($arr[3])) {
			$device_name = $arr[2];
		} else {
			$device_name = '';
		}
		
		return array(
			'file_name'   => $file_name,
			'version'     => $version,
			'device_name' => $device_name,
		);
	}
	
	/**
	 * リソースバージョンマスタのリソース関連のキー名を取得する
	 */
	static function getResVerKeys()
	{
		return self::$RES_VER_KEYS;
	}

	/**
	 * アセットバンドルをファイルに書き込む
	 * 
	 * @param string $dir ディレクトリ(m_asset_bundleテーブルのdirカラムと同じ、サブディレクトリでの指定)
	 * @param string $joint_file_name ファイル名
	 * @param string $contents 内容
	 * @return int|false file_put_contents結果
	 */
	function putAssetbundleFileContents($dir, $joint_file_name, $contents)
	{
//		umask(0002);
		// 新開発環境のシェルから、
		// ファイル設置用ディレクトリにchgrp apacheやchmod g+sができなくなった事に伴い、
		// Apache上のPHPからファイル設置する際はどのユーザーでも書き込めるパーミッションにする。(2013/10/30)
		umask(0000);
		$assetbundle_dir = BASE . '/data/resource/assetbundle/' . $dir;
		is_dir($assetbundle_dir) || mkdir($assetbundle_dir);

		$full_path = $assetbundle_dir . '/' . $joint_file_name;

		return file_put_contents($full_path, $contents);
	}
	
	/**
	 * m_asset_bundleデータを取得する
	 * 
	 * @param int $id 管理ID
	 * @return array m_asset_bundleデータ（m_asset_bundleのカラム名がキー）
	 */
	function getMasterAssetBundle($id)
	{
		return $this->db_r->GetRow(
			"SELECT * FROM m_asset_bundle WHERE id = ?",
			array($id)
		);
	}
	
	/**
	 * m_asset_bundleデータを一意制約に基づいて取得する
	 * 
	 * @param int    $file_type ファイルタイプ
	 * @param string $dir       ディレクトリ
	 * @param string $file_name ファイル名
	 * @param int    $version   ヴァージョン
	 * @return array m_asset_bundleデータ（m_asset_bundleのカラム名がキー）
	 */
	function getMasterAssetBundleByUniqueKey($file_type, $dir, $file_name, $version)
	{
		return $this->db_r->GetRow(
			"SELECT * FROM m_asset_bundle WHERE file_type = ? AND dir = ? AND file_name = ? AND version = ?",
			array($file_type, $dir, $file_name, $version)
		);
	}
	
	/** 
	 * m_asset_bundleデータを付加情報付きで取得する
	 * 
	 * 引数はgetMasterAssetBundle()と同じ。
	 * 戻り値に、
	 * $row['joint_file_name']['Android' or 'iPhone' or 'PC'] = ファイル名
	 * というデータが付く。
	 * モンスター画像のアセットバンドルの場合は、戻り値に、
	 * $row['image'] = モンスター画像のパス（サーバ内ファイルシステム上のパス）
	 * $row['icon']  = モンスターアイコンのパス（サーバ内ファイルシステム上のパス）
	 * というデータも付く。
	 */
	function getMasterAssetBundleEx($id)
	{
		$row = $this->getMasterAssetBundle($id);
		
		$joint_file_name = array();
		foreach ($this->BuildTarget as $label => $device_name) {
			$joint_file_name[$label] = self::jointFileName($row['file_name'], $row['version'], $device_name);
		}
		
		$row['joint_file_name'] = $joint_file_name;

		$dirname = dirname($row['dir']);
		if ($dirname == 'monster') {
			$monster_id = basename($row['dir']);
			$row['icon']  = $this->getMonsterImagePath('icon', $monster_id);
			$row['image'] = $this->getMonsterImagePath('image', $monster_id);
		}
		
		return $row;
	}
	
	/**
	 * m_asset_bundleデータ一覧を取得する
	 * 
	 * @param string $dir ディレクトリの先頭部分
	 * @param int $offset オフセット値
	 * @param int $limit  件数
	 * @return array m_asset_bundleデータ一覧（m_asset_bundle.idがキー）
	 */
	function getMasterAssetBundleList($dir, $offset = null, $limit = null)
	{
		if (!in_array($dir, $this->dir_allow)) {
			return Ethna::raiseError('Invalid dir.', E_USER_ERROR);
		}
		
		$param = array();
		$sql = "SELECT m.id, m.*"
		     . " FROM m_asset_bundle m"
		     . " WHERE dir LIKE '$dir%'"
		     . " ORDER BY start_date DESC, dir ASC, file_name ASC, version DESC, id DESC";
		
		if ($limit) {
			$param[] = $offset;
			$param[] = $limit;
			$sql .= " LIMIT ?,?";
		}
		
		return $this->db_r->db->GetAssoc($sql, $param);
	}
	
	/**
	 * m_asset_bundleデータ一覧を付加情報付きで取得する
	 * 
	 * 引数はgetMasterAssetBundleList()と同じ。
	 * $dirが'monster'の場合、戻り値に、
	 * $list[m_asset_bundle.id]['monster'] = array(m_monsterテーブルのカラム名 => 値)
	 *                         ['image'] = モンスター画像のパス（サーバ内ファイルシステム上のパス）
	 *                         ['icon']  = モンスターアイコンのパス（サーバ内ファイルシステム上のパス）
	 * というデータが付く。
	 * $dirが'bgmodel'の場合、戻り値に、
	 * $list[m_asset_bundle.id]['bgmodel_id'] = ゲーム背景のID（ゲーム背景の管理画面に"ID"として表示する為の文字列）
	 * というデータが付く。
	 */
	function getMasterAssetBundleListEx($dir, $offset = null, $limit = null)
	{
		$list = $this->getMasterAssetBundleList($dir, $offset, $limit);
		
		if ($dir == 'monster') {
			$monster_m = $this->backend->getManager('Monster');
			$monsters = $monster_m->getMasterMonsterAssoc();
			
			foreach ($list as $id => $row) {
				$monster_id = basename($row['dir']);

				$monster = array('monster_id' => $monster_id);
				if (isset($monsters[$monster_id])) {
					$monster = array_merge($monster, $monsters[$monster_id]);
				}
				
				$list[$id]['monster'] = $monster;
				
				$list[$id]['icon']  = $this->getMonsterImagePath('icon', $monster_id);
				$list[$id]['image'] = $this->getMonsterImagePath('image', $monster_id);
			}
		} else if ($dir == 'bgmodel') {
			foreach ($list as $id => $row) {
				$list[$id]['bgmodel_id'] = $this->getBgmodelId($row['file_name']);
			}
		} else if ($dir == 'map') {
			foreach ($list as $id => $row) {
				$list[$id]['map_id'] = $this->getMapId($row['file_name']);
			}
		} else if ($dir == 'worldmap') {
			foreach ($list as $id => $row) {
				$list[$id]['worldmap_id'] = $this->getWorldmapId($row['file_name']);
			}
		}
			
		return $list;
	}
	
	/**
	 * m_asset_bundleデータ件数を取得する
	 * 
	 * @param string $dir ディレクトリの先頭部分
	 * @return int 件数
	 */
	function countMasterAssetBundle($dir)
	{
		if (!in_array($dir, $this->dir_allow)) {
			return Ethna::raiseError('Invalid dir.', E_USER_ERROR);
		}
		
		$sql = "SELECT COUNT(*) AS cnt"
		     . " FROM m_asset_bundle"
		     . " WHERE dir LIKE '$dir%'";
		
		return $this->db_r->GetOne($sql);
	}
	
	/**
	 * m_asset_bundleデータをセットする
	 * 
	 * @param int $id 管理ID
	 * @param array $columns セットする情報の連想配列
	 * @return bool 成否
	 */
	function setMasterAssetBundle($id, $columns)
	{
		if (!preg_match('/^[0-9]+$/', $id)) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
		
		$where = "id = '$id'";
		$ret = $this->db->db->AutoExecute('m_asset_bundle', $columns, 'UPDATE', $where);
		if (!$ret) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		if ($this->db->db->affected_rows() > 1) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
		
		return true;
	}
	
	/**
	 * m_asset_bundleデータを生成する
	 * 
	 * @param array $columns セットする情報の連想配列
	 * @return bool 成否
	 */
	function createMasterAssetBundle($columns)
	{
		// m_asset_bundle.idがAUTO_INCREMENTでなくなった事への対応(2013/7/9)
		if (!isset($columns['id'])) {
			$id = $this->db->GetOne("SELECT MAX(id) + 1 FROM m_asset_bundle");
			$columns['id'] = $id;
		}
		
		if (!isset($columns['created_date'])) {
			$columns['created_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		
		$ret = $this->db->db->AutoExecute('m_asset_bundle', $columns, 'INSERT');
		if (!$ret) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		if ($this->db->db->affected_rows() != 1) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
		
		return true;
	}

	/**
	 * m_asset_bundleデータを更新する
	 * 
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateMasterAssetBundle($columns)
	{
		if (!is_numeric($columns['id'])) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
		
		$where = "id = " . $columns['id'];
		unset($columns['id']);
		
		return $this->db->db->AutoExecute('m_asset_bundle', $columns, 'UPDATE', $where);
	}

	/**
	 * m_asset_bundleデータを削除する
	 * 
	 * @param int $id 管理ID
	 * @return true|Ethna_Error 成否
	 */
	function deleteMasterAssetBundle($id)
	{
		$param = array($id);
		$sql = "DELETE FROM m_asset_bundle WHERE id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		$affected_rows = $this->db->db->affected_rows();
		if ($this->db->db->affected_rows() != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		return true;
	}
	
	/**
	 * モンスター画像のパスを取得
	 * 
	 * 管理画面でのみ使用するモンスターアイコンとモンスター画像のPNGファイルについて、
	 * サーバのファイルシステム上のパスを取得する。
	 * @param string $type 種別("image" or "icon")
	 * @param int $monster_id モンスターID
	 * @return string パス
	 */
	function getMonsterImagePath($type, $monster_id)
	{
		$path = BASE . '/data/resource/image/monster/' . $monster_id . '/monster_' . $type . '_' . $monster_id . '.png';
		
		return $path;
	}
	
	/**
	 * ゲーム背景のIDを取得
	 * 
	 * ゲーム背景の管理画面に"ID"として表示する為の文字列を取得する。
	 * このIDは、"管理ID"(m_asset_bundleテーブルのidカラム)とは別のもの。
	 * @param string $file_name ファイル名(m_asset_bundelテーブルのfile_nameカラムと同じ内容)
	 * @return string ゲーム背景のID
	 */
	function getBgmodelId($file_name)
	{
		$pos = strpos($file_name, '_');
		if ($pos === false) {
			return null;
		}
		
		return substr($file_name, $pos + 1);
	}
	
	/**
	 * クエストマップのIDを取得
	 * 
	 * クエストマップの管理画面に"ID"として表示する為の文字列を取得する。
	 * このIDは、"管理ID"(m_asset_bundleテーブルのidカラム)とは別のもの。
	 * @param string $file_name ファイル名(m_asset_bundelテーブルのfile_nameカラムと同じ内容)
	 * @return string クエストマップのID
	 */
	function getMapId($file_name)
	{
		return substr($file_name, strlen('QuestMap'));
	}
	
	/**
	 * ゲーム背景のディレクトリを取得
	 * 
	 * ファイル名からディレクトリ(m_assetbundleテーブルのdirカラムに登録する為のディレクトリ名)を取得する。
	 * @param string $file_name ファイル名(m_asset_bundelテーブルのfile_nameカラムと同じ内容)
	 * @return string ディレクトリ
	 */
	function getBgmodelDir($file_name)
	{
		$id = $this->getBgmodelId($file_name);
		if (!$id) {
			return null;
		}
		
		return 'bgmodel/' . $id;
	}
	
	/**
	 * クエストマップのディレクトリを取得
	 * 
	 * ファイル名からディレクトリ(m_assetbundleテーブルのdirカラムに登録する為のディレクトリ名)を取得する。
	 * @param string $file_name ファイル名(m_asset_bundelテーブルのfile_nameカラムと同じ内容)
	 * @return string ディレクトリ
	 */
	function getMapDir($file_name)
	{
		$id = $this->getMapId($file_name);
		if (!$id) {
			return null;
		}
		
		return 'map/' . $id;
	}
	function getWorldmapDir($file_name)
	{
		$id = $this->getWorldmapId($file_name);
		if (!$id) {
			return null;
		}
		
		return 'worldmap/' . $id;
	}
	
	/**
	 * サウンドのディレクトリを取得
	 * 
	 * ファイル名からディレクトリ(m_assetbundleテーブルのdirカラムに登録する為のディレクトリ名)を取得する。
	 * @param string $file_name ファイル名(m_asset_bundelテーブルのfile_nameカラムと同じ内容)
	 * @return string ディレクトリ
	 */
	function getSoundDir($file_name)
	{
		return 'sound/' . $file_name;
	}

	/**
	 * モンスターマスタIDを取得
	 * 
	 * ファイル名からモンスターマスタIDを取得する。
	 * @param string $file_name ファイル名(m_asset_bundelテーブルのfile_nameカラムと同じ内容)
	 * @return string モンスターマスタID
	 */
	function getMonsterId($file_name)
	{
		return substr($file_name, strlen('monster_atlas_'));
	}
	
	/**
	 * モンスターのディレクトリを取得
	 * 
	 * ファイル名からディレクトリ(m_assetbundleテーブルのdirカラムに登録する為のディレクトリ名)を取得する。
	 * @param string $file_name ファイル名(m_asset_bundelテーブルのfile_nameカラムと同じ内容)
	 * @return string ディレクトリ
	 */
	function getMonsterDir($file_name)
	{
		$monster_id = $this->getMonsterId($file_name);
		if (!$monster_id) {
			return null;
		}
		
		return 'monster/' . $monster_id;
	}

	/**
	 * バージョン関連データの一覧を取得
	 * 
	 * ・m_res_ver（リソースバージョンマスタ）
	 * ・log_res_ver_deletion（リソースバージョン削除ログ）
	 * の情報をまとめて参照する。
	 * @param int $offset
	 * @param int $limit
	 * @param bool $log 取得対象をログに限定するか
	 * @return array
	 */
	function getVersionList($offset = 0, $limit = 10, $log = false)
	{
		if ($log) {
			$where = "WHERE date_start < NOW()";
		} else {
			$where = "";
		}
		
		$res_ver_keys = implode(',', self::$RES_VER_KEYS);
		
		$param = array($offset, $limit);
		$sql = <<<EOD
SELECT *
FROM ((
  SELECT app_ver, {$res_ver_keys}, clear, date_start, NULL AS date_deletion
  FROM m_res_ver
  $where
) UNION (
  SELECT app_ver, {$res_ver_keys}, clear, date_start, date_deletion
  FROM log_res_ver_deletion
)) AS t1
ORDER BY date_start DESC, date_deletion IS NULL DESC
LIMIT ?, ?
EOD;

		return $this->db_r->GetAll($sql, $param);
	}
	
	/**
	 * 最新のバージョン関連データを取得する
	 * 
	 * @return array m_res_verデータ（m_res_verのカラム名がキー）
	 */
	function getLatestVersion()
	{
		$sql = "SELECT * FROM m_res_ver ORDER BY date_start DESC LIMIT 1";
			
		return $this->db_r->GetRow($sql);
	}
	
	/**
	 * バージョン関連データを取得する
	 * 
	 * @param string $date_start
	 * @return array
	 */
	function getVersion($date_start)
	{
		$param = array($date_start);
		$sql = "SELECT * FROM m_res_ver WHERE date_start = ?";
			
		return $this->db_r->GetRow($sql, $param);
	}
	
	/**
	 * バージョン関連データを更新する
	 * 
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateVersion($columns)
	{
		$where = "date_start = '" . $columns['date_start'] . "'";
		unset($columns['date_start']);
		
		return $this->db->db->AutoExecute('m_res_ver', $columns, 'UPDATE', $where);
	}
	
	/**
	 * バージョン関連データを新規作成する
	 * 
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function insertVersion($columns)
	{
		return $this->db->db->AutoExecute('m_res_ver', $columns, 'INSERT');
	}
	
	/**
	 * バージョン関連データを削除する
	 * @param string $date_start
	 * @return true|Ethna_Error 成否
	 */
	function deleteVersion($date_start)
	{
		$res_ver_keys = implode(',', self::$RES_VER_KEYS);
		
		// トランザクション開始
		$transaction = ($this->db->db->transCnt == 0); // トランザクション開始するか
		if ($transaction) $this->db->begin();

		// ログに保存
		$param = array($date_start);
		$sql = <<<EOD
INSERT INTO log_res_ver_deletion(app_ver, {$res_ver_keys}, clear, date_start, date_deletion)
SELECT app_ver, {$res_ver_keys}, clear, date_start, NOW()
FROM m_res_ver
WHERE date_start = ?
EOD;
		if (!$this->db->execute($sql, $param)) {
			if ($transaction) $this->db->rollback();
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		$affected_rows = $this->db->db->affected_rows();
		if ($this->db->db->affected_rows() != 1) {
			if ($transaction) $this->db->rollback();
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		// 削除実行
		$param = array($date_start);
		$sql = "DELETE FROM m_res_ver WHERE date_start = ?";
		if (!$this->db->execute($sql, $param)) {
			if ($transaction) $this->db->rollback();
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		$affected_rows = $this->db->db->affected_rows();
		if ($this->db->db->affected_rows() != 1) {
			if ($transaction) $this->db->rollback();
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		// トランザクション完了
		if ($transaction) $this->db->commit();
		return true;
	}
	
	public function getLatestAssetBundleList($search_param)
	{
		$sql = "
SELECT
  m1.id
  , m1.file_type
  , m1.dir
  , m1.file_name
  , m1.extension
  , m1.version
  , m1.date_start
  , m1.date_end
  , m1.active_flg
  , m1.date_created
  , m1.date_modified
FROM
    m_asset_bundle as m1
INNER JOIN  (SELECT
                file_name, MAX(version) AS version
             FROM
                m_asset_bundle
             GROUP BY
                file_name
             ) AS m2
ON m1.version = m2.version and
m1.file_name = m2.file_name
WHERE
  1 = 1
";
		$order = 'ORDER BY m1.id';

		return $this->_getList($sql, $search_param, $order, 'm1');
	}

	public function getLatestAssetBundleCount($search_param)
	{
		return $this->_getCount('m_asset_bundle', $search_param);
	}
	
	private function _getList($sql, $search_param, $order = null, $table_alias = null)
	{
		$db = $this->backend->getDB('m_r');

		$param = $this->_createCondition($search_param, $table_alias);

		$sql .= $param['where'];
		
		if (!is_null($order))
		{
			$sql .= $order;
		} 

		$data = $db->GetAll($sql, $param['param']);

		if (!$data)
		{
			return array();
		}
		else
		{
			return $data;
		}
	}

	private function _createCondition($search_param, $table_alias = null)
	{
		$where = '';
		$param = array();
	
		if (isset($search_param['file_name']) && !empty($search_param['file_name']))
		{
			if (is_null($table_alias))
			{
				$where .= ' AND file_name LIKE ?';
			}
			else
			{
				$where .= ' AND ' .$table_alias.'.file_name LIKE ?';
			}
			$param[] = '%' . $search_param['file_name'] . '%';
		}
	
		return array('where' => $where, 'param' => $param);
	}
	
	private function _getCount($table, $search_param)
	{
		$db = $this->backend->getDB('m_r');
	
		$sql = "
		SELECT
		COUNT(id) _count
		FROM
		$table
		WHERE
		1 = 1
		";
	
		$param = $this->_createCondition($search_param);
	
		$sql .= $param['where'];
		
//$this->backend->logger->log( LOG_ERR, "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" );
//$this->backend->logger->log( LOG_ERR, (int)$db->GetOne($sql, $param['param']);
		
	
		return (int)$db->GetOne($sql, $param['param']);
	}
	
	public function createLatestAssetBundleCsv($data)
	{
		$csv_title = array(
				'管理ID',
				'ファイルタイプ',
				'ディレクトリ',
				'ファイル名',
				'拡張子',
				'バージョン',
				'開始日時',
				'終了日時',
				'活性フラグ',
				'作成日時',
				'更新日時',
		);
	
		return parent::createCsv($csv_title, $data, 'latest_m_asset_bundle');
	}
}
?>