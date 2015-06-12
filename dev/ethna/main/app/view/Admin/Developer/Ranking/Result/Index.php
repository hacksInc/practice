<?php
/**
 *	Admin/Developer/Ranking/Result/Index.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *	admin_developer_ranking_result_index view implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_View_AdminDeveloperRankingResultIndex extends Pp_AdminViewClass
{
	/**
	 *	preprocess before forwarding.
	 *
	 *	@access public
	 */
	function preforward()
	{
		$ranking_m = $this->backend->getManager( 'AdminRanking' );
		$masters = $ranking_m->getMasterRankingAll();

		$now = date('Y-m-d H:i:s');
		foreach( $masters as $k => $v )
		{
			// 開催状態
			if( $now < $v['date_start'] )
			{
				$status = '開催待ち';
			}
			else if( $v['date_end'] < $now )
			{
				$status = '開催終了';
			}
			else
			{
				$status = '開催中';
			}
			$masters[$k]['status'] = $status;

			// 最終集計日時
			$info = $ranking_m->getRankingInfo( $v['ranking_id'] );
			if(( empty( $info ) === true )||( $info['date_modified'] < $v['date_start'] ))
			{	// 集計データなし
				$masters[$k]['last_update'] = '―';
				$masters[$k]['result_status'] = '未集計';
			}
			else
			{	// 集計データあり
				$masters[$k]['last_update'] = $info['date_modified'];
				$masters[$k]['result_status'] = ( $info['status'] == 0 ) ? '中間集計' : '最終集計';
			}
		}

		$this->af->setApp( 'masters', $masters );
	}
}

?>