<?php
/**
 *  Admin/Developer/Ranking/Prize/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_ranking_prize_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperRankingPrizeUpdateInput extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$ranking_m =& $this->backend->getManager( 'AdminRanking' );
		$ranking_prize_m =& $this->backend->getManager( 'AdminRankingPrize' );

		$id = $this->af->get( 'id' );
		$ranking_id = $this->af->get( 'ranking_id' );
		$prize = $ranking_prize_m->getRankingPrizeForId( $id );
		$master = $ranking_m->getMasterRanking( $ranking_id );

		if( $prize['prize_type'] != Pp_AdminRankingPrizeManager::PRIZE_TYPE_MONSTER )
		{	// モンスター以外はレベルを空にする
			$prize['lv'] = '';
		}
		if(( $prize['prize_type'] == Pp_AdminRankingPrizeManager::PRIZE_TYPE_COIN )||
		   ( $prize['prize_type'] == Pp_AdminRankingPrizeManager::PRIZE_TYPE_MEDAL ))
		{	// 賞品IDが必要ないものは空にしておく
			$prize['prize_id'] = '';
		}

		if( $prize['distribute_end'] == 0 )
		{	// 配布末尾順位に０が設定されている場合は空にする
			$prize['distribute_end'] = '';
		}

		$this->af->setApp( 'title', $master['title'] );
		$this->af->setApp( 'subtitle', $master['subtitle'] );
		$this->af->setApp( 'prize', $prize );
		$this->af->setApp( 'prize_type_options', $ranking_prize_m->PRIZE_TYPE_OPTIONS );
    }
}

?>
