<?php
/**
 *  Pp_AdminRankingManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_RankingManager.php';

/**
 *  Pp_AdminRankingManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminRankingManager extends Pp_RankingManager
{
	/**
	 *	全ランキングマスターを取得する
	 *
	 *	@return array ランキングマスター情報
	 */
	function getMasterRankingAll()
	{
		$sql = "SELECT * FROM m_ranking ORDER BY ranking_id DESC";
		return $this->db_r->GetAll( $sql );
	}

	/**
	 *	ランキングマスターの新規追加
	 */
	function insertMasterRanking( $columns )
	{
		return $this->db->db->AutoExecute( 'm_ranking', $columns, 'INSERT' );
	}

	/**
	 *	ランキングマスターの更新
	 */
	function updateMasterRanking( $columns )
	{
		if( !is_numeric( $columns['ranking_id'] ))
		{
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
		$where = "ranking_id = " . $columns['ranking_id'];
		unset( $columns['ranking_id'] );

		return $this->db->db->AutoExecute( 'm_ranking', $columns, 'UPDATE', $where );
	}

	/**
	 *	ランキングマスターの削除
	 */
	function deleteMasterRanking( $ranking_id )
	{
		$unit_m = $this->backend->getManager( 'Unit' );

		$param = array( $ranking_id );
		$sql = "DELETE FROM m_ranking WHERE ranking_id = ?";

		$unit_info = $unit_m->getUnitInfo();
		$unit_list = array_keys( $unit_info );
		foreach( $unit_list as $v )
		{
			$ret = $unit_m->executeForUnit( $v, $sql, $param, false );
			if( $ret->ErrorNo )
			{
				$this->backend->logger->log( LOG_WARNING,
					'deleteRankingData: delele m_ranking is failed. '.
					'ranking_id['.$ranking_id.'], unit['.$v.']'
				);
				return null;
			}
		}
		return true;
	}
}
?>
