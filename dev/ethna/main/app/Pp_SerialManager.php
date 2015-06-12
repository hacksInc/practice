<?php
/**
 *  Pp_SerialManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_SerialManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */

define( "_SERIAL_HASH_BASE_STR_",			'd#8G6%oN=' ); //HASH生成用の文字列

class Pp_SerialManager extends Ethna_AppManager
{
	/**
	 * DB接続(pp-ini.phpの'dsn_m_r'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_m_r = null;

	/**
	 * DB接続(pp-ini.phpの'dsn_cmn'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn = null;
	
	/**
	 * DB接続(pp-ini.phpの'dsn_cmn_r'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn_r = null;
	
	/**
	 * DB接続
	 */
	function setDB ( $dsn )
	{
		switch ( $dsn ) {
			case "db_cmn":
				if ( !$this->db_cmn ) {
					$this->db_cmn =& $this->backend->getDB( 'cmn' );
				}
				break;
				
			case "db_cmn_r":
				if ( !$this->db_cmn_r ) {
					$this->db_cmn_r =& $this->backend->getDB( 'cmn_r' );
				}
				break;
				
			case "db_m_r":
				if ( !$this->db_m_r ) {
					$this->db_m_r =& $this->backend->getDB( 'm_r' );
				}
				break;
				
			default:
				return false;
		}
		
		return true;
	}
	
	/**
	 * シリアルマスタ情報を取得する
	 * 
	 * @param int $campaign_id
	 * @return array
	 */
	function getMasterSerial ( $campaign_id )
	{
		if (!$this->db_m_r) {
			$this->db_m_r =& $this->backend->getDB('m_r');
		}

		$param = array($campaign_id);
		$sql = "SELECT * FROM m_serial"
		     . " WHERE campaign_id = ?";
		
		return $this->db_m_r->GetRow( $sql, $param );
	}
	
	/**
	 * シリアルマスタ情報を取得する（common_code使用）
	 */
	function getMasterSerialByCommonCode ( $common_code )
	{
		if (!$this->db_m_r) {
			$this->db_m_r =& $this->backend->getDB('m_r');
		}
		
		$param = array( $common_code );
		$sql = "SELECT * FROM m_serial WHERE common_code = ?";
		
		return $this->db_m_r->GetRow( $sql, $param );
	}
	
	/**
	 * シリアル情報を取得する（ユニーク系）
	 * 
	 * @param int $serial_id
	 * @return array
	 */
	function getSerialUnique ( $serial_id, $dsn = "db_cmn" )
	{
		if ( !$this->setDB( $dsn ) ) {
			return Ethna::raiseError("DSN SET ERROR FILE[%s] LINE[%d]", E_USER_ERROR, 
				__FILE__, __LINE__);
		}
		
		$param = array( $serial_id );
		$sql = "SELECT * FROM ct_serial_unique"
		     . " WHERE serial_id = ?";
		
		return $this->$dsn->GetRow( $sql, $param );
	}
	
	/**
	 * シリアル情報を取得する（共通系）
	 */
	function getSerialCommon ( $pp_id, $campaign_id, $dsn = "db_cmn" )
	{
		if ( !$this->setDB( $dsn ) ) {
			return Ethna::raiseError("DSN SET ERROR FILE[%s] LINE[%d]", E_USER_ERROR, 
				__FILE__, __LINE__);
		}
		
		$param = array( $campaign_id, $pp_id );
		$sql = "SELECT * FROM ct_serial_common WHERE campaign_id = ? AND pp_id = ?";
		
		return $this->$dsn->GetRow( $sql, $param );
	}
	
	/**
	 * シリアル情報を更新する（ユニーク用）
	 * 
	 * @param int $user_id
	 * @param int $serial_id
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function updateSerialUnique ( $pp_id, $serial_id )
	{
		if ( !$this->db_cmn ) {
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}
		
		// UPDATE実行
		$param = array( $pp_id, $serial_id );
		$sql = "UPDATE ct_serial_unique SET pp_id = ?, date_modified = NOW()"
			 . " WHERE serial_id = ? AND pp_id = 0";
		if ( !$this->db_cmn->execute( $sql, $param ) ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db_cmn->db->affected_rows();
		if ( $affected_rows != 1 ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * シリアル登録情報(共通形式)の追加
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $campaign_id キャンペーンID
	 *
	 * @return bool|object 正常終了:true, 正常終了（追加なし）:false, 更新エラー:Ethna_Errorオブジェクト
	 */
	function insertSerialCommon( $pp_id, $campaign_id )
	{
		if( empty( $pp_id ) || empty( $campaign_id ))
		{	// 追加対象の指定がない
			return false;
		}

		if (!$this->db_cmn) {
			$this->db_cmn =& $this->backend->getDB('cmn');
		}

		// DBに追加する
		$param = array( $campaign_id, $pp_id );
		$sql = "INSERT INTO ct_serial_common( campaign_id, pp_id, date_created ) "
			 . "VALUES( ?, ?, NOW() )";
		
		if( !$this->db_cmn->execute( $sql, $param ))
		{	// 追加エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return true;
	}
	
	/**
	 * シリアルコード作成（この関数はバッチから実行すること！）
	 *
	 * @param string $segment セグメント（二桁の数字）
	 * @param int $campaign_id キャンペーンID
	 * @param int $num 作成件数
	 */
	function createSerial ( $segment, $campaign_id, $num )
	{
		if ( !$this->db_cmn ) {
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}
		
		for ( $i = 0; $i < $num; $i++ ) {
			$code = $segment . sprintf( "%010d", mt_rand() );
			$param = array( $code, $campaign_id );
			$sql = "INSERT INTO ct_serial_unique( serial_id, campaign_id, date_created, date_modified ) VALUES( ?, ?, NOW(), NOW() )";
			if( !$this->db_cmn->execute( $sql, $param ))
			{	// DUPエラーは無視する
//				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
//					$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__ );
			}
		}
	}
}
?>
