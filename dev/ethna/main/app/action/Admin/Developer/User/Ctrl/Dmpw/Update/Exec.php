<?php
/**
 *  Admin/Developer/User/Ctrl/Dmpw/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_dmpw_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlDmpwUpdateExec extends Pp_AdminActionForm
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
        
		'account' => array(
            // Form definition
			// VAR_TYPE_INTにすると空文字列を渡されたときにエラーになるので文字列として扱う
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'account',       // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => '/[a-z]*/',      // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        ),
        
		'agree' => array(
			'type'        => VAR_TYPE_INT,    // Input type
			'required'    => true,            // Required Option(true/false)
			'name'        => '確認チェック',   // Display name
		),
        
		'confpass' => array(
			'type'        => VAR_TYPE_STRING,  // Input type
			'required'    => true,             // Required Option(true/false)
			'regexp'      => '/^jmjaabe$/',    // String by Regexp
			'name'        => '確認パスワード', // Display name
		),
    );
}

/**
 *  admin_developer_user_ctrl_dmpw_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlDmpwUpdateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_user_ctrl_dmpw_update_exec Action.
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
     *  admin_developer_user_ctrl_dmpw_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$user_m =& $this->backend->getManager('AdminUser');
		$admin_m =& $this->backend->getManager('Admin');
        
		$user_id = $this->af->get('id');
		$account = $this->af->get('account');
        
        // 更新する
        $dmpw_assoc = $user_m->updateDmpwForAdmin($user_id, $account);
        if (!is_array($dmpw_assoc) || empty($dmpw_assoc)) {
			$this->af->ae->add(null, "エラーが発生しました。ユーザが存在しないか、またはデータ移行パスワードが変更された可能性があります。");
            return 'admin_error_400';
        }
        
        // ログ（ファイル）
        $log_columns = array(
			'user'          => $this->session->get('lid'),
			'action'        => $this->backend->ctl->getCurrentActionName(),
            'user_id'       => $user_id,
            'account'       => $account,
            'old_dmpw_hash' => $dmpw_assoc['old_dmpw_hash'],
            'new_dmpw_hash' => $dmpw_assoc['new_dmpw_hash'],
        );
        
        $admin_m->addAdminOperationLog('/developer/user/ctrl', 'dmpw_update_log', $log_columns);
        
        // テンプレート変数にセット
        $this->af->setApp('new_dmpw', $dmpw_assoc['new_dmpw']);
        
        return 'admin_developer_user_ctrl_dmpw_update_exec';
    }
}

?>