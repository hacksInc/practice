<?php
/**
 *  Pp_LogDataViewUserManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LogdataViewManager.php';

/**
 *  Pp_LogDataViewUserManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewUserManager extends Pp_LogdataViewManager
{

	private $_verification = array(
		);

	/**
	 *
	 */
	private $_csv_user_log_data = array(
		'id',
		'api_transaction_id',
		'pp_id',
		'name',
		'rank',
		'uipw_hash',
		'age_verification',
		'age_verification_name',
		'ma_purchased_mix',
		'old_name',
		'old_uipw_hash',
		'old_age_verification',
		'old_age_verification_name',
		'old_ma_purchased_mix',
		'account_name',
		'date_created',
	);

	/**
	 *
	 */
	private $_csv_user_login_log_data = array(
		'id',
		'device_type',
		'pp_id',
		'date_login',
		'date_created',
	);

	/**
	 *
	 */
	private $_csv_user_tutorial_log_data = array(
		'id',
		'api_transaction_id',
		'pp_id',
		'name',
		'rank',
		'login_date',
		'tutorial_status',
		'tutorial_status_name',
		'account_name',
		'date_created',
	);

	/**
	 * ユーザー履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getUserLogDataCount($search_params)
	{
		$conditions = $this->_getUserLogDataConditions($search_params);
		$res = $this->_getUserLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * ユーザー履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getUserLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getUserLogDataConditions($search_params);
		$sort = array(
			'date_created' => 'DESC'
		);
		$res = $this->_getUserLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * ユーザーログイン履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getUserLoginLogDataCount($search_params)
	{
		$conditions = $this->_getUserLoginLogDataConditions($search_params);
		$res = $this->_getUserLoginLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * ユーザーログイン履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getUserLoginLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getUserLoginLogDataConditions($search_params);
		$sort = array(
			'date_created' => 'DESC'
		);
		$res = $this->_getUserLoginLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * ユーザーチュートリアル履歴情報の件数取得
	 *
	 * @param array $search_params
	 * @return mixed
	 */
	public function getUserTutorialLogDataCount($search_params)
	{
		$conditions = $this->_getUserTutorialLogDataConditions($search_params);
		$res = $this->_getUserTutorialLogDataCount($conditions['where'], $conditions['param']);
		if ($res === false){
			return false;
		}

		return $res;
	}

	/**
	 * ユーザーチュートリアル履歴情報の取得
	 *
	 * @param array $search_params
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getUserTutorialLogData($search_params, $limit=null, $offset=null)
	{

		$conditions = $this->_getUserTutorialLogDataConditions($search_params);
		$sort = array(
			'date_created' => 'DESC'
		);
		$res = $this->_getUserTutorialLogData($conditions['where'], $conditions['param'], $sort, $limit, $offset);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;

	}

	/**
	 * ユーザー情報履歴を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getUserLogDataConditions($search_params)
	{
		$where = "WHERE date_created >= ? AND date_created <= ?";
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
		if (isset($search_params['pp_id']) && $search_params['pp_id'] != ''){
			$where = $where . " AND pp_id = ?";
			$param[] = $search_params['pp_id'];
		}

		return array('where' => $where, 'param' => $param);
	}

	/**
	 * ユーザーログイン履歴情報を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getUserLoginLogDataConditions($search_params)
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
	 * ユーザーチュートリアル履歴情報を取得する検索条件を編集する
	 *
	 * @parami array $search_params
	 * @return mixed
	 */
	private function _getUserTutorialLogDataConditions($search_params)
	{
		$where = "WHERE date_created >= ? AND date_created <= ?";
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
		if (isset($search_params['pp_id']) && $search_params['pp_id'] != ''){
			$where = $where . " AND pp_id = ?";
			$param[] = $search_params['pp_id'];
		}

		return array('where' => $where, 'param' => $param);
	}

	/**
	 * ユーザー情報変更履歴を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getUserLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT "
			. " id,"
			. " api_transaction_id,"
			. " pp_id,"
			. " name,"
			. " rank,"
			. " uipw_hash,"
			. " age_verification,"
			. " CASE age_verification "
			. " WHEN -1 THEN '未チェック'"
			. " WHEN 0 THEN '20歳以上'"
			. " WHEN 1 THEN '14歳未満'"
			. " WHEN 2 THEN '18歳未満'"
			. " WHEN 3 THEN '20歳未満'"
			. " END as age_verification_name,"
			. " ma_purchased_mix,"
			. " old_name,"
			. " old_uipw_hash,"
			. " old_age_verification,"
			. " CASE old_age_verification "
			. " WHEN -1 THEN '未チェック'"
			. " WHEN 0 THEN '20歳以上'"
			. " WHEN 1 THEN '14歳未満'"
			. " WHEN 2 THEN '18歳未満'"
			. " WHEN 3 THEN '20歳未満'"
			. " END as old_age_verification_name,"
			. " old_ma_purchased_mix,"
			. " account_name,"
			. " date_created"
			. " FROM log_user_data " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * ユーザー情報変更履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getUserLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_user_data " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * ユーザーログイン履歴情報を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getUserLoginLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT"
			. " id,"
			. " device_type,"
			. " pp_id,"
			. " date_login,"
			. " date_created"
			. " FROM log_user_login " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * ユーザーログイン情報履歴の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getUserLoginLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_user_login " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * ユーザーチュートリアル履歴情報を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	private function _getUserTutorialLogData($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');

		// order by 句の編集
		$order_by = $this->_createSqlPhraseOrderBy($sort);

		// limit句の編集
		$option = $this->_createSqlPhraseLimit($limit, $offset);

		$sql = "SELECT"
			. " id,"
			. " api_transaction_id,"
			. " pp_id,"
			. " name,"
			. " rank,"
			. " login_date,"
			. " tutorial_status,"
			. " tutorial_status_name,"
			. " account_name,"
			. " date_created"
			. " FROM log_user_tutorial_data " . $where . $order_by . $option;
		// 実行
		return $log_db->GetAll($sql, $param);

	}

	/**
	 * ユーザーチュートリアル履歴情報の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	private function _getUserTutorialLogDataCount($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "SELECT count(*) FROM log_user_tutorial_data " . $where;

		// 実行
		return $log_db->GetOne($sql, $param);

	}

	/**
	 * ユーザー情報変更履歴のcsvファイル作成を行う
	 *
	 * @pramas array $user_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileUserLogData($user_log_data)
	{
		$log_name = 'user_log_data';
		$file_path = LOGDATA_PATH_USER_DATA;

		return $this->createCsvFile($file_path, $log_name, $user_log_data, $this->_csv_user_log_data);
	}

	/**
	 * ユーザーログイン履歴情報のcsvファイル作成を行う
	 *
	 * @pramas array $user_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileUserLoginLogData($user_log_data)
	{
		$log_name = 'user_login_log_data';
		$file_path = LOGDATA_PATH_USER_DATA;

		return $this->createCsvFile($file_path, $log_name, $user_log_data, $this->_csv_user_login_log_data);
	}

	/**
	 * ユーザーチュートリアル履歴情報のcsvファイル作成を行う
	 *
	 * @pramas array $user_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFileUserTutorialLogData($user_log_data)
	{
		$log_name = 'user_tutorial_log_data';
		$file_path = LOGDATA_PATH_USER_DATA;

		return $this->createCsvFile($file_path, $log_name, $user_log_data, $this->_csv_user_tutorial_log_data);
	}

}
