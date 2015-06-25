<?php
/**
 *  Admin/Kpi/World/Item/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_world_item_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiWorldItemView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$date   = $this->af->get('date');
		$format = $this->af->get('format');
		
		$admin_m =& $this->backend->getManager('Admin');
		$item_m  =& $this->backend->getManager('Item');
		$shop_m  =& $this->backend->getManager('AdminShop');
		
		$list = $admin_m->getKpiUserItemList($date);
		
		foreach ($list as $key => $row) {
			$list[$key]['platform'] = $shop_m->getPlatformDisplayNameFromUa($row['ua']);
			
			$item = $item_m->getMasterItem($row['item_id']);
			if (!$item || Ethna::isError($item)) {
				continue;
			}

			$list[$key]['name'] = $item['name_ja'];
		}
		
		if ($format == 'html') {
			// テンプレート変数にアサイン
			$this->af->setApp('list', $list);
		} else if ($format == 'csv') {
			// CSV準備
			$table = array();

			$table[] = array(
				'アイテム名', 
				'アイテムID', 
				'プラットホーム名', 
				'流通数', 
			);

			foreach ($list as $row) {
				$table[] = array(
					$row['name'], 
					$row['item_id'], 
					$row['platform'], 
					$row['sum_num'], 
				);
			}

			$this->af->setApp('table', $table);
			$this->af->setApp('filename', 'world_item_' . date('YmdHis', $_SERVER['REQUEST_TIME']) . '.csv');
		}
    }
}

?>
