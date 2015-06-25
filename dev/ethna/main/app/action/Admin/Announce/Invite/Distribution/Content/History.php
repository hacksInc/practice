<?php
/**
 *  Admin/Announce/Invite/Distribution/Content/History.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_announce_invite_distribution_content_history Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceInviteDistributionContentHistory extends Pp_Form_AdminAnnounceInviteDistributionContent
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'lu' => array('filter' => 'sync_lu'),
		'pageID',
    );
}

/**
 *  admin_announce_invite_distribution_content_history action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceInviteDistributionContentHistory extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_invite_distribution_content_history Action.
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
     *  admin_announce_invite_distribution_content_history action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_announce_invite_distribution_content_history';
    }
}

?>