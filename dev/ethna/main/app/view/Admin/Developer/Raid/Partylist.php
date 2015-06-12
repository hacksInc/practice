<?php
/**
 *	Admin/Developer/Raid/Partylist.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *	admin_developer_raid_partylist view implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_View_AdminDeveloperRaidPartylist extends Pp_AdminViewClass
{
//	var $helper_action_form = array(
//		'admin_developer_raid_create_exec' => null,
//	);

	/**
	 *	preprocess before forwarding.
	 *
	 *	@access public
	 */
	function preforward()
	{
		$raidparty_m = $this->backend->getManager( 'AdminRaidParty' );

		$cond = $this->af->get('cond');
		if ($cond == null) $cond = Pp_RaidPartyManager::PARTY_STATUS_NONE;
		
		$list = $raidparty_m->getPartyList($cond);
		error_log(print_r($list,true));

		$this->af->setApp( 'list', $list );
		$this->af->setApp( 'list_cnt', count($list) );
	}
}

?>