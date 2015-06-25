<?php
/**
 *  Pp_PortalThemeManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_PortalThemeManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PortalThemeManager extends Pp_PortalBaseManager
{
	protected $db_m_r = null;
	
	const LOCK_FLG_LOCK		= 0;
	const LOCK_FLG_UNLOCK	= 1;
	
	const CURRENT_FLG_CURRENT = 1;
	
	/**
	 * 指定テーママスターの取得
	 *
	 * @param int $theme_id
	 * @return array 取得データ
	 */
	function getMasterTheme ( $theme_id )
	{
		$list = $this->getMasterThemeList();

		return $list[$theme_id];
	}
	
	/**
	 * テーママスターの全件取得
	 *
	 * @return array 取得データ
	 */
	function getMasterThemeList ()
	{
		if ( is_null( $this->db_m_r ) ) {
			$this->db_m_r =& $this->backend->getDB( "m_r" );
		}
		
		$sql = "SELECT t.theme_id AS id, t.* FROM m_portal_theme t ORDER BY t.theme_id ASC";
		
		return $this->db_m_r->db->getAssoc( $sql );
	}
	
	/**
	 * 現在のテーマ情報の取得
	 * 
	 * @param int $pp_id サイコパスID
	 * @param bool $master DB接続先
	 * @return array 取得データ
	 */
	function getCurrentUserTheme ( $pp_id, $dsn = "db_r" )
	{
		$param = array( $pp_id, self::CURRENT_FLG_CURRENT );
		$sql = "SELECT * FROM ut_portal_user_theme WHERE pp_id = ? AND current_flg = ?";
		$row = $this->$dsn->GetRow( $sql, $param );
		
		if ( Ethna::isError( $row ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->$dsn->db->ErrorNo(), $this->$dsn->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		return $row;
	}
	
	/**
	 * ユーザーの所有しているテーマ情報全てを取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param bool $master DB接続先
	 * @return array 取得データ
	 */
	function getUserThemeList ( $pp_id, $dsn = "db" )
	{
		$param = array( $pp_id );
		$sql = "SELECT ut.theme_id AS id, ut.* FROM ut_portal_user_theme ut WHERE ut.pp_id = ?";
		
		return $this->$dsn->db->getAssoc( $sql, $param );
	}
	
	/**
	 * テーマ情報の作成
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $theme_id テーマID
	 * @param int $current_flg カレントフラグ
	 * @return bool 処理結果
	 */
	function insertUserTheme ( $pp_id, $theme_id, $current_flg = 0 )
	{
		$param = array( $pp_id, $theme_id, $current_flg );
		$sql = "INSERT INTO ut_portal_user_theme( pp_id, theme_id, current_flg, date_created, date_modified ) VALUES( ?, ?, ?, NOW(), NOW() )";
		
		if ( !$this->db->execute( $sql, $param ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->$dsn->db->ErrorNo(), $this->$dsn->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		return true;
	}
	
	/**
	 * テーマ情報の更新（主にテーマ切り替え用）
	 *
	 * @param int $entry_id 管理ID
	 * @param array $columns 処理データ
	 * @return bool|object 処理結果
	 */
	function updateUserTheme ( $entry_id, $columns )
	{
		if ( empty( $entry_id ) || count( $columns ) == 0 )
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}
		
		// 主キーが更新されるといかんので更新内容から削除
		if ( isset( $columns['pp_id'] ) ) unset( $columns['pp_id'] );

		// DB更新
		$str_set = array();
		$param = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = ?";
			$param[] = $v;
		}
		
		$param[] = $entry_id;
		$sql = "UPDATE ut_portal_user_theme SET ".implode( ',', $str_set ).", date_modified = NOW() WHERE entry_id = ?";
		if ( !$this->db->execute( "db", $sql, $param ) )
		{	// 更新エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->$dsn->db->ErrorNo(), $this->$dsn->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 更新された行数をチェック
		if ( $this->db->db->affected_rows() == 0 )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * テーマ変更
	 */
	function updateCurrentTheme ( $pp_id, $theme_id )
	{
		$commit = true;
		$this->db->begin();
		
		// いったん0で更新
		$param = array( $pp_id );
		$sql = "UPDATE ut_portal_user_theme SET current_flg = 0 WHERE pp_id = ?";
		if ( $commit ) $commit = $this->db->execute( $sql, $param );
		
		// 対象を1で更新
		$param = array( self::CURRENT_FLG_CURRENT, $pp_id, $theme_id );
		$sql = "UPDATE ut_portal_user_theme SET current_flg = ?, date_modified = NOW() WHERE pp_id = ? AND theme_id = ?";
		if ( $commit ) $commit = $this->db->execute( $sql, $param );
		
		if ( $commit )	$this->db->commit();
		else			$this->db->rollback();
		
		return $commit;
	}
}
?>
