<?php
/**
 *  Admin/Kpi/Item/Use/Index.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_item_use_index Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiItemUseIndex extends Pp_Form_AdminKpiItemUseBase
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
 *  admin_kpi_item_use_index action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiItemUseIndex extends Pp_Action_AdminKpiItemUseBase
{

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_kpi_item_use_index';
		}

		return null;
	}

	/**
	 *  admin_kpi_item_use_index action implementation.
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
			return 'admin_kpi_item_use_index';
		}

		$kpiusershop_m = $this->backend->getManager('KpiViewUserShop');
		$list = $kpiusershop_m->getListInUse($search_params);

		$this->af->setApp('list', $list);

		return 'admin_kpi_item_use_index';
	}
}
