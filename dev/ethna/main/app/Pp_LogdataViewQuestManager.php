<?php
/**
 *  Pp_LogDataViewQuestManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';

/**
 *  Pp_LogDataViewQuestManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewQuestManager extends Pp_LogdataViewManager
{

	/**
	 *
	 */
	private $_quest_status = array(
		'-1' => '削除済み',
		'0' => '新規',
		'1' => '開封済み',
		'2' => '受取済み',
	);

	/**
	 *
	 */
	private $_quest_type = array(
		'1' => '通常アイテム',
		'2' => 'モンスター',
		'3' => 'ビックリ玉',
		'4' => 'ガチャポイント',
		'5' => '合成メダル（コイン）',
		'6' => 'マジカルメダル',
	);

	private $_csv_quest_log_data = array(
		'id',
		'api_transaction_id',
		'user_id',
		'user_name',
		'rank',
		'area_id',
		'area_name',
		'quest_id',
		'quest_name',
		'team_id',
		'play_id',
		'quest_st',
		'quest_st_name',
		'active_team_id',
		'helper_user_id',
		'helper_user_name',
		'helper_monster_id',
		'helper_monster_name',
		'continue_cnt',
		'game_total',
		'bonus_big',
		'bonus_reg',
		//'overkill',
		'drop_gold',
		'get_exp',
		'bonus_type',
		'bonus_cd',
		//'bonus_overkill',
		'lose_battle_no',
		'gameover_type',
		'date_log',
	);

	/**
	 * クエスト履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getQuestLogDataCount($search_params)
	{
		$conditions = $this->_getQuestLogDataConditions($search_params);
		$res = $this->_getQuestLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * クエスト履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getQuestLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getQuestLogDataConditions($search_params);
		$sort = array(
			'date_log' => 'DESC'
		);
		$res = $this->_getQuestLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}
	//Kpi用に取得カラムを調整
	public function getQuestLogDataForKpiArea($search_params, $limit=null, $offset=null)
	{
		$conditions = $this->_getQuestLogDataConditions($search_params);
		$sort = array(
			'date_log' => 'ASC'
		);
		$res = $this->_getQuestLogDataForKpiArea($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}
		$log_data['count'] = count($res);
		$log_data['data'] = $res;
		return $log_data;
	}

	//Kpi集計
	public function countQuestLogForKpiArea($search_params)
	{
		$conditions = $this->_getQuestLogDataConditions($search_params);
		$res = $this->_countQuestLogForKpiArea($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}
		return $res;
	}
	public function distinctQuestLogForKpiArea($search_params)
	{
		$conditions = $this->_getQuestLogDataConditions($search_params);
		$res = $this->_distinctQuestLogForKpiArea($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}
		return $res;
	}
	public function sumQuestLogForKpiArea($sum, $search_params)
	{
		$conditions = $this->_getQuestLogDataConditions($search_params);
		$res = $this->_sumQuestLogForKpiArea($sum, $conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}
		return $res;
	}
	public function avgQuestLogForKpiArea($avg, $search_params)
	{
		$conditions = $this->_getQuestLogDataConditions($search_params);
		$res = $this->_avgQuestLogForKpiArea($avg, $conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}
		return $res;
	}

	/**
	 * クエスト履歴情報(play_id単位)の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getQuestPlayLogData($search_params)
	{

		$conditions = $this->_getQuestPlayLogDataConditions($search_params);
		$sort = array(
			'quest_st' => 'ASC'
		);
		$res = $this->_getQuestLogData($conditions['where'], $conditions['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * api_transaction_idを元にクエスト履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getQuestDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getQuestLogData($conditions['where'], $conditions['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * api_transaction_idを元にクエスト履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getQuestMonsterDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getQuestMonsterLogData($conditions['where'], $conditions['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * api_transaction_idを元にクエストチーム履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getQuestTeamDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getQuestTeamLogData($conditions['where'], $conditions['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * クエスト情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getQuestLogDataConditions($search_params)
	{
		$where = "WHERE date_log >= ? AND date_log <= ?";
		$param = array($search_params['date_from'], $search_params['date_to']);

		// 検索条件：クエストID
		if (isset($search_params['quest_id']) && $search_params['quest_id'] != ''){
			$where = $where . " AND quest_id = ?";
			$param[] = $search_params['quest_id'];
		}

		// 検索条件：ステータス
		if (isset($search_params['status']) && $search_params['status'] !== ''){ // 条件文が !='' だと値が0の時に通らない
			$where = $where . " AND quest_st = ?";
			$param[] = $search_params['status'];
		}

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

		// 検索条件：プラットフォーム
		if (isset($search_params['ua']) && $search_params['ua'] != ''){
			if ($search_params['ua'] == '1' || $search_params['ua'] == '2') {
				$where = $where . " AND ua = ?";
				$param[] = $search_params['ua'];
			}
		}

		// 検索条件：エリアID
		if (isset($search_params['area_id']) && $search_params['area_id'] != ''){
			$where = $where . " AND area_id = ?";
			$param[] = $search_params['area_id'];
		}

		// 検索条件：コンティニュー回数
		if (isset($search_params['continue_cnt']) && $search_params['continue_cnt'] !== ''){
			$where = $where . " AND continue_cnt = ?";
			$param[] = $search_params['continue_cnt'];
		}
		// 検索条件：コンティニュー回数＞
		if (isset($search_params['continue_gt']) && $search_params['continue_gt'] !== ''){
			$where = $where . " AND continue_cnt > ?";
			$param[] = $search_params['continue_gt'];
		}

		return array('where' => $where, 'param' => $param);
	}

	/**
	 * クエスト情報履歴(play_id単位)を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getQuestPlayLogDataConditions($search_params)
	{
		$where = "WHERE play_id = ?";
		$param[] = $search_params['play_id'];

		// 検索条件：ユーザーID
		if (isset($search_params['user_id']) && $search_params['user_id'] != ''){
			$where = $where . " AND user_id = ?";
			$param[] = $search_params['user_id'];
		}

		return array('where' => $where, 'param' => $param);
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
	 * クエスト履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getQuestLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);
		$sql = "SELECT "
			. " id,"
			. " api_transaction_id,"
			. " ua,"
			. " user_id,"
			. " user_name,"
			. " rank,"
			. " area_id,"
			. " area_name,"
			. " quest_id,"
			. " quest_name,"
			. " map_id,"
			. " map_no,"
			. " map_name,"
			. " team_id,"
			. " play_id,"
			. " quest_st,"
			. " active_team_id,"
			. " helper_user_id,"
			. " helper_user_name,"
			. " helper_monster_id,"
			. " helper_monster_name,"
			. " continue_cnt,"
			. " game_total,"
			. " bonus_big,"
			. " bonus_reg,"
			. " overkill,"
			. " drop_gold,"
			. " get_exp,"
			. " bonus_type,"
			. " bonus_cd,"
			. " bonus_overkill,"
			. " lose_battle_no,"
			. " gameover_type,"
			. " date_log,"
			. " case quest_st when 0 then 'スタート' when 1 then 'クリア' when 2 then 'ゲームオーバー' when 3 then 'コンティニュー' end as quest_st_name"
			. " FROM log_quest_data " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}
	//Kpi用に取得カラムを調整
	private function _getQuestLogDataForKpiArea($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex_r');
		//$log_db->execute('SET SESSION sql_big_selects = 1');
		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);
		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);
		$sql = "SELECT "
			. " api_transaction_id,"
			. " user_id,"
			. " rank,"
			. " area_id,"
			. " quest_st,"
			. " continue_cnt,"
			. " date_log"
			. " FROM log_quest_data " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);
	}

	//Kpi集計
	private function _countQuestLogForKpiArea($where, $param)
	{
		$log_db = $this->backend->getDB('logex_r');
		$sql = "SELECT count(*) FROM log_quest_data " . $where;
		// 実行
		return $log_db->GetOne($sql, $param);
	}
	private function _distinctQuestLogForKpiArea($where, $param)
	{
		$log_db = $this->backend->getDB('logex_r');
		$sql = "SELECT count(DISTINCT user_id) FROM log_quest_data " . $where;
		// 実行
		return $log_db->GetOne($sql, $param);
	}
	private function _sumQuestLogForKpiArea($sum, $where, $param)
	{
		$log_db = $this->backend->getDB('logex_r');
		$sql = "SELECT sum(".$sum.") FROM log_quest_data " . $where;
		// 実行
		return $log_db->GetOne($sql, $param);
	}
	private function _avgQuestLogForKpiArea($avg, $where, $param)
	{
		$log_db = $this->backend->getDB('logex_r');
		$sql = "SELECT avg(".$avg.") FROM log_quest_data " . $where;
		// 実行
		return $log_db->GetOne($sql, $param);
	}

	/**
	 * クエスト情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getQuestLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_quest_data " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * クエストチーム情報履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getQuestTeamLogData($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT * FROM log_quest_team_data " . $where;

		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * クエストモンスター情報履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getQuestMonsterLogData($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT * FROM log_quest_monster_data " . $where;

		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * クエスト履歴のcsvファイル作成を行う
	 *
	 * @pramas array $monster_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileQuestLogData($quest_log_data)
	{
		$log_name = 'quest_log_data';
		$file_path = LOGDATA_PATH_QUEST_DATA;

		return $this->createCsvFile($file_path, $log_name, $quest_log_data, $this->_csv_quest_log_data);
	}

}
