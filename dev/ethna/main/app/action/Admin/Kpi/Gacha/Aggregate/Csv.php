<?php
/**
 *  Admin/Kpi/Gacha/Aggregate/Csv.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_kpi_user_area_* で共通のアクションフォーム定義 */
class Pp_Form_AdminKpiGachaAggregate extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'search_gacha_ids' => array(
				'type'        => array(VAR_TYPE_INT),     // Input type
			),
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}

		parent::__construct($backend);
	}
}

/**
 *  admin_kpi_gacha_aggregate_csv Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiGachaAggregateCsv extends Pp_Form_AdminKpiGachaAggregate
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_gacha_ids',
		'search_flg',
	);

	/**
	 *  Form input value convert filter : sample
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	/*
	function _filter_sample($value)
	{
		//  convert to upper case.
		return strtoupper($value);
	}
	*/
}

/**
 *  admin_kpi_user_area_progress2_csv action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiGachaAggregateCsv extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_kpi_gacha_aggregate_csv Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{

		// アクセス制御
		if ($this->must_login && $this->must_permission) {
			$ret = $this->permit();
			if ($ret) {
				return $ret;
			}
		}

		if ($this->af->validate() > 0) {
			return 'admin_kpi_gacha_aggregate_index';
		}

	}

	/**
	 *  admin_kpi_gacha_aggregate_csv action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_kpi_gacha_aggregate_csv';
	}
}
