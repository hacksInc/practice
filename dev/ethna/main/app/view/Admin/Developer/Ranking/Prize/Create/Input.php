<?php
/**
 *  Admin/Developer/Ranking/Prize/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_ranking_prize_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperRankingPrizeCreateInput extends Pp_AdminViewClass
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
		$ranking_id = $this->af->get( 'ranking_id' );
		$master = $ranking_m->getMasterRanking( $ranking_id );

		$this->af->setApp( 'ranking_id', $ranking_id );
		$this->af->setApp( 'title', $master['title'] );
		$this->af->setApp( 'subtitle', $master['subtitle'] );
		$this->af->setApp( 'prize_type_options', $ranking_prize_m->PRIZE_TYPE_OPTIONS );
    }
}

?>
