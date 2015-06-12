<?php
/**
 *  Pp_PortalEventManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_PortalEventManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PortalEventManager extends Ethna_AppManager
{
	protected $db_m_r = null;
	
	/**
	 * DB接続
	 */
	private function _setDB ()
	{
		if ( is_null( $this->db_m_r ) ) $this->db_m_r = $this->backend->getDB( "m_r" );
	}
	
	/**
	 * イベントマスタ取得
	 */
	function getMasterEvent ( $event_id )
	{
		$this->_setDB();
		
		$param = array( $event_id );
		$sql = "SELECT * FROM m_portal_event WHERE event_id = ?";
		
		return $this->db_m_r->GetRow( $sql, $param );
	}
	
	/**
	 * イベントマスタ全件取得
	 */
	function getMasterEventList ()
	{
		$this->_setDB();
		
		$sql = "SELECT m.event_id AS id, m.* FROM m_portal_event m ORDER BY event_id ASC";
		
		return $this->db_m_r->db->GetAssoc( $sql );
	}
	
	/**
	 * シリアルマスタ取得
	 */
	function getMasterSerial ( $serial_id )
	{
		$this->_setDB();
		
		$param = array( $serial_id );
		$sql = "SELECT * FROM m_portal_serial WHERE serial_id = ?";
		
		return $this->db_m_r->GetRow( $sql, $param );
	}
	
	/**
	 * 指定コードのシリアルマスタを取得
	 */
	function getMasterSerialByCode ( $code )
	{
		$this->_setDB();
		
		$param = array( $code );
		$sql = "SELECT * FROM m_portal_serial WHERE code = ?";
		
		return $this->db_m_r->GetRow( $sql, $param );
	}
	
	/**
	 * シリアルマスタ全件取得
	 */
	function getMasterSerialList ()
	{
		$this->_setDB();
		
		$sql = "SELECT m.serial_id AS id, m.* FROM m_portal_serial m ORDER BY serial_id ASC";
		
		return $this->db_m_r->db->GetAssoc( $sql );
	}
	
	/**
	 * ユーザーのシリアル状況を取得
	 */
	function getUserSerialList ( $pp_id, $dsn = "db" )
	{
		$param = array( $pp_id );
		$sql = "SELECT u.serial_id AS id, u.* FROM ut_portal_user_serial u WHERE pp_id = ?";
		
		return $this->$dsn->db->GetAssoc( $sql, $param );
	}
	
	/**
	 * シリアル情報を追加
	 */
	function insertUserSerial ( $pp_id, $serial_id )
	{
		$param = array( $pp_id, $serial_id );
		$sql = "INSERT INTO ut_portal_user_serial( pp_id, serial_id, date_created ) VALUES( ?, ?, NOW() )";
		
		if ( !$this->db->execute( $sql, $param ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		return true;
	}
}
?>
