<?php
/**
 *  Admin/Announce/Message/Tips/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_message_tips_update_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageTipsUpdateInput extends Pp_Form_AdminAnnounceMessageTips
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'tip_id',
        'base_tip_id',
        'message' => array('required' => false),
        'btn_action',
    );
}

/**
 *  admin_announce_message_tips_update_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageTipsUpdateInput extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_tips_update_input Action.
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
     *  admin_announce_message_tips_update_input action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $message_m = $this->backend->getManager('AdminMessage');
        $tip_id = $this->af->get('tip_id');
        $btn_action = $this->af->get('btn_action');

        if ($btn_action === "update") {
            $row = $message_m->getMessageTips($tip_id);
            if (!$row) {
                return 'admin_error_500';
            }
            $this->af->set('tip_id', $row['tip_id']);
            $this->af->set('base_tip_id', $row['tip_id']);
            $this->af->set('message', $row['message']);
        }

        $this->af->setApp('row', $row);
//        $this->af->setAppNe('message', $row['message']);

        return 'admin_announce_message_tips_update_input';
    }
}
