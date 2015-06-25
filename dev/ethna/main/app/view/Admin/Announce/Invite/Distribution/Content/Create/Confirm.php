<?php
/**
 *  Admin/Announce/Invite/Distribution/Content/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_invite_distribution_content_create_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceInviteDistributionContentCreateConfirm extends Pp_AdminViewClass
{
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
		
		$g_dist_type = $this->af->get('g_dist_type');
		if ($g_dist_type == Pp_PresentManager::DIST_TYPE_MONSTER) {
			$monster = $monster_m->getMasterMonster($this->af->get('g_item_id'));
			$this->af->setApp('g_monster_name', $monster['name_ja']);
		}
		$i_dist_type = $this->af->get('i_dist_type');
		if ($i_dist_type == Pp_PresentManager::DIST_TYPE_MONSTER) {
			$monster = $monster_m->getMasterMonster($this->af->get('i_item_id'));
			$this->af->setApp('i_monster_name', $monster['name_ja']);
		}
		$this->af->setApp('g_dist_type', $present_m->DIST_TYPE_OPTIONS[$g_dist_type]);
		$this->af->setApp('i_dist_type', $present_m->DIST_TYPE_OPTIONS[$i_dist_type]);
		$this->af->setApp('form_template', $this->af->form_template);
    }
}

?>