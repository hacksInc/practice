<?php
/**
 *  Admin/Kpi/User/Hazard/Createfile.php
 *
 *  @author	 	$author}
 *  @access	 	public
 *  @package	Pp
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_user_hazard_createfile Form implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Form_AdminKpiUserHazardCreatefile extends Pp_Form_AdminKpiUserHazardBase
{
	var $form = array(
		'search_date_from' => array(
			'filter' => 'urldecode',
		),
		'search_date_to' => array(
			'filter' => 'urldecode',
		),
		'search_ua' => array(
			'filter' => 'urldecode',
		),
		'search_flg',
		'start',
		'type',	// l:level / s:stage
	);
}

/**
 *  admin_kpi_user_hazard_createfile action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiUserHazardCreatefile extends Pp_Action_AdminKpiUserHazardBase
{

	private $_date_from = null;
	private $_date_to = null;
	private $_type = null;

	private $_method_level = array(
		'count' => 'getLevelCount',
		'list' => 'getLevelList',
		'csv' => 'createLevelCsv',
	);

	private $_method_stage = array(
		'count' => 'getStageCount',
		'list' => 'getStageList',
		'csv' => 'createStageCsv',
	);

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_json_encrypt';
		}

		$this->_date_from = $this->af->get('search_date_from');
		$this->_date_to = $this->af->get('search_date_to');
		$this->_type = $this->af->get('type');

		$this->type = $this->_type;

		return null;
	}

	/**
	 *  admin_kpi_user_hazard_createfile action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	public function perform()
	{
		if ($this->af->get('search_flg') != '1')
		{
			$this->af->setApp('code', 100);
			return 'admin_json_encrypt';
		}

		$method = array();

		if ($this->_type == 'l')
		{
			$method = $this->_method_level;
		}
		else if ($this->_type == 's')
		{
			$method = $this->_method_stage;
		}

		$search_params = array(
			'date_from' => $this->_date_from,
			'date_to' => $this->_date_to,
			'ua' => $this->af->get('search_ua'),
		);

		$kpiuserhazard_m = $this->backend->getManager('KpiViewUserHazard');

		$count = $kpiuserhazard_m->{$method['count']}($search_params);

		if ($count === 0)
		{
			$this->af->setApp('code', 400);
			$this->af->setApp('err_msg', "対象となるデータが存在しません。");
			return 'admin_json_encrypt';
		}

		$list = parent::convertCsv($kpiuserhazard_m->{$method['list']}($search_params));

		$file_name = $kpiuserhazard_m->{$method['csv']}($list);

		if ($file_name === false)
		{
			$this->af->setApp('code', 400);
			$this->af->setApp('err_msg', 'ファイルの作成に失敗しました。');
			return 'admin_json_encrypt';
		}

		$this->af->setApp('code', 200);
		$this->af->setApp('file_name', $file_name);

		return 'admin_json_encrypt';
	}
}
