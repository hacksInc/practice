<?php
/**
 *  Admin/Announce/Loginbonus/Content/Create/Input2.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_loginbonus_content_input2 Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceLoginbonusContentCreateInput2 extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'login_bonus_id',
        'name',
        'date_start',
        'date_end',
        'dist_type0',
        'dist_type1',
        'dist_type2',
        'dist_type3',
        'dist_type4',
        'dist_type5',
        'dist_type6',
        'dist_type7',
        'dist_type8',
        'dist_type9',
        'number0',
        'number1',
        'number2',
        'number3',
        'number4',
        'number5',
        'number6',
        'number7',
        'number8',
        'number9',
        'item_id0',
        'item_id1',
        'item_id2',
        'item_id3',
        'item_id4',
        'item_id5',
        'item_id6',
        'item_id7',
        'item_id8',
        'item_id9',
        'lv0',
        'lv1',
        'lv2',
        'lv3',
        'lv4',
        'lv5',
        'lv6',
        'lv7',
        'lv8',
        'lv9',
    );
}

/**
 *  admin_announce_loginbonus_content_create_input2 action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceLoginbonusContentCreateInput2 extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_loginbonus_content_create_input2 Action.
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
     *  admin_announce_loginbonus_content_create_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_announce_loginbonus_content_create_input2';
    }
}

?>