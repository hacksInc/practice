<?php
/**
 *  Admin/Present/Distribution/Content/Bulk/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_present_distribution_content_bulk_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminPresentDistributionContentBulkConfirm extends Pp_Form_AdminPresentDistributionContent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'pp_id',
		'comment_id',
		'comment',
		'present_value',
		'item_id',
		'lv',
		'num',
		'ppid_list',
	);
}

/**
 *  admin_present_distribution_content_bulk_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminPresentDistributionContentBulkConfirm extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_present_distribution_content_bulk_confirm Action.
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
	 *  admin_present_distribution_content_bulk_confirm action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_present_distribution_content_bulk_confirm';
	}
}

