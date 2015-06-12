<?php
/**
 *  Admin/Announce/Message/Helpbar/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_helpbar_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageHelpbarUpdateConfirm extends Pp_Form_AdminAnnounceMessageHelpbar
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'helpbar_id',
		'base_helpbar_id',
		'message',
    );
}

/**
 *  admin_announce_message_helpbar_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageHelpbarUpdateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_helpbar_update_confirm Action.
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
     *  admin_announce_message_helpbar_update_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {

        $message_m = $this->backend->getManager('AdminMessage');
        $helpbar_id = $this->af->get('helpbar_id');
        $base_helpbar_id = $this->af->get('base_helpbar_id');
        $origin_message = $this->af->get('message');

        // 改行は削除しないこと
        $message = $message_m->convertNewlineCharacterToBr($origin_message);
        $this->af->set('message', $message);

        $max_cnt = 0;
        if ($message_m->checkCountTagByBr($message, $max_cnt) === false){
            $msg = "メッセージ：改行は入力できません";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_helpbar_update_input';
        }

        $max_cnt = 50;
        if ($message_m->checkLineLength($message, $max_cnt) === false){
            $msg = "メッセージ：1行に入力できる文字数は" . $max_cnt . "文字までです";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_helpbar_update_input';
        }

        if ($helpbar_id !== $base_helpbar_id){
            $res = $message_m->getMessageHelpbar($helpbar_id);
            /*if (!$res) {
                return 'admin_error_500';
            }*/

            if($res['helpbar_id']){
                $msg = "ID：入力されたIDは既に利用されています";
                $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
                return 'admin_announce_message_helpbar_update_input';
            }
        }
        return 'admin_announce_message_helpbar_update_confirm';
    }
}
