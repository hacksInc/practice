<?php
/**
 *  Pp_LogDataViewMissionManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';

/**
 *  Pp_LogDataViewMissionManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewMissionManager extends Pp_LogdataViewManager
{
	private $_csv_mission_log_data = array(
		'id',
		'api_transaction_id',
		'play_id',
		'pp_id',
		'mission_id',
		'accompany_character_id',
		'result_type',
		'status',
		'zone',
		'start_created',
		'end_created',
	);

	/**
	 * ミッション履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getMissionLogDataCount($search_params)
	{
		$conditions = $this->_getMissionLogDataConditions($search_params);
		$res = $this->_getMissionLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * ミッション履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getMissionLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getMissionLogDataConditions($search_params);
		$sort = array(
			's.date_created' => 'DESC'
		);
		$res = $this->_getMissionLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * キャラクター情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getMissionLogDataConditions($search_params)
	{
		$where = "WHERE ? <= s.date_created AND s.date_created <= ?";
		$param = array($search_params['date_from'], $search_params['date_to']);

		$admin_user_m = $this->backend->getManager('AdminUser');

		// 検索条件：ニックネーム
		if (isset($search_params['name']) && $search_params['name'] != ''){
			if ($search_params['name_option'] == '1') {
				$user = $admin_user_m->getUserBaseFromName($search_params['name']);
				if (!empty($user)) $users = array($user);
			} else {
				$users = $admin_user_m->getUserBaseFromNameLike($search_params['name']);
			}
			if (!empty($users)) {
				$ids = array();
				foreach($users as $user) {
					$ids[] = $user['pp_id'];
				}

				if (0 < count($ids)) {
					$where = $where . " AND s.pp_id in (?)";
					$param[] = join(",", $ids);
				}
			} else {
				$where = $where . " AND s.pp_id is null";
			}
		}

		// 検索条件：ユーザーID
		if (isset($search_params['pp_id']) && $search_params['pp_id'] != ''){
			$where = $where . " AND s.pp_id = ?";
			$param[] = $search_params['pp_id'];
		}

		return array('where' => $where, 'param' => $param);
	}

	/**
	 * キャラクター情報履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getMissionLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT"
			. " s.id,"
			. " s.api_transaction_id,"
			. " s.play_id,"
			. " r.play_id as result_play_id,"
			. " s.pp_id,"
			. " s.mission_id,"
			. " s.accompany_character_id,"
			. " r.result_type,"
			. " r.status,"
			. " r.zone,"
			. " s.date_created as start_created,"
			. " r.date_created as end_created"
			. " FROM log_ingame_start as s LEFT JOIN log_ingame_result as r ON s.play_id = r.play_id " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * キャラクター情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getMissionLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_ingame_start as s " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * api_transaction_idを元にミッション履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getMissionDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getMissionLogData($conditions['where'], $conditions['param'], $sort);
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
	 * csvファイル作成を行う
	 *
	 * @pramas array $mission_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileMissionLogData($mission_log_data)
	{
		$log_name = 'mission_log_data';
		$file_path = LOGDATA_PATH_MISSION_DATA;

		return $this->createCsvFile($file_path, $log_name, $mission_log_data, $this->_csv_mission_log_data);

	}
}
