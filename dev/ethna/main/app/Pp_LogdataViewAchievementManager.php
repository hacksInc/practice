<?php
/**
 *  Pp_LogDataViewAchievementManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';

/**
 *  Pp_LogDataViewAchievementManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewAchievementManager extends Pp_LogdataViewManager
{
	private $_csv_achievement_log_data = array(
		'pp_id',
		'ach_id',
		'date_created',
	);

	/**
	 * 勲章履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getAchievementLogDataCount($search_params)
	{
		$conditions = $this->_getAchievementLogDataConditions($search_params);
		$res = $this->_getAchievementLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * 勲章履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getAchievementLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getAchievementLogDataConditions($search_params);
		$sort = array(
			'date_created' => 'DESC'
		);
		$res = $this->_getAchievementLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * 勲章情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getAchievementLogDataConditions($search_params)
	{
		$where = "WHERE date_created >= ? AND date_created <= ?";
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
					$where = $where . " AND pp_id in (?)";
					$param[] = join(",", $ids);
				}
			} else {
				$where = $where . " AND pp_id is null";
			}
		}

		// 検索条件：ユーザーID
		if (isset($search_params['pp_id']) && $search_params['pp_id'] != ''){
			$where = $where . " AND pp_id = ?";
			$param[] = $search_params['pp_id'];
		}

		return array('where' => $where, 'param' => $param);
	}

	/**
	 * 勲章情報履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getAchievementLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('r');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT"
			. " pp_id,"
			. " ach_id,"
			. " date_created"
			. " FROM ut_user_achievement_rank " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * 勲章情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getAchievementLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('r');
		$sql = "SELECT count(*) FROM ut_user_achievement_rank " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * csvファイル作成を行う
	 *
	 * @pramas array $achievement_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileAchievementLogData($achievement_log_data)
	{
		$log_name = 'achievement_log_data';
		$file_path = LOGDATA_PATH_ACHIEVEMENT_DATA;

		return $this->createCsvFile($file_path, $log_name, $achievement_log_data, $this->_csv_achievement_log_data);

	}
}
