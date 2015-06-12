<?php
/**
 *  Admin/Announce/Message/Error/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_error_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageErrorCreateExec extends Pp_Form_AdminAnnounceMessageError
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'error_id',
		'message',
    );
}

/**
 *  admin_announce_message_error_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageErrorCreateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_error_create_exec Action.
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
     *  admin_announce_message_error_create_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$message_m =& $this->backend->getManager('AdminMessage');
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');
		
		$columns = array();
		foreach ($this->af->form as $key => $value) {
			/*if ($key == 'lu0') {
				continue;
            }*/
			
			$columns[$key] = $this->af->get($key);
		}
	    $columns['account_created'] = $this->session->get('lid');
	    $columns['account_modified'] = $this->session->get('lid');

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();
			
		$ret = $message_m->insertMessageError($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}
		// ログ
		$log_columns = $columns;
//		$log_columns['error_id'] = $message_m->getLastInsertErrorId();
		$admin_m->addAdminOperationLog('/announce/message', 'error_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $log_columns)
		);

		// トランザクション完了
		$db->commit();
		
        return 'admin_announce_message_error_create_exec';
    }
}
