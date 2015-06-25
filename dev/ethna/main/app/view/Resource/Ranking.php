<?php
/**
 *  Resource/Ranking.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  resource_ranking view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceRanking extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
	function preforward()
	{
		$ranking_m =& $this->backend->getManager('Ranking');
		$ranking_id = $this->af->get('ranking_id');
		$user_id = $this->af->get('user_id');
		session_start();
		if( empty( $ranking_id ) === true )
		{
			$ranking_id = $_SESSION['view_ranking_id'];
		}
		else
		{
			$_SESSION['view_ranking_id'] = $ranking_id;
		}
		if( empty( $user_id ) === true )
		{
			$user_id = $_SESSION['view_user_id'];
		}
		else
		{
			$_SESSION['view_user_id'] = $user_id;
		}

		$master = $ranking_m->getMasterRanking( $ranking_id );
		$info = $ranking_m->getRankingInfo( $ranking_id );
		if(( empty( $info ) === true )||( $info['date_modified'] < $master['date_start'] ))
		{	// 未集計もしくは、まだランキングが始まっていない
			$ranking_data = array();
			$ranking_list = array();
		}
		else
		{	// 集計データあり
			$ranking_data = $ranking_m->getRankingData( $ranking_id, $user_id );
			if(( int )$info['status'] === 1 )
			{	// 最終結果集計済み
				$ranking_list = $ranking_m->getRankingListHigherRank( $ranking_id, 20 );	// ランク20位まで取得
			}
			else
			{	// まだ最終結果は集計されていない
				if( empty( $user_id ) === false )
				{	// ユーザーIDはある
					if( empty( $ranking_data ) === false )
					{	// ランキングデータがあるのでランク内
						$ranking_list = $ranking_m->getRankingListForUserId( $ranking_id, $user_id );	// 自分の周辺を取得
					}
					else
					{	// ランキングデータがない場合はランク外
						$ranking_list = $ranking_m->getRankingList( $ranking_id, true );	// ランク下位から取得
					}
				}
				else
				{	// ユーザーIDがない
					$ranking_list = $ranking_m->getRankingList( $ranking_id, false );	// ランク上位から取得
				}

				// 上位ランキングデータの取得
				if( empty( $master['view_ranking_top'] ) === true )
				{	// 上位ランキング表示はしない
					$ranking_top_list = null;
				}
				else
				{	// 上位ランキング表示する
					$ranking_top_list = $ranking_m->getRankingListHigherNum(
						$ranking_id, $master['view_ranking_top']
					);
					/*
					$ranking_top_list = $ranking_m->getRankingListHigherRank(
						$ranking_id, $master['view_ranking_top']
					);
					*/
				}

				/*
				// 自分の周辺のランキングデータが上位ランキングデータと被っている場合はまとめる
				if( empty( $ranking_top_list ) === false )
				{
					// 上位ランキングを表示数分に削る
					$temp = array_slice( $ranking_top_list, 0, $master['view_ranking_top'] );

					$top_end = end( $temp );	// 削った後の最後のレコードを取得
					if( $ranking_list[0]['rank'] <= $top_end['rank'] )
					{	// ２つのリストの表示する順位が被る場合は１つにまとめる
						$temp = array_marge( $ranking_top_list, $ranking_list );	// 削る前のデータでマージする
						$ranking_list = array_unique( $temp );		// 同一のレコードは削除する
						$ranking_top_list = null;	// まとめた場合は上位ランキングリストは表示しない
					}
					else
					{	// まとめない場合は、削った後のデータを上位ランキングデータとして表示
						$ranking_top_list = $temp;
					}
				}
				*/
			}
		}

		$this->af->setApp('user_id', $user_id);
		$this->af->setApp('master', $master);
		$this->af->setApp('info', $info);
		$this->af->setApp('ranking_data', $ranking_data);
		$this->af->setApp('ranking_list', $ranking_list);
		$this->af->setApp('ranking_top_list', $ranking_top_list);
		$this->af->setApp('date_start', strftime("%Y/%m/%d %H:%M", strtotime($master['date_start'])));
		$this->af->setApp('date_end', strftime("%Y/%m/%d %H:%M", strtotime($master['date_end'])));
		$this->af->setApp('last_update', strftime("%Y/%m/%d %H:%M", strtotime($info['date_modified'])));
	}
}

?>
