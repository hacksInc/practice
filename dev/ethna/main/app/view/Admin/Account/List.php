<?php
/**
 *  Admin/Account/List.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_account_list view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAccountList extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$list = $admin_m->getAdminUserList();
		
		foreach ($list as $key => $row) {
			if ($row['role']) {
				$list[$key]['role_name'] = $admin_m->ACCESS_CONTROL_ROLE[$row['role']];
			}
		}
		
		$this->af->setApp('list', $list);
    }
}

?>
