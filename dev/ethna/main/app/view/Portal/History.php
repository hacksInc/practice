<?php
/**
 *  Portal/History.php
 *	イベント参加履歴
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_history view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_PortalHistory extends Pp_ViewClass
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
		$m_theme = $ptheme_m->getMasterThemeList();
		
		$user['theme_name'] = $m_theme[$user['theme_id']]['theme_name'];

		$theme_list = $ptheme_m->getUserThemeList( $pp_id, "db" );
		
		// jsに渡すデータの作成
		$theme_info = array();
		foreach ( $m_theme as $theme_id => $row ) {
			$theme_info[] = array(
				"theme_id"		=> $row['theme_id'],
				"theme_name"	=> $row['theme_name'],
				"use_point"		=> intval( $row['use_point'] ),
				"lock_flg"		=> ( !isset( $theme_list[$theme_id] ) ) ? 1 : 0,
				"selected_flg"	=> ( $theme_id == $user['theme_id'] ) ? 1 : 0,
			);
		}
		
		$theme_info = htmlspecialchars( json_encode( $theme_info ) );
		
		// 捜査線特化
		$u_serial = $pevent_m->getUserSerialList( $pp_id, "db" );
		$m_event = $pevent_m->getMasterEvent( 1 );
		
		// 投票ポイント関係
		$user_m =& $this->backend->getManager( "User" );
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		$this->af->setApp( "voting", $voting );
		
		$this->af->setApp( "user", $user );
		$this->af->setApp( "sc_num", count( $u_serial ) );
		$this->af->setApp( "m_event", $m_event );
		$this->af->setAppNe( "theme_info", $theme_info );
    }
}
?>