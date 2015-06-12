<?php
/**
 *  Admin/Kpi/User/Stresscare/Index.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

class Pp_Form_AdminKpiUserStresscare extends Pp_AdminActionForm
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

class Pp_Action_AdminKpiUserStresscare extends Pp_AdminActionClass
{

	protected function convert($data)
	{
		$result = array();

		if (empty($data)) return $result;

		$kpiuser_m = $this->backend->getManager('KpiViewUser');
		$master = $kpiuser_m->getCharacter();

		$count_ex = 0;
		$count_therapy = 0;

		$date_tally = null;

		foreach ($data as $key => $item)
		{
			$date_tally = new DateTime($item['date_tally']);
			$item['_date_tally'] = $date_tally->format('Y年m月d日');

			$item['name'] = (isset($master[$item['character_id']])) ? $master[$item['character_id']] : '';

			$count_ex = $count_ex + (int)$item['count_ex'];
			$count_therapy = $count_therapy + (int)$item['count_therapy'];

			$item['is_total'] = 0;

			$result[] = $item;

			if (
				(isset($data[$key + 1]) && $item['date_tally'] !== $data[$key + 1]['date_tally']) ||
				!isset($data[$key + 1])
			)
			{
				$_item = array(
					'date_tally' => $item['date_tally'],
					'_date_tally' => $item['_date_tally'],
					'name' => '総計',
					'count_ex' => $count_ex,
					'count_therapy' => $count_therapy,
					'is_total' => 1,
				);

				$result[] = $_item;

				$count_ex = 0;
				$count_therapy = 0;
			}
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
			$item['character_id'] = $item['name'];
			unset($item['_date_tally']);
			unset($item['name']);
			unset($item['is_total']);

			$result[] = $item;
		}

		return $result;
	}
}

/**
 *  admin_kpi_user_stresscare_index Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiUserStresscareIndex extends Pp_Form_AdminKpiUserStresscare
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
 *  admin_kpi_user_stresscare_index action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiUserStresscareIndex extends Pp_Action_AdminKpiUserStresscare
{

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_kpi_user_stresscare_index';
		}

		return null;
	}

	/**
	 *  admin_kpi_user_stresscare_index action implementation.
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
			return 'admin_kpi_user_stresscare_index';
		}

		$kpiuser_m = $this->backend->getManager('KpiViewUser');

		$list = parent::convert($kpiuser_m->getStresscareList($search_params));

		$this->af->setApp('list', $list);

		return 'admin_kpi_user_stresscare_index';
	}
}
