<?php
/**
 *  Admin/Present/Distribution/Content/End/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_present_distribution_content_end_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminPresentDistributionContentEndExec extends Pp_Form_AdminPresentDistributionContent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'present_mng_id',
	);
}

/**
 *  admin_present_distribution_content_end_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminPresentDistributionContentEndExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_present_distribution_content_end_exec Action.
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
	 *  admin_present_distribution_content_end_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$present_m =& $this->backend->getManager('AdminPresent');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array(
			'status' => Pp_PresentManager::DIST_STATUS_STOP,
			'account_update' => $this->session->get('lid'),
			//	'distribute_date_end' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
		);
		$present_mng_id = $this->af->get('present_mng_id');
		$ret = $present_m->setPresentMng($present_mng_id, $columns);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}

		return 'admin_present_distribution_content_end_exec';
	}
}

