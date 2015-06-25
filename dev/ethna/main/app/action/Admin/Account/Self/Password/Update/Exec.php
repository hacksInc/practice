<?php
/**
 *  Admin/Account/Self/Password/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_account_self_password_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAccountSelfPasswordUpdateExec extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'lpw',
		'new',
		'verify',
    );
	
	/**
	 * コンストラクタ
	 *
	 * 複数あるパスワード関連のフォーム定義はここで行う
	 */
	function __construct (&$controller)
	{
		$this->form_template['new'] = $this->form_template['lpw'];
		$this->form_template['new']['name'] = '新しいパスワード';
		
		$this->form_template['verify'] = $this->form_template['lpw'];
		$this->form_template['verify']['name'] = '新しいパスワード（確認）';

		parent::__construct($controller);
	}
}

/**
 *  admin_account_self_password_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAccountSelfPasswordUpdateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_account_self_password_update_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み

		$admin_m =& $this->backend->getManager('Admin');

		$lid    = $this->session->get('lid');
		$lpw    = $this->af->get('lpw');
		$new    = $this->af->get('new');
		$verify = $this->af->get('verify');
		
		if (strcmp($new, $verify) !== 0) {
			$this->af->ae->add(null, "新しいPasswordと新しいPassword（確認）が一致しません。", E_ERROR_DEFAULT);
			return 'admin_error_400';
		}
		
		if (!$admin_m->isValidAdminPassword($lid, $lpw)) {
			$this->ae->add(null, "Passwordが正しくありません。");
			return 'admin_error_400';
		}
    }

    /**
     *  admin_account_self_password_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$lid = $this->session->get('lid');
		$new = $this->af->get('new');
		
		if ($admin_m->updateAdminPassword($lid, $new) !== true) {
			return 'admin_error_500';
		}

		// ログ
		$admin_m->logAdminUserOperation($lid, $lid, 'update', 'パスワード');
		
        return 'admin_account_self_password_update_exec';
    }
}

?>