<?php
/**
 *  Admin/Developer/Gacha/Order/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pager.php';
require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_order_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaOrderIndex extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
/*
		$shop_m =& $this->backend->getManager('AdminShop');
		$monster_m =& $this->backend->getManager('AdminMonster');
		
		$gacha_id = $this->af->get('gacha_id');
		$order_id = $this->af->get('order_id');
		$page = $this->af->getPageFromPageID();
		
		$gacha_order_info = $shop_m->getGachaOrderInfo($gacha_id);
		$gacha_list       = $shop_m->getGachaListId($gacha_id);
		$order_id_list    = $shop_m->getGachaDrawOrderIdList($gacha_id);
		
		if (!$order_id) {
			$order_id = $gacha_order_info['active_order_id'];
		}
		
		$limit = 100;
		$offset = $limit * $page;

		$gacha_draw_list = $shop_m->getGachaDrawListForAdmin($gacha_id, $order_id, $offset, $limit);
		$monsters = $monster_m->getMasterMonsterAssoc();
		foreach ($gacha_draw_list as $i => $row) {
			$monster_id = $row['monster_id'];
			if (isset($monsters[$monster_id])) {
				$monster = $monsters[$monster_id];
			} else {
				$monster = array();
			}
			
			$gacha_draw_list[$i]['monster'] = $monster;
		}
		
		$total_number_of_monsters = $shop_m->countGachaDrawList($gacha_id, $order_id);

		$options = array(
			'mode'        => 'Sliding',
			'delta'       => 4,
			'importQuery' => false,
			'extraVars'   => array('gacha_id' => $gacha_id, 'order_id' => $order_id),
			'totalItems'  => $total_number_of_monsters,
			'perPage'     => $limit,
		);
		
		$pager =& Pager::factory($options);
		$links = $pager->getLinks();
		
		$this->af->setApp('gacha_order_info',         $gacha_order_info);
		$this->af->setApp('gacha_list',               $gacha_list);
		$this->af->setApp('gacha_draw_list',          $gacha_draw_list);
		$this->af->setApp('order_id_list',            $order_id_list);
		$this->af->setApp('order_id',                 $order_id);
		$this->af->setApp('total_number_of_monsters', $total_number_of_monsters);
		$this->af->setApp('form_template',            $this->af->form_template);
		$this->af->setAppNe('pager', $links);
*/
    }
}

?>