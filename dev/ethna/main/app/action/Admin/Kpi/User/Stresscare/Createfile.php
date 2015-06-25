<?php
/**
 *  Admin/Kpi/User/Stresscare/Createfile.php
 *
 *  @author	 	$author}
 *  @access	 	public
 *  @package	Pp
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_kpi_user_Stresscare_createfile Form implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Form_AdminKpiUserStresscareCreatefile extends Pp_Form_AdminKpiUserStresscare
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
		'search_area_id' => array(
			'filter' => 'urldecode',
		),
		'search_flg',
		'start',
	);
}

/**
 *  admin_kpi_user_Stresscare_createfile action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiUserStresscareCreatefile extends Pp_Action_AdminKpiUserStresscare
{

	private $_date_from = null;
	private $_date_to = null;

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_json_encrypt';
		}

		$this->_date_from = $this->af->get('search_date_from');
		$this->_date_to = $this->af->get('search_date_to');

		return null;
	}

	/**
	 *  admin_kpi_user_Stresscare_createfile action implementation.
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

		$search_params = array(
			'date_from' => $this->_date_from,
			'date_to' => $this->_date_to,
			'ua' => $this->af->get('search_ua'),
		);

		$kpiuser_m = $this->backend->getManager('KpiViewUser');

		$count = $kpiuser_m->getStresscareCount($search_params);

		if ($count === 0)
		{
			$this->af->setApp('code', 400);
			$this->af->setApp('err_msg', "対象となるデータが存在しません。");
			return 'admin_json_encrypt';
		}

		$list = $this->convertCsv($kpiuser_m->getStresscareList($search_params));

		$file_name = $kpiuser_m->createStresscareCsv($list);

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
