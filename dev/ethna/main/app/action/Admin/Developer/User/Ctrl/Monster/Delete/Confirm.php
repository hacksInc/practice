<?php
/**
 *  Admin/Developer/User/Ctrl/Monster/Delete/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once  dirname(__FILE__) . '/../Pp_Form_AdminDeveloperUserCtrlMonster.php';

/**
 *  admin_developer_user_ctrl_monster_delete_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlMonsterDeleteConfirm extends Pp_Form_AdminDeveloperUserCtrlMonster
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
 *  admin_developer_user_ctrl_monster_delete_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlMonsterDeleteConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_user_ctrl_monster_delete_confirm Action.
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
     *  admin_developer_user_ctrl_monster_delete_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_user_ctrl_monster_delete_confirm';
    }
}

?>