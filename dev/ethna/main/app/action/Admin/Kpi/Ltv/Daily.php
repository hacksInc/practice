<?php
/**
 *  Admin/Kpi/Ltv/Daily.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_ltv_daily Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiLtvDaily extends Pp_Form_AdminKpiLtvBase
{
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
 *  admin_kpi_ltv_daily action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiLtvDaily extends Pp_Action_AdminKpiLtvBase
{
	public function prepare()
	{
		$search_flg = $this->af->get('search_flg');
		if ($search_flg == '1')
		{
			if ($this->af->validate() > 0)
			{
				return 'admin_kpi_ltv_daily';
			}
		}
		return null;
	}

	/**
	 *  admin_kpi_ltv_daily action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	public function perform()
	{
		$date_from = $this->af->get('search_date_from');
		$date_to = $this->af->get('search_date_to');
		$this->type = $this->af->get('type');

		$search_params = array(
			'date_from' => $date_from,
			'date_to' => $date_to,
			'ua' => $this->af->get('search_ua'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		if ($search_flg != '1')
		{
			return 'admin_kpi_ltv_daily';
		}

		$kpiltv_m = $this->backend->getManager('KpiViewLtv');
		$list = $kpiltv_m->getListInDailyLtv($search_params);
		if (!empty($list))
		{
			$list = $kpiltv_m->editKpiLtvDayList($list, $this->type);
		}

		$this->af->setApp('list', $list);
		$this->af->setApp('disp_elapsed_date', $kpiltv_m->DISP_ELAPSED_DATE);

		return 'admin_kpi_ltv_daily';
	}
}
