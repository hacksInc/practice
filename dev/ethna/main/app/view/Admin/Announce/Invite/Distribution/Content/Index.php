<?php
/**
 *  Admin/Announce/Invite/Distribution/Content/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_invite_distribution_content_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceInviteDistributionContentIndex extends Pp_AdminViewClass
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
		$monster_m =& $this->backend->getManager('Monster');
		$user_m =& $this->backend->getManager('User');
		
		$list = $invite_m->getInviteMngList(0,100);
		if ($list) foreach ($list as $i => $row) {
			if ($row['g_dist_type'] == Pp_PresentManager::DIST_TYPE_MONSTER) {
				$monster = $monster_m->getMasterMonster($row['g_item_id']);
				$monster_name = $monster['name_ja'];
			} else $monster_name = '';
			$list[$i]['g_monster_name'] = $monster_name;
			$list[$i]['g_dist_types'] = $present_m->DIST_TYPE_OPTIONS[($list[$i]['g_dist_type'])];
			if ($row['i_dist_type'] == Pp_PresentManager::DIST_TYPE_MONSTER) {
				$monster = $monster_m->getMasterMonster($row['i_item_id']);
				$monster_name = $monster['name_ja'];
			} else $monster_name = '';
			$list[$i]['i_monster_name'] = $monster_name;
			$list[$i]['i_dist_types'] = $present_m->DIST_TYPE_OPTIONS[($list[$i]['i_dist_type'])];
			if ($list[$i]['date_start'] <= date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) && $list[$i]['date_end'] > date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])) $list[$i]['dist_term'] = 1;
		}

		$this->af->setApp('list', $list);
		
		$this->af->setApp('form_template', $this->af->form_template);
    }
}

?>