<?php
/**
 *  Pp_AdminKpiManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 *  @see [開発用KPI] http://dev-kpi.cave.co.jp/application/customize.php
 */

require_once 'Pp_KpiManager.php';

/**
 *  Pp_AdminKpiManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminKpiManager extends Pp_KpiManager
{
	/**
	 * バッチでのKPIタグ送信処理
	 * 
	 * 基本的にはkpi_set関数のラッパー。
	 * 自サーバ内にログファイル出力も行う。
	 * 引数は可変個数。kpi_set関数と同じ順番で指定すること。
	 * kpi_set関数の引数についての説明を、Pp_KpiManager::log関数のコメントに書いたのでそちらも参照の事。
	 */
	function batchKpiSet()
	{
		$admin_m = $this->backend->getManager('Admin');

		$args = func_get_args();
		$map = array(
			'time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
			'args' => implode(',', $args),
		);
		
		// LTSV生成
		$ltsv = Pp_Ltsv::encode($map);
		$contents = $ltsv . "\n";

		// ログファイル出力
		$filename = $admin_m->getLtsvLogFilename('/batch', 'kpi_set');
		$pathname = dirname($filename);
		is_dir($pathname) || mkdir($pathname, 0755, true);
		file_put_contents($filename, $contents, FILE_APPEND | LOCK_EX);
		
		// KPIサーバへ送信
		kpi_set(
			isset($args[0]) ? $args[0] : null,
			isset($args[1]) ? $args[1] : null,
			isset($args[2]) ? $args[2] : null,
			isset($args[3]) ? $args[3] : null,
			isset($args[4]) ? $args[4] : null,
			isset($args[5]) ? $args[5] : null,
			isset($args[6]) ? $args[6] : null,
			isset($args[7]) ? $args[7] : null
		);
	}
	
	/**
	 * 1人あたりの友だち平均数の集計を行う
	 * 
	 * 集計できるのは実行時点の値のみなので注意
	 * @param string $login_date_from いつ以降のログインユーザーを集計対象とするか(Y-m-d H:i:s)
	 */
	function makeKpiVrAverageFriendCount($login_date_from)
	{
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');
		$friend_m = $this->backend->getManager('Friend');
		
		foreach (array(
			Pp_UserManager::OS_IPHONE,
			Pp_UserManager::OS_ANDROID
		) as $ua) {
			// 集計実行
			$param = array(Pp_FriendManager::STATUS_FRIEND, $ua, $login_date_from);
			$sql = <<<EOD
SELECT FLOOR(AVG(num))
FROM (
  SELECT f.user_id, COUNT(*) AS num
  FROM t_user_base b, t_user_initial i, t_user_friend f
  WHERE b.user_id = i.user_id
  AND b.user_id = f.user_id
  AND i.monster_flg = 1
  AND f.status = ?
  AND b.ua = ?
  AND b.login_date >= ?
  GROUP BY f.user_id
) t1
EOD;
			$avg = $this->db_r->GetOne($sql, $param);
			if ($avg === null) {
				// 開発環境などのアクセスが少ない環境では、nullはあり得るが、
				// kpi_set関数に0を渡すと勝手に1に変更されるので、kpi_set関数は呼ばずにcontinue
				$this->backend->logger->log(LOG_WARNING, 'makeKpiVrAverageFriendCount empty. ua=[%d]', $ua);
				continue;
			} else if (!is_numeric($avg)) {
				$this->backend->logger->log(LOG_ERR, 'makeKpiVrAverageFriendCount failed. ua=[%d]', $ua);
				continue;
			}

			// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
			// "Apple-jgm-vr_average_friend_count"など
			$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-vr_average_friend_count";
			$kpi_m->batchKpiSet($tag,1,$avg,$_SERVER['REQUEST_TIME'],"","","","");
		}
	}
	
	/**
	 * マジカルメダル総所持数における有料割合の集計を行う
	 * 
	 * ※有料コイン数　/ コイン総流通数
	 * 集計できるのは実行時点の値のみなので注意
	 */
	function makeKpiMnPaidCoinPerTotalCirculation()
	{
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');

		foreach (array(
			Pp_UserManager::OS_IPHONE,
			Pp_UserManager::OS_ANDROID
		) as $ua) {
			// 集計実行
			$param = array($ua);
			$sql = <<<EOD
SELECT FLOOR(100 * SUM(medal) / (SUM(medal) + SUM(service_point)))
FROM t_user_base
WHERE ua = ?
EOD;
			$percentage = $this->db_r->GetOne($sql, $param);
			if (!is_numeric($percentage)) {
				$this->backend->logger->log(LOG_ERR, 'makeKpiMnPaidCoinPerTotalCirculation failed. ua=[%d]', $ua);
				continue;
			}

			// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
			// "Apple-jgm-mn_paid_coin_per_total_circulation"など
			$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-mn_paid_coin_per_total_circulation";
			$kpi_m->batchKpiSet($tag,1,$percentage,$_SERVER['REQUEST_TIME'],"","","","");
		}
	}
	
	/**
	 * 28日以内ログインユーザーでのマジカルメダル総所持数における有料割合の集計を行う
	 * 
	 * 集計できるのは実行時点の値のみなので注意
	 */
	function makeKpiMnPaidCoinPerCirculationOfUser28()
	{
		
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');
		
		$login_date_from = date('Y-m-d', $_SERVER['REQUEST_TIME'] - 28 * 86400)
		                 . ' 00:00:00';

		foreach (array(
			Pp_UserManager::OS_IPHONE,
			Pp_UserManager::OS_ANDROID
		) as $ua) {
			// 集計実行
			$param = array($ua, $login_date_from);
			$sql = <<<EOD
SELECT FLOOR(100 * SUM(medal) / (SUM(medal) + SUM(service_point)))
FROM t_user_base
WHERE ua = ?
AND login_date >= ?
EOD;
			$percentage = $this->db_r->GetOne($sql, $param);
			if (!is_numeric($percentage)) {
				$this->backend->logger->log(LOG_ERR, 'makeKpiMnPaidCoinPerTotalCirculation failed. ua=[%d]', $ua);
				continue;
			}

			// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
			// "Apple-jgm-mn_paid_coin_per_circulation_of_user_28"など
			$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-mn_paid_coin_per_circulation_of_user_28";
			$kpi_m->batchKpiSet($tag,1,$percentage,$_SERVER['REQUEST_TIME'],"","","","");
		}
	}
	
	/**
	 * DAUにおけるマジカルメダル(無料)の総数の集計を行う
	 * 
	 * 集計できるのは実行時点の値のみなので注意
	 */
	function makeKpiMagicalmedalFreeTotalDau()
	{
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');
		
		$login_date_from = date('Y-m-d', $_SERVER['REQUEST_TIME'] - 1 * 86400)
		                 . ' 00:00:00';

		foreach (array(
			Pp_UserManager::OS_IPHONE,
			Pp_UserManager::OS_ANDROID
		) as $ua) {
			// 集計実行
			$param = array($ua, $login_date_from);
			$sql = <<<EOD
SELECT SUM(service_point)
FROM t_user_base
WHERE ua = ?
AND login_date >= ?
EOD;
			$num = $this->db_r->GetOne($sql, $param);
			if (!is_numeric($num)) {
				$this->backend->logger->log(LOG_ERR, 'makeKpiMagicalmedalFreeTotalDau failed. ua=[%d]', $ua);
				continue;
			}
			
			// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
			// "Apple-jgm-magicalmedal_free_total_dau"など
			$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-magicalmedal_free_total_dau";
			$kpi_m->batchKpiSet($tag,1,$num,$_SERVER['REQUEST_TIME'],"","","","");
		}
	}
	
	/**
	 * DAUにおけるマジカルメダル(有料)の総数の集計を行う
	 * 
	 * 集計できるのは実行時点の値のみなので注意
	 */
	function makeKpiMagicalmedalTotalDau()
	{
		$user_m = $this->backend->getManager('AdminUser');
		$kpi_m = $this->backend->getManager('AdminKpi');
		
		$login_date_from = date('Y-m-d', $_SERVER['REQUEST_TIME'] - 1 * 86400)
		                 . ' 00:00:00';

		foreach (array(
			Pp_UserManager::OS_IPHONE,
			Pp_UserManager::OS_ANDROID
		) as $ua) {
			// 集計実行
			$param = array($ua, $login_date_from);
			$sql = <<<EOD
SELECT SUM(medal)
FROM t_user_base
WHERE ua = ?
AND login_date >= ?
EOD;
			$num = $this->db_r->GetOne($sql, $param);
			if (!is_numeric($num)) {
				$this->backend->logger->log(LOG_ERR, 'makeKpiMagicalmedalTotalDau failed. ua=[%d]', $ua);
				continue;
			}
			
			// 集計結果をKPIサーバへ送信＆ローカルログファイルに記録
			// "Apple-jgm-magicalmedal_total_dau"など
			$tag = $kpi_m->getPlatformByUa($ua) . "-jgm-magicalmedal_total_dau";
			$kpi_m->batchKpiSet($tag,1,$num,$_SERVER['REQUEST_TIME'],"","","","");
		}
	}
}
?>