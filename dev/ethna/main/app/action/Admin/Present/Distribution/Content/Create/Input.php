<?php
/**
 *  Admin/Present/Distribution/Content/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_present_distribution_content_create_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminPresentDistributionContentCreateInput extends Pp_Form_AdminPresentDistributionContent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'present_mng_id' => array('required' => false),
	);
}

/**
 *  admin_present_distribution_content_create_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminPresentDistributionContentCreateInput extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_present_distribution_content_create_input Action.
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
	 *  admin_present_distribution_content_create_input action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$present_m =& $this->backend->getManager('AdminPresent');
		$present_mng_id = $this->af->get('present_mng_id');

		$row = null;
		if ($present_mng_id) {
			$row = $present_m->getPresentMng($present_mng_id);
			if (!$row) {
				return 'admin_error_500';
			}
		}
		if ($row == null) $row['target_type'] = 2;
		$this->af->setApp('row', $row);

		return 'admin_present_distribution_content_create_input';
	}
}

