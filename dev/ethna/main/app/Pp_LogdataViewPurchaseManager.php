<?php
/**
 *  Pp_LogDataViewPurchaseManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';

/**
 *  Pp_LogDataViewPurchaseManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewPurchaseManager extends Pp_LogdataViewManager
{
	private $_csv_purchase_log_data = array(
		'id',
		'api_transaction_id',
		'user_id',
		'name',
		'rank',
		'game_transaction_id',
		'receipt_product_id',
		'receipt',
		'google_signature',
		'app_id',
		'res_flg',
		'res_flg_name',
		'response',
		'account_name',
		'date_log',
	);

	/**
	 * 勲章履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getPurchaseLogDataCount($search_params)
	{
		$conditions = $this->_getPurchaseLogDataConditions($search_params);
		$res = $this->_getPurchaseLogDataCount($conditions['where'], $conditions['param']);
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
	public function getPurchaseLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getPurchaseLogDataConditions($search_params);
		$sort = array(
			'date_log' => 'DESC'
		);
		$res = $this->_getPurchaseLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
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
	private function _getPurchaseLogDataConditions($search_params)
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
			//if ($search_params['item_id'] != ''){
			$where = $where . " AND user_id = ?";
			$param[] = $search_params['user_id'];
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
	private function _getPurchaseLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT"
			. " id,"
			. " api_transaction_id,"
			. " user_id,"
			. " name,"
			. " rank,"
			. " game_transaction_id,"
			. " receipt_product_id,"
			. " receipt,"
			. " google_signature,"
			. " app_id,"
			. " ua,"
			. " CASE ua WHEN 1 THEN 'iphone' WHEN 2 THEN 'android' END as ua_name,"
			. " res_flg,"
			. " CASE res_flg WHEN 0 THEN '正常終了' WHEN 1 THEN 'エラー' END as res_flg_name,"
			. " response,"
			. " account_name,"
			. " date_log"
			. " FROM log_purchase_data " . $where . $order_by . $option;
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
	private function _getPurchaseLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_purchase_data " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * api_transaction_idを元に勲章履歴情報を取得する
	 *
	 * @param mixed $api_transaction_id
	 * @return array
	 */
	public function getPurchaseDataByApiTransactionId($api_transaction_id)
	{
		$conditions = $this->_getApiTransactionIdConditions($api_transaction_id);
		$sort = array(
			'api_transaction_id' => 'ASC',
		);
		$res = $this->_getPurchaseLogData($conditions['where'], $conditions['param'], $sort);
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
	 * @pramas array $purchase_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFilePurchaseLogData($purchase_log_data)
	{
		$log_name = 'purchase_log_data';
		$file_path = LOGDATA_PATH_PURCHASE_DATA;

		return $this->createCsvFile($file_path, $log_name, $purchase_log_data, $this->_csv_purchase_log_data);

	}
}
