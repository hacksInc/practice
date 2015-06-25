<?php
/**
 *  Admin/Announce/Message/Help/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_help_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageHelpIndex extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_message_help_create_exec' => null,
	);

	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$message_m = $this->backend->getManager('AdminMessage');
		$user_m = $this->backend->getManager('User');

		$list = $message_m->getMessageHelpList(0, 100000, false, $lang, $ua);
//error_log(var_export($list, true));
		
		if ($list) foreach ($list as $i => $row) {
			
//			$list[$i]['help_type'] = $row['type'];

		}

		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);
        $this->af->setApp('form_template', $this->af->form_template);
    }
}
