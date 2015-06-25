<?php
/**
 *  Admin/Account/Role/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_account_role_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAccountRoleUpdateExec extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'lid' => array(
            'custom'   => 'checkLidExists,checkLidNotCurrent',
        ),
		'role',
    );
}

/**
 *  admin_account_role_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAccountRoleUpdateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_account_role_update_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
/*
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }
*/

    /**
     *  admin_account_role_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$lid = $this->af->get('lid');
		$role = $this->af->get('role');
		
		$columns = array('role' => $role);
		
		if ($admin_m->setAdminUser($lid, $columns) !== true) {
			return 'admin_error_500';
		}

		// ログ
		$role_name = $admin_m->ACCESS_CONTROL_ROLE[$role];
		$admin_m->logAdminUserOperation($this->session->get('lid'), $lid, 'update', $role_name);
		
		return 'admin_account_role_update_exec';
    }
}

?>