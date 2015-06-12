<?php
/**
 *  Admin/Account/Permission.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_account_permission view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAccountPermission extends Pp_AdminViewClass
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
		$this->af->setApp('permission_master', $admin_m->ACCESS_CONTROL_PERMISSION);

		$this->af->setApp('role_master_cnt', count($admin_m->ACCESS_CONTROL_ROLE));
	}
}

?>