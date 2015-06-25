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
class Pp_View_PortalVoting extends Pp_ViewClass
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
		$user_m =& $this->backend->getManager( "User" );
		
		$user = $puser_m->getUserBase( $pp_id );
		$m_theme = $ptheme_m->getMasterTheme( $user['theme_id'] );
		
		$user['theme_name'] = $m_theme['theme_name'];
		
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		
		$m_voting_1 = $pvoting_m->getMasterVotingListRand();
		$r_voting = $pvoting_m->getVotingReportList();
		
		$time_limit = $pvoting_m->getTimeLimit( strtotime( "2015-04-07 15:00:00" ) );
		
		$rand_keys = array_rand( $m_voting_1, count( $m_voting_1 ) );
		shuffle( $rand_keys );
		$m_voting_2 = array();
		foreach ( $rand_keys as $key ) {
			$m_voting_2[] = $m_voting_1[$key];
		}
		
		$m_voting = $pvoting_m->getMasterVotingListAssoc();
		
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
		
		$this->af->setApp( "user", $user );
		$this->af->setApp( "voting", $voting );
		$this->af->setApp( "m_voting", $m_voting );
		$this->af->setApp( "m_voting_1", $m_voting_1 );
		$this->af->setApp( "m_voting_2", $m_voting_2 );
		$this->af->setApp( "r_voting", $r_voting );
		$this->af->setApp( "time_limit", $time_limit );
    }
}
?>