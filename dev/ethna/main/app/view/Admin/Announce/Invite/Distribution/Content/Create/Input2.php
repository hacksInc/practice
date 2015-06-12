<?php
/**
 *  Admin/Announce/Invite/Distribution/Content/Create/Input2.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_invite_distribution_content_create_input2 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceInviteDistributionContentCreateInput2 extends Pp_AdminViewClass
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
		if (!$invite_mng_id) {
			$invite_mng_id = -1;
		}
		
		$this->af->setApp('invite_mng_id', $invite_mng_id);
		$this->af->setApp('dist_type_options', $present_m->DIST_TYPE_OPTIONS);
    }
}

?>