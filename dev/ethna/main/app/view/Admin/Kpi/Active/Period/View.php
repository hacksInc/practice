<?php
/**
 *  Admin/Kpi/Active/Period/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_active_period_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiActivePeriodView extends Pp_AdminViewClass
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
			case Pp_AdminManager::DURATION_TYPE_HOURLY:
			case Pp_AdminManager::DURATION_TYPE_DAILY:
				$date_from = $start;
				$date_to = date('Y-m-d', strtotime($end . ' 00:00:00') + 86400);
				break;

			case Pp_AdminManager::DURATION_TYPE_MONTHLY:
				$date_from = $start;
				$date_to = date('Y-m', strtotime($end . '-01 00:00:00') + (86400 * 31));
				break;
		}
		
		$date_from .= substr('0000-00-01 00:00:00', strlen($start));
		$date_to   .= substr('0000-00-01 00:00:00', strlen($end));

		// KPI集計値を取得
		$list = $periodlog_m->getKpiPeriodUserList($date_from, $date_to, $duration_type, 
			Pp_PeriodlogManager::ACTION_TYPE_ACTIVE, Pp_AdminPeriodlogManager::COUNTING_TYPE_UU
		);
		
		// 表示用の補足情報を付ける
		if ($duration_type == Pp_AdminManager::DURATION_TYPE_HOURLY) {
			$len = strlen('YYYY-MM-DD HH');
		} else {
			$len = strlen($start);
		}
		foreach ($list as $key => $row) {
			$list[$key]['date_period'] = substr($row['date_start'], 0, $len);
			$list[$key]['platform'] = $shop_m->getPlatformDisplayNameFromUa($row['ua']);
		}

		if ($format == 'html') {
			// テンプレート変数にアサイン
			$this->af->setApp('list', $list);
		} else if ($format == 'csv') {
			// CSV準備
			$table = array();

			$table[] = array('date_period', 'platform', 'num');
			foreach ($list as $row) {
				$table[] = array($row['date_period'], $row['platform'], $row['num']);
			}

			$this->af->setApp('table', $table);
			$this->af->setApp('filename', 'active_' . date('YmdHis', $_SERVER['REQUEST_TIME']) . '.csv');
		}
    }
}

?>
