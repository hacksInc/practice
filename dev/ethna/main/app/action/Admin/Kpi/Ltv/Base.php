<?php
/**
 *  Admin/Kpi/Ltv/Base.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

class Pp_Form_AdminKpiLtvBase extends Pp_AdminActionForm
{
	private $_search_date_range = array(
			'd' => array('range' => 30, 'label' => '30日'),
			'm' => array('range' => 180, 'label' => '6ヵ月'),
			'c' => array('range' => 180, 'label' => '6ヵ月'),
	);
	
	public function __construct(&$backend)
	{
		$form_template = array(
			'search_date_from' => array(
				// Form definition
				'type'		=> VAR_TYPE_DATETIME,	// Input type
				'form_type' => FORM_TYPE_TEXT,		// Form type
				'name'		=> '検索日(開始日)',	// Display name

				//  Validator (executes Validator by written order.)
				'required'	=> true,				// Required Option(true/false)
				'custom'    => '_validity_date_range',
			),
			'search_date_to' => array(
				// Form definition
				'type'		=> VAR_TYPE_DATETIME,	// Input type
				'form_type'	=> FORM_TYPE_TEXT,		// Form type
				'name'		=> '検索日(終了日)',	// Display name

				//  Validator (executes Validator by written order.)
				'required'	=> true,				// Required Option(true/false)
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
	
	/**
	 *  Form elements validate
	 *
	 *  @access public
	 *  @return void
	 */
	function _validity_date_range($name)
	{
		$date_from = $this->form_vars['search_date_from'];
		$date_to = $this->form_vars['search_date_to'];
	
		$ts_date_from = strtotime(date($date_from));
		$ts_date_to = strtotime(date($date_to));

		if ($ts_date_from > $ts_date_to)
		{
			$this->ae->add(null, '開始日と終了日を正しく設定してください。');
		}
		else
		{
			$type = $this->form_vars['type'];
			$diff_days = ($ts_date_to - $ts_date_from) / (24*3600);
			if ($diff_days > $this->_search_date_range[$type]['range']) {
				$this->ae->add(null, '期間は'.$this->_search_date_range[$type]['label'].'以内で指定してください。.');
			}
		}
	}
}

class Pp_Action_AdminKpiLtvBase extends Pp_AdminActionClass
{

	protected $type = null;

	protected function convert($data)
	{
		$result = array();

		if (empty($data)) return $result;

		$total_price = 0;
		$uu_purchase = 0;

		$date_tally = null;

		foreach ($data as $key => $item)
		{
			$total_price = $total_price + (int)$item['total_price'];
			$uu_purchase = $uu_purchase + (int)$item['uu_purchase'];

			$item['is_total'] = 0;

			$date_tally = new DateTime($item['date_tally']);
			$item['_date_tally'] = $date_tally->format('Y年m月d日');

			$key_au = 'dau';

			if ($this->type == 'm')
			{
				$date_tally = new DateTime($item['date_tally'] . '01');
				$item['_date_tally'] = $date_tally->format('Y年m月');

				$key_au = 'mau';
			}

			$item['_rate'] = round($item['rate'], 4) * 100;

			$result[] = $item;

			if (
				(isset($data[$key + 1]) && $item['date_tally'] !== $data[$key + 1]['date_tally']) ||
				!isset($data[$key + 1])
			)
			{
				$rate = $uu_purchase / $item[$key_au];

				$_item = array(
					'date_tally' => $item['date_tally'],
					'_date_tally' => $item['_date_tally'],
					'name' => '総計',
					'price' => '-',
					'amount' => '-',
					'total_price' => $total_price,
					$key_au => $item[$key_au],
					'uu_purchase' => $uu_purchase,
					'rate' => $rate,
					'_rate' => round($rate, 4) * 100,
					'is_total' => 1,
				);

				$result[] = $_item;

				$total_price = 0;
				$uu_purchase = 0;
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
			unset($item['_date_tally']);
			unset($item['_rate']);
			unset($item['is_total']);

			$result[] = $item;
		}

		return $result;
	}
}
