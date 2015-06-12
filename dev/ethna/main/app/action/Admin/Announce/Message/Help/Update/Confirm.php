<?php
/**
 *  Admin/Announce/Message/Help/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_help_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageHelpUpdateConfirm extends Pp_Form_AdminAnnounceMessageHelp
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'help_id',
        'base_help_id',
        'use_name',
        'message',
    );
}

/**
 *  admin_announce_message_help_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageHelpUpdateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_help_update_confirm Action.
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
     *  admin_announce_message_help_update_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $message_m = $this->backend->getManager('AdminMessage');

        $help_id = $this->af->get('help_id');
        $base_help_id = $this->af->get('base_help_id');
        $origin_message = $this->af->get('message');

        // 改行は削除しないこと
        $message = $message_m->convertNewlineCharacterToBr($origin_message);
        $this->af->set('message', $message);

        // 改行は6行、文字数が全角15文字(半角だと30文字)
        $max_cnt = 6;
        if ($message_m->checkCountTagByBr($message, $max_cnt) === false){
            $msg = "メッセージ：1行に入力できる改行は" . $max_cnt . "行までです";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_help_update_input';
        }

        $max_cnt = 15;
        if ($message_m->checkLineLength($message, $max_cnt) === false){
            $msg = "メッセージ：1行に入力できる文字数は" . $max_cnt . "文字までです";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_help_update_input';
        }

        if($help_id !== $base_help_id){
            $res = $message_m->getMessageHelp($help_id);
            if($res['help_id']){
                $msg = "ID：入力されたIDは既に利用されています";
                $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
                return 'admin_announce_message_help_update_input';
            }
        }

        return 'admin_announce_message_help_update_confirm';
    }
}
