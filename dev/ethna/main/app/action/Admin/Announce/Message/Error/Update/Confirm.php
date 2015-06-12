<?php
/**
 *  Admin/Announce/Message/Error/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_error_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageErrorUpdateConfirm extends Pp_Form_AdminAnnounceMessageError
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'error_id',
        'base_error_id',
        'message',
    );
}

/**
 *  admin_announce_message_error_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageErrorUpdateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_error_update_confirm Action.
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
     *  admin_announce_message_error_update_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {

        $message_m = $this->backend->getManager('AdminMessage');
        $error_id = $this->af->get('error_id');
        $base_error_id = $this->af->get('base_error_id');
        $origin_message = $this->af->get('message');

        // 改行は削除しないこと
        $message = $message_m->convertNewlineCharacterToBr($origin_message);
        $this->af->set('message', $message);

        $max_cnt = 6;
        if ($message_m->checkCountTagByBr($message, $max_cnt) === false){
            $msg = "メッセージ：1行に入力できる改行は" . $max_cnt . "行までです";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_error_update_input';
        }

        $max_cnt = 50;
        if ($message_m->checkLineLength($message, $max_cnt) === false){
            $msg = "メッセージ：1行に入力できる文字数は" . $max_cnt . "文字までです";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_error_update_input';
        }

        if ($error_id !== $base_error_id){
            $res = $message_m->getMessageError($error_id);
            /*if (!$res) {
                return 'admin_error_500';
            }*/

            if($res['error_id']){
                $msg = "ID：入力されたIDは既に利用されています";
                $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
                return 'admin_announce_message_error_update_input';
            }
        }

        return 'admin_announce_message_error_update_confirm';
    }
}
