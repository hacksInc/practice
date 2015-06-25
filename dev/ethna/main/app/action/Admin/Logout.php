<?php
/**
 *  Admin/Logout.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_logout Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogout extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  admin_logout action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogout extends Pp_AdminActionClass
{
	protected $must_login = false;
	
 	protected $must_permission = false;

	/**
     *  preprocess of admin_logout Action.
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
     *  admin_logout action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		if ($this->session->isStart()) {
			$this->session->destroy();
		}

		$this->ae->add(null, "ログアウトしました。");
		
		return 'admin_login';
    }
}

?>