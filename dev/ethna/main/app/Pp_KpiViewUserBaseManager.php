<?php
/**
 *  Pp_KpiViewUserBaseManager.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewUserBaseManager
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_KpiViewUserBaseManager extends Pp_KpiViewManager
{

	public function getCountInDaily($search_param)
	{
		return parent::_getCount('kpi_base_daily', $search_param);
	}

	public function getListInDaily($search_param)
	{
		$sql = "
SELECT
  date_tally
  , count_regist
  , dau
  , uu_purchase
  , rate
  , total_price
  , arppu
FROM
  kpi_base_daily
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createCsvInDaily($data)
	{
		$csv_title = array(
			'検索月',
			'新規登録アカウント作成数',
			'DAU',
			'課金UU',
			'課金率',
			'販売額',
			'ARPPU',
		);

		return parent::createCsv($csv_title, $data, 'kpi_base_daily');
	}

	public function getCountInMonthly($search_param)
	{
		return parent::_getCount('kpi_base_monthly', $search_param);
	}

	public function getListInMonthly($search_param)
	{
		$sql = "
SELECT
  date_tally
  , count_regist
  , mau
  , uu_purchase
  , rate
  , total_price
  , arppu
FROM
  kpi_base_monthly
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createCsvInMonthly($data)
	{
		$csv_title = array(
			'検索月',
			'新規登録アカウント作成数',
			'MAU',
			'課金UU',
			'課金率',
			'販売額',
			'ARPPU',
		);

		return parent::createCsv($csv_title, $data, 'kpi_base_monthly');
	}
}
