<?php
/**
 *  Api/User/Dmpw/Set.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  api_user_dmpw_set Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiUserDmpwSet extends Pp_ApiActionForm
{
	/**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		// 'account','dmpw','new_dmpw'があるが、バリデートはperform内で行うので、ここには記述省略
    );
}

/**
 *  api_user_dmpw_set action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiUserDmpwSet extends Pp_ApiActionClass
{
	/**
     *  preprocess of api_user_dmpw_set Action.
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
     *  api_user_dmpw_set action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$user_id = $this->getAuthenticatedBasicAuth('user');
		$account  = $this->af->get('account');
		$dmpw     = $this->af->get('dmpw');
		$new_dmpw = $this->af->get('new_dmpw');
		
		$user_m =& $this->backend->getManager('User');
		
		// データ移行パスワードチェック
		$new_dmpw_ng_type = $user_m->getDmpwNgType($new_dmpw);
		$new_dmpw_ok = ($new_dmpw_ng_type == Pp_UserManager::MIGRATE_PW_NG_TYPE_NONE) ? 1 : 0;
		if (!$new_dmpw_ok) {
			$this->af->setApp('new_dmpw_ok',      $new_dmpw_ok,      true);
			$this->af->setApp('new_dmpw_ng_type', $new_dmpw_ng_type, true);
			return 'error_500';
		}
		
		$ret = $user_m->updateDmpw($user_id, $account, $dmpw, $new_dmpw);
		if (!$ret || Ethna::isError($ret)) {
			$this->af->setApp('new_dmpw_ok',      $new_dmpw_ok,      true);
			$this->af->setApp('new_dmpw_ng_type', $new_dmpw_ng_type, true);
			return 'error_500';
		}
		
		$this->af->setApp('new_dmpw_ok',      $new_dmpw_ok,      true);
		$this->af->setApp('new_dmpw_ng_type', $new_dmpw_ng_type, true);
		
		return 'api_json_encrypt';
    }
}

?>