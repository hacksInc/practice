<?php
/**
 *  Admin/Announce/Help/Category/Flag/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_announce_help_category_flag_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpCategoryFlagExec extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'category_id',
		'test_flag',
    );
}

/**
 *  admin_announce_help_category_flag_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpCategoryFlagExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_help_category_flag_exec Action.
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
     *  admin_announce_help_category_flag_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$help_m =& $this->backend->getManager('AdminHelp');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array(
			'category_id' => $this->af->get('category_id'),
			'test_flag'  => $this->af->get('test_flag'),
		);

		$ret = $help_m->updateHelpCategory($columns);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}

		// ログ
		$admin_m->addAdminOperationLog('/announce/help', 'category_log',
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $columns)
		);

        return 'admin_announce_help_category_flag_exec';
    }
}

?>
