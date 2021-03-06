<?php
/**
 *  Admin/Announce/Message/Tips/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_tips_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageTipsIndex extends Pp_AdminViewClass
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
		$message_m = $this->backend->getManager('AdminMessage');
		$user_m = $this->backend->getManager('User');

		$list = $message_m->getMessageTipsList(0, 100000, false, $lang, $ua);
//error_log(var_export($list, true));
		
		if ($list) foreach ($list as $i => $row) {
			
//			$list[$i]['tips_type'] = $row['type'];

		}

		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);
        $this->af->setApp('form_template', $this->af->form_template);
    }
}
