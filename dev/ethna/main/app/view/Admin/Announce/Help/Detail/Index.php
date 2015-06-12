<?php
/**
 *  Admin/Announce/Help/Detail/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_help_detail_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHelpDetailIndex extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_help_detail_create_exec' => null,
	);

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$help_m =& $this->backend->getManager('AdminHelp');
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');

		$list = $help_m->getHelpDetailList(0, 100000, false);
		//error_log(var_export($list, true));

		if ($list) foreach ($list as $i => $row) {
		}

		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
