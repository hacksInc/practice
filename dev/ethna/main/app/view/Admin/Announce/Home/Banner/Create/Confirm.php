<?php
/**
 *  Admin/Announce/Home/Banner/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_home_banner_create_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHomeBannerCreateConfirm extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_home_banner_create_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$this->af->setApp('form_template', $this->af->form_template);
    }
}

?>