<?php
/**
 *  Admin/Kpi/Shop/User/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_shop_user_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiShopUserView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$duration_type = $this->af->get('duration_type');
		$start         = $this->af->get('start');
		$end           = $this->af->get('end');
		$format        = $this->af->get('format');
		
		$admin_m =& $this->backend->getManager('Admin');
		$shop_m  =& $this->backend->getManager('AdminShop');
		$periodlog_m =& $this->backend->getManager('AdminPeriodlog');
		
		switch ($duration_type) {
			case Pp_AdminManager::DURATION_TYPE_DAILY:
				$date_from = $start;
				$date_to = date('Y-m-d', strtotime($end . ' 00:00:00') + 86400);

				$base_list = $admin_m->getCreatedUserBaseList($date_from, $date_to);
				break;

			case Pp_AdminManager::DURATION_TYPE_MONTHLY:
				$date_from = $start;
				$date_to = date('Y-m', strtotime($end . '-01 00:00:00') + (86400 * 31));

				$base_list = $admin_m->getCreatedUserBaseMonthlyList($date_from, $date_to);
				break;
		}

		$list = array();
		foreach ($base_list as $row) {
			$app_id = $row['target'];
			if (!$app_id) {
				// プラットフォーム問わずの集計値は使わない
				continue;
			}
			
			$key = $row['date_action'] . '_' . $row['target'];
			$list[$key][$row['type']] = $row['statistic'];
			$list[$key]['date_action'] = $row['date_action'];
			$list[$key]['platform'] = $shop_m->getPlatformDisplayNameFromAppId($app_id);
		}
		
		ksort($list);
		
		// DAUまたはMAUを取得
		$list2 = $periodlog_m->getKpiPeriodUserList($date_from, $date_to, $duration_type, Pp_PeriodlogManager::ACTION_TYPE_ACTIVE, Pp_AdminPeriodlogManager::COUNTING_TYPE_UU);
		$len = strlen($date_from);
		foreach ($list2 as $row) {
			$date_action = substr($row['date_start'], 0, $len);
			$key = $date_action . '_' . $row['app_id'];
			$list[$key]['active_user_num'] = $row['num'];
			
			// DAU率またはMAU率を求める
			$list[$key]['active_user_percentage'] = number_format(
				100 * $list[$key]['active_user_num'] / $list[$key]['user_create_total_num'], 1
			);
			
		}
		
		// 課金UUを取得
		$start_iso_date = $start; // 開始日（ISOで有効なY-m-d H:i:s形式）
		$end_iso_date   = $end;   // 終了日（ISOで有効なY-m-d H:i:s形式。端点含む）
		if ($duration_type == Pp_AdminManager::DURATION_TYPE_MONTHLY) {
			$start_iso_date .= '-01';
			$end_iso_date   .= '-01';
		}
		$start_iso_date .= ' 00:00:00';
		$end_iso_date   .= ' 00:00:00';

		$list3 = $admin_m->getKpiUserShopList($start_iso_date, $end_iso_date, $duration_type, Pp_AdminManager::KPI_TYPE_UU, 0);
//error_log(__FILE__ . ':' . __LINE__ . ':' . var_export($list3, true));
		$len = strlen($date_from);
		foreach ($list3 as $row) {
			$date_action = substr($row['date_use_start'], 0, $len);
			$key = $date_action . '_' . $row['app_id'];
			$list[$key]['payment_user_num'] = $row['kpi_value'];
		}

		// ARPU, ARPPUを求める
		$list4 = $admin_m->getKpiUserShopList($start_iso_date, $end_iso_date, $duration_type, Pp_AdminManager::KPI_TYPE_SUM_PRICE, 0);
		$len = strlen($date_from);
		foreach ($list4 as $row) {
			$date_action = substr($row['date_use_start'], 0, $len);
			$key = $date_action . '_' . $row['app_id'];
			
			if ($list[$key]['user_create_total_num'] != 0) {
				$list[$key]['arpu'] = number_format(
					$row['kpi_value'] / $list[$key]['user_create_total_num'], 1
				);
			}

			if ($list[$key]['payment_user_num'] != 0) {
				$list[$key]['arppu'] = number_format(
					$row['kpi_value'] / $list[$key]['payment_user_num'], 1
				);
			}
		}
		
		// 表示用の項目名を準備
		switch ($duration_type) {
			case Pp_AdminManager::DURATION_TYPE_DAILY:
				$active_name = 'DAU';
				break;

			case Pp_AdminManager::DURATION_TYPE_MONTHLY:
				$active_name = 'MAU';
				break;
		}
		
		if ($format == 'html') {
			// テンプレート変数にアサイン
			$this->af->setApp('active_name', $active_name);
			$this->af->setApp('list', $list);
		} else if ($format == 'csv') {
			// CSV準備
			$table = array();

//			$table[] = array('集計実行日時', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
//			$table[] = array('集計対象期間', $start, '～', $end);

			$table[] = array(
				'日付', 
				'プラットフォーム', 
				'新規登録者数', 
				'会員数', 
				$active_name, 
				$active_name . '率', 
				'課金UU', 
				'ARPU', 
				'ARPPU'
			);

			foreach ($list as $row) {
				$table[] = array(
					$row['date_action'], 
					$row['platform'], 
					$row['user_create_daily_num'], 
					$row['user_create_total_num'], 
					$row['active_user_num'], 
					$row['active_user_percentage'], 
					$row['payment_user_num'], 
					$row['arpu'], 
					$row['arppu']
				);
			}

			$this->af->setApp('table', $table);
			$this->af->setApp('filename', 'user_' . date('YmdHis', $_SERVER['REQUEST_TIME']) . '.csv');
		}
	}
}

?>
