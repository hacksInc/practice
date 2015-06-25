<?php
/**
 *  Admin/Announce/Message/Help/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_help_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageHelpUpdateExec extends Pp_Form_AdminAnnounceMessageHelp
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'help_id',
		'use_name',
		'message',
    );
}

/**
 *  admin_announce_message_help_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageHelpUpdateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_help_update_exec Action.
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
     *  admin_announce_message_help_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$message_m =& $this->backend->getManager('AdminMessage');
		$admin_m =& $this->backend->getManager('Admin');
		
		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}
	    $columns['account_modified'] = $this->session->get('lid');
		$ret = $message_m->updateMessageHelp($columns);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}

		// ログ
		$admin_m->addAdminOperationLog('/announce/message', 'help_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $columns)
		);
		
        return 'admin_announce_message_help_update_exec';
    }
}
