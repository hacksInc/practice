<?php
/**
 *  Pp_KpiViewLtvManager.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewLtvManager
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_KpiViewLtvManager extends Pp_KpiViewManager
{
	/** LTV（日）一覧表示日数 */
	const LTV_LIST_DAYS = 180;

	/** LTV（月）一覧表示月数 */
	const LTV_LIST_MONTHLIES = 6;

	/** 登録月別課金額&課金UU一覧表示月数 */
	const LTV_LIST_CHARGE = 6;
	
	/** N日後のLTV（日）表示対象日数(0:当日) */
	var $DISP_ELAPSED_DATE = array(0, 1, 2, 3, 4, 5, 6, 13, 29, 59, 89, 179);
	
	/** N日後のLTV（月）表示対象月数(0:当月) */
	var $DISP_ELAPSED_MONTH_DATE = array(0, 1, 2, 3, 4, 5);

	/** 登録月別課金額&課金UU表示対象月数(0:当月) */
	var $DISP_ELAPSED_CHARGE_DATE = array(0, 1, 2, 3, 4, 5);
	
	public function getCountInDailyLtv($search_param)
	{
		return parent::_getCount('kpi_ltv_daily', $search_param);
	}

	public function getListInDailyLtv($search_param)
	{
		$sql = "
SELECT
  date_tally
  , date_install
  , ltv
FROM
  kpi_ltv_daily
WHERE
  1 = 1
";

		return $this->_getList($sql, $search_param);
	}

	public function createCsvInDailyLtv($list)
	{
		$log_name = 'kpi_daily_ltv';
		$file_path = KPIDATA_TMP_PATH;
		
		$old_umask = umask(0000);
		if (!is_dir(KPIDATA_TMP_PATH)) {
			mkdir(KPIDATA_TMP_PATH, 0777, true); // 後でターミナル上のjugmonユーザで削除できるように、0777にする。
		}
		umask($old_umask);
		
		$rand_num = mt_rand();
		$today_date = date('Ymd', time());
		$file_name = $log_name .'_' . $today_date . '_' . $rand_num;
		if (!$fp=@fopen($file_path . '/' . $file_name, 'a')){
			return false;
		}
		
		$title_list = array(
				'日付',
				'当日',
		);
		for($i=0;$i<=self::LTV_LIST_DAYS;$i++){
			if (in_array($i, $this->DISP_ELAPSED_DATE)) {
				if ($i == 0) continue;
				$title_list[] = ($i + 1) . '日目';
			}
		}
		$title_str = implode(',', $title_list);
		fwrite($fp, mb_convert_encoding($title_str, "Shift_JIS", "UTF-8") . "\r\n");
		
		$data_list = array();
		foreach ($list as $data_k => $data_v){
			$data_list = array();
			$data_list[] = $data_v['date_install'];
			foreach ($data_v['list'] as $k => $v){
				if (!in_array($k, $this->DISP_ELAPSED_DATE)) {
					break;
				}
				$rate = $v['ltv'];
				if ($v['ltv'] !== '-') {
					$rate = $rate . '%';
				}
				$data_list[] = $rate;
			}
			$str = implode(',', $data_list);
			fwrite($fp, mb_convert_encoding($str, "Shift-JIS", "UTF-8") . "\r\n");
		}
		fclose($fp);
		
		return $file_name;
	}

	public function getCountInMonthlyLtv($search_param)
	{
		return parent::_getCount('kpi_ltv_monthly', $search_param);
	}

	public function getListInMonthlyLtv($search_param)
	{
		$sql = "
SELECT
  date_tally
  , date_install
  , ltv
FROM
  kpi_ltv_monthly
WHERE
  1 = 1
";
	
		return $this->_getList($sql, $search_param);
	}
	
	public function createCsvInMonthlyLtv($list)
	{
		$log_name = 'kpi_daily_ltv';
		$file_path = KPIDATA_TMP_PATH;
	
		$old_umask = umask(0000);
		if (!is_dir(KPIDATA_TMP_PATH)) {
			mkdir(KPIDATA_TMP_PATH, 0777, true); // 後でターミナル上のjugmonユーザで削除できるように、0777にする。
		}
		umask($old_umask);
	
		$rand_num = mt_rand();
		$today_date = date('Ymd', time());
		$file_name = $log_name .'_' . $today_date . '_' . $rand_num;
		if (!$fp=@fopen($file_path . '/' . $file_name, 'a')){
			return false;
		}
	
		$title_list = array(
				'日付',
				'当月',
		);
		for($i=0;$i<=self::LTV_LIST_MONTHLIES;$i++){
			if (in_array($i, $this->DISP_ELAPSED_MONTH_DATE)) {
				if ($i == 0) continue;
				$title_list[] = ($i + 1) . 'ヵ月';
			}
		}
		$title_str = implode(',', $title_list);
		fwrite($fp, mb_convert_encoding($title_str, "Shift_JIS", "UTF-8") . "\r\n");
	
		$data_list = array();
		foreach ($list as $data_k => $data_v){
			$data_list = array();
			$data_list[] = $data_v['date_install'];
			foreach ($data_v['list'] as $k => $v){
				if (!in_array($k, $this->DISP_ELAPSED_MONTH_DATE)) {
					break;
				}
				$rate = $v['ltv'];
				if ($v['ltv'] !== '-') {
					$rate = $rate . '%';
				}
				$data_list[] = $rate;
			}
			$str = implode(',', $data_list);
			fwrite($fp, mb_convert_encoding($str, "Shift-JIS", "UTF-8") . "\r\n");
		}
		fclose($fp);
	
		return $file_name;
	}

	public function getCountInChargeLtv($search_param)
	{
		return parent::_getCount('kpi_purchase', $search_param);
	}
	
	public function getListInChargeLtv($search_param)
	{
		$sql = "
SELECT
  date_tally
  , date_install
  , total_price
  , rate
FROM
  kpi_purchase
WHERE
  1 = 1
";
	
		return $this->_getList($sql, $search_param);
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
		$sql .= 'ORDER BY date_install DESC, date_tally ASC';

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
			$where .= '  AND (? <= date_install AND date_install <= ?)';
			$param[] = $search_param['date_from'];
			$param[] = $search_param['date_to'];
			$param[] = $search_param['date_from'];
			$param[] = $search_param['date_to'];
		}
	
		if (isset($search_param['ua']))
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

	/**
	 * KPI LTV（日）データを一覧表示データの形に形成する
	 *
	 * @params array $ltv_data
	 * @params string $type
	 * @return array
	 */
	public function editKpiLtvDayList ($ltv_data, $type)
	{
		if ( !$ltv_data || !(is_array($ltv_data)) )
		{
			return null;
		}
	
		$view_cond = $this->_getViewDateCondition($type);

		$date_install = '';
		$count_install = 0;
		$cnt=0;

		for ($i=0; $i < $view_cond['list']; $i++)
		{
			if (in_array($i, $view_cond['elapsed_date']))
			{
				$list[$i]['ltv'] = '-';
			}
		}

		foreach($ltv_data as $k => $v)
		{
			if ($date_install != $v['date_install'])
			{
				if ($date_install != '')
				{
					$dt_date = new DateTime($date_install);
					$tmp_date = $dt_date->format('Ymd');
					$tmp_list = array(
							'list' => $list,
							'date_install' => $dt_date->format('Y年m月d日'),
							'date_install_day' => date('D', strtotime($date_install)),
					);
					$view_list[$tmp_date] = $tmp_list;
				}
				$date_install = $v['date_install'];
				$cnt = 0;
			}

			if ($cnt == 0 || isset($list[$cnt]))
			{
				$list[$cnt]['ltv'] = $v['ltv'];
				$list[$cnt] = array_merge($list[$cnt], $v);
			}
			$cnt++;
		}

		$dt_date = new DateTime($date_install);
		$tmp_date = $dt_date->format('Ymd');
		$tmp_list = array(
				'list' => $list,
				'date_install' => $dt_date->format('Y年m月d日'),
				'date_install_day' => date('D', strtotime($date_install)),
		);
		$view_list[$tmp_date] = $tmp_list;

		return $view_list;
	}

	
	/**
	 * KPI LTV（月）データを一覧表示データの形に形成する
	 *
	 * @params array $ltv_data
	 * @params string $type
	 * @return array
	 */
	public function editKpiLtvMonthlyList ($ltv_data, $type)
	{
		if ( !$ltv_data || !(is_array($ltv_data)) )
		{
			return null;
		}
	
		$view_cond = $this->_getViewDateCondition($type);
	
		$date_install = '';
		$count_install = 0;
		$cnt=0;
	
		for ($i=0; $i < $view_cond['list']; $i++)
		{
			if (in_array($i, $view_cond['elapsed_date']))
			{
				$list[$i]['ltv'] = '-';
			}
		}

		foreach($ltv_data as $k => $v)
		{
			if ($date_install != $v['date_install'])
			{
				if ($date_install != '')
				{
					$dt_date = new DateTime($date_install.'01');
					$tmp_date = $dt_date->format('Ym');
					$tmp_list = array(
							'list' => $list,
							'date_install' => $dt_date->format('Y年m月'),
					);
					$view_list[$tmp_date] = $tmp_list;
				}
				$date_install = $v['date_install'];
				$cnt = 0;
			}

			if ($cnt == 0 || isset($list[$cnt]))
			{
				$list[$cnt]['ltv'] = $v['ltv'];
				$list[$cnt] = array_merge($list[$cnt], $v);
			}
			$cnt++;
		}

		$dt_date = new DateTime($date_install.'01');
		$tmp_date = $dt_date->format('Ym');
		$tmp_list = array(
				'list' => $list,
				'date_install' => $dt_date->format('Y年m月'),
		);
		$view_list[$tmp_date] = $tmp_list;

		return $view_list;
	}

	/**
	 * 登録月別課金額&課金UUデータを一覧表示データの形に形成する
	 *
	 * @params array $ltv_data
	 * @params string $type
	 * @return array
	 */
	public function editKpiLtvChargeList ($ltv_data, $type)
	{
		if ( !$ltv_data || !(is_array($ltv_data)) )
		{
			return null;
		}
	
		$view_cond = $this->_getViewDateCondition($type);
	
		$date_install = '';
		$count_install = 0;
		$cnt=0;
	
		for ($i=0; $i < $view_cond['list']; $i++)
		{
			if (in_array($i, $view_cond['elapsed_date']))
			{
				$list[$i]['total_price'] = '-';
				$list[$i]['rate'] = '-';
			}
		}
	
		foreach($ltv_data as $k => $v)
		{
			if ($date_install != $v['date_install'])
			{
				if ($date_install != '')
				{
					$dt_date = new DateTime($date_install.'01');
					$tmp_date = $dt_date->format('Ym');
					$tmp_list = array(
							'list' => $list,
							'date_install' => $dt_date->format('Y年m月'),
					);
					$view_list[$tmp_date] = $tmp_list;
				}
				$date_install = $v['date_install'];
				$cnt = 0;
			}

			if ($cnt == 0 || isset($list[$cnt]))
			{
				$list[$cnt]['total_price'] = $v['total_price'];
				$list[$cnt]['rate'] = $v['rate'];
				$list[$cnt] = array_merge($list[$cnt], $v);
			}
			$cnt++;
		}

		$dt_date = new DateTime($date_install.'01');
		$tmp_date = $dt_date->format('Ym');
		$tmp_list = array(
				'list' => $list,
				'date_install' => $dt_date->format('Y年m月'),
		);
		$view_list[$tmp_date] = $tmp_list;

		return $view_list;
	}

	/**
	 * 表示に関連する日付条件取得
	 *
	 * @param string $type
	 * @return array
	 */
	private function _getViewDateCondition($type)
	{
		if ($type == 'd')
		{
			return array(
					'list' => self::LTV_LIST_DAYS,
					'elapsed_date' => $this->DISP_ELAPSED_DATE
			);
		}
		elseif($type == 'm')
		{
			return array(
					'list' => self::LTV_LIST_MONTHLIES,
					'elapsed_date' => $this->DISP_ELAPSED_MONTH_DATE
			);
		}
		else
		{
			return array(
					'list' => self::LTV_LIST_CHARGE,
					'elapsed_date' => $this->DISP_ELAPSED_CHARGE_DATE
			);
		}
	}
}
