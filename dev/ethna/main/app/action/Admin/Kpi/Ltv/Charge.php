<?php
/**
 *  Admin/Kpi/Ltv/Charge.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_ltv_charge Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiLtvCharge extends Pp_Form_AdminKpiLtvBase
{
	private $_date_from = null;
	private $_date_to = null;

	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_ua',
		'search_flg',
		'start',
		'type',	// d:daily / m:monthly / c:charge
	);
}

/**
 *  admin_kpi_ltv_charge action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiLtvCharge extends Pp_Action_AdminKpiLtvBase
{
	public function prepare()
	{
		$search_flg = $this->af->get('search_flg');
		if ($search_flg == '1')
		{
			if ($this->af->validate() > 0)
			{
				return 'admin_kpi_ltv_charge';
			}
		}

		$this->_date_from = $this->af->get('search_date_from');
		$this->_date_to = $this->af->get('search_date_to');
		$this->type = $this->af->get('type');

		return null;
	}

	/**
	 *  admin_kpi_ltv_charge action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	public function perform()
	{
		$this->_date_from = str_replace('-', '', $this->_date_from);
		$this->_date_to = str_replace('-', '', $this->_date_to);

		$search_params = array(
			'date_from' => $this->_date_from,
			'date_to' => $this->_date_to,
			'ua' => $this->af->get('search_ua'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		if ($search_flg != '1')
		{
			return 'admin_kpi_ltv_charge';
		}

		$kpiltv_m = $this->backend->getManager('KpiViewLtv');
		$list = $kpiltv_m->getListInChargeLtv($search_params);

		if (!empty($list))
		{
			$list = $kpiltv_m->editKpiLtvChargeList($list, $this->type);
		}

		$this->af->setApp('list', $list);
		$this->af->setApp('disp_elapsed_date', $kpiltv_m->DISP_ELAPSED_CHARGE_DATE);

		return 'admin_kpi_ltv_charge';
	}
}
