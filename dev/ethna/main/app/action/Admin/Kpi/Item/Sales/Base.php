<?php
/**
 *  Admin/Kpi/Item/Sales/Base.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

class Pp_Form_AdminKpiItemSalesBase extends Pp_AdminActionForm
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

class Pp_Action_AdminKpiItemSalesBase extends Pp_AdminActionClass
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
