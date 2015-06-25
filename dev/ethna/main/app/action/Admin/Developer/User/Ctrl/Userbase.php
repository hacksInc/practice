<?php
/**
 *  Admin/Developer/User/Ctrl/Userbase.php
 *
 *  @author	 {$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_userbase Form implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_Form_AdminDeveloperUserCtrlUserbase extends Pp_AdminActionForm
{
	/**
	 *  @access private
	 *  @var	array   form definition.
	 */
	var $form = array(
		'id' => array(
			// Form definition
			// VAR_TYPE_INTにすると空文字列を渡されたときにエラーになるので文字列として扱う
			'type'		=> VAR_TYPE_STRING, // Input type
			'form_type'	=> FORM_TYPE_TEXT,  // Form type
			'name'		=> 'id',			// Display name

			//  Validator (executes Validator by written order.)
			'required'	=> true,			// Required Option(true/false)
			'min'		=> null,			// Minimum value
			'max'		=> null,			// Maximum value
			'regexp'	=> '/[0-9]*/',	  	// String by Regexp
			'mbregexp'	=> null,			// Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',	// Matching encoding when using mbregexp
		),
	);
}

/**
 *  admin_developer_user_ctrl_userbase action implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_Action_AdminDeveloperUserCtrlUserbase extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_developer_user_ctrl_userbase Action.
	 *
	 *  @access public
	 *  @return string	forward name(null: success.
	 *								false: in case you want to exit.)
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
	 *  admin_developer_user_ctrl_userbase action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_developer_user_ctrl_userbase';
	}
}
