<?php
/**
 *  Admin/Announce/Message/Tips/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_tips_create_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageTipsCreateConfirm extends Pp_Form_AdminAnnounceMessageTips
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'tip_id',
		'message',
    );
}

/**
 *  admin_announce_message_tips_create_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageTipsCreateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_tips_create_confirm Action.
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
     *  admin_announce_message_tips_create_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $message_m = $this->backend->getManager('AdminMessage');

        $tip_id = $this->af->get('tip_id');
        $origin_message = $this->af->get('message');

        // 改行は削除しないこと
        $message = $message_m->convertNewlineCharacterToBr($origin_message);
        $this->af->set('message', $message);

        // 改行は7行、文字数が全角17文字(半角だと34文字)
        $max_cnt = 7;
        if ($message_m->checkCountTagByBr($message, $max_cnt) === false){
            $msg = "メッセージ：1行に入力できる改行は" . $max_cnt . "行までです";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_tips_create_input';
        }

        $max_cnt = 17;
        if ($message_m->checkLineLength($message, $max_cnt) === false){
            $msg = "メッセージ：1行に入力できる文字数は" . $max_cnt . "文字までです";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_tips_create_input';
        }

        $res = $message_m->getMessageTips($tip_id);
        if($res['tip_id']){
            $msg = "ID：入力されたIDは既に利用されています";
            $this->ae->addObject('message', Ethna::raiseNotice($msg, E_FORM_INVALIDVALUE));
            return 'admin_announce_message_tips_create_input';
        }

        return 'admin_announce_message_tips_create_confirm';
    }
}
