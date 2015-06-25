<?php
/**
 *	Admin/Developer/Ranking/Prize/Index.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *	admin_developer_ranking_prize_index view implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_View_AdminDeveloperRankingPrizeIndex extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_ranking_prize_create_exec' => null,
	);

	/**
	 *	preprocess before forwarding.
	 *
	 *	@access public
	 */
	function preforward()
	{
		$ranking_m =& $this->backend->getManager( 'AdminRanking' );
		$ranking_prize_m =& $this->backend->getManager( 'AdminRankingPrize' );
		$item_m =& $this->backend->getManager( 'Item' );
		$monster_m =& $this->backend->getManager( 'Monster' );

		$ranking_id = $this->af->get( 'ranking_id' );
		$master = $ranking_m->getMasterRanking( $ranking_id );

		$this->af->setApp( 'ranking_id', $ranking_id );
		$this->af->setApp( 'title', $master['title'] );
		$this->af->setApp( 'subtitle', $master['subtitle'] );

		// ランキングIDから配布賞品一覧を取得
		$prize = $ranking_prize_m->getRankingPrizeForRankingId( $ranking_id );
		foreach( $prize as $k => $v )
		{
			$prize[$k]['prize_type_name'] = $ranking_prize_m->PRIZE_TYPE_OPTIONS[$v['prize_type']];
			$prize[$k]['prize_status_name'] = $ranking_prize_m->PRIZE_STATUS_OPTIONS[$v['status']];

			// 賞品名の取得
			if( $v['prize_type'] == Pp_AdminRankingPrizeManager::PRIZE_TYPE_ITEM )
			{
				$item = $item_m->getMasterItem( $v['prize_id'] );
				$prize[$k]['prize_name'] = ( empty( $item['name_ja'] ) === true ) ? '？' : $item['name_ja'];
			}
			else if( $v['prize_type'] == Pp_AdminRankingPrizeManager::PRIZE_TYPE_MONSTER )
			{
				$monster = $monster_m->getMasterMonster( $v['prize_id'] );
				$prize[$k]['prize_name'] = ( empty( $monster['name_ja'] ) === true ) ? '？' : $monster['name_ja'];
			}
			else
			{
				$prize[$k]['prize_name'] = '';
			}
		}
		$this->af->setApp( 'prize', $prize );
	}
}

?>