<?php
/**
 *  Admin/Kpi/User/Hazard/Base.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

class Pp_Form_AdminKpiUserHazardBase extends Pp_AdminActionForm
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
		);

		foreach ($form_template as $key => $value)
		{
			$this->form_template[$key] = $value;
		}
		parent::__construct($backend);
	}
}

class Pp_Action_AdminKpiUserHazardBase extends Pp_AdminActionClass
{

	protected $type = null;

	protected function convert($data)
	{
		$result = array();

		if (empty($data)) return $result;

		$kpiuserhazard_m = $this->backend->getManager('KpiViewUserHazard');
		$master = $kpiuserhazard_m->getStage();

		$date_tally = null;

		foreach ($data as $key => $item)
		{
			$date_tally = new DateTime($item['date_tally']);
			$item['_date_tally'] = $date_tally->format('Y年m月d日');

			if ($this->type === 's')
			{
				$item = array(
					'date_tally' => $item['date_tally'],
					'_date_tally' => $item['_date_tally'],
					'stage_id' => $item['stage_id'],
					'stage_name' => (isset($master[$item['stage_id']])) ? $master[$item['stage_id']] : '',
					'count_hazard' => $item['count_hazard'],
				);
			}

			$result[] = $item;
		}

		return $result;
	}

	protected function convertCsv($data)
	{
		$result = array();

		$_data = $this->convert($data);

		foreach ($_data as $key => $item)
		{
			$item['date_tally'] = $item['_date_tally'];
			unset($item['_date_tally']);

			if ($this->type === 's')
			{
				unset($item['stage_id']);
			}

			$result[] = $item;
		}

		return $result;
	}
}
