<?php
/**
 *  Pp_LogDataViewCharacterManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';

/**
 *  Pp_LogDataViewCharacterManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewCharacterManager extends Pp_LogdataViewManager
{
	private $_csv_character_log_data = array(
		'id',
		'api_transaction_id',
		'processing_type',
		'pp_id',
		'character_id',
		'crime_coef',
		'crime_coef_prev',
		'body_coef',
		'body_coef_prev',
		'intelli_coef',
		'intelli_coef_prev',
		'mental_coef',
		'mental_coef_prev',
		//'ex_stress_care',
		//'ex_stress_care_prev',
		'date_created',
	);

	/**
	 * 課金アイテム購入履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getCharacterLogDataCount($search_params)
	{
		$conditions = $this->_getCharacterLogDataConditions($search_params);
		$res = $this->_getCharacterLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * 課金アイテム購入履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getCharacterLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getCharacterLogDataConditions($search_params);
		$sort = array(
			'date_created' => 'DESC'
		);
		$res = $this->_getCharacterLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
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
	private function _getCharacterLogDataConditions($search_params)
	{
		$where = "WHERE ? <= date_created AND date_created <= ?";
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

		// 検索条件：ストレスケア
		// A01 定時ストレスケア
		// A02 臨時ストレスケア
		// A05 セラピー診断
		$where = $where . " AND processing_type not in ('A01', 'A02', 'A05')";

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
	private function _getCharacterLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT"
			. " id,"
			. " api_transaction_id,"
			. " processing_type,"
			. " pp_id,"
			. " character_id,"
			. " crime_coef,"
			. " crime_coef_prev,"
			. " body_coef,"
			. " body_coef_prev,"
			. " intelli_coef,"
			. " intelli_coef_prev,"
			. " mental_coef,"
			. " mental_coef_prev,"
			//. " ex_stress_care,"
			//. " ex_stress_care_prev,"
			. " date_created"
			. " FROM log_character " . $where . $order_by . $option;
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
	private function _getCharacterLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_character " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * api_transaction_idを元に課金アイテム購入履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getCharacterDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getCharacterLogData($conditions['where'], $conditions['param'], $sort);
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
	 * @pramas array $character_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileCharacterLogData($character_log_data)
	{
		$log_name = 'character_log_data';
		$file_path = LOGDATA_PATH_CHARACTER_DATA;

		return $this->createCsvFile($file_path, $log_name, $character_log_data, $this->_csv_character_log_data);

	}
}
