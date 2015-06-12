<?php
/**
 *  Admin/Kpi/Shop/Userlog/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_shop_userlog_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiShopUserlogView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
	{
		$user_id = $this->af->get('id');
		$format  = $this->af->get('format');

		$base = $this->af->getApp('base');
		
		$admin_m =& $this->backend->getManager('Admin');
		$shop_m =& $this->backend->getManager('AdminShop');
		$item_m =& $this->backend->getManager('Item');

		$user_shop_list = $admin_m->getLogUserShopList($user_id, null, null);
		
		$master_item_list = $item_m->getMasterItem();
		$item_name_assoc = array();
		foreach ($master_item_list as $row) {
			$item_name_assoc[$row['item_id']] = $row['name_ja'];
		}
		
		$platform_display_name = $shop_m->getPlatformDisplayNameFromAppId($base['app_id']);

		if ($format == 'html') {
			// テンプレート変数にアサイン
			$this->af->setApp('user_shop_list',        $user_shop_list);
			$this->af->setApp('item_name_assoc',       $item_name_assoc);
			$this->af->setApp('platform_display_name', $platform_display_name);
			
		} else if ($format == 'csv') {
			// CSV準備
			$table = array();
			
			$table[] = array('日付(年月)', '購入アイテム名', 'プラットホーム名', 'num', 'price');
			
			foreach ($user_shop_list as $row) {
				$table[] = array($row['date_use'], $item_name_assoc[$row['item_id']], $platform_display_name, $row['num'], $row['price']);
			}

			$this->af->setApp('table', $table);
			$this->af->setApp('filename', 'log_user_shop_' . date('YmdHis', $_SERVER['REQUEST_TIME']) . '.csv');
		}
	}
}

?>