<?php
/**
 *  Admin/Program/Entry/Ini/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_program_entry_ini_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminProgramEntryIniUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_program_entry_ini_update_exec' => null,
	);
	
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$admin_m->includeEntryIni();
    }
}

?>