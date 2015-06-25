<?php
/**
 *  Portal/SerialRegist.php
 *	シリアル受付
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_serialRegist view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_PortalSerialRegist extends Pp_ViewClass
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
		
		$user = $puser_m->getUserBase( $pp_id );
		$m_theme = $ptheme_m->getMasterTheme( $user['theme_id'] );
		
		$user['theme_name'] = $m_theme['theme_name'];
		
		// 投票ポイント関係
		$user_m =& $this->backend->getManager( "User" );
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		$this->af->setApp( "voting", $voting );
		
		$this->af->setApp( "user", $user );
    }
}
?>