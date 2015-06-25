<?php
/**
 *  Admin/Developer/User/Ctrl/Userfriendapply.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_userfriendapply Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlUserfriendapply extends Pp_AdminActionForm
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
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'friend_id',     // Display name
            'required'    => true,            // Required Option(true/false)
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
 *  admin_developer_user_ctrl_userfriendapply action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlUserfriendapply extends Pp_AdminActionClass
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
		$friend_m =& $this->backend->getManager('AdminFriend');
		
		$user_id = $this->af->get('id');
		$friend_id = $this->af->get('friend_id');
		$user_base = $user_m->getUserBase($user_id);
		$friend_base = $user_m->getUserBase($friend_id);
		
		//フレンド数を取得
		$my_friend_cnt = $friend_m->countUserFriend(
			$user_id
		);
		//フレンドの最大値に達していてこれ以上増やせない
		if ($my_friend_cnt >= $user_m->getUserFriendMax($user_id)) {
			$this->af->setApp('status_detail_code', SDC_FRIEND_MAX_ERROR, true);
		}
		//相手のフレンド数を取得
		$trg_friend_cnt = $friend_m->countUserFriend(
			$friend_id
		);
		//相手がフレンドの最大値に達していてこれ以上増やせない
		if ($trg_friend_cnt >= $user_m->getUserFriendMax($friend_id)) {
			$this->af->setApp('status_detail_code', SDC_FRIEND_MAX_TARGET_ERROR, true);
		}
		
		$src_friend_list = array();
		$src_friend_cnt = array();
		$dst_friend_list = array();
		$dst_friend_cnt = array();
		
		//フレンドID一覧を取得
		for ($i = 1; $i <= 4; $i++) {
			$src_friend_list[$i] = $friend_m->getFriendListAd($user_id, $i);
			$src_friend_cnt[$i] = count($src_friend_list[$i]);
			$dst_friend_list[$i] = $friend_m->getFriendListAd($user_id, $i);
			$dst_friend_cnt[$i] = count($dst_friend_list[$i]);
		}
		
		$this->af->setApp('base', $user_base);
		$this->af->setApp('friend', $friend_base);
		$this->af->setApp('src_friend_cnt',  $src_friend_cnt);
		$this->af->setApp('dst_friend_cnt',  $dst_friend_cnt);
		
        return 'admin_developer_user_ctrl_userfriendapply';
    }
}

?>