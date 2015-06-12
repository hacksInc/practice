<?php
/**
 *  Admin/Announce/Message/Dialog/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_dialog_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageDialogUpdateConfirm extends Pp_Form_AdminAnnounceMessageDialog
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'dialog_id',
		'base_dialog_id',
		'dialog_type',
		'use_name',
		'message',
    );
}

/**
 *  admin_announce_message_dialog_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageDialogUpdateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_dialog_update_confirm Action.
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
     *  admin_announce_message_dialog_update_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $message_m = $this->backend->getManager('AdminMessage');

        $dialog_id = $this->af->get('dialog_id');
        $base_dialog_id = $this->af->get('base_dialog_id');
        $dialog_type = $this->af->get('dialog_type');
        $origin_message = $this->af->get('message');

        // 改行は削除しないこと
        $message = $message_m->convertNewlineCharacterToBr($origin_message);
        $this->af->set('message', $message);

        // type別でチェックが異なるのでここで振分をする
        // メッセージの内容から、タグ(改行タグ、カラーコードタグ)を削除した上で文字数チェックを行う
        switch($dialog_type){
            case 1:     // 通常
            case 5:     // EX
                // 改行は6行、文字数が全角15文字(半角だと30文字)
                $max_cnt = 6;
                if ($message_m->checkCountTagByBr($message, $max_cnt) === false){
                    $msg = "メッセージ：1行に入力できる改行は" . $max_cnt . "行までです";
                    $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
                    return 'admin_announce_message_dialog_update_input';
                }
                break;
            case 2:     // フレンドダイアログタイトル
            case 4:     // Webダイアログタイトル
                // 改行はなし、文字数が全角15文字(半角だと30文字)
                $max_cnt = 0;
                if ($message_m->checkCountTagByBr($message, $max_cnt) === false){
                    $msg = "メッセージ：改行は入力できません";
                    $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
                    return 'admin_announce_message_dialog_update_input';
                }
                break;
            case 3:     // フレンドダイアログメッセージ
                // 改行は2行、文字数が全角15文字(半角だと30文字)
                $max_cnt = 2;
                if ($message_m->checkCountTagByBr($message, $max_cnt) === false){
                //if (count($message_list) > 2){
                    $msg = "メッセージ：入力できる改行は" . $max_cnt . "行までです";
                    $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
                    return 'admin_announce_message_dialog_update_input';
                }
                break;
        }
        $max_cnt = 15;
        if ($message_m->checkLineLength($message, $max_cnt) === false){
            $msg = "メッセージ：1行に入力できる文字数は" . $max_cnt . "文字までです";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_dialog_update_input';
        }
        if($dialog_id !== $base_dialog_id){
            $res = $message_m->getMessageDialog($dialog_id);
            /*if (!$res) {
                return 'admin_error_500';
            }*/

            if($res['dialog_id']){
                $msg = "ID：入力されたIDは既に利用されています";
                $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
                return 'admin_announce_message_dialog_update_input';
            }
        }

        return 'admin_announce_message_dialog_update_confirm';
    }
}
