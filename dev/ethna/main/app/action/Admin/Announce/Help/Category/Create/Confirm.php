<?php
/**
 *  Admin/Announce/Help/Category/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_help_category_create_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpCategoryCreateConfirm extends Pp_Form_AdminAnnounceHelpCategory
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'priority',
		//		'date_disp',
		'title',
		'date_start',
		'date_end',
	);
}

/**
 *  admin_announce_help_category_create_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpCategoryCreateConfirm extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_help_category_create_confirm Action.
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
	 *  admin_announce_help_category_create_confirm action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_announce_help_category_create_confirm';
	}
}

