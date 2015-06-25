<?php
/**
 *  Pp_LogDataViewGachaManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';

/**
 *  Pp_LogDataViewGachaManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewGachaManager extends Pp_LogdataViewManager
{

	/**
	 * csvファイル出力項目
	 */
	private $_csv_gacha_log_data = array(
		'id',
		'api_transaction_id',
		'pp_id',
		'gacha_id',
		'gacha_type',
		'photo_id',
		'photo_lv',
		'date_created',
	);

	/**
	 * ガチャ履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getGachaLogDataCount($search_params)
	{
		$conditions = $this->_getGachaLogDataConditions($search_params);
		$res = $this->_getGachaLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * ガチャ履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getGachaLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getGachaLogDataConditions($search_params);
		$sort = array(
			'date_created' => 'DESC'
		);
		$res = $this->_getGachaLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * api_transaction_idを元にガチャ履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getGachaLogDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getGachaLogData($conditions['where'], $conditions['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * api_transaction_idを元にガチャ履歴詳細情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getGachaPrizeLogDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
			'id' => 'ASC',
		);
		$res = $this->_getGachaPrizeLogData($conditions['where'], $conditions['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * ガチャ情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getGachaLogDataConditions($search_params)
	{
		$where = "WHERE date_created >= ? AND date_created <= ?";
		$param = array($search_params['date_from'], $search_params['date_to']);

		// 検索条件：ステータス
		if (isset($search_params['gacha_id']) && $search_params['gacha_id'] != ''){
			$where = $where . " AND gacha_id = ?";
			$param[] = $search_params['gacha_id'];
		}

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
	 * ガチャ履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getGachaLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT * FROM log_photo_gacha " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * ガチャ情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getGachaLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_photo_gacha " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * ガチャ履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getGachaPrizeLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT * FROM log_gacha_prize_data " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * ガチャ履歴のcsvファイル作成を行う
	 *
	 * @pramas array $gacha_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileGachaLogData($gacha_log_data)
	{
		$log_name = 'gacha_log_data';
		$file_path = LOGDATA_PATH_GACHA_DATA;

		return $this->createCsvFile($file_path, $log_name, $gacha_log_data, $this->_csv_gacha_log_data);
	}

}
