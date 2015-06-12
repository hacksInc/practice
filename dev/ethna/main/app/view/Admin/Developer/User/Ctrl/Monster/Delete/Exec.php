<?php
/**
 *  Admin/Developer/User/Ctrl/Monster/Delete/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_monster_delete_exec view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserCtrlMonsterDeleteExec extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$user_m    =& $this->backend->getManager('AdminUser');
		$monster_m =& $this->backend->getManager('AdminMonster');
		
		$user_id          = $this->af->get('user_id');
		$user_monster_ids = $this->af->get('user_monster_ids');

		$user_base = $user_m->getUserBase($user_id);
		
		$monster_m->clearUserMonsterListCache($user_id);
		$user_monster_assoc = $monster_m->getUserMonsterAssocForAdmin($user_id);
		
		$this->af->setApp('base',        $user_base);
		$this->af->setApp('monster_cnt', count($user_monster_assoc));
    }
}

?>