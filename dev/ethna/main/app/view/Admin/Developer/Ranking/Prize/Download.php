<?php
/**
 *  Admin/Developer/Ranking/Prize/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_ranking_prize_download view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperRankingPrizeDownload extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
/*	
    function preforward()
    {
    }
*/	

	function forward ()
	{
		$ranking_id = $this->af->get( 'ranking_id' );
		$ranking_prize_m = $this->backend->getManager( 'AdminRankingPrize' );

		$db = $this->backend->getDB();

		// カラムの一覧を取得
		$sql = "show full columns from t_ranking_prize";
		$columns = $db->GetAll( $sql );

		// コメント（カラムの説明）
		$data = array();
		$temp = array();
		foreach( $columns as $column )
		{
			if( in_array( $column['Field'], $ranking_prize_m->LOAD_PRIZE_COLUMNS ) === false )
			{	// 出力対象のカラムでなければ飛ばす
				continue;
			}
			array_push( $temp, $column['Comment'] );
		}
		array_push( $data, $temp );		// ヘッダー行として追加

		// 指定のランキングIDの賞品一覧を取得
		$prizes = $ranking_prize_m->getRankingPrizeForRankingId( $ranking_id );
		foreach( $prizes as $prize )
		{
			$temp = array(
				$prize['distribute_start'],
				$prize['distribute_end'],
				$prize['prize_type'],
				$prize['prize_id'],
				$prize['lv'],
				$prize['number']
			);
			array_push( $data, $temp );
		}

		// CSVにして出力
		$filename = "jm_ranking_prize_".$ranking_id."_".date( "Ymd" ).".csv";
		$this->outputCsv($data, $filename);
	}
}

?>
