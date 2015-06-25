<?php
/**
 *  Admin/Announce/Loginbonus/Content/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_loginbonus_content_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceLoginbonusContentCreateInput extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'max_id',
    );
}

/**
 *  admin_announce_loginbonus_content_create_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceLoginbonusContentCreateInput extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_loginbonus_content_create_input Action.
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
     *  admin_announce_loginbonus_content_create_input action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_announce_loginbonus_content_create_input';
    }
}

?>