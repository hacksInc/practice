<?php
/**
 *  Pp_KpiViewManager.php
 *
 *  @author		{$author}
 *  @package	Pp
 *  @version	$Id$
 */


/**
 *  Pp_KpiViewManager
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_KpiViewManager extends Ethna_AppManager
{
	/**
	 * リテンションデータをDBに登録する
	 *
	 *
	 * @param array $data	   取り込み対象データ
	 * @param string $tran_date 処理日
	 * @param integer $ua  OS種別 0:iOS 1:Android
	 * @return mixed
	 */
	public function addRetentionData($data, $tran_date, $ua)
	{
		$log_db = $this->backend->getDB('logex');
		$now = date('Y-m-d H:i:s', time());
		$count_login = $data[2];
		if ($data[1] == 0) {
			$count_login = 0;
		}
		// もしかしたらdate_tallyとdate_installは逆かもしれない
		$param = array(
			'ua' => $ua,
			'date_tally' => $tran_date,
			'date_install' => $data[0],
			'count_download' => $data[1],
			'count_login' => $count_login,
			'continuance_rate' => round(str_replace('%', '', $data[3]), 2),
			'date_created' => $now,
			'date_modified' => $now,
		);
		$sql = "INSERT INTO kpi_user_continuance_rate("
			 . " ua,"
			 . " date_tally,"
			 . " date_install,"
			 . " count_download,"
			 . " count_login,"
			 . " continuance_rate,"
			 . " date_created,"
			 . " date_modified)"
			 . " VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

		return $this->_executeQuery($sql, $param);
	}

	/**
	 * マスタ登録されているマップ情報のリストを取得
	 *
	 * @return mixed
	 */
	public function getMasterMap()
	{
		$sql = "select map_id, name_ja as map_name from m_map";
		$param = array();
		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * CSV作成
	 *
	 * @param array $tilte
	 * @param array $data
	 * @param string $log_name
	 * @return boolean|string
	 */
	public function createCsv($tilte, $data, $log_name)
	{

		$datetime = new DateTime();
		$file_name = $log_name .'_' . $datetime->format('Ymd') . '_' . mt_rand();

		if (!$fp=@fopen(KPIDATA_TMP_PATH . '/' . $file_name, 'a'))
		{
			return false;
		}

		// タイトル書き込み
		fwrite($fp, mb_convert_encoding(implode(',', $tilte), "Shift-JIS", "UTF-8") . "\r\n");

		// データ書き込み
		foreach ($data as $key => $item)
		{
			$_item = array();

			foreach ($item as $value)
			{
				$_item[] = str_replace(',', '，', $value);
			}

			fwrite($fp, mb_convert_encoding(implode(',', $_item), "Shift-JIS", "UTF-8") . "\r\n");
		}

		fclose($fp);

		return $file_name;
	}

	/**
	 * クエリ実行
	 *
	 * @param string $sql
	 * @param array $param
	 * @return
	 */
	protected function _executeQuery($sql, $param)
	{

		$log_db = $this->backend->getDB('logex');
		if (!$log_db->execute($sql, $param)) {
			$tmp_sql = $log_db->db->last_query;
			$this->backend->logger->log(LOG_ERR, 'Query:' . print_r($tmp_sql, true));
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$log_db->db->ErrorNo(), $log_db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		return true;

	}

	/**
	 * limit 句の生成を行う
	 *
	 * @param array $sort
	 * @return string $order_by
	 */
	protected function _createSqlPhraseLimit($limit_cnt, $offset) {
		// limit句の編集
		$limit = '';
		if (! is_null ( $limit_cnt )) {
			$limit = " LIMIT " . $limit_cnt;
			if (! is_null ( $offset ) && $offset != '') {
				$limit = $limit . " OFFSET " . $offset;
			}
		}

		return $limit;
	}

	/**
	 * order by 句の生成を行う
	 *
	 * @param array $sort
	 * @return string $order_by
	 */
	protected function _createSqlPhraseOrderBy($sort)
	{
		$order_by = '';
		if (!is_null($sort) && is_array($sort)){
			foreach($sort as $k => $v){
				$tmp[] = $k . " " . $v;
			}
			$order_by = " ORDER BY " . implode(",", $tmp);
		}

		return $order_by;
	}

	/**
	 * 件数取得
	 *
	 * @param string $table
	 * @param array $search_param
	 * @return number
	 */
	protected function _getCount($table, $search_param)
	{
		$db = $this->backend->getDB('logex_r');

		$sql = "
SELECT
  COUNT(id) _count
FROM
  $table
WHERE
  1 = 1
";

		$param = $this->_createCondition($search_param);

		$sql .= $param['where'];

		return (int)$db->GetOne($sql, $param['param']);
	}

	/**
	 * リスト取得
	 *
	 * @param string $sql
	 * @param array $search_param
	 * @return array
	 */
	protected function _getList($sql, $search_param)
	{
		$db = $this->backend->getDB('logex_r');

		$param = $this->_createCondition($search_param);

		$sql .= $param['where'];

		$data = $db->GetAll($sql, $param['param']);

		if (!$data)
		{
			return array();
		}
		else
		{
			return $data;
		}
	}

	/**
	 * 条件生成
	 *
	 * @param array $search_param
	 * @return array
	 */
	protected function _createCondition($search_param)
	{
		$where = '';
		$param = array();

		if (isset($search_param['date_from']) && !empty($search_param['date_from']) &&
			isset($search_param['date_to']) && !empty($search_param['date_to']))
		{
			$where .= '  AND (? <= date_tally AND date_tally <= ?)';
			$param[] = $search_param['date_from'];
			$param[] = $search_param['date_to'];
		}

		if (isset($search_param['ua']) && !empty($search_param['ua']))
		{
			$where .= '  AND ua = ?';
			$param[] = $search_param['ua'];
		}

		if (isset($search_param['area_id']) && !empty($search_param['area_id']))
		{
			$where .= '  AND area_id = ?';
			$param[] = $search_param['area_id'];
		}

		return array('where' => $where, 'param' => $param);
	}
}
