<?php
/**
 *  Admin/Announce/Help/Category/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_help_category_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHelpCategoryUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_help_category_update_exec' => null,
	);

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$help_m =& $this->backend->getManager('AdminHelp');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}