<?php
/**
 *  Admin/Kpi/Shop/Sum/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_shop_sum_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiShopSumView extends Pp_AdminViewClass
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
		$platform_query = $this->af->get('platform_query');
		
		$admin_m =& $this->backend->getManager('Admin');
		$item_m =& $this->backend->getManager('Item');
		$shop_m =& $this->backend->getManager('AdminShop');

		// KPI情報を取得
		if ($duration_type == Pp_AdminManager::DURATION_TYPE_MONTHLY) {
			$start .= '-01';
			$end   .= '-01';
		}

		$start_date = $start . ' 00:00:00';
		$end_date   = $end . ' 00:00:00';
		
		$group_sum_flg = ($platform_query == 'mix');
		
		$list = $admin_m->getKpiUserShopList($start_date, $end_date, $duration_type, Pp_AdminManager::KPI_TYPE_SUM_NUM, null, $group_sum_flg);
		$price_total = 0;

		switch ($duration_type) {
			case Pp_AdminManager::DURATION_TYPE_DAILY:
				$date_len = 10;
				$date_name = '消費日';
				break;

			case Pp_AdminManager::DURATION_TYPE_MONTHLY:
				$date_len = 7;
				$date_name = '消費月';
				break;
		}
		foreach ($list as $key => $row) {
			$list[$key]['date_use_formatted'] = substr($row['date_use_start'], 0, $date_len);
			$list[$key]['platform'] = $shop_m->getPlatformDisplayNameFromAppId($list[$key]['app_id']);
			$list[$key]['price_sum'] = $row['price'] * $row['kpi_value'] / $row['num'];
			$price_total += $list[$key]['price_sum'];
		}
		
		// アイテムマスター情報を取得
		$m_item = array();
		foreach ($item_m->getMasterItem() as $row) {
			$m_item[$row['item_id']] = $row;
		}
		
		if ($format == 'html') {
			// テンプレート変数にアサイン
			$this->af->setApp('list', $list);
			$this->af->setApp('date_name', $date_name);
			$this->af->setApp('m_item', $m_item);
			$this->af->setApp('price_total', $price_total);
			
		} else if ($format == 'csv') {
			// CSV準備
			$table = array();
//			if ($duration_type == 2) {
//				$table[] = array('日別個別売上');
//			} else if ($duration_type == 3) {
//				$table[] = array('月別個別売上');
//			}
			
//			$table[] = array('集計実行日時', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
//			$table[] = array('集計対象期間', $start, '～', $end);

			$table[] = array($date_name, 'プラットフォーム', 'ショップID', 'アイテムID', 'アイテム名', '個数', '価格', '件数', '合計価格');
			foreach ($list as $row) {
				$table[] = array($row['date_use_formatted'], $row['platform'], $row['shop_id'], $row['item_id'], $m_item[$row['item_id']]['name_ja'], $row['num'], $row['price'], $row['kpi_value'], $row['price_sum']);
			}
			
			$table[] = array('合計', '', '', '', '', '', '', '', $price_total);

			$this->af->setApp('table', $table);
			$this->af->setApp('filename', 'shop_' . date('YmdHis', $_SERVER['REQUEST_TIME']) . '.csv');
		}
    }
}

?>
