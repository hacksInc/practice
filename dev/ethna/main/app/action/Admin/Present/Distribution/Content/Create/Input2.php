<?php
/**
 *  Admin/Present/Distribution/Content/Create/Input2.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_present_distribution_content_create_input2 Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminPresentDistributionContentCreateInput2 extends Pp_Form_AdminPresentDistributionContent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'present_mng_id',
		'target_type',
		'access_date_start',
		'access_date_end',
		'pp_id',
		'comment_id',
		'comment',
		'present_category',
		'present_value',
		'item_id',
		'lv',
		'num',
		'distribute_date_start',
		'distribute_date_end',
	);
}

/**
 *  admin_present_distribution_content_create_input2 action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminPresentDistributionContentCreateInput2 extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_present_distribution_content_create_input2 Action.
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
	 *  admin_present_distribution_content_create_input2 action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_present_distribution_content_create_input2';
	}
}

