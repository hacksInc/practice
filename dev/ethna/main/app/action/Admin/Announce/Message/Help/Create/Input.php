<?php
/**
 *  Admin/Announce/Message/Help/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_help_create_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageHelpCreateInput extends Pp_Form_AdminAnnounceMessageHelp
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'help_id' => array('required' => false),
        'message' => array('required' => false),
        'use_name' => array('required' => false),
        'btn_action',
    );
}

/**
 *  admin_announce_message_help_create_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageHelpCreateInput extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_help_create_input Action.
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
     *  admin_announce_message_help_create_input action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$message_m =& $this->backend->getManager('AdminMessage');
		$help_id = $this->af->get('help_id');
        $btn_action = $this->af->get('btn_action');

        if ($btn_action === "copy" && $help_id) {
            $row = $message_m->getMessageHelp($help_id);
            if (!$row) {
                return 'admin_error_500';
            }
            $this->af->set('help_id', $row['help_id']);
            $this->af->set('use_name', $row['use_name']);
            $this->af->set('message', $row['message']);
		}
		
        return 'admin_announce_message_help_create_input';
    }
}
