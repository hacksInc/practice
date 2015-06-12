<?php
/**
 *  Pp_AdminRaidPartyManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_RaidPartyManager.php';

/**
 *  Pp_AdminRaidPartyManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminRaidPartyManager extends Pp_RaidPartyManager
{
	/**
	 * パーティ情報を連想配列で取得する
	 * 
	 * ※ADODbのGetAssoc関数を使用するので、以下に注意
	 * ・カラム名を指定する場合は先頭を"party_id"にすること
	 * ・取得結果は、 $assoc[party_id] = array(party_id以外のカラム名 => 値) となるので、
	 * 　先頭に指定したparty_idを参照する際は注意
	 * @param array $party_id_list パーティIDの配列
	 * @param array $colnames 取得するカラム名の配列（省略可）
	 * @return パーティ情報
	 */
	function getPartyAssoc($party_id_list, $colnames = null)
	{
		if (is_array($colnames)) {
			$colnames_clause = implode(",", $colnames);
		} else {
			$colnames_clause = "*";
		}
		
		$bind_clause = str_repeat("?,", count($party_id_list) - 1) . "?";
		
		$sql = "SELECT {$colnames_clause}"
			 . " FROM t_raid_party"
			 . " WHERE party_id IN({$bind_clause})";
			 
		return $this->db_r->db->GetAssoc($sql, $party_id_list);
	}
	
	/**
	 * パーティ情報の一覧を取得する
	 * @param int $status フィルタ条件のパーティステータス（PARTY_STATUS_NONEだったら指定なし）
	 * @param int $limit 取得最大件数
	 * @return パーティ情報 date_modifiedで降順
	 */
	function getPartyList($status=self::PARTY_STATUS_NONE, $limit=100)
	{
		$param = array($status);
	
		$sql = "SELECT *"
			 . " FROM t_raid_party";
		if ($status != self::PARTY_STATUS_NONE) $sql .= " WHERE status=?";
		else                                  $sql .= " WHERE status>?";
		$sql .= " ORDER BY date_modified DESC LIMIT $limit";
		return $this->db->db->GetAll($sql, $param);
	}
	
}
?>