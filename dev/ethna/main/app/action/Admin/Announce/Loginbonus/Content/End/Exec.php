<?php
/**
 *  Admin/Announce/Loginbonus/Content/End/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_loginbonus_content_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceLoginbonusContentEndExec extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'id',
    );
}

/**
 *  admin_announce_loginbonus_content_end_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceLoginbonusContentEndExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_loginbonus_content_end_exec Action.
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
     *  admin_announce_loginbonus_content_end_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$loginbonus_m =& $this->backend->getManager('AdminLoginbonus');
		$admin_m =& $this->backend->getManager('Admin');
		$login_bonus_id = $this->af->get("id");
		$lb = $loginbonus_m->getLoginbonusId($login_bonus_id);
		
		$name = $lb['name'];
		$date_start = $lb['date_start'];
	//	$date_end = date('Y-m-d', $_SERVER['REQUEST_TIME']);//今日を終了日にする
		$date_end = date('Y-m-d', $_SERVER['REQUEST_TIME']-86400);//昨日を終了日にする
		
		$columns = array(
					'login_bonus_id' => $login_bonus_id,
					'name'           => $name,
					'date_start'     => $date_start,
					'date_end'       => $date_end,
					'account_reg'    => $this->session->get('lid'),
					'account_upd'    => $this->session->get('lid'),
		);
		$ret = $loginbonus_m->updateLoginBonus($columns);
		if (!$ret || Ethna::isError($ret)) {
			$this->af->setAppNe('err_msg', $ret);
	        return 'admin_announce_loginbonus_content_error';
		}
		
	//	return 'admin_announce_loginbonus_content_index';
		header( "Location: /admin/announce/loginbonus/content/index" );
		exit;
    }
}

?>