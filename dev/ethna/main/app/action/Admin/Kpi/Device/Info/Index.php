<?php
/**
 *  Admin/Kpi/Device/info/Index.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_device_info_index Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiDeviceInfoIndex extends Pp_Form_AdminKpiDeviceInfoBase
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
 *  admin_kpi_device_info_index action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiDeviceInfoIndex extends Pp_Action_AdminKpiDeviceInfoBase
{
	private $_date_from = null;
	private $_date_to = null;

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_kpi_device_info_index';
		}
		
		$this->_date_from = $this->af->get('search_date_from');
		$this->_date_to = $this->af->get('search_date_to');

		return null;
	}

	/**
	 *  admin_kpi_device_info_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	public function perform()
	 {
		$date_from = str_replace('-', '', $this->_date_from);
		$date_to = str_replace('-', '', $this->_date_to);

		$search_params = array(
			'date_from' => $date_from,
			'date_to' => $date_to,
			'ua' => $this->af->get('search_ua'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		if ($search_flg != '1')
		{
			return 'admin_kpi_device_info_index';
		}

		$kpidevice_m = $this->backend->getManager('KpiViewDevice');
		$list = $kpidevice_m->getListInDevice($search_params);

		$this->af->setApp('list', $list);

		return 'admin_kpi_device_info_index';
	}
}
