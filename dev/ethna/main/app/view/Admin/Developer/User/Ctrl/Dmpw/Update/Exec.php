<?php
/**
 *  Admin/Developer/User/Ctrl/Dmpw/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_dmpw_update_exec view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserCtrlDmpwUpdateExec extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$user_m =& $this->backend->getManager('AdminUser');
        
        $table = 't_user_base';
        
        $user_id = $this->af->get('id');
        
        $base = $user_m->getUserBaseDirect($user_id);
        
		$label = $developer_m->getMasterColumnsLabel($table);
        
        $this->af->setApp('base',  $base);
        $this->af->setApp('label', $label);
    }
}

?>