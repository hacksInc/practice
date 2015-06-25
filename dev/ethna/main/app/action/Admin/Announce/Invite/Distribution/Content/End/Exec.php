<?php
/**
 *  Admin/Announce/Invite/Distribution/Content/End/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_invite_distribution_content_end_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceInviteDistributionContentEndExec extends Pp_Form_AdminAnnounceInviteDistributionContent
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'invite_mng_id',
    );
}

/**
 *  admin_announce_invite_distribution_content_end_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceInviteDistributionContentEndExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_invite_distribution_content_end_exec Action.
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
     *  admin_announce_invite_distribution_content_end_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$invite_m =& $this->backend->getManager('Invite');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array(
			'status' => Pp_InviteManager::DIST_STATUS_STOP,
			'account_upd' => $this->session->get('lid'),
		//	'date_end' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
		);
		$invite_mng_id = $this->af->get('invite_mng_id');
		$ret = $invite_m->updateInviteMng($invite_mng_id, $columns);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}
		
        return 'admin_announce_invite_distribution_content_end_exec';
    }
}

?>