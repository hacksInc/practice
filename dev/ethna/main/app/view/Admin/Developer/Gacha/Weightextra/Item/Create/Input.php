<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Item/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_weightextra_item_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaWeightextraItemCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_gacha_weightextra_item_create_exec' => null,
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

		$this->af->setApp('gacha_extra_category_list', $gacha_extra_category_list);
//		$this->af->setApp('form_template',       $this->af->form_template);
    }
}

?>