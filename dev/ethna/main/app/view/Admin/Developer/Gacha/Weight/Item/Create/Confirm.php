<?php
/**
 *  Admin/Developer/Gacha/Weight/Item/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_weight_item_create_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaWeightItemCreateConfirm extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_gacha_weight_item_create_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		$monster_m =& $this->backend->getManager('Monster');
		
		$gacha_id = $this->af->get('gacha_id');
		$monster_id = $this->af->get('monster_id');
		$weight_float = $this->af->get('weight_float');
		
		$weight = $shop_m->convertWeightFloatToWeight($weight_float);
		
		$monster = $monster_m->getMasterMonster($monster_id);
		$rarity = $monster['m_rare'];
		
		$gacha_category_list = $shop_m->getGachaCatgoryListExForAdmin($gacha_id);
		
		foreach ($gacha_category_list as $row) {
			if ($row['rarity'] == $rarity) {
				$number_of_monsters = $shop_m->computeNumberOfMonstersPerGachaItem($weight, $row['weight']);
			}
		}
		
		$this->af->setApp('gacha_category_list', $gacha_category_list);
		$this->af->setApp('rarity',              $rarity);
		$this->af->setApp('number_of_monsters',  $number_of_monsters);
//		$this->af->setApp('form_template',       $this->af->form_template);
    }
}

?>