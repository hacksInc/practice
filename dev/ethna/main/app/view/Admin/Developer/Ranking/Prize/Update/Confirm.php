<?php
/**
 *  Admin/Developer/Ranking/Prize/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_ranking_prize_update_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperRankingPrizeUpdateConfirm extends Pp_AdminViewClass
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
		$prize_type = intval( $this->af->get( 'prize_type' ));
		$prize_id = intval( $this->af->get( 'prize_id' ));
		$status = $this->af->get( 'status' );

		$master = $ranking_m->getMasterRanking( $ranking_id );

		$this->af->setApp( 'prize_type_str', $ranking_prize_m->PRIZE_TYPE_OPTIONS[$prize_type] );
		$this->af->setApp( 'prize_status_str', $ranking_prize_m->PRIZE_STATUS_OPTIONS[$status] );

		// 賞品名の取得
		if( $prize_type === Pp_AdminRankingPrizeManager::PRIZE_TYPE_ITEM )
		{	// 通常アイテム
			$item_m =& $this->backend->getManager( 'Item' );
			$item = $item_m->getMasterItem( $prize_id );
			if( empty( $item['name_ja'] ) === true )
			{
				$prize_name = '※賞品のIDはマスタに登録されていません';
			}
			else
			{
				$prize_name = $item['name_ja'];
			}
		}
		else if( $prize_type === Pp_AdminRankingPrizeManager::PRIZE_TYPE_MONSTER )
		{	// モンスター
			$monster_m =& $this->backend->getManager( 'Monster' );
			$monster = $monster_m->getMasterMonster( $prize_id );
			if( empty( $monster['name_ja'] ) === true )
			{
				$prize_name = '※賞品のIDはマスタに登録されていません';
			}
			else
			{
				$prize_name = $monster['name_ja'];
			}
		}
		else
		{	// その他
			$prize_name = '';
		}

		$this->af->setApp( 'id', $this->af->get( 'id' ));
		$this->af->setApp( 'title', $master['title'] );
		$this->af->setApp( 'subtitle', $master['subtitle'] );
		$this->af->setApp( 'ranking_id', $ranking_id );
		$this->af->setApp( 'distribute_start', $this->af->get( 'distribute_start' ));
		$this->af->setApp( 'distribute_end', $this->af->get( 'distribute_end' ));
		$this->af->setApp( 'prize_type', $prize_type );
		$this->af->setApp( 'prize_id', $prize_id );
		$this->af->setApp( 'prize_name', $prize_name );
		$this->af->setApp( 'lv', $this->af->get( 'lv' ));
		$this->af->setApp( 'number', $this->af->get( 'number' ));
		$this->af->setApp( 'status', $status );
    }
}

?>