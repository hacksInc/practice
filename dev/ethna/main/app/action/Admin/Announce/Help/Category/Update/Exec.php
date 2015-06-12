<?php
/**
 *  Admin/Announce/Help/Category/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_help_category_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpCategoryUpdateExec extends Pp_Form_AdminAnnounceHelpCategory
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'category_id',
		'priority',
		'date_disp',
		'title',
		'date_start',
		'date_end',
	);
}

/**
 *  admin_announce_help_category_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpCategoryUpdateExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_help_category_update_exec Action.
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
	 *  admin_announce_help_category_update_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$help_m =& $this->backend->getManager('AdminHelp');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}
		$category_id = $this->af->get('category_id');

		$row = $help_m->getHelpCategory($category_id);
		if (!$row) {
			return 'admin_error_500';
		}

		$columns = array(
			'category_id'            => $category_id,
			'priority'               => $this->af->get('priority'),
			'title'                  => $this->af->get('title'),
			'date_disp'              => $this->af->get('date_disp'),
			'date_start'             => $this->af->get('date_start'),
			'date_end'               => $this->af->get('date_end'),
		);

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// DBへ登録
		$ret = $help_m->updateHelpCategory($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}

		// ログ
		$log_columns = $columns;
		$admin_m->addAdminOperationLog('/announce/help', 'category_log',
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $log_columns)
		);

		// トランザクション完了
		$db->commit();

		return 'admin_announce_help_category_update_exec';
	}
}
