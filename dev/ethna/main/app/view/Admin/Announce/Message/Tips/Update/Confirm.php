<?php
/**
 *  Admin/Announce/Message/Tips/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_tips_update_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageTipsUpdateConfirm extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_message_tips_update_exec' => null,
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
		$this->af->setAppNe('message', $this->af->get('message'));
    }
}
