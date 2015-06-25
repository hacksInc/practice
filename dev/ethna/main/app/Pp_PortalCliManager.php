<?php
/**
 *  Pp_PortalCliManager.php
 *	ポータル関係のバッチ処理
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once "Pp_UserManager.php";

/**
 *  Pp_PortalCliManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PortalCliManager extends Ethna_AppManager
{
	protected $db_portal = null;
	protected $db_m_r = null;
	protected $db_unit1 = null;
	
	protected $m_theme = null;
	
	/**
	 * Androidユーザー引継ぎ処理
	 * 内部的には、手動でやってた部分の自動化
	 */
	function convertAndroidUser ( $from, $to )
	{
		// 変数初期化
		$point = 0;
		
		// DBインスタンス作成
		if ( is_null( $this->db_portal ) )	$this->db_portal =& $this->backend->getDB( "p" );
		if ( is_null( $this->db_m_r ) )		$this->db_m_r =& $this->backend->getDB( "m_r" );
		if ( is_null( $this->db_unit1 ) )	$this->db_unit1 =& $this->backend->getDB( "unit1" );
		
echo( "-----------------\n" );
echo( "引継ぎ開始：$from => $to \n" );
		
		// テーマ所持情報の引継ぎ
		// 新端末でも購入した重複分については、その分をポイントに還元
		$param = array( $from );
		$sql = "SELECT t.theme_id AS key_id, t.* FROM user_theme t WHERE t.user_ID = ?";
		$from_list = $this->db_portal->db->getAssoc( $sql, $param );
//		$from_list = $this->db_portal->GetAll( $sql, $param );
		
		if ( is_null( $this->m_theme ) ) {
			$sql = "SELECT m.theme_id AS key_id, m.* FROM m_portal_theme m";
			$this->m_theme = $this->db_m_r->db->getAssoc( $sql );
		}
		
		$param = array( $to );
		$sql = "SELECT t.theme_id AS key_id, t.* FROM ut_portal_user_theme t WHERE t.pp_id = ?";
		$to_list = $this->db_unit1->db->getAssoc( $sql, $param );
//		$to_list = $this->db->getAll( $sql, $param );
		
		foreach ( $from_list as $theme_id => $row ) {
			if ( isset( $to_list[$theme_id] ) ) {
				$point += $this->m_theme[$theme_id]['use_point'];
echo( "テーマ「" . $this->m_theme[$theme_id]['chara_name'] . "」は所持済み→" . $this->m_theme[$theme_id]['use_point'] . "に変換\n" );
			} else {
				$param = array( $to, $theme_id );
				$sql = "INSERT INTO ut_portal_user_theme( pp_id, theme_id, date_created, date_modified ) VALUES( ?, ?, NOW(), NOW() )";
				$this->db_unit1->execute( $sql, $param );
echo( "テーマ「" . $this->m_theme[$theme_id]['chara_name'] . "」を引き継ぎ\n" );
			}
		}
		
		// ポータルポイントの引継ぎ
		$param = array( $from );
		$sql = "SELECT SUM( point ) AS point FROM user_point WHERE user_ID = ?";
		$from_point = $this->db_portal->getRow( $sql, $param );
		
		// まあ、ないとは思うけどマイナス値だったらお情けで帳消し
		if ( $from_point['point'] > 0 ) $point += $from_point['point'];
		
		if ( $point > 0 ) {
			$param = array( $point, $to );
			$sql = "UPDATE ut_portal_user_base SET point = point + ?, date_modified = NOW() WHERE pp_id = ?";
			$this->db_unit1->execute( $sql, $param );
			
			$param = array( $to, $point, "Android引継ぎ補填分" );
			$sql = "INSERT INTO ut_portal_point_history( pp_id, type, point, memo, date_created ) VALUES( ?, 1, ?, ?, NOW() )";
			$this->db_unit1->execute( $sql, $param );
echo( "ポイント引継ぎ：$point \n" );
		} else {
echo( "ポイント引継ぎ：なし（所持ポイントなし） \n" );
		}
		
		// ニュース閲覧履歴の引継ぎ
		// ここは強引にSQL一個で引き継ぐ
		$param = array( $to, $from );
		$sql = "INSERT IGNORE INTO ut_portal_news_history SELECT ? AS pp_id, news_ID, regist_date FROM psycho_pass2014.news_read WHERE user_ID = ?";
		$this->db_unit1->execute( $sql, $param );
		
echo( "ニュース履歴の引継ぎ\n" );
		
		// イベント情報の引継ぎ
		// ここもSQLのみで
		$param = array( $to, $from );
		$sql = "INSERT IGNORE INTO ut_portal_user_serial SELECT ? AS pp_id, serial_id, date_created FROM psycho_pass2014.ct_event_serial WHERE user_id = ?";
		$this->db_unit1->execute( $sql, $param );
		
echo( "イベント履歴の引継ぎ\n" );
echo( "引継ぎ完了\n" );
echo( "-----------------\n" );
	}
}
?>
