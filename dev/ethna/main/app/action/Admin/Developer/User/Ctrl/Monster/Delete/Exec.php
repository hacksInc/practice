<?php
/**
 *  Admin/Developer/User/Ctrl/Monster/Delete/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once  dirname(__FILE__) . '/../Pp_Form_AdminDeveloperUserCtrlMonster.php';

/**
 *  admin_developer_user_ctrl_monster_delete_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlMonsterDeleteExec extends Pp_Form_AdminDeveloperUserCtrlMonster
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'user_id' => array(
			'required' => true,
		),
		
		'user_monster_ids' => array(
			'required' => true,
		),
    );
}

/**
 *  admin_developer_user_ctrl_monster_delete_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlMonsterDeleteExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_user_ctrl_monster_delete_exec Action.
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
     *  admin_developer_user_ctrl_monster_delete_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$monster_m =& $this->backend->getManager('AdminMonster');
		
		$user_id          = $this->af->get('user_id');
		$user_monster_ids = $this->af->get('user_monster_ids');
		
		foreach ($user_monster_ids as $user_monster_id) {
			$ret = $monster_m->delete($user_id, $user_monster_id);
			if (!$ret || Ethna::isError($ret)) {
				return 'admin_error_500';
			}
		}
		
        return 'admin_developer_user_ctrl_monster_delete_exec';
    }
}

?>