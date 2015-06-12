<?php
/**
 *  Admin/Announce/Event/News/Content/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_event_news_content_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceEventNewsContentUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_event_news_content_update_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
    }
}

?>