<?php
/**
 *  Admin/Announce/Message/Helpbar/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_helpbar_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageHelpbarCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_message_helpbar_create_exec' => null,
	);

	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$helpbar_id = $this->af->get('helpbar_id');
//		$this->af->setApp('helpbar_id', $helpbar_id);
//		$this->af->setApp('use_name', $this->af->get('use_name'));
//		$this->af->setAppNe('message', $this->af->get('message'));

    }
}
