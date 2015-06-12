<?php
/**
 *  Admin/Kpi/World/Monster/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_world_monster_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiWorldMonsterView extends Pp_AdminViewClass
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
		
		$admin_m   =& $this->backend->getManager('Admin');
		$monster_m =& $this->backend->getManager('Monster');
		$shop_m    =& $this->backend->getManager('AdminShop');
		
		$list = $admin_m->getKpiUserMonsterList($date);
		
		$master_monster_assoc = $monster_m->getMasterMonsterAssoc();
		
		foreach ($list as $key => $row) {
			$list[$key]['platform'] = $shop_m->getPlatformDisplayNameFromUa($row['ua']);
			
			if (isset($master_monster_assoc[$row['monster_id']])) {
				$list[$key]['name'] = $master_monster_assoc[$row['monster_id']]['name_ja'];
			}
		}
		
		if ($format == 'html') {
			// テンプレート変数にアサイン
			$this->af->setApp('list', $list);
		} else if ($format == 'csv') {
			// CSV準備
			$table = array();

			$table[] = array(
				'モンスター名', 
				'モンスターID', 
				'プラットホーム名', 
				'流通数', 
			);

			foreach ($list as $row) {
				$table[] = array(
					$row['name'], 
					$row['monster_id'], 
					$row['platform'], 
					$row['sum_num'], 
				);
			}

			$this->af->setApp('table', $table);
			$this->af->setApp('filename', 'world_monster_' . date('YmdHis', $_SERVER['REQUEST_TIME']) . '.csv');
		}
    }
}

?>
