<?php
/**
 *  Admin/Developer/Gacha/Banner/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_banner_update_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaBannerUpdateConfirm extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_gacha_banner_update_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$this->af->setApp('lang', 'ja');
		$this->af->setApp('form_template', $this->af->form_template);
    }
}

?>