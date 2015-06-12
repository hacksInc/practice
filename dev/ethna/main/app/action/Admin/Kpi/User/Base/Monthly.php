<?php
/**
 *  Admin/Kpi/User/Base/Monthly.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_user_base_monthly Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiUserBaseMonthly extends Pp_Form_AdminKpiUserBaseBase
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
 *  admin_kpi_user_base_monthly action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiUserBaseMonthly extends Pp_Action_AdminKpiUserBaseBase
{

	protected $type = 'm';

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_kpi_user_base_monthly';
		}

		return null;
	}

	/**
	 *  admin_kpi_user_base_monthly action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	public function perform()
	{
		$date_from = $this->af->get('search_date_from');
		$date_to = $this->af->get('search_date_to');

		$search_params = array(
			'date_from' => str_replace('-', '', $date_from),
			'date_to' => str_replace('-', '', $date_to),
			'ua' => $this->af->get('search_ua'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		if ($search_flg != '1')
		{
			return 'admin_kpi_user_base_monthly';
		}

		$kpiusershop_m = $this->backend->getManager('KpiViewUserBase');
		$list = parent::convert($kpiusershop_m->getListInMonthly($search_params));

		$this->af->setApp('list', $list);

		return 'admin_kpi_user_base_monthly';
	}
}
