<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Category/Update/Multi/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_weightextra_category_update_multi_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaWeightextraCategoryUpdateMultiConfirm extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_gacha_weightextra_category_update_multi_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		
		$gacha_id = $this->af->get('gacha_id');
		
		$gacha_extra_category_list = $shop_m->getGachaExtraCatgoryListExForAdmin($gacha_id);
		
		foreach ($gacha_extra_category_list as $i => $row) {
			$weight_float = $this->af->getWeightFloatByRarity($row['rarity']);
			$gacha_extra_category_list[$i]['weight_float'] = $weight_float;
		}
		
		$this->af->setApp('gacha_extra_category_list', $gacha_extra_category_list);
    }
}

?>