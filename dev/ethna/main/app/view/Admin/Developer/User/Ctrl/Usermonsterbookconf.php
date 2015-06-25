<?php
/**
 *  Admin/Developer/User/Ctrl/Usermonsterbookconf.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_usermonsterbookconf view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserCtrlUsermonsterbookconf extends Pp_AdminViewClass
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
		$monster_m =& $this->backend->getManager('AdminMonster');

		$user_id = $this->af->get('id');
		$monster_id = $this->af->get('monster_id');
		$status = $this->af->get('status');
		
		$user_base = $user_m->getUserBase($user_id);
		$monster_data = $monster_m->getMasterMonster($monster_id);
		
		/*
		$ret = $monster_m->setUserMonsterBook($user_id, $monster_id, $status)
		if (!$ret || Ethna::isError($ret)) {
			$this->af->setAppNe('err_msg', $ret);
			return 'admin_developer_user_ctrl_usermonsterbookconf';
		}
		*/
		
		$this->af->setApp('base', $user_base);
		$this->af->setApp('monster_name', $monster_data['name_ja']);
		
		parent::preforward();
	}
}

?>
