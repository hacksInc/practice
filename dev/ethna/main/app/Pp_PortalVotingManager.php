<?php
/**
 *  Pp_PortalVotingManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_PortalVotingManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PortalVotingManager extends Ethna_AppManager
{
	protected $db_cmn = null;
	protected $db_m_r = null;
	
	const VOTING_ID = 2;	// 投票ID
	const VOTING_END = "2015-04-10 16:00:00";	// 投票終了時刻
	const IS_CLOSE = false;	// 集計完了
	
	/**
	 * DB接続
	 */
	private function _setMDB ()
	{
		if ( is_null( $this->db_m_r ) ) $this->db_m_r = $this->backend->getDB( "m_r" );
	}
	
	/**
	 * DB接続
	 */
	private function _setCDB ()
	{
		if ( is_null( $this->db_cmn ) ) $this->db_cmn = $this->backend->getDB( "cmn" );
	}
	
	/*
	 * 投票集計結果取得
	 */
	function getVotingReportList ( $voting_id = self::VOTING_ID )
	{
		$this->_setCDB();
		
		$param = array( $voting_id );
		$sql = "SELECT * FROM ct_portal_voting_report WHERE voting_id = ? ORDER BY point DESC";
	    
		return $this->db_cmn->getAll( $sql, $param );
	}

	/**
	 * 投票実行
	 * 第二回は執行ポイント（票）形式に変更となった
	 */
	function execVoting ( $pp_id, $voting_id, $item_id_1, $item_id_2, $point )
	{
		$puser_m =& $this->backend->getManager( "PortalUser" );
		$user_m =& $this->backend->getManager( "User" );
		
		$this->_setCDB();
		
		$this->db->begin();
		$this->db_cmn->begin();
		
		$param = array( $voting_id, $pp_id, $item_id_1, $item_id_2, $point );
		$sql = "INSERT INTO ct_portal_voting( voting_id, pp_id, item_id_1, item_id_2, point, date_created ) VALUES( ?, ?, ?, ?, ?, NOW() )";
		if ( !$this->db_cmn->execute( $sql, $param ) ) {
			$this->db->rollback();
			$this->db_cmn->rollback();
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		if ( Ethna::isError( $user_m->addUserVotingPoint( $pp_id, $point * -1 ) ) ) {
			$this->db->rollback();
			$this->db_cmn->rollback();
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		$this->db_cmn->commit();
		$this->db->commit();
		
		return true;
	}

	/**
	 * 投票マスタ取得
	 */
	function getMasterVotingList ( $voting_id = self::VOTING_ID )
	{
		$this->_setMDB();
	    
		$param = array( $voting_id );
		$sql = "SELECT * FROM m_portal_voting WHERE voting_id = ?";
	    
		return $this->db_m_r->getAll( $sql, $param );
	}
	
	/**
	 * 投票マスタ取得
	 */
	function getMasterVotingListAssoc ( $voting_id = self::VOTING_ID )
	{
		$this->_setMDB();
	    
		$param = array( $voting_id );
		$sql = "SELECT item_id AS id, m.* FROM m_portal_voting m WHERE voting_id = ?";
	    
		return $this->db_m_r->db->getAssoc( $sql, $param );
	}
	
	/**
	 * 投票マスタ取得
	 */
	function getMasterVotingListRand ( $voting_id = self::VOTING_ID )
	{
		$this->_setMDB();
	    
		$param = array( $voting_id );
		$sql = "SELECT * FROM m_portal_voting WHERE voting_id = ? ORDER BY RAND()";
	    
		return $this->db_m_r->getAll( $sql, $param );
	}

	/**
	 * 投票マスタ取得
	 */
	function getMasterVoting ( $voting_id, $item_id )
	{
		$this->_setMDB();
		
		$param = array( $voting_id, $item_id );
		$sql = "SELECT * FROM m_portal_voting WHERE voting_id = ? AND item_id = ?";
	    
		return $this->db_m_r->getRow();
	}

	/**
	 * 投票残り時間
	 */
	function getTimeLimit ( $time_limit )
	{
		$left = $time_limit - time();
		
		if ( $left <= 0 ) return "投票受付終了しました。";
		
		$str = "";
		
		if ( $left >= 3600 * 24 )	$str .= floor( $left / ( 3600 * 24 ) ) . "日";
		elseif ( $left >= 3600 )	$str .= floor( ( $left % ( 3600 * 24 ) ) / 3600 ) . "時間";
		else						$str .= floor( ( $left % 3600 ) / 60 ) . "分";
		
		return $str;
	}
	
	/**
	 * 集計処理
	 */
	function sumVoting ( $voting_id = self::VOTING_ID )
	{
		$this->_setCDB();
		
		$m_voting = $this->getMasterVotingListAssoc( $voting_id );
		
		$param = array( $voting_id );
		$sql = "SELECT item_id_1, item_id_2, SUM(point) AS point FROM ct_portal_voting WHERE voting_id = ? GROUP BY item_id_1, item_id_2";
		$result = $this->db_cmn->GetAll( $sql, $param );
		
		$list = array();
		foreach ( $result as $row ) {
			if ( isset( $list[$row['item_id_2']][$row['item_id_1']] ) ) {
				if ( $list[$row['item_id_2']][$row['item_id_1']]['point'] > $row['point'] ) {
					// ポイントが低ければ、高いほうに合算
					$list[$row['item_id_2']][$row['item_id_1']]['point'] += $row['point'];
				} else {
					$list[$row['item_id_1']][$row['item_id_2']] = $row;
					$list[$row['item_id_1']][$row['item_id_2']]['point'] += $list[$row['item_id_2']][$row['item_id_1']]['point'];
					unset( $list[$row['item_id_2']][$row['item_id_1']] );
				}
			} else {
				// 逆順が存在しないなら比較もしないので、通常通り登録
				$list[$row['item_id_1']][$row['item_id_2']] = $row;
			}
		}
		
		// DBに登録
		$this->db_cmn->begin();
		
		$param = array( $voting_id );
		$sql = "DELETE FROM ct_portal_voting_report WHERE voting_id = ?";
		if ( !$this->db_cmn->execute( $sql, $param ) ) {
			$this->db_cmn->rollback();
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		echo( "----------------\n" );
		echo( date( "Y-m-d h:i:s" ) . "\n" );
		echo( "----------------\n" );
		
		foreach ( $list as $item_id_1 => $list2 ) {
			foreach ( $list2 as $item_id_2 => $row ) {
				$param = array( $voting_id, $item_id_1, $item_id_2, $row['point'] );
				$sql = "INSERT INTO ct_portal_voting_report( voting_id, item_id_1, item_id_2, point, date_created, date_modified ) VALUES( ?, ?, ?, ?, NOW(), NOW() )";
				if ( !$this->db_cmn->execute( $sql, $param ) ) {
					$this->db_cmn->rollback();
					return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
						$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__ );
				}
				
				// ログ用にとりあえず吐く
				echo( $m_voting[$item_id_1]['item_name'] . "×" . $m_voting[$item_id_2]['item_name'] . "\t" . $row['point'] . "\n" );
			}
		}
		
		echo( "----------------\n" );
		
		$this->db_cmn->commit();
		
		return true;
	}
}
?>
