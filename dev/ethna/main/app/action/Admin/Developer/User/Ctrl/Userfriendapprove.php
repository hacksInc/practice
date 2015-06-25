<?php
/**
 *  Admin/Developer/User/Ctrl/Userfriendapprove.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_userfriendapprove Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlUserfriendapprove extends Pp_AdminActionForm
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
 *  admin_developer_user_ctrl_userfriendapprove action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlUserfriendapprove extends Pp_AdminActionClass
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
		
		//データチェック
		$user_friend = $friend_m->getUserFriend($user_id, $friend_id);
		if ($user_friend) {
			//申請されている状態以外はエラー
			if ($user_friend['status'] != Pp_FriendManager::STATUS_REQUEST_R) {
				return 'admin_developer_user_ctrl_userfriendapproveerr';
			}
		} else { //データが存在していない
			return 'admin_developer_user_ctrl_userfriendapproveerr';
		}
		$friend_user = $friend_m->getUserFriend($friend_id, $user_id);
		if ($friend_user) {
			//申請中状態以外はエラー
			if ($friend_user['status'] != Pp_FriendManager::STATUS_REQUEST_S) {
				return 'admin_developer_user_ctrl_userfriendapproveerr';
			}
		} else { //データが存在していない
			return 'admin_developer_user_ctrl_userfriendapproveerr';
		}
		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();
		
		//フレンド状態にする
		$ret = $friend_m->setUserFriend($user_id, $friend_id, 
				array('status' => Pp_FriendManager::STATUS_FRIEND, 'date_bring' => NULL));
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_developer_user_ctrl_userfriendapproveerr';
		}
		//フレンド状態にする
		$ret = $friend_m->setUserFriend($friend_id, $user_id, 
				array('status' => Pp_FriendManager::STATUS_FRIEND, 'date_bring' => NULL));
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_developer_user_ctrl_userfriendapproveerr';
		}
		// トランザクション完了
		$db->commit();

		$logdata_m = $this->backend->getManager('Logdata');
		$logdata_friend_m = $this->backend->getManager('LogdataFriend');
        // ログ情報
        $input_params = array(
            'user_id' => $user_id,
            'friend_id' => $friend_id,
            'api_transaction_id' => $logdata_m->createApiTransactionId($user_id),
            'account_name' => 'API_friend_proc',
            'old_status' => '',
            'old_date_friend' => null,
            'processing_type' => '',
            'processing_type_name' => '',
        );
        // ログ情報
        $user_base['new_friend_rest'] = $user_base['friend_rest'];
        $friend_base['new_friend_rest'] = $friend_base['friend_rest'];
        $input_params['old_status'] = $user_friend['status'];
        $input_params['old_date_friend'] = $user_friend['date_modified'];
        $input_params['processing_type'] = 'H31';
        $input_params['processing_type_name'] = 'フレンド申請承認';
        $logdata_friend_m->trackingFriendRequest($input_params, $user_base, $friend_base);
		
        return 'admin_developer_user_ctrl_userfriend';
    }
}

?>