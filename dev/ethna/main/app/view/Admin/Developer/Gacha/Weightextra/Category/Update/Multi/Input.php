<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Category/Update/Multi/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_weightextra_category_update_multi_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaWeightextraCategoryUpdateMultiInput extends Pp_AdminViewClass
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

		$this->af->setApp('gacha_extra_category_list', $gacha_extra_category_list);
    }
}

?>