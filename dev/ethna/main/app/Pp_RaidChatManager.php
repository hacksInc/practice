<?php
/**
 *  Pp_RaidChatManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_RaidManager.php';

/**
 *  Pp_RaidChatManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RaidChatManager extends Pp_RaidManager
{
	/**
	 * 発言ログの記録
	 * 
	 * @param int $party_id パーティID
	 * @param int $user_id 発言ユーザーID
	 * @param int $stamp_id 発言スタンプID
	 *
	 */
	function logChat( $party_id, $user_id, $stamp_id )
	{
		$param = array( $party_id, $user_id, $stamp_id );
		$sql = "INSERT INTO log_raid_chat( party_id, user_id, stamp, date_created )"
			 . "VALUES( ?, ?, ?, NOW())";
		if( !$this->db_unit1->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 * 発言ログの取得
	 * 
	 * @param int $party_id パーティID
	 * @param int $date_begin 取得開始日時（nullの場合はパーティの全発言を取得）
	 *
	 * @return 発言リスト
	 */
	function getChat( $party_id, $date_begin = null )
	{
		$param = array( $party_id );
		$sql = "SELECT * FROM log_raid_chat WHERE party_id = ? ";
		if( is_null( $date_begin ) === false )
		{
			$sql .= "AND date_created >= ? ";
			$param[] = $date_begin;
		}
		$sql .= "ORDER BY id DESC";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}
}
?>
