<?php
/**
 *  Admin/Kpi/User/Mission/Fail/Base.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Base.php';

class Pp_Form_AdminKpiUserMissionFailBase extends Pp_Form_AdminKpiUserMissionBase
{
}

class Pp_Action_AdminKpiUserMissionFailBase extends Pp_Action_AdminKpiUserMissionBase
{

	protected function convert($data)
	{
		$result = array();

		if (empty($data)) return $result;

		$kpiusermission_m = $this->backend->getManager('KpiViewUserMission');

		$master = $kpiusermission_m->getStageByArea();

		foreach ($data as $key => $item)
		{
			$stage_name = (isset($master[$item['area_id']])) ? $master[$item['area_id']] : '';

			$_item = array(
				'stage_name' => $stage_name,
				'area_name' => $item['area_name'],
				'mission_name' => $item['mission_name'],
				'uu_fail' => $item['uu_fail'],
				'battery' => $item['battery'],
				'life' => $item['life'],
				'timeup' => $item['timeup'],
				'zone1' => $item['zone1'],
				'zone2' => $item['zone2'],
				'zone3' => $item['zone3'],
				'zone4' => $item['zone4'],
				'zone5' => $item['zone5'],
			);

			$result[] = $_item;
		}

		return $result;
	}

	protected function convertCsv($data)
	{
		$result = array();

		$_data = $this->convert($data);

		foreach ($_data as $key => $item)
		{
			unset($item['_clear_rate']);

			$result[] = $item;
		}

		return $result;
	}
}
