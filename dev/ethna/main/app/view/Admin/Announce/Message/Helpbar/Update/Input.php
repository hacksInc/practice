<?php
/**
 *  Admin/Announce/Message/Helpbar/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_helpbar_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageHelpbarUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_message_helpbar_update_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$row = $this->af->getApp('row');
		
        $this->af->setApp('form_template', $this->af->form_template);
        $this->af->setAppNe('message', $row['message']);
    }
}
