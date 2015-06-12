<?php
/**
 *  Admin/Announce/Home/Banner/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_home_banner_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHomeBannerUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_home_banner_update_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$news_m =& $this->backend->getManager('AdminNews');
        
        $this->af->setApp('mtime', $news_m->getHomeBannerDirMtime());
    }
}

?>