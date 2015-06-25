<?php
/**
 *  Admin/Kpi/User/Mission/Base.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

class Pp_Form_AdminKpiUserMissionBase extends Pp_AdminActionForm
{

	public function __construct(&$backend)
	{
		$form_template = array(
			'search_date_from' => array(
				// Form definition
				'type'		=> VAR_TYPE_DATETIME,	// Input type
				'form_type' => FORM_TYPE_TEXT,		// Form type
				'name'		=> '検索日(開始日)',	// Display name

				//  Validator (executes Validator by written order.)
				'required'	=> false,				// Required Option(true/false)
			),
			'search_date_to' => array(
				// Form definition
				'type'		=> VAR_TYPE_DATETIME,	// Input type
				'form_type'	=> FORM_TYPE_TEXT,		// Form type
				'name'		=> '検索日(終了日)',	// Display name

				//  Validator (executes Validator by written order.)
				'required'	=> false,				// Required Option(true/false)
			),
			'search_ua' => array(
				// Form definition
				'type'		=> VAR_TYPE_STRING,		// Input type
				'form_type'	=> FORM_TYPE_SELECT,	// Form type
				'option' => array(
					'0' => 'ALL',
					'1' => 'iOS',
					'2' => 'Android',
				),
				'name'		=> '集計項目', 			// Display name

				//  Validator (executes Validator by written order.)
				'required'	=> false,				// Required Option(true/false)
			),
			'search_area_id' => array(
				// Form definition
				'type'		=> VAR_TYPE_STRING,		// Input type
				'form_type'	=> FORM_TYPE_SELECT,	// Form type
				'option' => array(),
				'name'		=> 'エリアID', 			// Display name

				//  Validator (executes Validator by written order.)
				'required'	=> false,				// Required Option(true/false)
			),
		);

		foreach ($form_template as $key => $value)
		{
			$this->form_template[$key] = $value;
		}
		parent::__construct($backend);
	}
}

class Pp_Action_AdminKpiUserMissionBase extends Pp_AdminActionClass
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
				'uu_start' => $item['uu_start'],
				'uu_clear' => $item['uu_clear'],
				'clear_rate' => $item['clear_rate'],
				'_clear_rate' => round($item['clear_rate'], 4) * 100,
				'count_challenge' => $item['count_challenge'],
				'count_clear' => $item['count_clear'],
				'count_spare_domi_total' => $item['count_spare_domi_total'],
				'count_spare_domi_unused' => $item['count_spare_domi_unused'],
				'count_spare_domi_used' => $item['count_spare_domi_used'],
				'count_best' => $item['count_best'],
				'count_normal' => $item['count_normal'],
				'count_fail' => $item['count_fail'],
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
