<?php
/**
 *  Admin/Kpi/Gacha/User/Createfile.php
 *
 *  @author	 	$author}
 *  @access	 	public
 *  @package	Pp
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_gacha_user_createfile Form implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Form_AdminKpiGachaUserCreatefile extends Pp_Form_AdminKpiGachaUserBase
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
	
	/**
	 *  Form input value convert filter : sample
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	function _filter_urldecode($value)
	{
		return urldecode($value);
	}
}

/**
 *  admin_kpi_gacha_user_createfile action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiGachaUserCreatefile extends Pp_Action_AdminKpiGachaUserBase
{

	private $_date_from = null;
	private $_date_to = null;

	function prepare()
	{
		if ($this->af->validate() > 0)
		{
			$this->af->setApp('code', 400);
			$err_msg = null;
			foreach ($this->ae->getMessageList() as $v)
			{
				$err_msg .= $v.PHP_EOL;
			}
			$this->af->setApp('err_msg', $err_msg);

			return 'admin_json_encrypt';
		}

		$this->_date_from = $this->af->get('search_date_from');
		$this->_date_to = $this->af->get('search_date_to');

		return null;
	}

	/**
	 *  admin_kpi_gacha_user_createfile action implementation.
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

		$gacha_ids = null;
		if ($_GET['search_gacha_ids'][0] != 'null')
		{
			$gacha_ids = urldecode($_GET['search_gacha_ids'][0]);
			$gacha_ids = split(',', $gacha_ids);
		}

		$search_params = array(
			'date_from' => $this->_date_from,
			'date_to' => $this->_date_to,
			'ua' => $this->af->get('search_ua'),
			'gacha_ids' => $gacha_ids,
		);

		$kpigacha_m = $this->backend->getManager('KpiViewGacha');
		$count = $kpigacha_m->getCountInGachaUser($search_params);
		if ($count === 0)
		{
			$this->af->setApp('code', 400);
			$this->af->setApp('err_msg', "対象となるデータが存在しません。");
			return 'admin_json_encrypt';
		}

		$list = $kpigacha_m->getListInGachaUser($search_params);

		$file_name = $kpigacha_m->createCsvInGachaUser($list);
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
