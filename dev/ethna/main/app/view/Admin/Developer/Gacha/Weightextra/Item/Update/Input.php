<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Item/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_weightextra_item_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaWeightextraItemUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_gacha_weightextra_item_update_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		
		$gacha_id   = $this->af->get('gacha_id');
		$rarity     = $this->af->get('rarity'); 
		$monster_id = $this->af->get('monster_id');
		$monster_lv = $this->af->get('monster_lv');
		
		$gacha_extra_category_list = $shop_m->getGachaExtraCatgoryListExForAdmin($gacha_id);
		
		$gacha_extra_item = $shop_m->getGachaExtraItem($gacha_id, $rarity, $monster_id, $monster_lv);
		$gacha_extra_item['weight_float'] = $shop_m->convertWeightToWeightFloat($gacha_extra_item['weight']);

		$this->af->setApp('gacha_extra_category_list', $gacha_extra_category_list);
		$this->af->setApp('gacha_extra_item',          $gacha_extra_item);
    }
}

?>