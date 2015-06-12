<?php
/**
 *  Admin/Kpi/User/Area/Progress/Csv.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_kpi_user_area_* で共通のアクションフォーム定義 */
class Pp_Form_AdminKpiUserAreaProgress extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'search_ua' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_SELECT,   // Form type
				'option'      => array(
					'' => '',
					'0' => 'ALL',
					'1' => 'iOS',
					'2' => 'Android',
				),
				'name'        => '集計項目', // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,             // Required Option(true/false)
				//'min'         => 30000,            // Minimum value
				//'max'         => 40000,            // Maximum value
			),
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}

		parent::__construct($backend);
	}
}

/**
 *  admin_kpi_user_area_progress_csv Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiUserAreaProgressCsv extends Pp_Form_AdminKpiUserAreaProgress
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_ua',
		'search_quest_id',
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
 *  admin_kpi_user_area_progress_csv action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiUserAreaProgressCsv extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_log_cs_area_csv Action.
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
			return 'admin_kpi_user_area_progress_index';
		}

	}

	/**
	 *  admin_kpi_user_area_progress_csv action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_kpi_user_area_progress_csv';
	}
}
