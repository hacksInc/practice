<?php
/**
 *  Admin/Developer/User/Ctrl/Userfrienddel.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_userfrienddel Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlUserfrienddel extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'id' => array(
            // Form definition
			// VAR_TYPE_INTにすると空文字列を渡されたときにエラーになるので文字列として扱う
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'id',            // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => '/[0-9]*/',      // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        ),

		'friend_id' => array(
            'required'    => true,                // Required Option(true/false)
        ),
    );

    /**
     *  Form input value convert filter : sample
     *
     *  @access protected
     *  @param  mixed   $value  Form Input Value
     *  @return mixed           Converted result.
     */
    /*
    function _filter_sample($value)
    {
        //  convert to upper case.
        return strtoupper($value);
    }
    */
}

/**
 *  admin_developer_user_ctrl_userfrienddel action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlUserfrienddel extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_user_edit Action.
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
     *  admin_developer_user_edit action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$developer_m =& $this->backend->getManager('Developer');
		
		$user_m =& $this->backend->getManager('User');
		$friend_m =& $this->backend->getManager('Friend');
		
		$user_id = $this->af->get('id');
		$friend_id = $this->af->get('friend_id');
		
		$user_base = $user_m->getUserBaseForApiResponse($user_id);
		$friend_base = $user_m->getUserBaseForApiResponse($friend_id);
		
		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();
		
		//削除する
		$ret = $friend_m->deleteUserFriend($user_id, $friend_id);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			$this->af->setAppNe('err_msg', $ret);
			return 'admin_developer_user_ctrl_error';
		}
		$ret = $friend_m->deleteUserFriend($friend_id, $user_id);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			$this->af->setAppNe('err_msg', $ret);
			return 'admin_developer_user_ctrl_error';
		}
		
		//フレンドの最大値から現在のフレンド数を引いた値
		$ret = $user_m->setUserBase($user_id, array('friend_rest' => $user_m->getUserFriendMax($user_id) - $friend_m->countUserFriend($user_id)));
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			$this->af->setAppNe('err_msg', $ret);
			return 'admin_developer_user_ctrl_error';
		}
		$ret = $user_m->setUserBase($friend_id, array('friend_rest' => $user_m->getUserFriendMax($friend_id) - $friend_m->countUserFriend($friend_id)));
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			$this->af->setAppNe('err_msg', $ret);
			return 'admin_developer_user_ctrl_error';
		}
		
		// トランザクション完了
		$db->commit();
		
        return 'admin_developer_user_ctrl_userfriend';
    }
}

?>