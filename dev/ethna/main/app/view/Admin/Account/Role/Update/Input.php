<?php
/**
 *  Admin/Account/Role/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_account_role_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAccountRoleUpdateInput extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$lid = $this->af->get('lid');
		$admin_m =& $this->backend->getManager('Admin');
		
		$user = $admin_m->getAdminUser($lid);
		if ($user['role']) {
			$user['role_name'] = $admin_m->ACCESS_CONTROL_ROLE[$user['role']];
		}
		
		$this->af->setApp('user', $user);
		$this->af->setApp('role_master', $admin_m->ACCESS_CONTROL_ROLE);
    }
}

?>
