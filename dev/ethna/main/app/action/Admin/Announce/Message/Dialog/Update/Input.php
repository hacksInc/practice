<?php
/**
 *  Admin/Announce/Message/Dialog/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_dialog_update_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageDialogUpdateInput extends Pp_Form_AdminAnnounceMessageDialog
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'dialog_id',
		'base_dialog_id',
        'dialog_type' => array('required' => false),
        'use_name' => array('required' => false),
        'message' => array('required' => false),
        'btn_action',
    );
}

/**
 *  admin_announce_message_dialog_update_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageDialogUpdateInput extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_dialog_update_input Action.
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
    }

    /**
     *  admin_announce_message_dialog_update_input action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$message_m =& $this->backend->getManager('AdminMessage');
		$dialog_id = $this->af->get('dialog_id');
        $btn_action = $this->af->get('btn_action');

        if ($btn_action === "update" && $dialog_id) {
            $row = $message_m->getMessageDialog($dialog_id);
            if (!$row) {
                return 'admin_error_500';
            }
            $this->af->set('dialog_id', $row['dialog_id']);
            $this->af->set('base_dialog_id', $row['dialog_id']);
            $this->af->set('dialog_type', $row['dialog_type']);
            $this->af->set('use_name', $row['use_name']);
            $this->af->set('message', $row['message']);
            $this->af->form['dailog_type']['default'] = $row['dialog_type'];
        }
		$this->af->setApp('row', $row);
//		$this->af->setAppNe('message', $row['message']);

		return 'admin_announce_message_dialog_update_input';
    }
}
