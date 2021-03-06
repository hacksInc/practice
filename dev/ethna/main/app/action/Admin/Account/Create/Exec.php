<?php
/**
 *  Admin/Account/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_account_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAccountCreateExec extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'lid',
		'lpw',
		'role',
    );
}

/**
 *  admin_account_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAccountCreateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_account_create_exec Action.
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
     *  admin_account_create_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$lid = $this->af->get('lid');
		$lpw = $this->af->get('lpw');
		$role = $this->af->get('role');
		
		if ($admin_m->createAdminUser($lid, $lpw, $role) !== true) {
			return 'admin_error_500';
		}
		
		// ログ
		$admin_m->logAdminUserOperation($this->session->get('lid'), $lid, 'create');
		
		return 'admin_account_create_exec';
    }
}

?>