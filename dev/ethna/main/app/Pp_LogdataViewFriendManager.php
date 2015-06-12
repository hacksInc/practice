<?php
/**
 *  Pp_LogDataViewFriendManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';

/**
 *  Pp_LogDataViewFriendManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewFriendManager extends Pp_LogdataViewManager
{

	private $_csv_friend_log_data = array(
		'id',
		'api_transaction_id',
		'user_id',
		'name',
		'rank',
		'processing_type',
		'processing_type_name',
		'u_user_id',
		'u_name',
		'u_rank',
		'u_old_friend_rest',
		'u_friend_rest',
		'u_friend_max_num',
		'u_reader_monster_id',
		'u_reader_monster_name',
		'u_reader_monster_rare',
		'u_reader_monster_lv',
		'u_reader_monster_skill_lv',
		'f_user_id',
		'f_name',
		'f_rank',
		'f_old_friend_rest',
		'f_friend_rest',
		'f_friend_max_num',
		'f_reader_monster_id',
		'f_reader_monster_name',
		'f_reader_monster_rare',
		'f_reader_monster_lv',
		'f_reader_monster_skill_lv',
		'date_log',
	);

	/**
	 * フレンド履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getFriendLogDataCount($search_params)
	{
		$conditions = $this->_getFriendLogDataConditions($search_params);
		$res = $this->_getFriendLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * フレンド履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getFriendLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getFriendLogDataConditions($search_params);
		$sort = array(
			'fd.date_log' => 'DESC'
		);
		$res = $this->_getFriendLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * フレンド履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getFriendLogInfoData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getFriendLogInfoDataConditions($search_params);
		$sort = array(
			'fd.date_log' => 'DESC'
		);
		$res = $this->_getFriendLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * フレンド情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getFriendLogDataConditions($search_params)
	{
		$where = "WHERE date_log >= ? AND date_log <= ?";
		$param = array($search_params['date_from'], $search_params['date_to']);

		// 検索条件：ニックネーム(フレンド申請者)
		if (isset($search_params['name']) && $search_params['name'] != ''){
			if ($search_params['name_option'] == '1') {
				$where = $where . " AND name = ?";
				$param[] = $search_params['name'];
			} else {
				$where = $where . " AND name LIKE ?";
				$param[] = '%' . $search_params['name'] . '%';
			}
		}

		// 検索条件：ニックネーム(フレンド申請受側)
		if (isset($search_params['friend_name']) && $search_params['friend_name'] != ''){
			if ($search_params['friend_name_option'] == '1') {
				$where = $where . " AND friend_name = ?";
				$param[] = $search_params['friend_name'];
			} else {
				$where = $where . " AND friend_name LIKE ?";
				$param[] = '%' . $search_params['friend_name'] . '%';
			}
		}

		// 検索条件：ユーザーID(フレンド申請者)
		if (isset($search_params['user_id']) && $search_params['user_id'] != ''){
			$where = $where . " AND user_id = ?";
			$param[] = $search_params['user_id'];
		}

		// 検索条件：ユーザーID(フレンド申請受側)
		if (isset($search_params['friend_user_id']) && $search_params['friend_user_id'] != ''){
			$where = $where . " AND friend_user_id = ?";
			$param[] = $search_params['friend_user_id'];
		}

		return array('where' => $where, 'param' => $param);
	}

	/**
	 * フレンド情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getFriendLogInfoDataConditions($search_params)
	{

		// 検索条件：ユーザーID(フレンド申請者)
		$where = $where . " WHERE user_id = ? AND friend_user_id = ?";
		$param = array($search_params['user_id'], $search_params['friend_user_id']);

		return array('where' => $where, 'param' => $param);
	}

	/**
	 * フレンド情報履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getFriendLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT "
			. " fd.*,"
			. " fud_u.id as u_log_id,"
			. " fud_u.user_id as u_user_id,"
			. " fud_u.name as u_name,"
			. " fud_u.rank as u_rank,"
			. " fud_u.old_friend_rest as u_old_friend_rest,"
			. " fud_u.friend_rest as u_friend_rest,"
			. " fud_u.friend_max_num as u_friend_max_num,"
			. " fud_u.reader_monster_user_id as u_reader_monster_user_id,"
			. " fud_u.reader_monster_id as u_reader_monster_id,"
			. " fud_u.reader_monster_name as u_reader_monster_name,"
			. " fud_u.reader_monster_rare as u_reader_monster_rare,"
			. " fud_u.reader_monster_lv as u_reader_monster_lv,"
			. " fud_u.reader_monster_skill_lv as u_reader_monster_skill_lv,"
			. " fud_f.id as f_log_id,"
			. " fud_f.user_id as f_user_id,"
			. " fud_f.name as f_name,"
			. " fud_f.rank as f_rank,"
			. " fud_f.old_friend_rest as f_old_friend_rest,"
			. " fud_f.friend_rest as f_friend_rest,"
			. " fud_f.friend_max_num as f_friend_max_num,"
			. " fud_f.reader_monster_user_id as f_reader_monster_user_id,"
			. " fud_f.reader_monster_id as f_reader_monster_id,"
			. " fud_f.reader_monster_name as f_reader_monster_name,"
			. " fud_f.reader_monster_rare as f_reader_monster_rare,"
			. " fud_f.reader_monster_lv as f_reader_monster_lv,"
			. " fud_f.reader_monster_skill_lv as f_reader_monster_skill_lv"
			. " FROM"
			. " (select * from log_friend_data " . $where . $option . ") fd"
			. " LEFT OUTER JOIN log_friend_user_data fud_u"
			. " ON fud_u.api_transaction_id = fd.api_transaction_id"
			. " AND fud_u.user_id = fd.user_id"
			. " LEFT OUTER JOIN log_friend_user_data fud_f"
			. " ON fud_f.api_transaction_id = fd.api_transaction_id"
			. " AND fud_f.user_id = fd.friend_user_id"
			. $order_by;

		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * アイテム情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getFriendLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_friend_data " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * api_transaction_idを元にアイテム履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getFriendDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
			'id' => 'ASC',
		);
		$res = $this->_getFriendLogData($conditions['where'], $conditions['param'], $sort);
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
	 * @pramas array $friend_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileFriendLogData($friend_log_data)
	{
		$log_name = 'friend_log_data';
		$file_path = LOGDATA_PATH_FRIEND_DATA;

		$title_log_data = array();
		$log_data = array();
		return $this->createCsvFile($file_path, $log_name, $friend_log_data, $this->_csv_friend_log_data);

	}
}
