<?php
/**
 *  Admin/Kpi/User/Hazard/Stage.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_user_hazard_stage Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiUserHazardStage extends Pp_Form_AdminKpiUserHazardBase
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
 *  admin_kpi_user_hazard_stage action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiUserHazardStage extends Pp_Action_AdminKpiUserHazardBase
{

	protected $type = 's';

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_kpi_user_hazard_stage';
		}

		return null;
	}

	/**
	 *  admin_kpi_user_hazard_stage action implementation.
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
			return 'admin_kpi_user_hazard_stage';
		}

		$kpiuserhazard_m = $this->backend->getManager('KpiViewUserHazard');
		$list = parent::convert($kpiuserhazard_m->getStageList($search_params));

		$this->af->setApp('list', $list);

		return 'admin_kpi_user_hazard_stage';
	}
}
