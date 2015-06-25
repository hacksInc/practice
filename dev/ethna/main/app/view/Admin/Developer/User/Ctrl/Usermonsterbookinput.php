<?php
/**
 *  Admin/Developer/User/Ctrl/Usermonsterbookinput.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_usermonsterbookinput view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserCtrlUsermonsterbookinput extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$user_m =& $this->backend->getManager('User');

		$user_id = $this->af->get('id');
		
		$user_base = $user_m->getUserBase($user_id);
		
		$this->af->setApp('base', $user_base);
		
		parent::preforward();
	}
}

?>
