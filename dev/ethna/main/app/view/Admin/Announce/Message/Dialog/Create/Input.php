<?php
/**
 *  Admin/Announce/Message/Dialog/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_dialog_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageDialogCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_message_dialog_create_exec' => null,
	);

	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$dialog_id = $this->af->get('dialog_id');
/*		$this->af->setApp('dialog_id', $dialog_id);
		$this->af->setApp('dialog_type', $this->af->get('dialog_type'));
		$this->af->setApp('use_name', $this->af->get('use_name'));
		$this->af->setAppNe('message', $this->af->get('message'));
        
        $row = $this->af->getApp('row');
		
        $this->af->setApp('form_template', $this->af->form_template);
        $this->af->setAppNe('message', $row['message']);
 */
    }
}
