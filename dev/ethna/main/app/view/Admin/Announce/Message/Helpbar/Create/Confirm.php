<?php
/**
 *  Admin/Announce/Message/Helpbar/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_helpbar_create_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageHelpbarCreateConfirm extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$this->af->setApp('form_template', $this->af->form_template);
		$this->af->setApp('message', $this->af->get('message'));
    }
}
