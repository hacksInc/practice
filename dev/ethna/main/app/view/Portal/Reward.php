<?php
/**
 *  Portal/Votingresult.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  portal_votingresult view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_PortalReward extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $pp_id = $this->af->getRequestedBasicAuth( 'user' );
        
        $ptheme_m =& $this->backend->getManager( "PortalTheme" );
        $puser_m =& $this->backend->getManager( "PortalUser" );
		$pvoting_m =& $this->backend->getManager( "PortalVoting" );
		$raid_m =& $this->backend->getManager( "Raid" );
        
        $user = $puser_m->getUserBase( $pp_id );
        $m_theme = $ptheme_m->getMasterTheme( $user['theme_id'] );
        
        $user['theme_name'] = $m_theme['theme_name'];
        
		$total = $raid_m->getRaidTotal( 1 );
		
		$time_limit = $pvoting_m->getTimeLimit( strtotime( "2015-04-07 15:00:00" ) );
		
		// 投票ポイント関係
		$user_m =& $this->backend->getManager( "User" );
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		$this->af->setApp( "voting", $voting );
		
        $this->af->setApp( "user", $user );
		$this->af->setApp( "total", $total );
		$this->af->setApp( "time_limit", $time_limit );
    }
}
?>