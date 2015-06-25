<?php
/**
 *  Pp_KpiViewDeviceManager.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewDeviceManager
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_KpiViewDeviceManager extends Pp_KpiViewManager
{

	public function getCountInDevice($search_param)
	{
		return parent::_getCount('kpi_device_info', $search_param);
	}

	public function getListInDevice($search_param)
	{
		$sql = "
SELECT
  model
  , os
  , memory
  , amount
  , rate
FROM
  kpi_device_info
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createCsvInDevice($data)
	{
		$csv_title = array(
			'デバイスのモデル',
			'OSヴァージョン',
			'システムのメモリ量',
			'台数',
			'割合',
		);

		return parent::createCsv($csv_title, $data, 'kpi_device_info');
	}
}
