<?php
/**
 *  Admin/Kpi/Item/Sales/Daily.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_item_sales_daily Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiItemSalesDaily extends Pp_Form_AdminKpiItemSalesBase
{
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_ua',
		'search_flg',
		'start',
	);
}

/**
 *  admin_kpi_item_sales_daily action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiItemSalesDaily extends Pp_Action_AdminKpiItemSalesBase
{

	protected $type = 'd';

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_kpi_item_sales_daily';
		}

		return null;
	}

	/**
	 *  admin_kpi_item_sales_daily action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	public function perform()
	{
		$date_from = $this->af->get('search_date_from');
		$date_to = $this->af->get('search_date_to');

		$search_params = array(
			'date_from' => $date_from,
			'date_to' => $date_to,
			'ua' => $this->af->get('search_ua'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		if ($search_flg != '1')
		{
			return 'admin_kpi_item_sales_daily';
		}

		$kpiusershop_m = $this->backend->getManager('KpiViewUserShop');
		$list = parent::convert($kpiusershop_m->getListInDaily($search_params));

		$this->af->setApp('list', $list);

		return 'admin_kpi_item_sales_daily';
	}
}
