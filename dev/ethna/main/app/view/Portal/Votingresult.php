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
class Pp_View_PortalVotingresult extends Pp_ViewClass
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
		
		$user = $puser_m->getUserBase( $pp_id );
		$m_theme = $ptheme_m->getMasterTheme( $user['theme_id'] );
		
		$user['theme_name'] = $m_theme['theme_name'];
		
		$m_voting = $pvoting_m->getMasterVotingList( 1 );
		$r_voting = $pvoting_m->getVotingReportList( 1 );
		
		// 項目はアトランダムに
		$list = array();
		$assoc = array();
		$cnt = count( $m_voting );
		for ( $i = 0; $i < $cnt; $i++ ) {
			$key = array_rand( $m_voting );
			
			$list[] = $m_voting[$key];
			$assoc[$m_voting[$key]['item_id']] = $m_voting[$key];
			
			unset( $m_voting[$key] );
		}

		// ランキング用の文字列生成
		$rank_str = array(
			"1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th", "11th", "12th", "13th", "14th", "15th"
		);

		// 結果に対して順位文字列を入れておく（同率対応）
		$rank = 0;
		$rank_skip = 0;
		foreach ( $r_voting as $key => $row ) {
			$r_voting[$key]['rank_str'] = $rank_str[$rank];
			
			if ( isset( $r_voting[$key + 1] ) && $row['point'] > $r_voting[$key + 1]['point'] ) {
				$rank += ( $rank_skip + 1 );
				$rank_skip = 0;
			} else {
				$rank_skip++;
			}
		}
		
		// 投票ポイント関係
		$user_m =& $this->backend->getManager( "User" );
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		$this->af->setApp( "voting", $voting );
		
		$this->af->setApp( "user", $user );
		$this->af->setApp( "m_voting", $m_voting );
		$this->af->setApp( "r_voting", $r_voting );
		$this->af->setApp( "list", $list );
		$this->af->setApp( "assoc", $assoc );
    }
}
?>