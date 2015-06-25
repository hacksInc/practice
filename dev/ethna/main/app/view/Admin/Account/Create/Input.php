<?php
/**
 *  Admin/Account/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_account_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAccountCreateInput extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');

		$this->af->setApp('role_master', $admin_m->ACCESS_CONTROL_ROLE);
		
		parent::preforward();
    }
}

?>
