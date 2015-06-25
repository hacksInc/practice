<?php
/**
 *  Pp_LogDataViewManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';

/**
 *  Pp_LogDataManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewMonsterManager extends Pp_LogdataViewManager
{
	/**
	 * モンスター情報CSVファイルレイアウト
	 */ 
	private $_csv_monster_log_data = array(
		'id',
		'api_transaction_id',
		'user_monster_id',
		'user_id',
		'name',
		'rank',
		'monster_id',
		'monster_name',
		'rare',
		'exp',
		'lv',
		'hp',
		'attack',
		'hp_plus',
		'attack_plus',
		'heal_plus',
		'skill_lv',
		'processing_type',
		'processing_type_name',
		'monster_status',
		'monster_status_name',
		'account_name',
		'price',
		'date_log',
	);

	/**
	 * 強化合成モンスター情報CSVファイルレイアウト
	 */ 
	private $_csv_powerup_monster_log_data = array(
		'id',
		'api_transaction_id',
		'user_id',
		'name',
		'rank',
		'user_monster_id',
		'monster_id',
		'monster_name',
		'rare',
		'add_exp',
		'exp',
		'lv',
		'hp',
		'attack',
		'hp_plus',
		'attack_plus',
		'heal_plus',
		'skill_lv',
		'old_exp',
		'old_lv',
		'old_hp',
		'old_attack',
		'old_hp_plus',
		'old_attack_plus',
		'old_heal_plus',
		'old_skill_lv',
		'cost',
		'old_num',
		'num',
		'date_log',
	);

	/**
	 * 進化合成モンスター情報CSVファイルレイアウト
	 */ 
	private $_csv_evolution_monster_log_data = array(
		'id',
		'api_transaction_id',
		'user_id',
		'name',
		'rank',
		'user_monster_id',
		'monster_id',
		'monster_name',
		'rare',
		'exp',
		'lv',
		'hp',
		'attack',
		'hp_plus',
		'attack_plus',
		'heal_plus',
		'skill_lv',
		'old_user_monster_id',
		'old_monster_id',
		'old_monster_name',
		'old_rare',
		'old_exp',
		'old_lv',
		'old_hp',
		'old_attack',
		'old_hp_plus',
		'old_attack_plus',
		'old_heal_plus',
		'old_skill_lv',
		'cost',
		'old_num',
		'num',
		'date_log',
	);

	/**
	 * 売却モンスター情報CSVファイルレイアウト
	 */ 
	private $_csv_sell_monster_log_data = array(
		'id',
		'api_transaction_id',
		'user_id',
		'name',
		'rank',
		'monster_cnt',
		'sell_price',
		'user_monster_id',
		'monster_id',
		'monster_name',
		'rare',
		'exp',
		'lv',
		'hp',
		'attack',
		'hp_plus',
		'attack_plus',
		'heal_plus',
		'skill_lv',
		'processing_type',
		'processing_type_name',
		'account_name',
		'price',
		'date_log',
	);

	/**
	 * モンスター履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getMonsterLogDataCount($search_params)
	{
		$conditions = $this->_getMonsterLogDataConditions($search_params);
		$res = $this->_getMonsterLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * モンスター履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getMonsterLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getMonsterLogDataConditions($search_params);
		$sort = array(
			'date_log' => 'DESC'
		);
		$res = $this->_getMonsterLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * モンスター強化合成履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getMonsterPowerupLogDataCount($search_params)
	{
		$conditions = $this->_getMonsterPowerupLogDataConditions($search_params);
		$res = $this->_getMonsterPowerupLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * モンスター強化合成履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getMonsterPowerupLogData($search_params, $limit, $offset)
	{

		$conditions = $this->_getMonsterPowerupLogDataConditions($search_params);
		$sort = array(
			'date_log' => 'DESC'
		);
		$res = $this->_getMonsterPowerupLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * モンスター進化合成履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getMonsterEvolutionLogDataCount($search_params)
	{
		$conditions = $this->_getMonsterEvolutionLogDataConditions($search_params);
		$res = $this->_getMonsterEvolutionLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * モンスター進化合成履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getMonsterEvolutionLogData($search_params, $limit, $offset)
	{

		$conditions = $this->_getMonsterEvolutionLogDataConditions($search_params);
		$sort = array(
			'date_log' => 'DESC'
		);
		$res = $this->_getMonsterEvolutionLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * モンスター進化合成履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getMonsterSellLogDataCount($search_params)
	{
		$conditions = $this->_getMonsterSellLogDataConditions($search_params);
		$res = $this->_getMonsterSellLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * モンスター進化合成履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getMonsterSellLogData($search_params, $limit, $offset)
	{

		$conditions = $this->_getMonsterSellLogDataConditions($search_params);
		$sort = array(
			'date_log' => 'DESC'
		);
		$res = $this->_getMonsterSellLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * api_transaction_idを元にモンスター履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getMonsterDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getMonsterLogData($conditions['where'], $conditions['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * api_transaction_idを元にモンスター強化合成履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getMonsterPowerupDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getMonsterPowerupLogData($conditions['where'], $conditions['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * api_transaction_idを元にモンスター強化合成履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getMonsterEvolutionDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getMonsterEvolutionLogData($conditions['where'], $conditions['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * api_transaction_idを元に検索する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getApiTransactionIdConditions($search_params)
	{
		if (is_array($search_params)){
			foreach($search_params as $k => $v){
				$str[] = '?';
				$val[] = $v;
			}
			$where = "WHERE  api_transaction_id in (" . implode(',', $str) . ")";
			$param = $val;
		} else {
			$where = "WHERE  api_transaction_id = ?";
			$param[] = $search_params;
		}
		return array('where' => $where, 'param' => $param);
	}

	/**
	 * モンスター情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getMonsterLogDataConditions($search_params)
	{

		return $this->_getMonsterLogDataBaseConditions($search_params);

	}

	/**
	 * モンスター強化合成情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getMonsterPowerupLogDataConditions($search_params)
	{

		return $this->_getMonsterLogDataBaseConditions($search_params);

	}

	/**
	 * モンスター進化合成情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getMonsterEvolutionLogDataConditions($search_params)
	{

		return $this->_getMonsterLogDataBaseConditions($search_params);

	}

	/**
	 * モンスター売却情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getMonsterSellLogDataConditions($search_params)
	{

		return $this->_getMonsterLogDataBaseConditions($search_params);

	}

	/**
	 * モンスター情報履歴を取得する基本検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getMonsterLogDataBaseConditions($search_params)
	{
		$where = "WHERE date_log >= ? AND date_log <= ?";
		$param = array($search_params['date_from'], $search_params['date_to']);

		// 検索条件：ニックネーム
		if (isset($search_params['name']) && $search_params['name'] != ''){
			if ($search_params['name_option'] == '1') {
				$where = $where . " AND name = ?";
				$param[] = $search_params['name'];
			} else {
				$where = $where . " AND name LIKE ?";
				$param[] = '%' . $search_params['name'] . '%';
			}
		}

		// 検索条件：ユーザーID
		if (isset($search_params['user_id']) && $search_params['user_id'] != ''){
			$where = $where . " AND user_id = ?";
			$param[] = $search_params['user_id'];
		}

		return array('where' => $where, 'param' => $param);
	}

	/**
	 * モンスター情報履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getMonsterLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT"
			. " id,"
			. " api_transaction_id,"
			. " user_monster_id,"
			. " user_id,"
			. " name,"
			. " rank,"
			. " monster_id,"
			. " monster_name,"
			. " rare,"
			. " exp,"
			. " lv,"
			. " hp,"
			. " attack,"
			. " hp_plus,"
			. " attack_plus,"
			. " heal_plus,"
			. " skill_lv,"
			. " processing_type,"
			. " processing_type_name,"
			. " status,"
			. " case status when 0 then '生成' when -1 then '消滅' end as status_name,"
			. " account_name,"
			. " price,"
			. " add_exp,"
			. " add_skill_lv,"
			. " skill_match,"
			. " attribute_match,"
			. " date_log"
			. " FROM log_monster_data " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * モンスター情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getMonsterLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_monster_data " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * モンスター強化合成情報履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getMonsterPowerupLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT * FROM log_monster_powerup_data " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * モンスター強化合成情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getMonsterPowerupLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_monster_powerup_data " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * モンスター進化合成情報履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getMonsterEvolutionLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT * FROM log_monster_evolution_data " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * モンスター進化合成情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getMonsterEvolutionLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_monster_evolution_data " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * モンスター売却情報履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getMonsterSellLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT * FROM log_monster_sell_data " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * モンスター売却合成情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getMonsterSellLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_monster_sell_data " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * モンスター情報履歴のcsvファイル作成を行う
	 *
	 * @pramas array $monster_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileMonsterLogData($monster_log_data)
	{
		$log_name = 'monster_log_data';
		$file_path = LOGDATA_PATH_MONSTER_DATA;

		return $this->createCsvFile($file_path, $log_name, $monster_log_data, $this->_csv_monster_log_data);
	}

	/**
	 * モンスター強化合成履歴のcsvファイル作成を行う
	 *
	 * @pramas array $monster_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFilePowerupMonsterLogData($monster_log_data, $powerup_item_list)
	{
		$log_name = 'powerup_monster_log_data';
		$file_path = LOGDATA_PATH_MONSTER_DATA;

		foreach ($monster_log_data as $k => $v) {
			$item_data['num'] = $powerup_item_list[$v['api_transaction_id']]['num'];
			$item_data['old_num'] = $powerup_item_list[$v['api_transaction_id']]['old_num'];
			$powerup_monster_log_data[] = array_merge($v, $item_data);
		}

		return $this->createCsvFile($file_path, $log_name, $powerup_monster_log_data, $this->_csv_powerup_monster_log_data);
	}

	/**
	 * モンスター進化合成履歴のcsvファイル作成を行う
	 *
	 * @pramas array $monster_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileEvolutionMonsterLogData($monster_log_data, $evolution_item_list)
	{
		$log_name = 'evolution_monster_log_data';
		$file_path = LOGDATA_PATH_MONSTER_DATA;

		foreach ($monster_log_data as $k => $v) {
			$item_data['num'] = $evolution_item_list[$v['api_transaction_id']]['num'];
			$item_data['old_num'] = $evolution_item_list[$v['api_transaction_id']]['old_num'];
			$evolution_monster_log_data[] = array_merge($v, $item_data);
		}

		return $this->createCsvFile($file_path, $log_name, $evolution_monster_log_data, $this->_csv_evolution_monster_log_data);
	}

	/**
	 * モンスター売却履歴のcsvファイル作成を行う
	 *
	 * @pramas array $monster_log_data
	 * @pramas array $monster_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileSellMonsterLogData($monster_log_data, $sell_monster_list, $sell_item_list)
	{
		$log_name = 'sell_monster_log_data';
		$file_path = LOGDATA_PATH_MONSTER_DATA;

		foreach ($monster_log_data as $k => $v) {
			$item_data['num'] = $sell_item_list[$v['api_transaction_id']]['num'];
			$item_data['old_num'] = $sell_item_list[$v['api_transaction_id']]['old_num'];
			foreach($sell_monster_list[$v['api_transaction_id']] as $sell_k => $sell_v){
				$sell_monster_log_data[] = array_merge(array_merge($v, $item_data), $sell_v);
			}
		}

		return $this->createCsvFile($file_path, $log_name, $sell_monster_log_data, $this->_csv_sell_monster_log_data);
	}

}
