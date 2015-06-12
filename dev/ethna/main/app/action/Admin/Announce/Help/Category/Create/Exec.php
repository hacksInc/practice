<?php
/**
 *  Admin/Announce/Help/Category/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_help_category_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpCategoryCreateExec extends Pp_Form_AdminAnnounceHelpCategory
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'priority',
		//		'date_disp',
		'date_disp' => array('required' => false, 'custom' => null),
		'title',
		'date_start',
		'date_end',
	);
}

/**
 *  admin_announce_help_category_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpCategoryCreateExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_help_category_create_exec Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */

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

	/**
	 *  admin_announce_help_category_create_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$help_m =& $this->backend->getManager('AdminHelp');
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}

		$columns['date_disp'] = $this->af->get('date_start');

		// 仮挿入
		$columns['test_flag'] = 0;

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		$ret = $help_m->insertHelpCategory($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}

		$category_id = $help_m->getLastInsertCategoryId();

		// ログ
		$log_columns = $columns;
		$log_columns['category_id'] = $category_id;
		$admin_m->addAdminOperationLog('/announce/help', 'category_log',
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $log_columns)
		);

		// トランザクション完了
		$db->commit();

		return 'admin_announce_help_category_create_exec';
	}
}

