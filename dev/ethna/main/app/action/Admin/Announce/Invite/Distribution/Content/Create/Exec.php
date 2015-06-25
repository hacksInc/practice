<?php
/**
 *  Admin/Announce/Invite/Distribution/Content/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_invite_distribution_content_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceInviteDistributionContentCreateExec extends Pp_Form_AdminAnnounceInviteDistributionContent
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'invite_mng_id',
		'date_start',
		'date_end',
		'invite_max',
		'g_dist_type',
		'g_item_id',
		'g_lv',
		'g_number',
		'i_dist_type',
		'i_item_id',
		'i_lv',
		'i_number',
    );
}

/**
 *  admin_announce_invite_distribution_content_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceInviteDistributionContentCreateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_invite_distribution_content_create_exec Action.
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
     *  admin_announce_invite_distribution_content_create_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$invite_m =& $this->backend->getManager('Invite');
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');
		
		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}
		$columns['status'] = Pp_InviteManager::DIST_STATUS_START;
		$columns['g_dist_user_cnt'] = $columns['i_dist_user_cnt'] = $columns['i_dist_user_total'] = 0;
		$columns['account_upd'] = '';
		
		$invite_mng_id = $columns['invite_mng_id'];
		unset($columns['invite_mng_id']);
		if ($invite_mng_id >= 0) {
			$columns['account_upd'] = $this->session->get('lid');
			$ret = $invite_m->updateInviteMng($invite_mng_id, $columns);
		}
		else {
			$columns['account_reg'] = $this->session->get('lid');
			$ret = $invite_m->insertInviteMng($invite_mng_id, $columns);
		}
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}
		
        return 'admin_announce_invite_distribution_content_create_exec';
    }
}

?>