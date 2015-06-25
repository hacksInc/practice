<?php
/**
 *  Pp_PortalNewsManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_PortalNewsManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PortalNewsManager extends Ethna_AppManager
{
	protected $db_m_r = null;
	
	const PORTAL_NEWS_KBN_PICKUP	= 1;
	const PORTAL_NEWS_KBN_NEWS		= 2;
	
	const PORTAL_NEWS_DEL_FLG_NODELETE	= 0;
	const PORTAL_NEWS_DEL_FLG_DELETE	= 1;
	
	/**
	 * 最新のピックアップを取得
	 */
	function getPickup ()
	{
		if ( is_null( $this->db_m_r ) ) {
			$this->db_m_r =& $this->backend->getDB( "m_r" );
		}
		
		$param = array( self::PORTAL_NEWS_KBN_PICKUP, self::PORTAL_NEWS_DEL_FLG_NODELETE );
		$sql =	"SELECT m.id AS news_id, m.* FROM m_portal_news m " .
				"WHERE m.news_kbn = ? AND m.del_flg = ? AND CAST(m.open_date AS DATETIME) <= NOW() " .
				"ORDER BY m.disp_date DESC LIMIT 1";
		
		return $this->db_m_r->GetRow( $sql, $param );
	}
	
	/**
	 * 公開中のニュース一覧を取得
	 */
	function getNewsList ( $pp_id )
	{
		if ( is_null( $this->db_m_r ) ) {
			$this->db_m_r =& $this->backend->getDB( "m_r" );
		}
		
		$param = array( self::PORTAL_NEWS_KBN_NEWS, self::PORTAL_NEWS_DEL_FLG_NODELETE );
		$sql =	"SELECT m.id AS news_id, m.* FROM m_portal_news m " .
				"WHERE m.news_kbn = ? AND m.del_flg = ? AND CAST(m.open_date AS DATETIME) <= NOW() " .
				"ORDER BY m.disp_date DESC";
		
		$news = $this->db_m_r->db->getAssoc( $sql, $param );
		
		if ( Ethna::isError( $news ) ) {
			return array();
		}
		
		$param = array( $pp_id );
		$bind = array();
		foreach ( $news as $row ) {
			$param[] = $row['ID'];
			$bind[] = "?";
		}
		
		$sql = "SELECT u.news_id AS id, u.* FROM ut_portal_news_history u WHERE pp_id = ? AND news_id IN ( " . implode( ",", $bind ) . " )";
		$history = $this->db->db->getAssoc( $sql, $param );
		
		// マージ
		foreach ( $news as $news_id => &$row ) {
			$row['read'] = isset( $history[$news_id] );
			
			$row['date'] = $this->disp2date( $row['disp_date'] );
			
			$row['new'] = ( $row['date'] < date( "Y-m-d" ) ) ? false : true;
			
			if ( $row['read'] ) $row['new'] = false;
		}
		
		return $news;
	}
	
	/**
	 * ニュースを一件取得
	 */
	function getNews ( $news_id )
	{
		if ( is_null( $this->db_m_r ) ) {
			$this->db_m_r =& $this->backend->getDB( "m_r" );
		}
		
		$param = array( $news_id, self::PORTAL_NEWS_KBN_NEWS, self::PORTAL_NEWS_DEL_FLG_NODELETE );
		$sql =	"SELECT * FROM m_portal_news " .
				"WHERE ID = ? AND news_kbn = ? AND del_flg = ? AND CAST(open_date AS DATETIME) <= NOW()";
		
		return $this->db_m_r->GetRow( $sql, $param );
	}
	
	/**
	 * ニュースを読む
	 */
	function readNews ( $pp_id, $news_id, &$read_now )
	{
		$read_now = false;
		
		$param = array( $pp_id, $news_id );
		$sql = "SELECT * FROM ut_portal_news_history WHERE pp_id = ? AND news_id = ?";
		
		$row = $this->db->GetRow( $sql, $param );
		
		if ( Ethna::isError( $row ) ) {
			return false;
		}
		
		// 既読は処理しない
		if ( isset( $row['pp_id'] ) ) return true;
		
		$puser_m =& $this->backend->getManager( "PortalUser" );
		
		$commit = true;
		$this->db->begin();
		
		$param = array( $pp_id, $news_id );
		$sql = "INSERT INTO ut_portal_news_history( pp_id, news_id, date_created ) VALUES( ?, ?, NOW() )";
		if ( $commit && !$this->db->execute( $sql, $param ) ) {
			$commit = false;
		}
		
		if ( $commit ) {
			$result = $puser_m->addPoint( $pp_id, 5, "ニュース閲覧" );
			if ( Ethna::isError( $result ) ) {
				$commit = false;
			}
		}
		
		if ( $commit )	$this->db->commit();
		else			$this->db->rollback();
		
		$read_now = true;
		
		return $commit;
	}
	
	/**
	 * ニュースの日付型を変更
	 */
	private function disp2date ( $disp_date )
	{
		return ( substr( $disp_date, 0, 4 ) . "-" . substr( $disp_date, 4, 2 ) . "-" . substr( $disp_date, 6, 2 ) );
	}
}
?>
