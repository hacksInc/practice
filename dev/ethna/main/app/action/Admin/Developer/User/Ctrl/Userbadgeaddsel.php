<?php
/**
 *  Admin/Developer/User/Ctrl/Userbadgeaddsel.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_userbadgeaddsel Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlUserbadgeaddsel extends Pp_AdminActionForm
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

		'table' => array(
            'required'    => true,                // Required Option(true/false)
        ),
		'badge_list',
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
 *  admin_developer_user_ctrl_userbadgeaddsel action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlUserbadgeaddsel extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_user_ctrl_userbadgeaddsel Action.
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
     *  admin_developer_user_ctrl_userbadgeaddsel action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$developer_m =& $this->backend->getManager('Developer');

		$table = $this->af->get('table');

		$user_m =& $this->backend->getManager('User');
		$badge_m =& $this->backend->getManager('Badge');
		
		$user_id = $this->af->get('id');
		$user_base = $user_m->getUserBaseForApiResponse($user_id);

		$badge_list = $this->af->get('badge_list');
		$badges = explode("\r\n", $badge_list);
		$badge_alls = $badge_m->getMasterBadge();
		$badge_add = array();
		$badge_err = array();
		$badge_all = array();
		foreach($badge_alls as $k => $v) {
			$badge_all[$v['badge_id']] = $v;
		}
		foreach($badges as $key => $val) {
			if (array_key_exists($val, $badge_all) ) {
				$ret = $badge_m->addUserBadgeUpperLimit($user_id, $val, 1);
				$badge_add[] = array('id' => $val, 'name' => $badge_all[$val]['name_ja']);
			} else {
				$badge_err[] = $val;
			}
		}
		$this->af->setApp('base',         $user_base);
		$this->af->setApp('badge_add',    $badge_add);
		$this->af->setApp('badge_add_cnt',count($badge_add));
		$this->af->setApp('badge_err',    $badge_err);
		$this->af->setApp('badge_err_cnt',count($badge_err));

        return 'admin_developer_user_ctrl_userbadgeaddsel';
    }
}

?>