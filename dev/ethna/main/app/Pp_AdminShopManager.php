<?php
/**
 *	Pp_AdminShopManager.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once 'Pp_ShopManager.php';
require_once 'array_column.php';

/**
 *	Pp_AdminShopManager
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_AdminShopManager extends Pp_ShopManager
{
	/**
	 * ウェイトの固定小数点の位置
	 */
	const WEIGHT_DECIMAL_POINT_POSITION = 2;
	
	/**
	 * DB接続(pp-ini.phpの'dsn_logex_r'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_logex_r = null;
	
	/**
	 * ウェイトの小数点表記←→DB保存用整数変換の倍率
	 */
	protected $weight_scale_factor = null;
	
	/** 最後に新規作成されたガチャID */
	protected $last_insert_gacha_id = null;
	
	/**
	 * ガチャドローリスト取得用のSQLクエリ結果
	 * 
	 * @see function queryLogGachaDrawList
	 */
	protected $log_gacha_draw_list_result = null;

	/**
	 * ガチャドローリストの表示用ラベル情報
	 * 
	 * 表示やCSV出力時のカラムの順序が、この変数のキーの順になるので注意
	 */
	protected $log_gacha_draw_list_labels = array(
		'rarity'	   => 'レアリティ', 
		'monster_id'   => 'モンスターID', 
		'monster_name' => 'モンスター名',
		'user_id'	   => '取得ユーザーID', 
		'date_draw'    => '取得日付',
	);
	
	/**
	 * コンストラクタ
	 */
	function __construct ( &$backend )
	{
		parent::__construct( $backend );
		
		$this->weight_scale_factor = pow(10, self::WEIGHT_DECIMAL_POINT_POSITION);
	}

	/**
	 * プラットフォーム表示名（管理画面用）を取得する
	 * 
	 * @param int $platform プラットフォームID（self::PLATFORM_～の値）
	 * @return string プラットフォーム表示名（管理画面用）
	 */
	protected function getPlatformDisplayName($platform)
	{
		switch ($platform) {
			case self::PLATFORM_APPLE:
				return 'apple';
			
			case self::PLATFORM_GOOGLE:
				return 'google';
		}

		return 'any';
	}
	
	/**
	 * app_idからプラットフォーム表示名（管理画面用）を取得する
	 * 
	 * @param int $app_id ケイブ決済サーバapp_id
	 * @return string 'apple' or 'google'
	 */
	function getPlatformDisplayNameFromAppId($app_id)
	{
		$platform = $this->getPlatformIdFromAppId($app_id);
		
		return $this->getPlatformDisplayName($platform);
	}
	
	/**
	 * User-Agent種別からからプラットフォーム表示名（管理画面用）を取得する
	 * 
	 * @param int $ua User-Agent種別
	 * @return string 'apple' or 'google'
	 */
	function getPlatformDisplayNameFromUa($ua)
	{
		$platform = $this->getPlatformIdFromUa($ua);
		
		return $this->getPlatformDisplayName($platform);
	}
	
	/**
	 * 管理ページ用のガチャ一覧を取得する
	 * 
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getGachaListForAdmin($offset = 0, $limit = 100000, $end = false)
	{
		$param = array();
		$sql = "SELECT *"
			 . " FROM m_gacha_list";
		
		if ($end) {
			$sql .= " WHERE date_end <= NOW()"
				 .	" ORDER BY date_end DESC, gacha_id DESC"; // 最新の表示終了データを先頭に
		} else {
			$sql .= " WHERE date_end > NOW()"
				 .	" ORDER BY sort_list ASC, date_start DESC, gacha_id DESC";
		}

		$param[] = $offset;
		$param[] = $limit;
		$sql .= " LIMIT ?, ?";

		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * ガチャリストマスタを新規作成する
	 * 
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーは含めなくてよい）
	 * @return bool 成否
	 */
	function insertGachaList($columns)
	{
		if (!isset($columns['gacha_id'])) {
			$max = $this->db->GetOne("SELECT MAX(gacha_id) FROM m_gacha_list");
			if (!$max) $max = 0;

			$columns['gacha_id'] = $max + 1;
		}
			
		$this->last_insert_gacha_id = $columns['gacha_id'];
		
		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		
		return $this->db->db->AutoExecute('m_gacha_list', $columns, 'INSERT');
	}
	
	/** 最後に新規作成されたガチャIDを取得する */
	function getLastInsertGachaId()
	{
		return $this->last_insert_gacha_id;
	}
	
	/**
	 * ガチャリストマスタを更新する
	 * 
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateGachaList($columns)
	{
		if (!is_numeric($columns['gacha_id'])) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
		
		$where = 'gacha_id = ' . $columns['gacha_id'];
		unset($columns['gacha_id']);
		
		return $this->db->db->AutoExecute('m_gacha_list', $columns, 'UPDATE', $where);
	}
	
	/**
	 * ガチャID指定でガチャのカテゴリ一覧を取得する（管理画面用付加情報付き）
	 * 
	 * 基底クラスの function getGachaCatgoryList の戻り値に加えて、各行の連想配列に、"number_of_monsters", "percentage_of_monsters" のキー名で付加情報が付く。
	 * @return array ガチャのカテゴリ一覧（管理画面用付加情報付き）
	 */
	function getGachaCatgoryListExForAdmin($gacha_id)
	{
		$gacha_item_list = $this->getGachaItemListExForAdmin($gacha_id);
		$gacha_category_list = $this->getGachaCatgoryList($gacha_id);
		
		$assoc = array(); // $assoc[rarity] = number_of_monsters
		foreach ($gacha_item_list as $ikey => $ival) {
			$number_of_monsters = $ival['number_of_monsters'];
			$rarity = $ival['rarity'];
			
			if (!isset($assoc[$rarity])) {
				$assoc[$rarity] = 0;
			}
			
			$assoc[$rarity] += $number_of_monsters;
		}
		
		$total_number_of_monsters = array_sum($assoc);
		
		foreach($gacha_category_list as $ckey => $cval) {
			$gacha_category_list[$ckey]['weight_float'] = $this->convertWeightToWeightFloat($cval['weight']);
		
			$rarity = $cval['rarity'];
			if (isset($assoc[$rarity])) {
				$number_of_monsters = $assoc[$rarity];
				$percentage_of_monsters = 100 * $number_of_monsters / $total_number_of_monsters;

				$gacha_category_list[$ckey]['number_of_monsters'] = $number_of_monsters;
				$gacha_category_list[$ckey]['percentage_of_monsters'] = $percentage_of_monsters;
			}
		}

		return $gacha_category_list;
	}
	
	/**
	 * ガチャID指定でガチャのアイテムリスト一覧を取得する（管理画面用付加情報付き）
	 * 
	 * 基底クラスの function getGachaItemList の戻り値に加えて、各行の連想配列に、"number_of_monsters", "percentage_of_monsters" のキー名で付加情報が付く。
	 * @return array アイテムリスト一覧（管理画面用付加情報付き）
	 */
	function getGachaItemListExForAdmin($gacha_id)
	{
		$gacha_item_list = $this->getGachaItemList($gacha_id);
		$gacha_category_list = $this->getGachaCatgoryList($gacha_id);
		
		$total_number_of_monsters = 0;
		foreach ($gacha_item_list as $ikey => $ival) {
			$iweight = $ival['weight'];
			
			$cweight = null;
			foreach($gacha_category_list as $ckey => $cval) {
				if ($cval['rarity'] == $ival['rarity']) {
					$cweight = $cval['weight'];
					break;
				}
			}
			
//			$number_of_monsters = floor($iweight * $cweight / 10000);
			$number_of_monsters = $this->computeNumberOfMonstersPerGachaItem($iweight, $cweight);
			$total_number_of_monsters += $number_of_monsters;
			
			$gacha_item_list[$ikey]['number_of_monsters'] = $number_of_monsters;
		}
		
		foreach ($gacha_item_list as $ikey => $ival) {
			$gacha_item_list[$ikey]['percentage_of_monsters'] = 100 * $ival['number_of_monsters'] / $total_number_of_monsters;
			$gacha_item_list[$ikey]['weight_float'] = $this->convertWeightToWeightFloat($ival['weight']);
		}
		
		return $gacha_item_list;
	}
	
	/** ウェイトを小数点表記へ変換する */
	function convertWeightToWeightFloat($weight)
	{
		return $weight / $this->weight_scale_factor;
	}
	
	/** ウェイトを整数表記へ変換する */
	function convertWeightFloatToWeight($weight_float)
	{
		return intval($weight_float * $this->weight_scale_factor);
	}
	
	/**
	 * ガチャのカテゴリが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @return boolean 存在する/しない
	 */
	function isGachaCatgoryExists($gacha_id, $rarity)
	{
		$list = $this->getGachaCatgoryList($gacha_id);
		if (!is_array($list)) {
			return false;
		}
		
		$key = array_search($rarity, array_column($list, 'rarity'));
		
		return ($key !== false);
	}
	
	/**
	 * ガチャのアイテムが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @param int $monster_id モンスターID
	 * @param int $monster_lv モンスターLV
	 * @return boolean 存在する/しない
	 */
	function isGachaItemExists($gacha_id, $rarity, $monster_id, $monster_lv)
	{
		$row = $this->getGachaItem($gacha_id, $rarity, $monster_id, $monster_lv);
		$is_exists = (is_array($row) && (count($row) > 0));
		
		return $is_exists;
	}
	
	/**
	 * ガチャのアイテムを取得する
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @param int $monster_id モンスターID
	 * @param int $monster_lv モンスターLV
	 * @return array ガチャアイテム情報（m_gacha_itemlistテーブルのカラム名がキー）
	 */
	function getGachaItem($gacha_id, $rarity, $monster_id, $monster_lv)
	{
		$param = array($gacha_id, $rarity, $monster_id, $monster_lv);
		$sql = "SELECT * FROM m_gacha_itemlist"
			 . " WHERE gacha_id = ? AND rarity = ? AND monster_id = ? AND monster_lv = ?";

		return $this->db_r->GetRow($sql, $param);
	}
	
	/**
	 * ガチャカテゴリマスタを新規作成する
	 * 
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列）
	 * @return bool 成否
	 */
	function insertGachaCategory($columns)
	{
		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		
		return $this->db->db->AutoExecute('m_gacha_category', $columns, 'INSERT');
	}
	
	/**
	 * ガチャアイテムリストマスタを新規作成する
	 * 
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列）
	 * @return bool 成否
	 */
	function insertGachaItemList($columns)
	{
		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		
		return $this->db->db->AutoExecute('m_gacha_itemlist', $columns, 'INSERT');
	}
	
	/**
	 * ガチャカテゴリマスタを更新する
	 * 
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateGachaCategory($columns)
	{
		$clauses = array();
		foreach (array('gacha_id', 'rarity') as $key) {
			if (!is_numeric($columns[$key])) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
			
			$clauses[] = $key . " = " . $columns[$key];
			unset($columns[$key]);
		}
		
		$where = implode(' AND ', $clauses);
		
		return $this->db->db->AutoExecute('m_gacha_category', $columns, 'UPDATE', $where);
	}
	
	/**
	 * ガチャアイテムマスタを更新する
	 * 
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateGachaItemList($columns)
	{
		$clauses = array();
		foreach (array('gacha_id', 'rarity', 'monster_id', 'monster_lv') as $key) {
			if (!is_numeric($columns[$key])) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
			
			$clauses[] = $key . " = " . $columns[$key];
			unset($columns[$key]);
		}
		
		$where = implode(' AND ', $clauses);
		
		return $this->db->db->AutoExecute('m_gacha_itemlist', $columns, 'UPDATE', $where);
	}
	
	/**
	 * ガチャオーダー情報を更新する
	 * 
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateGachaOrderInfo($columns)
	{
		$clauses = array();
		foreach (array('gacha_id') as $key) {
			if (!is_numeric($columns[$key])) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
			
			$clauses[] = $key . " = " . $columns[$key];
			unset($columns[$key]);
		}
		
		$where = implode(' AND ', $clauses);
		
		return $this->db->db->AutoExecute('t_gacha_order_info', $columns, 'UPDATE', $where);
	}
	
	/**
	 * ガチャカテゴリマスタを削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @return true|Ethna_Error 成否
	 */
	function deleteGachaCategory($gacha_id, $rarity)
	{
		$param = array($gacha_id, $rarity);
		$sql = "DELETE FROM m_gacha_category WHERE gacha_id = ? AND rarity = ?";
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
	 * ガチャアイテムリストマスタを削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @param int $monster_id モンスターID
	 * @param int $monster_lv モンスターLV
	 * @return true|Ethna_Error 成否
	 */
	function deleteGachaItemList($gacha_id, $rarity, $monster_id, $monster_lv)
	{
		$param = array($gacha_id, $rarity, $monster_id, $monster_lv);
		$sql = "DELETE FROM m_gacha_itemlist WHERE gacha_id = ? AND rarity = ? AND monster_id = ? AND monster_lv = ?";
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
	 * ガチャドローリスト中のオーダーIDを取得する
	 * 
	 * @param int $gacha_id
	 * @return array オーダーIDの配列（DISTINCT済み）
	 */
/*
	function getGachaDrawOrderIdList($gacha_id)
	{
		$param = array($gacha_id);
		$sql = "SELECT DISTINCT(order_id) FROM t_gacha_draw_list WHERE gacha_id = ?";
		
		$list = $this->db_r->GetCol($sql, $param);
		if (is_array($list)) {
			sort($list, SORT_NUMERIC);
		}
		
		return $list;
	}
*/

	/**
	 * ガチャドローリストの件数を取得する
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $order_id オーダーID
	 * @return int 件数
	 */
/*
	function countGachaDrawList($gacha_id, $order_id)
	{
		$param = array($gacha_id, $order_id);
		$sql = "SELECT COUNT(*) FROM t_gacha_draw_list WHERE gacha_id = ? AND order_id = ?";
		
		return $this->db_r->GetOne($sql, $param);
	}
*/

	/**
	 * ガチャドローリストの件数をドロー日時で絞り込んで取得する
	 * 
	 * @param int $gacha_id ガチャID
	 * @param string $date_draw_start 対象とするドロー日時の開始日時（端点含む。Y-m-d H:i:s形式）
	 * @param string $date_draw_end   対象とするドロー日時の終了日時（端点含まない。Y-m-d H:i:s形式）
	 * @return int 件数
	 */
/*
	function countGachaDrawListDate($gacha_id, $date_draw_start, $date_draw_end = null)
	{
		if ($date_draw_end === null) {
			$date_draw_end = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		
		$param = array($gacha_id, $date_draw_start, $date_draw_end);
		$sql = "SELECT COUNT(*)"
			 . " FROM t_gacha_draw_list"
			 . " WHERE gacha_id = ?"
			 . " AND date_draw >= ?"
			 . " AND date_draw < ?";

		return $this->db_r->GetOne($sql, $param);
	}
*/
	
	/**
	 * ガチャドローリストログの件数を取得する
	 * 
	 * @param int $gacha_id ガチャID
	 * @param string $date_draw_start 対象とするドロー日時の開始日時（端点含む。Y-m-d H:i:s形式）
	 * @param string $date_draw_end   対象とするドロー日時の終了日時（端点含まない。Y-m-d H:i:s形式）
	 * @return int 件数
	 */
	function countLogGachaDrawList($gacha_id, $date_draw_start = null, $date_draw_end = null)
	{
		if (!$this->db_logex_r) {
			$this->db_logex_r =& $this->backend->getDB('logex_r');
		}
		
		$conditions = $this->_getLogGachaDrawListConditions(
				compact('gacha_id', 'date_draw_start', 'date_draw_end'));
		
		$param = $conditions['param'];
		$sql = "SELECT COUNT(*) FROM log_gacha_draw_list"
			 . $conditions['where'];
		
		return $this->db_logex_r->GetOne($sql, $param);
	}
	
	/**
	 * 管理ページ用のガチャドローリスト一覧を取得する
	 * 
	 * 戻り値の書式は、
	 * array(
	 *	 0 => array(
	 *	   キー名 => 値,
	 *	 ),
	 *	 1 => ...以下同様
	 * )
	 * となる。
	 * キーは、t_gacha_draw_listのカラムと同名のものに加えて、
	 * "monster_idx": モンスターID単位でのインデックス値（モンスターIDごとに1始まりの連番。別のモンスターIDだと再び1から採番）
	 * "number_of_monsters": モンスターID単位での総数（管理画面上の表記は“枚数”となる場合もある）
	 * "percentage_of_monsters": モンスターID単位での総数が、モンスターID問わずの総数の何パーセントか
	 * も付く。
	 * @param int $gacha_id ガチャID
	 * @param int $order_id オーダーID
	 * @param int $offset オフセット値
	 * @param int $limit  件数
	 * @return array t_gacha_draw_listデータ一覧
	 */
/*
	function getGachaDrawListForAdmin($gacha_id, $order_id, $offset, $limit)
	{
		$param = array($gacha_id, $order_id);
		$sql = "SELECT * FROM t_gacha_draw_list"
			 . " WHERE gacha_id = ? AND order_id = ?"
			 . " ORDER BY list_id";
		$result =& $this->db_r->query($sql, $param);
		if (Ethna::isError($result)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		$number_of_monsters_assoc = array(); // $number_of_monsters_assoc[モンスターID] = 総枚数
		
		$list = array();
		$offset_plus_limit = $offset + $limit;
		$cnt = 0;
		while ($row = $result->FetchRow()) {
			$cnt++;
			
			$monster_id = $row['monster_id'];
			
			if (!isset($number_of_monsters_assoc[$monster_id])) {
				$number_of_monsters_assoc[$monster_id] = 0;
			}

			$number_of_monsters_assoc[$monster_id] += 1;

			if ($cnt <= $offset) {
				continue;
			}
			
			if ($cnt > $offset_plus_limit) {
				continue;
			}
			
			$row['monster_idx'] = $number_of_monsters_assoc[$monster_id];

			$list[] = $row;
		}
		
		$number_of_monsters_total = array_sum($number_of_monsters_assoc);
		
		foreach ($list as $i => $row) {
			$number_of_monsters_tmp = $number_of_monsters_assoc[$row['monster_id']];
			$list[$i]['number_of_monsters'] = $number_of_monsters_tmp;
			$list[$i]['percentage_of_monsters'] = 100 * $number_of_monsters_tmp / $number_of_monsters_total;
		}

		return $list;
	}
*/
	
	/**
	 * 管理ページ用のガチャドローリストログ一覧を取得する
	 * 
	 * 戻り値の書式は、
	 * array(
	 *	 0 => array(
	 *	   キー名 => 値,
	 *	 ),
	 *	 1 => ...以下同様
	 * )
	 * となる。
	 * キーは、log_gacha_draw_listのカラムと同名のもの。
	 * @param int $gacha_id ガチャID
	 * @param string $date_draw_start 対象とするドロー日時の開始日時（端点含む。Y-m-d H:i:s形式）
	 * @param string $date_draw_end   対象とするドロー日時の終了日時（端点含まない。Y-m-d H:i:s形式）
	 * @param int $offset オフセット値
	 * @param int $limit  件数
	 * @return array log_gacha_draw_listデータ一覧
	 */
	function queryLogGachaDrawList($gacha_id, $date_draw_start	= null, $date_draw_end = null, $offset = null, $limit = null)
	{
		if (!$this->db_logex_r) {
			$this->db_logex_r =& $this->backend->getDB('logex_r');
		}
		
		$admin_m =& $this->backend->getManager('Admin');
		
		$conditions = $this->_getLogGachaDrawListConditions(
				compact('gacha_id', 'date_draw_start', 'date_draw_end'));
		
		$param = $conditions['param'];
		$sql = "SELECT * FROM log_gacha_draw_list"
			 . $conditions['where'] . $conditions['order'];
		
		if ($limit) {
			$param[] = $offset;
			$param[] = $limit;
			$sql .= " LIMIT ?,?";
		}
		
		$adodb_countrecs_old = $admin_m->setAdodbCountrecs(false);
		$result =& $this->db_logex_r->query($sql, $param);
		$admin_m->setAdodbCountrecs($adodb_countrecs_old);
		if (Ethna::isError($result)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db_logex_r->db->ErrorNo(), $this->db_logex_r->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		$this->log_gacha_draw_list_result = $result;
	}
	
	/**
	 * ガチャドローリスト取得用のSQLクエリから1行フェッチする
	 * 
	 * 事前にqueryLogGachaDrawList関数を実行しておく必要がある
	 * @return array fetchRow結果
	 */
	function fetchLogGachaDrawList()
	{
		return $this->log_gacha_draw_list_result->FetchRow();
	}
	
	/**
	 * ガチャドローリストの1行分の情報を表示用フォーマットに変換する
	 * 
	 * Smartyでの表示およびCSVでの出力のいずれの場合でも、この関数を使用すること。
	 * @param array $row ガチャドローリストの1行分の情報（fetchRow結果）
	 * @return array 変換後の配列（連想配列ではないので注意）
	 */
	function convertLogGachaDrawListRow($row)
	{
		static $monsters = null;
		
		if ($monsters === null) {
			$monster_m = $this->backend->getManager('Monster');
			$monsters = $monster_m->getMasterMonsterAssoc();
		}

		$arr = array();
		foreach (array_keys($this->log_gacha_draw_list_labels) as $key) {
			$value = null;
			if (array_key_exists($key, $row)) {
				$value = $row[$key];
			} else if ($key == 'monster_name') {
				$monster_id = $row['monster_id'];
				if (isset($monsters[$monster_id])) {
					$value = $monsters[$monster_id]['name_ja'];
				}
			}
			
			$arr[] = $value;
		}
		
		return $arr;
	}
	
	/**
	 * ガチャドローリストの表示用ラベル情報を取得する
	 * 
	 * Smartyでの表示およびCSVでの出力のいずれの場合でも、この関数を使用すること。
	 * @return array ラベル情報
	 */
	function getLogGachaDrawListLabels()
	{
		return $this->log_gacha_draw_list_labels;
	}
	
	/**
	 * ガチャドローリストを取得する検索条件を取得する
	 *
	 * @parami array $search_params 検索条件をあらわす連想配列。キーは"gacha_id"(必須), "date_draw_start"(省略可), , "date_draw_end"(省略可) 
	 * @return mixed SQL用に加工した検索条件。キーは"param", "where", "order"
	 */
	protected function _getLogGachaDrawListConditions($search_params)
	{
		$param = array($search_params['gacha_id']);
		$where = " WHERE gacha_id = ?";
		
		if ($search_params['date_draw_start']) {
			$param[] = $search_params['date_draw_start'];
			$where .= " AND date_draw >= ?";
		}
		
		if ($search_params['date_draw_end']) {
			$param[] = $search_params['date_draw_end'];
			$where .= " AND date_draw < ?";
		}
		
		$order = " ORDER BY date_draw DESC, id DESC";
		
		return array('param' => $param, 'where' => $where, 'order' => $order);
	}
	
	/**
	 * ガチャカテゴリマスタを複製する
	 * 
	 * @param int $src_gacha_id 複製元ガチャID
	 * @param int $dest_gacha_id 複製先ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function copyGachaCategory($src_gacha_id, $dest_gacha_id)
	{
		$param = array($dest_gacha_id, $src_gacha_id);
		$sql = "INSERT INTO m_gacha_category(gacha_id, rarity, weight, date_created)"
			 . " SELECT ?, rarity, weight, NOW()"
			 . " FROM m_gacha_category"
			 . " WHERE gacha_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return true;
	}
	
	/**
	 * ガチャアイテムリストマスタを複製する
	 * 
	 * @param int $src_gacha_id 複製元ガチャID
	 * @param int $dest_gacha_id 複製先ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function copyGachaItemlist($src_gacha_id, $dest_gacha_id)
	{
		$param = array($dest_gacha_id, $src_gacha_id);
		$sql = "INSERT INTO m_gacha_itemlist(gacha_id, rarity, monster_id, monster_lv, weight, date_created)"
			 . " SELECT ?, rarity, monster_id, monster_lv, weight, NOW()"
			 . " FROM m_gacha_itemlist"
			 . " WHERE gacha_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return true;
	}

	/**
	 * ガチャリスト管理情報を削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function deleteGachaListInfo( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "DELETE FROM t_gacha_list_info WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 削除に失敗
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		$affected_rows = $this->db->db->affected_rows();
		if( $this->db->db->affected_rows() > 1 )
		{	// ガチャIDはPrimaryKeyなので複数削除されるのはおかしい
			return Ethna::raiseError( "rows[%d]", E_USER_ERROR, $affected_rows );
		}
		return true;
	}

	/**
	 * 一時抽選情報を削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function deleteGachaCategoryInfo( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "DELETE FROM t_gacha_category_info WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 削除に失敗
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * 二時抽選情報を削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function deleteGachaItemInfo( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "DELETE FROM t_gacha_item_info WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 削除に失敗
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * おまけガチャリスト管理情報を削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function deleteGachaExtraListInfo( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "DELETE FROM t_gacha_extra_list_info WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 削除に失敗
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		$affected_rows = $this->db->db->affected_rows();
		if( $this->db->db->affected_rows() > 1 )
		{	// ガチャIDはPrimaryKeyなので複数削除されるのはおかしい
			return Ethna::raiseError( "rows[%d]", E_USER_ERROR, $affected_rows );
		}
		return true;
	}

	/**
	 * おまけガチャの一時抽選情報を削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function deleteGachaExtraCategoryInfo( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "DELETE FROM t_gacha_extra_category_info WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 削除に失敗
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * おまけガチャの二時抽選情報を削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function deleteGachaExtraItemInfo( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "DELETE FROM t_gacha_extra_item_info WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 削除に失敗
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * ガチャリスト管理情報に指定のガチャIDのレコードが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean 存在する:true, しない:false
	 */
	function isGachaListInfoExists( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "SELECT gacha_id FROM t_gacha_list_info WHERE gacha_id = ? LIMIT 1";
		$row = $this->db_r->GetRow( $sql, $param );
		return ( is_array( $row ) && count( $row ) > 0 ) ? true : false;
	}

	/**
	 * 一次抽選情報に指定のガチャIDのレコードが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean 存在する:true, しない:false
	 */
	function isGachaCategoryInfoExists( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "SELECT gacha_id FROM t_gacha_item_info WHERE gacha_id = ? LIMIT 1";
		$row = $this->db_r->GetRow( $sql, $param );
		return ( is_array( $row ) && count( $row ) > 0 ) ? true : false;
	}

	/**
	 * 二次抽選情報に指定のガチャIDのレコードが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean 存在する:true, しない:false
	 */
	function isGachaItemInfoExists( $gacha_id )
	{
		$row = $this->getGachaListInfo( $gacha_id );
		return ( is_array( $row ) && count( $row ) > 0 ) ? true : false;
	}

	/**
	 * おまけガチャリスト管理情報に指定のガチャIDのレコードが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean 存在する:true, しない:false
	 */
	function isGachaExtraListInfoExists( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "SELECT gacha_id FROM t_gacha_extra_list_info WHERE gacha_id = ? LIMIT 1";
		$row = $this->db_r->GetRow( $sql, $param );
		return ( is_array( $row ) && count( $row ) > 0 ) ? true : false;
	}
	/**
	 * おまけガチャの一次抽選情報に指定のガチャIDのレコードが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean 存在する:true, しない:false
	 */
	function isGachaExtraCategoryInfoExists( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "SELECT gacha_id FROM t_gacha_extra_item_info WHERE gacha_id = ? LIMIT 1";
		$row = $this->db_r->GetRow( $sql, $param );
		return ( is_array( $row ) && count( $row ) > 0 ) ? true : false;
	}

	/**
	 * おまけガチャの二次抽選情報に指定のガチャIDのレコードが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean 存在する:true, しない:false
	 */
	function isGachaExtraItemInfoExists( $gacha_id )
	{
		$row = $this->getGachaExtraListInfo( $gacha_id );
		return ( is_array( $row ) && count( $row ) > 0 ) ? true : false;
	}

	/**
	 * ガチャカテゴリマスタから一次抽選情報を作成する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function createGachaCategoryInfo( $gacha_id )
	{
		$param = array( $gacha_id, $gacha_id );
		$sql = "INSERT INTO t_gacha_category_info( gacha_id, rarity, weight, date_created )"
			 . " SELECT ?, rarity, weight, NOW()"
			 . " FROM m_gacha_category"
			 . " WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * ガチャアイテムリストマスタから二次抽選情報を作成する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function createGachaItemInfo( $gacha_id )
	{
		$param = array( $gacha_id, $gacha_id );
		$sql = "INSERT INTO t_gacha_item_info( gacha_id, rarity, monster_id, monster_lv, weight, date_created )"
			 . " SELECT ?, rarity, monster_id, monster_lv, weight, NOW()"
			 . " FROM m_gacha_itemlist"
			 . " WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * 一次＆二次抽選情報を元にガチャリスト管理情報を作成する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function createGachaListInfo( $gacha_id )
	{
		$param = array( $gacha_id );

		// 一次抽選テーブルからカテゴリ最大値を取得
		$sql = "SELECT SUM(weight) as category_max FROM t_gacha_category_info WHERE gacha_id = ?";
		$row = $this->db->GetRow( $sql, $param );	// INSERT直後のレコードに対するクエリなのでマスタDBで実行
		if(( is_array( $row ) === false )||( count( $row ) === 0 ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		$category_max = ( is_null( $row['category_max'] ) === true ) ? 0 : $row['category_max'];

		// 二次抽選テーブルからアイテム最大値を取得
		$sql = "SELECT SUM(weight) as item_max FROM t_gacha_item_info WHERE gacha_id = ?";
		$row = $this->db->GetRow( $sql, $param );	// INSERT直後のレコードに対するクエリなのでマスタDBで実行
		if(( is_array( $row ) === false )||( count( $row ) === 0 ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		$item_max = ( is_null( $row['item_max'] ) === true ) ? 0 : $row['item_max'];

		// ガチャリスト管理情報を作成
		$param = array( $gacha_id, $category_max, $item_max );
		$sql = "INSERT INTO t_gacha_list_info( gacha_id, category_max, item_max, date_created )"
			 . "VALUES ( ?, ?, ?, NOW())";
		if( !$this->db->execute( $sql, $param ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * おまけガチャカテゴリマスタから一次抽選情報を作成する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function createGachaExtraCategoryInfo( $gacha_id )
	{
		$param = array( $gacha_id, $gacha_id );
		$sql = "INSERT INTO t_gacha_extra_category_info( gacha_id, rarity, weight, date_created )"
			 . " SELECT ?, rarity, weight, NOW()"
			 . " FROM m_gacha_extra_category"
			 . " WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * おまけガチャアイテムリストマスタから二次抽選情報を作成する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function createGachaExtraItemInfo( $gacha_id )
	{
		$param = array( $gacha_id, $gacha_id );
		$sql = "INSERT INTO t_gacha_extra_item_info( gacha_id, rarity, monster_id, monster_lv, weight, date_created )"
			 . " SELECT ?, rarity, monster_id, monster_lv, weight, NOW()"
			 . " FROM m_gacha_extra_itemlist"
			 . " WHERE gacha_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * 一次＆二次抽選情報を元におまけガチャリスト管理情報を作成する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return boolean|object 成功時:true, 失敗時:Ethna_Error
	 */
	function createGachaExtraListInfo( $gacha_id )
	{
		$param = array( $gacha_id );

		// 一次抽選テーブルからカテゴリ最大値を取得
		$sql = "SELECT SUM(weight) as category_max FROM t_gacha_extra_category_info WHERE gacha_id = ?";
		$row = $this->db->GetRow( $sql, $param );	// INSERT直後のレコードに対するクエリなのでマスタDBで実行
		if(( is_array( $row ) === false )||( count( $row ) === 0 ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		$category_max = ( is_null( $row['category_max'] ) === true ) ? 0 : $row['category_max'];

		// 二次抽選テーブルからアイテム最大値を取得
		$sql = "SELECT SUM(weight) as item_max FROM t_gacha_extra_item_info WHERE gacha_id = ?";
		$row = $this->db->GetRow( $sql, $param );	// INSERT直後のレコードに対するクエリなのでマスタDBで実行
		if(( is_array( $row ) === false )||( count( $row ) === 0 ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		$item_max = ( is_null( $row['item_max'] ) === true ) ? 0 : $row['item_max'];

		// ガチャリスト管理情報を作成
		$param = array( $gacha_id, $category_max, $item_max );
		$sql = "INSERT INTO t_gacha_extra_list_info( gacha_id, category_max, item_max, date_created )"
			 . "VALUES ( ?, ?, ?, NOW())";
		if( !$this->db->execute( $sql, $param ))
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * ガチャリスト管理情報を取得する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return array ガチャリスト管理情報（t_gacha_list_infoテーブルのカラム名がキー）
	 */
	function getGachaListInfo( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "SELECT * FROM t_gacha_list_info WHERE gacha_id = ?";
		$row = $this->db_r->GetRow( $sql, $param );
		return $row;
	}

	/**
	 * おまけガチャリスト管理情報を取得する
	 * 
	 * @param int $gacha_id ガチャID
	 * @return array ガチャリスト管理情報（t_gacha_list_infoテーブルのカラム名がキー）
	 */
	function getGachaExtraListInfo( $gacha_id )
	{
		$param = array( $gacha_id );
		$sql = "SELECT * FROM t_gacha_extra_list_info WHERE gacha_id = ?";
		$row = $this->db_r->GetRow( $sql, $param );
		return $row;
	}

	/**
	 * おまけガチャカテゴリマスタを新規作成する
	 * 
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列）
	 * @return bool 成否
	 */
	function insertGachaExtraCategory($columns)
	{
		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		
		return $this->db->db->AutoExecute('m_gacha_extra_category', $columns, 'INSERT');
	}

	/**
	 * おまけガチャカテゴリマスタを削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @return true|Ethna_Error 成否
	 */
	function deleteGachaExtraCategory($gacha_id, $rarity)
	{
		$param = array($gacha_id, $rarity);
		$sql = "DELETE FROM m_gacha_extra_category WHERE gacha_id = ? AND rarity = ?";
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
	 * おまけガチャカテゴリマスタを更新する
	 * 
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateGachaExtraCategory($columns)
	{
		$clauses = array();
		foreach (array('gacha_id', 'rarity') as $key) {
			if (!is_numeric($columns[$key])) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
			
			$clauses[] = $key . " = " . $columns[$key];
			unset($columns[$key]);
		}
		
		$where = implode(' AND ', $clauses);
		
		return $this->db->db->AutoExecute('m_gacha_extra_category', $columns, 'UPDATE', $where);
	}

	/**
	 * おまけガチャアイテムリストマスタを新規作成する
	 * 
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列）
	 * @return bool 成否
	 */
	function insertGachaExtraItemList($columns)
	{
		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		
		return $this->db->db->AutoExecute('m_gacha_extra_itemlist', $columns, 'INSERT');
	}

	/**
	 * おまけガチャアイテムリストマスタを削除する
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @param int $monster_id モンスターID
	 * @param int $monster_lv モンスターLV
	 * @return true|Ethna_Error 成否
	 */
	function deleteGachaExtraItemList($gacha_id, $rarity, $monster_id, $monster_lv)
	{
		$param = array($gacha_id, $rarity, $monster_id, $monster_lv);
		$sql = "DELETE FROM m_gacha_extra_itemlist WHERE gacha_id = ? AND rarity = ? AND monster_id = ? AND monster_lv = ?";
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
	 * おまけガチャアイテムマスタを更新する
	 * 
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateGachaExtraItemList($columns)
	{
		$clauses = array();
		foreach (array('gacha_id', 'rarity', 'monster_id', 'monster_lv') as $key) {
			if (!is_numeric($columns[$key])) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
			
			$clauses[] = $key . " = " . $columns[$key];
			unset($columns[$key]);
		}
		
		$where = implode(' AND ', $clauses);
		
		return $this->db->db->AutoExecute('m_gacha_extra_itemlist', $columns, 'UPDATE', $where);
	}


	/**
	 * ガチャID指定でガチャのカテゴリ一覧を取得する（管理画面用付加情報付き）
	 * 
	 * 基底クラスの function getGachaCatgoryList の戻り値に加えて、各行の連想配列に、"number_of_monsters", "percentage_of_monsters" のキー名で付加情報が付く。
	 * @return array ガチャのカテゴリ一覧（管理画面用付加情報付き）
	 */
	function getGachaExtraCatgoryListExForAdmin($gacha_id)
	{
		$gacha_extra_item_list = $this->getGachaExtraItemListExForAdmin($gacha_id);
		$gacha_extra_category_list = $this->getGachaExtraCatgoryList($gacha_id);
		
		$assoc = array(); // $assoc[rarity] = number_of_monsters
		foreach ($gacha_extra_item_list as $ikey => $ival) {
			$number_of_monsters = $ival['number_of_monsters'];
			$rarity = $ival['rarity'];
			
			if (!isset($assoc[$rarity])) {
				$assoc[$rarity] = 0;
			}
			
			$assoc[$rarity] += $number_of_monsters;
		}
		
		$total_number_of_monsters = array_sum($assoc);
		
		foreach($gacha_extra_category_list as $ckey => $cval) {
			$gacha_extra_category_list[$ckey]['weight_float'] = $this->convertWeightToWeightFloat($cval['weight']);
		
			$rarity = $cval['rarity'];
			if (isset($assoc[$rarity])) {
				$number_of_monsters = $assoc[$rarity];
				$percentage_of_monsters = 100 * $number_of_monsters / $total_number_of_monsters;

				$gacha_extra_category_list[$ckey]['number_of_monsters'] = $number_of_monsters;
				$gacha_extra_category_list[$ckey]['percentage_of_monsters'] = $percentage_of_monsters;
			}
		}

		return $gacha_extra_category_list;
	}
	
	/**
	 * ガチャID指定でガチャのアイテムリスト一覧を取得する（管理画面用付加情報付き）
	 * 
	 * 基底クラスの function getGachaExtraItemList の戻り値に加えて、各行の連想配列に、"number_of_monsters", "percentage_of_monsters" のキー名で付加情報が付く。
	 * @return array アイテムリスト一覧（管理画面用付加情報付き）
	 */
	function getGachaExtraItemListExForAdmin($gacha_id)
	{
		$gacha_extra_item_list = $this->getGachaExtraItemList($gacha_id);
		$gacha_extra_category_list = $this->getGachaExtraCatgoryList($gacha_id);
		
		$total_number_of_monsters = 0;
		foreach ($gacha_extra_item_list as $ikey => $ival) {
			$iweight = $ival['weight'];
			
			$cweight = null;
			foreach($gacha_extra_category_list as $ckey => $cval) {
				if ($cval['rarity'] == $ival['rarity']) {
					$cweight = $cval['weight'];
					break;
				}
			}
			
//			$number_of_monsters = floor($iweight * $cweight / 10000);
			$number_of_monsters = $this->computeNumberOfMonstersPerGachaItem($iweight, $cweight);
			$total_number_of_monsters += $number_of_monsters;
			
			$gacha_extra_item_list[$ikey]['number_of_monsters'] = $number_of_monsters;
		}
		
		foreach ($gacha_extra_item_list as $ikey => $ival) {
			$gacha_extra_item_list[$ikey]['percentage_of_monsters'] = 100 * $ival['number_of_monsters'] / $total_number_of_monsters;
			$gacha_extra_item_list[$ikey]['weight_float'] = $this->convertWeightToWeightFloat($ival['weight']);
		}
		
		return $gacha_extra_item_list;
	}

	/**
	 * おまけガチャのアイテムを取得する
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @param int $monster_id モンスターID
	 * @param int $monster_lv モンスターLV
	 * @return array ガチャアイテム情報（m_gacha_extra_itemlistテーブルのカラム名がキー）
	 */
	function getGachaExtraItem($gacha_id, $rarity, $monster_id, $monster_lv)
	{
		$param = array($gacha_id, $rarity, $monster_id, $monster_lv);
		$sql = "SELECT * FROM m_gacha_extra_itemlist"
			 . " WHERE gacha_id = ? AND rarity = ? AND monster_id = ? AND monster_lv = ?";

		return $this->db_r->GetRow($sql, $param);
	}


	/**
	 * おまけガチャのアイテムが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @param int $monster_id モンスターID
	 * @param int $monster_lv モンスターLV
	 * @return boolean 存在する/しない
	 */
	function isGachaExtraItemExists($gacha_id, $rarity, $monster_id, $monster_lv)
	{
		$row = $this->getGachaExtraItem($gacha_id, $rarity, $monster_id, $monster_lv);
		$is_exists = (is_array($row) && (count($row) > 0));
		
		return $is_exists;
	}

	/**
	 * おまけガチャのカテゴリが存在するか
	 * 
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レアリティ
	 * @return boolean 存在する/しない
	 */
	function isGachaExtraCatgoryExists($gacha_id, $rarity)
	{
		$list = $this->getGachaExtraCatgoryList($gacha_id);
		if (!is_array($list)) {
			return false;
		}
		
		$key = array_search($rarity, array_column($list, 'rarity'));
		
		return ($key !== false);
	}

}
?>