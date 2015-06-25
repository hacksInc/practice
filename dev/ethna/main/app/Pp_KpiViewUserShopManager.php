<?php
/**
 *  Pp_KpiViewUserShopManager.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewUserShopManager
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_KpiViewUserShopManager extends Pp_KpiViewManager
{

	public function getCountInDaily($search_param)
	{
		return parent::_getCount('kpi_user_shop_daily', $search_param);
	}

	public function getListInDaily($search_param)
	{
		$sql = "
SELECT
  date_tally
  , name
  , price
  , amount
  , total_price
  , dau
  , uu_purchase
  , rate
FROM
  kpi_user_shop_daily
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createCsvInDaily($data)
	{
		$csv_title = array(
			'日付',
			'アイテム名',
			'単価',
			'販売数',
			'総額',
			'DAU',
			'購入UU',
			'課金率',
		);

		return parent::createCsv($csv_title, $data, 'kpi_item_sales_daily');
	}

	public function getCountInMonthly($search_param)
	{
		return parent::_getCount('kpi_user_shop_monthly', $search_param);
	}

	public function getListInMonthly($search_param)
	{
		$sql = "
SELECT
  date_tally
  , name
  , price
  , amount
  , total_price
  , mau
  , uu_purchase
  , rate
FROM
  kpi_user_shop_monthly
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createCsvInMonthly($data)
	{
		$csv_title = array(
			'日付',
			'アイテム名',
			'単価',
			'販売数',
			'総額',
			'MAU',
			'購入UU',
			'課金率',
		);

		return parent::createCsv($csv_title, $data, 'kpi_item_sales_monthly');
	}

	public function getCountInUse($search_param)
	{
		return parent::_getCount('kpi_user_item', $search_param);
	}

	public function getListInUse($search_param)
	{
		$sql = "
SELECT
  date_tally
  , name
  , count_used
  , count_stock
  , count_stock_uu_login
FROM
  kpi_user_item
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createCsvInUse($data)
	{
		$csv_title = array(
			'日付',
			'アイテム名',
			'使用数',
			'総在庫数',
			'ログインUU限定総在庫数',
		);

		return parent::createCsv($csv_title, $data, 'kpi_user_item');
	}


	public function getSellItem()
	{
		$result = array();

		$db = $this->backend->getDB('m_r');

$sql = "
SELECT
  m1.sell_id
  , m1.item_id
  , m1.num
  , m2.name_ja
FROM
  m_sell_item m1
  INNER JOIN m_sell_list m2
    ON m1.sell_id = m2.sell_id
";

		$data = $db->GetAll($sql);

		if (!$data) return $result;

		foreach ($data as $item)
		{
			$result[$item['sell_id']] = $item;
		}

		return $result;
	}

	public function getSumAccountingInDaily($search_param)
	{
		$db = $this->backend->getDB('logex_r');

		$sql = "
SELECT
  count(DISTINCT pp_id) uu_purchase
  , sell_id
  , price
  , SUM(price) total_price
  , date_created
FROM
  log_accounting
WHERE
  DATE_FORMAT(date_created,'%Y%m%d')  = DATE_FORMAT(?,'%Y%m%d')
GROUP BY
  sell_id
";
		$data = $db->GetAll($sql, $search_param);

		if (!$data)
		{
			return array();
		}
		else
		{
			return $data;
		}
	}

	public function insertKpiInDaily($data)
	{
		$db = $this->backend->getDB('logex_r');

		$sql = "
INSERT
INTO kpi_user_shop_daily(
  ua
  , date_tally
  , sell_id
  , name
  , price
  , amount
  , total_price
  , dau
  , uu_purchase
  , rate
  , date_created
  , date_modified
)
VALUES (
  ?
  , ?
  , ?
  , ?
  , ?
  , ?
  , ?
  , ?
  , ?
  , ?
  , NOW()
  , NOW()
)
";

		try
		{
			$db->begin();

			foreach ($data as $item)
			{
				$param = array(
					$item['ua'],
					$item['date_tally'],
					$item['sell_id'],
					$item['name'],
					$item['price'],
					$item['amount'],
					$item['total_price'],
					$item['dau'],
					$item['uu_purchase'],
					$item['rate'],
				);

				$result = $db->execute($sql, $param);

				if (!$result || Ethna::isError($result))
				{
					$db->rollback();
					return;
				}
			}

			$db->commit();
		}
		catch (Exception $ex)
		{
			$db->rollback();
			throw $ex;
		}

	}
}
