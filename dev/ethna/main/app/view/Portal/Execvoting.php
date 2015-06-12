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
class Pp_View_PortalExecvoting extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $pp_id = $this->af->getRequestedBasicAuth( 'user' );
        
		$item_id_1 = $this->af->get( "item_id" );
		$item_id_2 = $this->af->get( "item_id2" );
		
        $ptheme_m =& $this->backend->getManager( "PortalTheme" );
        $puser_m =& $this->backend->getManager( "PortalUser" );
		$pvoting_m =& $this->backend->getManager( "PortalVoting" );
        $user_m =& $this->backend->getManager( "User" );
		
        $user = $puser_m->getUserBase( $pp_id );
        $m_theme = $ptheme_m->getMasterTheme( $user['theme_id'] );
        
        $user['theme_name'] = $m_theme['theme_name'];
        
		$m_voting = $pvoting_m->getMasterVotingListAssoc();
		
		// 投票ポイント関係
		$user_m =& $this->backend->getManager( "User" );
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		$this->af->setApp( "voting", $voting );
		
        $this->af->setApp( "user", $user );
		$this->af->setApp( "m_voting", $m_voting );
    }
}
?>