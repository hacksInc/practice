<?php
/**
 *  Pp_PeriodlogManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_PeriodlogManager.php';

/**
 *  Pp_AdminPeriodlogManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminPeriodlogManager extends Pp_PeriodlogManager
{
	/** 集計種別：ユニークユーザ */
	const COUNTING_TYPE_UU = 1;

	/**
	 * DB接続(pp-ini.phpの'dsn_log_r'で定義したDB)
	 */
	protected $db_log_r = null;

	function __construct(&$backend) {
		parent::__construct($backend);
		
		if (!$this->db_log_r) {
			$this->db_log_r =& $this->backend->getDB('log_r');
		}
	}
	
	/** 期間別ユニークユーザーログからKPI集計を生成する */
	function makeKpiPeriodUserUnique($date_start, $period_type)
	{
		$param = array($date_start, $period_type);
		$sql = <<<EOD
SELECT action_type, ua, COUNT(*) AS cnt
FROM log_period_user_unique
WHERE date_start = ?
AND period_type = ?
GROUP BY action_type, ua;
EOD;
		$rows = $this->db_log_r->GetAll($sql, $param);
		if (Ethna::isError($rows)) {
			return;
		}
		
		if ($rows) foreach ($rows as $row) {
			$param = array($date_start, $period_type, $row['action_type'], self::COUNTING_TYPE_UU, $row['ua'], $row['cnt']);
			$sql = <<<EOD
INSERT INTO kpi_period_user(date_start, period_type, action_type, counting_type, ua, num, date_created)
VALUES (?, ?, ?, ?, ?, ?, NOW())
EOD;
			if (!$this->db->execute($sql, $param)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}
	}
	
	function getKpiPeriodUserList($date_from, $date_to, $period_type, $action_type, $counting_type)
	{
		$date_from .= substr('0000-00-01 00:00:00', strlen($date_from));
		$date_to   .= substr('0000-00-01 00:00:00', strlen($date_to));

		$param = array($date_from, $date_to, $period_type, $action_type, $counting_type);
		$sql = <<<EOD
SELECT date_start, ua, num
FROM kpi_period_user
WHERE date_start >= ?
AND date_start < ?
AND period_type = ?
AND action_type = ?
AND counting_type = ?
EOD;

		return $this->db_r->GetAll($sql, $param);
	}
	
	/**
	 * 登録月別月次利用UUの集計を行う
	 * 
	 * @param string $date_start 集計対象とする期間開始日時(Y-m-d H:i:s形式)
	 */
	function makeKpiAcInstallMau($date_start)
	{
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');

		// 集計データ $stat_data[ua][年月] = 値
		$stat_data = array(
			Pp_UserManager::OS_IPHONE => array(),
			Pp_UserManager::OS_ANDROID => array(),
		);
		
		// 集計実行
		$param = array($date_start, self::PERIOD_TYPE_MONTHLY, self::ACTION_TYPE_ACTIVE);
		$sql = <<<EOD
SELECT user_id, ua
FROM log_period_user_unique
WHERE date_start = ?
AND period_type = ?
AND action_type = ?
EOD;
		$result =& $this->db_log_r->query($sql, $param);
		if (Ethna::isError($result)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		while ($row = $result->FetchRow()) {
			$user_id = $row['user_id'];
			$ua = $row['ua'];

			$ym = $user_m->getUserInitialMonsterKpiYm($user_id);
			if (!$ym) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				continue;
			}
			
			if (!isset($stat_data[$ua][$ym])) {
				$stat_data[$ua][$ym] = 0;
			}
			
			$stat_data[$ua][$ym] += 1;
		}
		
		// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
		foreach ($stat_data as $ua => $assoc) {
			foreach ($assoc as $ym => $count) {
				// "Apple-jgm-ac_1309_install_mau"など
				$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-ac_" . $ym . "_install_mau";
				$kpi_m->batchKpiSet($tag,1,$count,strtotime($date_start),"","","","");
			}
		}
	}
	
	/**
	 * 登録月別消費UUの集計を行う
	 * 
	 * @param string $date_start 集計対象とする期間開始日時(Y-m-d H:i:s形式)
	 */
	function makeKpiMtInstallPaidMau($date_start)
	{
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');

		// 集計データ $stat_data[ua][年月] = 値
		$stat_data = array(
			Pp_UserManager::OS_IPHONE => array(),
			Pp_UserManager::OS_ANDROID => array(),
		);
		
		// 集計実行
		$param = array($date_start, self::PERIOD_TYPE_MONTHLY, self::ACTION_TYPE_PAYMENT_USE);
		$sql = <<<EOD
SELECT user_id, ua
FROM log_period_user_accumu
WHERE date_start = ?
AND period_type = ?
AND action_type = ?
AND num > 0
EOD;
		$result =& $this->db_log_r->query($sql, $param);
		if (Ethna::isError($result)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		while ($row = $result->FetchRow()) {
			$user_id = $row['user_id'];
			$ua = $row['ua'];
			
			$ym = $user_m->getUserInitialMonsterKpiYm($user_id);
			if (!$ym) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				continue;
			}
			
			if (!isset($stat_data[$ua][$ym])) {
				$stat_data[$ua][$ym] = 0;
			}
			
			$stat_data[$ua][$ym] += 1;
		}
		
		// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
		foreach ($stat_data as $ua => $assoc) {
			foreach ($assoc as $ym => $count) {
				// "Apple-jgm-mt_1309_install_paid_mau"など
				$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-mt_" . $ym . "_install_paid_mau";
				$kpi_m->batchKpiSet($tag,1,$count,strtotime($date_start),"","","","");
			}
		}
	}
	
	/**
	 * 登録月別消費売り上げの集計を行う
	 * 
	 * @param string $date_start 集計対象とする期間開始日時(Y-m-d H:i:s形式)
	 */
	function makeKpiMtInstallUserSales($date_start)
	{
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');

		// 集計データ $stat_data[ua][年月] = 値
		$stat_data = array(
			Pp_UserManager::OS_IPHONE => array(),
			Pp_UserManager::OS_ANDROID => array(),
		);
		
		// 集計実行
		$param = array($date_start, self::PERIOD_TYPE_MONTHLY, self::ACTION_TYPE_PAYMENT_USE);
		$sql = <<<EOD
SELECT user_id, ua, num
FROM log_period_user_accumu
WHERE date_start = ?
AND period_type = ?
AND action_type = ?
AND num > 0
EOD;
		$result =& $this->db_log_r->query($sql, $param);
		if (Ethna::isError($result)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		while ($row = $result->FetchRow()) {
			$user_id = $row['user_id'];
			$ua = $row['ua'];
			$num = $row['num'];
			
			$ym = $user_m->getUserInitialMonsterKpiYm($user_id);
			if (!$ym) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				continue;
			}
			
			if (!isset($stat_data[$ua][$ym])) {
				$stat_data[$ua][$ym] = 0;
			}
			
			$stat_data[$ua][$ym] += $num;
		}
		
		// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
		foreach ($stat_data as $ua => $assoc) {
			foreach ($assoc as $ym => $count) {
				// "Apple-jgm-mt_1309_install_user_sales"など
				$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-mt_" . $ym . "_install_user_sales";
				$kpi_m->batchKpiSet($tag,1,$count,strtotime($date_start),"","","","");
			}
		}
	}

	/**
	 * 累計総課金額分布（週別）用の範囲を求める
	 * 
	 * 100円以下、101円～500円、501円～1000円、1001円～2000円、2001円～5000円、5001円以上で取得
	 * @param int $num 課金額
	 * @return array 範囲(array(min, max))
	 */
	protected function getKpiMtCumulativeSalesRange($num)
	{
		$min = null;
		foreach (array(
			100, 500, 1000, 2000, 5000
		) as $max) {
			if ($num <= $max) {
				return array($min, $max);
			}
			
			$min = $max + 1;
		}
		
		return array($min, null);
	}
		
	/**
	 * 累計総課金額分布（週別）用の範囲の文字列表記を求める
	 * 
	 * @param int $num 課金額
	 * @return string タグ名称の末尾として使用する為の文字列
	 */
	protected function getKpiMtCumulativeSalesRangeStr($num)
	{
		list($min, $max) = $this->getKpiMtCumulativeSalesRange($num);
		
		$str = '';
		if ($min !== null) {
			$str .= $min . '_';
		}
		
		if ($max !== null) {
			$str .= $max;
		} else {
			$str .= 'more';
		}
		
		return $str;
	}
	
	/**
	 * 累計総課金額分布（週別）の集計を行う
	 * 
	 * @param string $date_start 集計対象とする期間開始日時(Y-m-d H:i:s形式)
	 */
	function makeKpiMtCumulativeSales($date_start)
	{
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');

		// 集計データ $stat_data[ua][範囲の文字列] = 値
		$stat_data = array(
			Pp_UserManager::OS_IPHONE => array(),
			Pp_UserManager::OS_ANDROID => array(),
		);
		
		// 集計実行
		$param = array($date_start, self::PERIOD_TYPE_WEEKLY, self::ACTION_TYPE_PAYMENT_USE);
		$sql = <<<EOD
SELECT user_id, ua, num
FROM log_period_user_accumu
WHERE date_start = ?
AND period_type = ?
AND action_type = ?
AND num > 0
EOD;
		$result =& $this->db_log_r->query($sql, $param);
		if (Ethna::isError($result)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		while ($row = $result->FetchRow()) {
			$user_id = $row['user_id'];
			$ua = $row['ua'];
			$num = $row['num'];
			
			$str = $this->getKpiMtCumulativeSalesRangeStr($num);
			
			if (!isset($stat_data[$ua][$str])) {
				$stat_data[$ua][$str] = 0;
			}
			
//			$stat_data[$ua][$str] += $num;
			$stat_data[$ua][$str] += 1;
		}
		
		// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
		foreach ($stat_data as $ua => $assoc) {
			foreach ($assoc as $str => $count) {
				// "Google-jgm-mt_cumulative_sales_101_500"など
				$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-mt_cumulative_sales_" . $str;
				$kpi_m->batchKpiSet($tag,1,$count,strtotime($date_start),"","","","");
			}
		}
	}

	/**
	 * 累計総課金回数分布（週別）用の範囲を求める
	 * 
	 * 1回、2回、3-5回、6-10回、11-20回、21-50回、51回以上で取得
	 * @param int $num 回数
	 * @return array 範囲(array(min, max))
	 */
	protected function getKpiMtCumulativePaidCountRange($num)
	{
		$min = null;
		foreach (array(
			1, 2, 5, 10, 20, 50
		) as $max) {
			if ($num <= $max) {
				return array($min, $max);
			}
			
			$min = $max + 1;
		}
		
		return array($min, null);
	}
		
	/**
	 * 累計総課金回数分布（週別）用の範囲の文字列表記を求める
	 * 
	 * @param int $num 回数
	 * @return string タグ名称の末尾として使用する為の文字列
	 */
	protected function getKpiMtCumulativePaidCountRangeStr($num)
	{
		list($min, $max) = $this->getKpiMtCumulativePaidCountRange($num);
		
		if ($min === null) {
			return $max;
		} else if ($max === null) {
			return $min . '_more';
		} else if ($min == $max) {
			return $max;
		} else {
			return $min . '_' . $max;
		}
	}
	
	/**
	 * 累計総課金回数分布（週別）の集計を行う
	 * 
	 * @param string $date_start 集計対象とする期間開始日時(Y-m-d H:i:s形式)
	 */
	function makeKpiMtCumulativePaidCount($date_start)
	{
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');

		// 集計データ $stat_data[ua][範囲の文字列] = 値
		$stat_data = array(
			Pp_UserManager::OS_IPHONE => array(),
			Pp_UserManager::OS_ANDROID => array(),
		);
		
		// 集計実行
		$param = array($date_start, self::PERIOD_TYPE_WEEKLY, self::ACTION_TYPE_PAYMENT_USE_NUM);
		$sql = <<<EOD
SELECT user_id, ua, num
FROM log_period_user_accumu
WHERE date_start = ?
AND period_type = ?
AND action_type = ?
AND num > 0
EOD;
		$result =& $this->db_log_r->query($sql, $param);
		if (Ethna::isError($result)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		while ($row = $result->FetchRow()) {
			$user_id = $row['user_id'];
			$ua = $row['ua'];
			$num = $row['num'];
			
			$str = $this->getKpiMtCumulativePaidCountRangeStr($num);
			
			if (!isset($stat_data[$ua][$str])) {
				$stat_data[$ua][$str] = 0;
			}
			
			$stat_data[$ua][$str] += 1;
		}
		
		// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
		foreach ($stat_data as $ua => $assoc) {
			foreach ($assoc as $str => $count) {
				// "Apple-jgm-mt_cumulative_paid_count_11_20"など
				$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-mt_cumulative_paid_count_" . $str;
				$kpi_m->batchKpiSet($tag,1,$count,strtotime($date_start),"","","","");
			}
		}
	}
}
?>