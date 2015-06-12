<?php
/**
 *  Admin/Developer/Ranking/Prize/Upload/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';
require_once 'Pp_AdminRankingPrizeManager.php';

/**
 *  admin_developer_ranking_prize_upload_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperRankingPrizeUploadConfirm extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
	function preforward()
	{
		$ranking_prize_m =& $this->backend->getManager( 'AdminRankingPrize' );
		$ranking_m =& $this->backend->getManager( 'Ranking' );
		$item_m =& $this->backend->getManager( 'Item' );
		$monster_m =& $this->backend->getManager( 'Monster' );

		// ランキングマスタ取得
		$ranking_id = $this->af->get( 'ranking_id' );
		$master = $ranking_m->getMasterRanking( $ranking_id );

		// CSVファイル読み込み
		$csv = $this->af->get( 'csv' );
		$contents = file_get_contents( $csv['tmp_name'] );
		$rows = explode( "\n", $contents );

		$prizes = array();
		foreach( $rows as $i => $row )
		{
			if(( $i === 0 )||( empty( $row ) === true ))
			{	// 不要な行は読み飛ばす
				continue;
			}

			$temp = explode( ',', $row );	// 区切りごとに分ける

			// 賞品名の取得
			$prize_type_name = $ranking_prize_m->PRIZE_TYPE_OPTIONS[$temp[2]];
			if( $temp[2] == Pp_AdminRankingPrizeManager::PRIZE_TYPE_ITEM )
			{	// 通常アイテム
				$item = $item_m->getMasterItem( $temp[3] );
				$prize_name = ( empty( $item['name_ja'] ) === true ) ? '？' : $item['name_ja'];
			}
			else if( $temp[2] == Pp_AdminRankingPrizeManager::PRIZE_TYPE_MONSTER )
			{	// モンスター
				$monster = $monster_m->getMasterMonster( $temp[3] );
				$prize_name = ( empty( $monster['name_ja'] ) === true ) ? '？' : $monster['name_ja'];
			}
			else
			{	// その他
				$prize_name = '';
			}

			// 配列の最後尾にデータを追加していく
			array_push(
				$prizes,
				array(
					'distribute_start' => $temp[0],
					'distribute_end'   => $temp[1],
					'prize_type'       => $temp[2],
					'prize_type_name'  => $prize_type_name,
					'prize_id'         => $temp[3],
					'prize_name'       => $prize_name,
					'lv'               => $temp[4],
					'number'           => $temp[5]
				)
			);
		}

		$this->af->setApp( 'title', $master['title'] );
		$this->af->setApp( 'subtitle', $master['subtitle'] );
		$this->af->setApp( 'ranking_id', $ranking_id );
		$this->af->setApp( 'prizes', $prizes );
		$this->af->setApp( 'csv', $contents );
	}
}

?>
