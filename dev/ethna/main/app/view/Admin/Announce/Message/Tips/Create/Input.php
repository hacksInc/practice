<?php
/**
 *  Admin/Announce/Message/Tips/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_tips_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageTipsCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_message_tips_create_exec' => null,
	);

	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$tip_id = $this->af->get('tip_id');
		//$this->af->setApp('tip_id', $tip_id);
		//$this->af->setAppNe('message', $this->af->get('message'));
        

    }
}
