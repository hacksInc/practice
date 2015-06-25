<?php
/**
 *  Admin/Announce/Help/Detail/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_help_detail_create_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHelpDetailCreateConfirm extends Pp_AdminViewClass
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
		$this->af->setApp('form_template', $this->af->form_template);
		$this->af->setAppNe('title', $this->af->get('title'));
		$this->af->setAppNe('body',  $this->af->get('body'));
	}
}

