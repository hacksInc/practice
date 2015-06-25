<?php
/**
 *  Pp_KpiViewUserManager.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewManager
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_KpiViewUserManager extends Pp_KpiViewManager
{
	/** 継続率一覧表示日数 */
	const CONTINUANCE_LIST_DAYS = 180;

	/** N日後の継続率表示対象日数(0:当日) */
	var $DISP_ELAPSED_DATE = array(0, 1, 2, 3, 4, 5, 6, 13, 29, 59, 89, 179);

	/**
	 * 指定期間のKPI 継続率の件数を取得する
	 *
	 * @params datetime $date_from
	 * @params datetime $date_to
	 * @return mixed
	 */
	public function getKpiUserContinuanceListByDateInstallCount ($search_param)
	{
		$condition = $this->_getConditionKpiUserContinuanceListByDateInstall($search_param);

		return $this->getKpiUserContinuanceCount($condition['condition'], $condition['param']);
	}

	/**
	 * 指定期間のKPI 継続率を取得する
	 *
	 * @param array $search_param
	 * @return mixed
	 */
	public function getKpiUserContinuanceListByDateInstall ($search_param)
	{
		$condition = $this->_getConditionKpiUserContinuanceListByDateInstall($search_param);
		$sort = array(
			'date_install' => 'DESC',
			'date_tally' => 'ASC',
		);

		$res = $this->getKpiUserContinuance($condition['condition'], $condition['param'], $sort);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;
	}

	/**
	 * 指定期間のKPI 継続率を指定する(条件編集)
	 *
	 * @param array $search_param
	 * @return array
	 */
	private function _getConditionKpiUserContinuanceListByDateInstall ($search_param)
	{
		$where = " where (date_install >= ? and date_install <= ?)"
			   . " and (date_tally >= ? and date_tally <= ?)"
			   . " and ua = ?";
		$param = array(
			$search_param['date_to'],
			$search_param['date_from'],
			$search_param['date_to'],
			$search_param['date_from'],
			$search_param['ua']
		);

		return array('condition' => $where, 'param' => $param);
	}

	/**
	 * KPI 継続率を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @param array $sort
	 * @param integer $limit
	 * @param integer $offset
	 * @return mixed
	 */
	public function getKpiUserContinuance ($where, $param, $sort=null, $limit=null, $offset=null)
	{
		$log_db = $this->backend->getDB('logex');
		$order = $this->_createSqlPhraseOrderBy($sort);
		$sql = "select * from kpi_user_continuance_rate" . $where . $order;

		return $log_db->GetAll($sql, $param);
	}

	/**
	 * KPI 継続率の件数を取得する
	 *
	 * @param string $where
	 * @param array $param
	 * @return mixed
	 */
	public function getKpiUserContinuanceCount ($where, $param)
	{
		$log_db = $this->backend->getDB('logex');
		$sql = "select count(*) from kpi_user_continuance_rate" . $where;

		return $log_db->GetOne($sql, $param);
	}

	/**
	 * 指定日のKPI 継続率を取得する
	 *
	 * @params datetime $tran_date
	 * @return mixed
	 */
	public function getKpiUserContinuanceListByDateLoginTally ($tran_date)
	{
		$condition = $this->_getConditionKpiUserContinuanceListByDateLoginTally ($tran_date);

		$res = $this->getKpiUserContinuance ($condition['condition'], $condition['param']);
		if ($res === false){
			return false;
		}

		$log_data['count'] = count($res);
		$log_data['data'] = $res;

		return $log_data;
	}

	/**
	 * 指定日のKPI 継続率の件数を取得する
	 *
	 * @params datetime $tran_date
	 * @return mixed
	 */
	public function getKpiUserContinuanceListByDateLoginTallyCount ($tran_date)
	{
		$condition = $this->_getConditionKpiUserContinuanceListByDateLoginTally ($tran_date);

		$res = $this->getKpiUserContinuanceCount ($condition['condition'], $condition['param']);

		return $res;
	}

	/**
	 * 指定日のKPI 継続率を取得する(条件編集)
	 *
	 * @params datetime $tran_date
	 * @return mixed
	 */
	private function _getConditionKpiUserContinuanceListByDateLoginTally ($date_tally)
	{
		$where = " where date_tally = ?";
		$param = array($date_tally);

		return array('condition' => $where, 'param' => $param);
	}

	/**
	 * KPI 還元率データを一覧表示データの形に形成する
	 *
	 * @params array $kpi_continuance_data
	 * @return array
	 */
	public function editKpiUserContinuanceList ($kpi_continuance_data)
	{

		if ( !$kpi_continuance_data || !(is_array($kpi_continuance_data)) ) {
			return null;
		}

		$date_install = '';
		$count_install = 0;
		$cnt=0;
		for ($i=0; $i < self::CONTINUANCE_LIST_DAYS; $i++){
		   if (in_array($i, $this->DISP_ELAPSED_DATE)) {
					$list[$i]['rate'] = '-';
			}
		}

		foreach($kpi_continuance_data as $k => $v){
			if ($date_install != $v['date_install']){
				if ($date_install != '') {
					$tmp_date = date('Ymd', strtotime($date_install));
					$tmp_list = array(
						'list' => $list,
						'count_download' => $count_download,
						'date_install' => date('Y-m-d', strtotime($date_install)),
						'date_install_day' => date('D', strtotime($date_install)),
					);
					$view_list[$tmp_date] = $tmp_list;
				}
				$date_install = $v['date_install'];
				$count_download = $v['count_download'];
				$cnt = 0;
			}
//$this->backend->logger->log(LOG_INFO, '************************ rate result==[' . print_r($v['continuance_rate'], true) . ']');
			$bk_color = 0;
			if ($v['continuance_rate'] == 100) {
				$bk_color = 90;
			} else {
				$bk_color = floor($v['continuance_rate'] / 10) * 10;
			}
			if ($v['count_download'] == 0) {
				$bk_color = '-gray';
			}

			if ($cnt == 0 || isset($list[$cnt])) {
				$list[$cnt]['rate'] = round($v['continuance_rate'], 2);
	   			$list[$cnt]['bk_color'] = $bk_color;
				$list[$cnt] = array_merge($list[$cnt], $v);
			}
			$cnt++;
		}
		$tmp_date = date('Ymd', strtotime($date_install));
		$tmp_list = array(
			'list' => $list,
			'count_download' => $count_download,
			'date_install' => date('Y-m-d', strtotime($date_install)),
			'date_install_day' => date('D', strtotime($date_install)),
		);
		$view_list[$tmp_date] = $tmp_list;

		return $view_list;
	}

	/**
	 * KPI 継続率のCSVファイル作成
	 *
	 * @param array $kpi_continuance_data
	 * @return mixed
	 */
	public function createCsvFileKpiUserContinuanceRate($kpi_continuance_data, $count_day)
	{
		$log_name = 'kpi_user_continuance_rate';
		$file_path = KPIDATA_TMP_PATH;

		$old_umask = umask(0000);
		if (!is_dir(KPIDATA_TMP_PATH)) {
			mkdir(KPIDATA_TMP_PATH, 0777, true); // 後でターミナル上のjugmonユーザで削除できるように、0777にする。
		}
		umask($old_umask);

		$rand_num = mt_rand();
		$today_date = date('Ymd', time());
		$file_name = $log_name .'_' . $today_date . '_' . $rand_num;
		if (!$fp=@fopen($file_path . '/' . $file_name, 'a')){
			return false;
		}

		$title_list = array(
			'日付',
			'曜日',
			'当日ダウンロード数',
		);
		for($i=1;$i<=$count_day;$i++){
			if (in_array($i - 1, $this->DISP_ELAPSED_DATE)) {
				$title_list[] = $i . '日目 継続率';
				$title_list[] = $i . '日目 ログイン数';
			}
		}
		$title_str = implode(',', $title_list);
		fwrite($fp, mb_convert_encoding($title_str, "Shift_JIS", "UTF-8") . "\r\n");

		$data_list = array();
		foreach ($kpi_continuance_data as $data_k => $data_v){
			$data_list = array();
			$data_list[] = $data_v['date_install'];
			$data_list[] = $data_v['date_install_day'];
			$data_list[] = $data_v['count_download'];
			foreach ($data_v['list'] as $k => $v){
				if (!in_array($k, $this->DISP_ELAPSED_DATE)) {
					break;
				}
				$rate = $v['rate'];
				if ($v['rate'] !== '-') {
					$rate = $rate . '%';
				}
				$data_list[] = $rate;
				$data_list[] = $v['count_login'];
			}
			$str = implode(',', $data_list);
			fwrite($fp, mb_convert_encoding($str, "Shift-JIS", "UTF-8") . "\r\n");
		}
		fclose($fp);

		return $file_name;
	}

	public function getStresscareCount($search_param)
	{
		return parent::_getCount('kpi_stresscare', $search_param);
	}

	public function getStresscareList($search_param)
	{
		$sql = "
SELECT
  date_tally
  , character_id
  , count_ex
  , count_therapy
FROM
  kpi_stresscare
WHERE
  1 = 1
";

		return parent::_getList($sql, $search_param);
	}

	public function createStresscareCsv($data)
	{
		$csv_title = array(
			'日付',
			'キャラクター',
			'臨時ストレスケア実行回数',
			'セラピー受診実行回数',
		);

		return parent::createCsv($csv_title, $data, 'kpi_user_stresscare_index');
	}

	public function getCharacter()
	{
		$result = array();

		$db = $this->backend->getDB('m_r');

		$data = $db->GetAll("SELECT character_id, name_ja FROM m_character");

		if (!$data) return $result;

		foreach ($data as $item)
		{
			$result[$item['character_id']] = $item['name_ja'];
		}

		return $result;
	}
}
