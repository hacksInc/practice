<?php
/**
 *  Admin/Kpi/Gacha/Rate/Createfile.php
 *
 *  @author	 	$author}
 *  @access	 	public
 *  @package	Pp
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_gacha_rate_createfile Form implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Form_AdminKpiGachaRateCreatefile extends Pp_Form_AdminKpiGachaRateBase
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
	);
}

/**
 *  admin_kpi_gacha_rate_createfile action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiGachaRateCreatefile extends Pp_Action_AdminKpiGachaRateBase
{

	private $_date_from = null;
	private $_date_to = null;

	function prepare()
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
	 *  admin_kpi_gacha_rate_createfile action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		if ($this->af->get('search_flg') != '1')
		{
			$this->af->setApp('code', 100);
			return 'admin_json_encrypt';
		}

		$this->_date_from = str_replace('-', '', $this->_date_from);
		$this->_date_to = str_replace('-', '', $this->_date_to);

		$search_params = array(
			'date_from' => $this->_date_from,
			'date_to' => $this->_date_to,
			'ua' => $this->af->get('search_ua'),
		);

		$kpigacha_m = $this->backend->getManager('KpiViewGacha');

		$count = $kpigacha_m->getCountInGachaRate($search_params);

		if ($count === 0)
		{
			$this->af->setApp('code', 400);
			$this->af->setApp('err_msg', "対象となるデータが存在しません。");
			return 'admin_json_encrypt';
		}

		$list = $kpigacha_m->getListInGachaRate($search_params);

		$file_name = $kpigacha_m->createCsvInGachaRate($list);
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
