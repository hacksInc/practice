<?php
/**
 *  Admin/Developer/User/Ctrl/Listchg.php
 *
 *  @author	 {$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_listchg Form implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_Form_AdminDeveloperUserCtrlListchg extends Pp_AdminActionForm
{
	/**
	 *  @access private
	 *  @var	array   form definition.
	 */
	var $form = array(
		'id' => array(
			// Form definition
			// VAR_TYPE_INTにすると空文字列を渡されたときにエラーになるので文字列として扱う
			'type'		=> VAR_TYPE_STRING,	// Input type
			'form_type'	=> FORM_TYPE_TEXT,  // Form type
			'name'		=> 'id',			// Display name

			//  Validator (executes Validator by written order.)
			'required'	=> false,			// Required Option(true/false)
			'min'		=> null,			// Minimum value
			'max'		=> null,			// Maximum value
			'regexp'	=> '/[0-9]*/',	  	// String by Regexp
			'mbregexp'	=> null,			// Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8', // Matching encoding when using mbregexp
		),

		'name',
		'attr',
		'ban_limit',
	);
}

/**
 *  admin_developer_user_listchg action implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_Action_AdminDeveloperUserCtrlListchg extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_developer_user_listchg Action.
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
	 *  admin_developer_user_ctrl_listchg action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$id			= $this->af->get('id');
		$name   	= $this->af->get('name');
		$attr   	= $this->af->get('attr');
		$ban_limit	= $this->af->get('ban_limit');
		if (strlen($ban_limit) == 0) $ban_limit = null;

		$user_m =& $this->backend->getManager('AdminUser');

		$ret = $user_m->updateUserBase($id, array(
			'name' => $name,
			'attr' => $attr,
			'ban_limit' => $ban_limit,
		));
		if (!$ret || Ethna::isError($ret)) {
			$this->af->setAppNe('err_msg', $ret);
			return 'admin_developer_user_ctrl_error';
		}

		$base = $user_m->getUserBase($id);

		$this->af->setApp('base', $base);
		$this->af->setApp('by', 'id');

		return 'admin_developer_user_ctrl_list';
	}
}
