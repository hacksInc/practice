<?php
/**
 *  Admin/Developer/Gacha/Weight/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_weight_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaWeightIndex extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$monster_m =& $this->backend->getManager('AdminMonster');
		
		$gacha_id = $this->af->get('gacha_id');
		
		$gacha_category_list = $shop_m->getGachaCatgoryListExForAdmin($gacha_id);
		$gacha_item_list     = $shop_m->getGachaItemListExForAdmin($gacha_id);
		$gacha_list          = $shop_m->getGachaListId($gacha_id);
		$gacha_order_info    = $shop_m->getGachaOrderInfo($gacha_id);
		
		$total_number_of_monsters = array_sum(array_column($gacha_category_list, 'number_of_monsters'));

		$monsters = $monster_m->getMasterMonsterAssoc();
		
		foreach ($gacha_item_list as $i => $row) {
			$monster_id = $row['monster_id'];

			if (isset($monsters[$monster_id])) {
				$monster = $monsters[$monster_id];
			} else {
				$monster = array();
			}
			
			$path = $assetbundle_m->getMonsterImagePath('image', $monster_id);
			if (is_file($path)) {
				$mtime = filemtime($path);
				$monster['mtime'] = $mtime;
			}
			
			$gacha_item_list[$i]['monster'] = $monster;
		}
		
		$this->af->setApp('gacha_category_list', $gacha_category_list);
		$this->af->setApp('gacha_item_list',     $gacha_item_list);
		$this->af->setApp('gacha_list',          $gacha_list);
		$this->af->setApp('gacha_order_info',         $gacha_order_info);
		$this->af->setApp('total_number_of_monsters', $total_number_of_monsters);
		$this->af->setApp('form_template',       $this->af->form_template);
    }
}

?>