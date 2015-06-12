<?php
/**
 *  Admin/Developer/User/ViewList.php
 *
 *  @author	 {$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_view_list Form implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_Form_AdminDeveloperUserViewList extends Pp_AdminActionForm
{
	/**
	 *  @access private
	 *  @var	array   form definition.
	 */
	var $form = array(
		'by' => array(
			// Form definition
			'type'		=> VAR_TYPE_STRING,		// Input type
			'form_type'	=> FORM_TYPE_TEXT,		// Form type
			'name'		=> 'by',				// Display name

			// Validator (executes Validator by written order.)
			'required'	=> true,			// Required Option(true/false)
			'min'		=> 1,				// Minimum value
			'max'		=> 4,				// Maximum value
			'regexp'	=> '/^id$|^name$|^term$/', // String by Regexp
			'mbregexp'	=> null,			// Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',	// Matching encoding when using mbregexp
		),

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
			'regexp'	=> '/[0-9]*/',		// String by Regexp
			'mbregexp'	=> null,			// Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',	// Matching encoding when using mbregexp
		),

		'nickname',
		'date_term_start',
		'date_term_end',
	);
}

/**
 *  admin_developer_user_list action implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_Action_AdminDeveloperUserViewList extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_developer_user_list Action.
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
	 *  admin_developer_user_view_list action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$by = $this->af->get('by');
		$id = $this->af->get('id');
		$nickname = $this->af->get('nickname');
		$date_start = $this->af->get('date_term_start');
		$date_end = $this->af->get('date_term_end');

		$user_m =& $this->backend->getManager('AdminUser');

		$base = null;
		switch ($by) {
			case 'id':
				$base = $user_m->getUserBase($id);
				if (!$base) {
					$this->af->setApp('err_mes', "検索ユーザが見つかりませんでした");
					return 'admin_developer_user_view_select';
				}
				break;

			case 'name':
				$bases = $user_m->getUserBaseFromNameLike($nickname);
				$bases_cnt = count($bases);
				if ($bases_cnt == 1) {
					$base = $bases[0];
				} else if ($bases_cnt > 1) {
					$this->af->setApp('bases', $bases);
					return 'admin_developer_user_view_name';
				} else {
					$this->af->setApp('err_mes', "検索ユーザが見つかりませんでした");
					return 'admin_developer_user_view_select';
				}
				break;

			case 'term':
				$bases = $user_m->getUserBaseFromLogindate($date_start, $date_end);
				$bases_cnt = count($bases);
				if ($bases_cnt == 1) {
					$base = $bases[0];
				} else if ($bases_cnt > 1) {
					$this->af->setApp('bases', $bases);
					return 'admin_developer_user_view_term';
				} else {
					$this->af->setApp('err_mes', "検索ユーザが見つかりませんでした");
					return 'admin_developer_user_view_select';
				}
				break;
		}

		$this->af->setApp('base', $base);

		return 'admin_developer_user_view_list';
	}
}
