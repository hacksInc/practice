<?php
/**
 *  Admin/Announce/Invite/Distribution/Content/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_invite_distribution_content_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceInviteDistributionContentCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_invite_distribution_content_create_exec' => null,
	);

	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$present_m =& $this->backend->getManager('Present');
		$invite_m =& $this->backend->getManager('Invite');
		$invite_mng_id = $this->af->get('invite_mng_id');
		if ($invite_mng_id >= 0) {
			$row = $invite_m->getInviteMng($invite_mng_id);
		}
	//error_log(print_r($row,true));

		// 表示終了日時デフォルト値の設定
		if ($invite_mng_id) {
			$date_start = $row['date_start'];
			$date_end = $row['date_end'];
			$invite_max = $row['invite_max'];
			$g_dist_type = $row['g_dist_type'];
			$g_number = $row['g_number'];
			$g_item_id = $row['g_item_id'];
			$g_lv = $row['g_lv'];
			$i_dist_type = $row['i_dist_type'];
			$i_number = $row['i_number'];
			$i_item_id = $row['i_item_id'];
			$i_lv = $row['i_lv'];
		} else {
			$invite_mng_id = -1;
			$date_start = date('Y-m-01', $_SERVER['REQUEST_TIME']) . ' 00:00:00';
			$date_end = date('Y-m-t', $_SERVER['REQUEST_TIME']) . ' 23:59:59';
			$invite_max = 10;
			$g_dist_type = 0;
			$g_number = 1;
			$g_item_id = 0;
			$g_lv = 1;
			$i_dist_type = 0;
			$i_number = 1;
			$i_item_id = 0;
			$i_lv = 1;
		}
		
		$this->af->setApp('invite_mng_id', $invite_mng_id);
		$this->af->setApp('date_start', $date_start);
		$this->af->setApp('date_end', $date_end);
		$this->af->setApp('invite_max', $invite_max);
		$this->af->setApp('dist_type_options', $present_m->DIST_TYPE_OPTIONS);
		$this->af->setApp('g_dist_type', $g_dist_type);
		$this->af->setApp('g_number', $g_number);
		$this->af->setApp('g_item_id', $g_item_id);
		$this->af->setApp('g_lv', $g_lv);
		$this->af->setApp('i_dist_type', $i_dist_type);
		$this->af->setApp('i_number', $i_number);
		$this->af->setApp('i_item_id', $i_item_id);
		$this->af->setApp('i_lv', $i_lv);
    }
}

?>