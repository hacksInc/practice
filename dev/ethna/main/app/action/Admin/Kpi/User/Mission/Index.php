<?php
/**
 *  Admin/Kpi/User/Mission/Index.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_user_mission_index Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiUserMissionIndex extends Pp_Form_AdminKpiUserMissionBase
{
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_ua',
		'search_area_id',
		'search_flg',
		'start',
	);

	public function setFormDef_PreHelper()
	{
		$kpiusermission_m = $this->backend->getManager('KpiViewUserMission');
		$this->form['search_area_id']['option'] = $kpiusermission_m->getArea();
	}
}

/**
 *  admin_kpi_user_mission_index action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiUserMissionIndex extends Pp_Action_AdminKpiUserMissionBase
{

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_kpi_user_mission_index';
		}

		return null;
	}

	/**
	 *  admin_kpi_user_mission_index action implementation.
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
			'area_id' => $this->af->get('search_area_id'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		if ($search_flg != '1')
		{
			return 'admin_kpi_user_mission_index';
		}

		$kpiusermission_m = $this->backend->getManager('KpiViewUserMission');

		$list = parent::convert($kpiusermission_m->getList($search_params));

		$this->af->setApp('list', $list);

		return 'admin_kpi_user_mission_index';
	}
}
