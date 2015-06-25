<?php
/**
 *  Portal/Event.php
 *	イベントページ
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_event view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_PortalEvent extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$pp_id = $this->af->getRequestedBasicAuth( 'user' );
		
		$pevent_m =& $this->backend->getManager( "PortalEvent" );
		$ptheme_m =& $this->backend->getManager( "PortalTheme" );
		$puser_m =& $this->backend->getManager( "PortalUser" );
		
		$user = $puser_m->getUserBase( $pp_id );
		$m_theme = $ptheme_m->getMasterTheme( $user['theme_id'] );
		
		$user['theme_name'] = $m_theme['theme_name'];
		
		// イベント今後もやるかわからんので、とりあえずサイコパス捜査線のみ
		$m_serial = $pevent_m->getMasterSerialList();
		$u_serial = $pevent_m->getUserSerialList( $pp_id, "db" );
		
		// 投票ポイント関係
		$user_m =& $this->backend->getManager( "User" );
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		$this->af->setApp( "voting", $voting );
		
		$this->af->setApp( "user", $user );
		$this->af->setApp( "m_serial", $m_serial );
		$this->af->setApp( "u_serial", $u_serial );
    }
}
?>