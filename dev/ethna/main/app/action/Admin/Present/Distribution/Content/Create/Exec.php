<?php
/**
 *  Admin/Present/Distribution/Content/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_present_distribution_content_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminPresentDistributionContentCreateExec extends Pp_Form_AdminPresentDistributionContent
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
 *  admin_present_distribution_content_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminPresentDistributionContentCreateExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_present_distribution_content_create_exec Action.
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
	 *  admin_present_distribution_content_create_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$present_m =& $this->backend->getManager('AdminPresent');

		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}
		$columns['status'] = Pp_PresentManager::DIST_STATUS_START;
		$columns['distribute_user_total'] = 0;
		$columns['account_update'] = '';
		if ($columns['present_category'] == Pp_PresentManager::CATEGORY_PHOTO)
		{
			$columns['present_value'] = $columns['item_id'];
		}
		unset($columns['item_id']);

		$present_mng_id = $columns['present_mng_id'];
		unset($columns['present_mng_id']);
		if ($present_mng_id >= 0) {
			$columns['account_update'] = $this->session->get('lid');
			$ret = $present_m->updatePresentMng($present_mng_id, $columns);
		}
		else {
			$columns['account_regist'] = $this->session->get('lid');
			$ret = $present_m->insertPresentMng($present_mng_id, $columns);
		}
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}

		return 'admin_present_distribution_content_create_exec';
	}
}

