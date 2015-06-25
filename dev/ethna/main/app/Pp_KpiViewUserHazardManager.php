<?php
/**
 *  Pp_KpiViewUserHazardManager.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewUserHazardManager
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_KpiViewUserHazardManager extends Pp_KpiViewManager
{

	public function getLevelCount($search_param)
	{
		return parent::_getCount('kpi_hazard_level', $search_param);
	}

	public function getLevelList($search_param)
	{
		$sql = "
SELECT
  date_tally
  , lv
  , count_hazard
FROM
  kpi_hazard_level
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createLevelCsv($data)
	{
		$csv_title = array(
			'日付',
			'エリアストレスレベル',
			'サイコハザード発生回数',
		);

		return parent::createCsv($csv_title, $data, 'kpi_user_hazard_level');
	}

	public function getStageCount($search_param)
	{
		return parent::_getCount('kpi_hazard_stage', $search_param);
	}

	public function getStageList($search_param)
	{
		$sql = "
SELECT
  date_tally
  , stage_id
  , count_hazard
FROM
  kpi_hazard_stage
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createStageCsv($data)
	{
		$csv_title = array(
			'日付',
			'STAGE',
			'サイコハザード発生回数',
		);

		return parent::createCsv($csv_title, $data, 'kpi_user_hazard_stage');
	}

	public function getStage()
	{
		$result = array();

		$db = $this->backend->getDB('m_r');

		$data = $db->GetAll("SELECT stage_id, name_ja FROM m_stage");

		if (!$data) return $result;

		foreach ($data as $item)
		{
			$result[$item['stage_id']] = $item['name_ja'];
		}

		return $result;
	}
}
