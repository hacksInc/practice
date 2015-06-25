<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Item/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_weightextra_item_update_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaWeightextraItemUpdateConfirm extends Pp_AdminViewClass
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
		
		$gacha_id     = $this->af->get('gacha_id');
		$rarity       = $this->af->get('rarity'); 
		$weight_float = $this->af->get('weight_float');
		
		$weight = $shop_m->convertWeightFloatToWeight($weight_float);
		
		$gacha_extra_category_list = $shop_m->getGachaExtraCatgoryListExForAdmin($gacha_id);

		foreach ($gacha_extra_category_list as $row) {
			if ($row['rarity'] == $rarity) {
				$number_of_monsters = $shop_m->computeNumberOfMonstersPerGachaItem($weight, $row['weight']);
			}
		}
		
		$this->af->setApp('gacha_extra_category_list', $gacha_extra_category_list);
		$this->af->setApp('number_of_monsters',  $number_of_monsters);
    }
}

?>