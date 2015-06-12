<?php
/**
 *  Pp_KpiViewUserMissionManager.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewUserMissionManager
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_KpiViewUserMissionManager extends Pp_KpiViewManager
{

	public function getCount($search_param)
	{
		return parent::_getCount('kpi_mission', $search_param);
	}

	public function getList($search_param)
	{
		$sql = "
SELECT
  area_id
  , area_name
  , mission_id
  , mission_name
  , uu_start
  , uu_clear
  , clear_rate
  , count_challenge
  , count_clear
  , count_spare_domi_total
  , count_spare_domi_unused
  , count_spare_domi_used
  , count_best
  , count_normal
  , count_fail
FROM
  kpi_mission
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createCsv($data)
	{
		$csv_title = array(
			'ステージ名',
			'エリア名',
			'ミッション名',
			'スタートUU',
			'クリアUU',
			'クリア率',
			'ミッション挑戦回数',
			'クリア回数',
			'総予備ドミネータ使用回数',
			'予備ドミネータ未使用クリア数',
			'予備ドミネータ使用クリア数',
			'BESTクリア回数',
			'NORMALクリア回数',
			'FAIL回数',
		);

		return parent::createCsv($csv_title, $data, 'kpi_user_mission_index');
	}

	public function getFailCount($search_param)
	{
		return parent::_getCount('kpi_mission_fail', $search_param);
	}

	public function getFailList($search_param)
	{
		$sql = "
SELECT
  area_id
  , area_name
  , mission_id
  , mission_name
  , uu_fail
  , battery
  , life
  , timeup
  , zone1
  , zone2
  , zone3
  , zone4
  , zone5
FROM
  kpi_mission_fail
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createFailCsv($data)
	{
		$csv_title = array(
			'ステージ名',
			'エリア名',
			'ミッション名',
			'FAIL UU',
			'バッテリー切れFAIL',
			'ライフ０FAIL',
			'時間切れFAIL',
			'FAILゾーン1',
			'FAILゾーン2',
			'FAILゾーン3',
			'FAILゾーン4',
			'FAILゾーン5',
		);

		return parent::createCsv($csv_title, $data, 'kpi_user_mission_fail');
	}

	public function getArea()
	{
		$result = array();

		$db = $this->backend->getDB('m_r');

		$data = $db->GetAll("SELECT area_id, name_ja FROM m_area");

		if (!$data) return $result;

		foreach ($data as $item)
		{
			$result[$item['area_id']] = $item['area_id'] . ':' . $item['name_ja'];
		}

		return $result;
	}

	public function getStageByArea()
	{
		$result = array();

		$db = $this->backend->getDB('m_r');

		$sql = "
SELECT
  m1.area_id
  , m2.name_ja
FROM
  m_area m1
  INNER JOIN m_stage m2
    ON m1.stage_id = m2.stage_id
";

		$data = $db->GetAll($sql);

		if (!$data) return $result;

		foreach ($data as $item)
		{
			$result[$item['area_id']] = $item['name_ja'];
		}

		return $result;
	}
}
