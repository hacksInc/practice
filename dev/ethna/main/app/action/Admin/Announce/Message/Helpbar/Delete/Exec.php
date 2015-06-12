<?php
/**
 *  Admin/Announce/Message/Helpbar/Delete/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_helpbar_delete_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageHelpbarDeleteExec extends Pp_Form_AdminAnnounceMessageHelpbar
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'helpbar_id',
    );
}

/**
 *  admin_announce_message_helpbar_delete_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageHelpbarDeleteExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_helpbar_delete_exec Action.
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
     *  admin_announce_message_helpbar_delete_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$message_m =& $this->backend->getManager('AdminMessage');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array(
            'helpbar_id' => $this->af->get('helpbar_id'),
            'del_flg' => '1',
	        'account_modified' => $this->session->get('lid'),
//			'date_end' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
		);

		$ret = $message_m->updateMessageHelpbar($columns);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}
		
		// ログ
		$admin_m->addAdminOperationLog('/announce/message', 'helpbar_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $columns)
		);
		
        return 'admin_announce_message_helpbar_delete_exec';
    }
}
