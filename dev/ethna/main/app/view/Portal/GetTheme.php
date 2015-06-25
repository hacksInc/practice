<?php
/**
 *  Portal/GetTheme.php
 *	テーマ獲得
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_getTheme view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_PortalGetTheme extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$pp_id = $this->af->getRequestedBasicAuth( 'user' );
		
		$theme_id = $this->af->get( "theme_id" );
		
		$ptheme_m =& $this->backend->getManager( "PortalTheme" );
		$puser_m =& $this->backend->getManager( "PortalUser" );
		
		$user = $puser_m->getUserBase( $pp_id, "db" );
		$m_theme = $ptheme_m->getMasterTheme( $theme_id );
		
		$user['theme_name'] = $m_theme['theme_name'];

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
		
		// 投票ポイント関係
		$user_m =& $this->backend->getManager( "User" );
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		$this->af->setApp( "voting", $voting );
		
		$this->af->setApp( "user", $user );
		$this->af->setAppNe( "theme_info", $theme_info );
		
    }
}
?>