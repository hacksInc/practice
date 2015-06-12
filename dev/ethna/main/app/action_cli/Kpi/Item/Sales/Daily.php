<?php
/**
 *  KPI集計を行う
 *
 *  @author		{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_CliActionClass.php';

class Pp_Cli_Form_KpiItemSalesDaily extends Pp_ActionForm
{
	var $form = array();
}

class Pp_Cli_Action_KpiItemSalesDaily extends Pp_CliActionClass
{

	private $_master_sell = null;

	public function prepare()
	{
		return null;
	}

	public function perform()
	{
		$this->backend->logger->log(LOG_INFO, '******************************** start');

		$tran_date = $_REQUEST['argv1'];

		$kpiusershop_m = $this->backend->getManager('KpiViewUserShop');

		$this->_master_sell = $kpiusershop_m->getSellItem();

		$data = $this->_convert($kpiusershop_m->getSumAccountingInDaily($tran_date));

		$kpiusershop_m->insertKpiInDaily($data);

		$this->backend->logger->log(LOG_INFO, '******************************** end');

		exit(0);
	}

	private function _convert($data)
	{
		$result = array();

		foreach ($data as $key => $item)
		{
			$_item = $this->_master_sell[$item['sell_id']];
			$_item_id = $_item['item_id'] . '_' . $_item['num'];

			if (!isset($result[$_item_id]))
			{
				$result[$_item_id] = $item;
				$result[$_item_id]['date_tally'] = $item['date_created'];
				$result[$_item_id]['name'] = $_item['name_ja'];
				continue;
			}

			$result[$_item_id]['uu_purchase'] = (int)$result[$_item_id]['uu_purchase'] + (int)$item['uu_purchase'];
			$result[$_item_id]['total_price'] = (int)$result[$_item_id]['total_price'] + (int)$item['total_price'];
		}

		foreach ($result as $key => $item)
		{
			$result[$key]['amount'] = (int)$item['total_price'] / (int)$item['price'];

			// TODO
			$result[$key]['ua'] = '1';
			$result[$key]['dau'] = '0';
			$result[$key]['rate'] = '0.000000000000';
		}

		return $result;
	}
}
