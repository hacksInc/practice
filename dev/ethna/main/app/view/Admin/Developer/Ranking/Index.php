<?php
/**
 *	Admin/Developer/Ranking/Index.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *	admin_developer_ranking_index view implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_View_AdminDeveloperRankingIndex extends Pp_AdminViewClass
{
//	var $helper_action_form = array(
//		'admin_developer_ranking_create_exec' => null,
//	);

	/**
	 *	preprocess before forwarding.
	 *
	 *	@access public
	 */
	function preforward()
	{
		$ranking_m = $this->backend->getManager( 'AdminRanking' );
		$masters = $ranking_m->getMasterRankingAll();

		// 開催状態
		$now = date('Y-m-d H:i:s');
		foreach( $masters as $k => $v )
		{
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
		}

		$this->af->setApp( 'masters', $masters );
	}
}

?>