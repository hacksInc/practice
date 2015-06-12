<?php
/**
 *  Api/User/Account/Check.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp/Ngword.php';

/**
 *  api_user_account_check Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiUserAccountCheck extends Pp_ApiActionForm
{
	protected $password_type = 'appw';

    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		// 'account', 'dmpw'はアクションフォームではバリデートしない
		// （戻り値（HTTPボディ）にOKかNGかを含めたいので、アクションクラスのperformでチェックする）
    );
}

/**
 *  api_user_account_check action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiUserAccountCheck extends Pp_ApiActionClass
{
	protected $must_authenticate = false;

    /**
     *  preprocess of api_user_account_check Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        if ($this->af->validate() > 0) {
            return 'error_400';
        }

        return null;
    }

    /**
     *  api_user_account_check action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$account = $this->af->get('account');
		$dmpw    = $this->af->get('dmpw');

		$user_m = $this->backend->getManager('User');

		// アカウントチェック
		$account_ng_type = $user_m->getAccountNgTypeStrict($account);
		$account_ok = ($account_ng_type == Pp_UserManager::MIGRATE_ID_NG_TYPE_NONE) ? 1 : 0;
		
		// データ移行パスワードチェック
		$dmpw_ng_type = $user_m->getDmpwNgType($dmpw);
		$dmpw_ok = ($dmpw_ng_type == Pp_UserManager::MIGRATE_PW_NG_TYPE_NONE) ? 1 : 0;
		
		$this->af->setApp('account_ok',      $account_ok,      true);
		$this->af->setApp('account_ng_type', $account_ng_type, true);
		$this->af->setApp('dmpw_ok',         $dmpw_ok,         true);
		$this->af->setApp('dmpw_ng_type',    $dmpw_ng_type,    true);
		
        return 'api_json_encrypt';
    }
}

?>