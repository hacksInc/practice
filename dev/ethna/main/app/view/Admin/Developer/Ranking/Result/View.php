<?php
/**
 *	Admin/Developer/Ranking/Result/View.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *	admin_developer_ranking_result_view view implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_View_AdminDeveloperRankingResultView extends Pp_AdminViewClass
{
	/**
	 *	preprocess before forwarding.
	 *
	 *	@access public
	 */
	function preforward()
	{
		$ranking_m = $this->backend->getManager( 'AdminRanking' );

		$ranking_id = $this->af->get( 'ranking_id' );
		$master = $ranking_m->getMasterRanking( $ranking_id );
		$info = $ranking_m->getRankingInfo( $ranking_id );
		$ranking_list = $ranking_m->getRankingListAll( $ranking_id );

		$this->af->setApp( 'title', $master['title'] );
		$this->af->setApp( 'subtitle', $master['subtitle'] );
		$this->af->setApp( 'last_update', $info['date_modified'] );
		$this->af->setApp( 'record_count', $info['record_count'] );
		$this->af->setApp( 'ranking_list', $ranking_list );
	}
}

?>