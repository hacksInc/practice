<?php
/**
 *  Pp_AdminFriendManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_FriendManager.php';

/**
 *  Pp_AdminFriendManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminFriendManager extends Pp_FriendManager
{
	/**
	 * フレンド一覧を取得する
	 * 
	 * @param int $user_id
	 * @param int $status 0だったらstatusに関わらず全件取得
	 * @param bool $get_cache キャッシュから取得するか …キャッシュは関数内のstatic変数
	 * @param bool $set_cache キャッシュにセットするか
	 * @return array
	 */
	function getFriendListAd($user_id, $status, $get_cache = true, $set_cache = true)
	{
		if (!$status) {
			$status = 0;
		}
		
		static $cache = array(); // $cache[user_id][status] = rows
		
		if ($get_cache) {
			if (array_key_exists($user_id, $cache) &&
				array_key_exists($status, $cache[$user_id])
			) {
				return $cache[$user_id][$status];
			}
		}
		
		$param = array($user_id);
		$sql = "SELECT f.*, b.name FROM t_user_friend f, t_user_base b"
		     . " WHERE f.user_id = ? AND f.friend_id = b.user_id";
		if ($status != 0) {
			$param = array($user_id, $status);
			$sql .= " AND status = ?";
		}
		$sql .= " ORDER BY date_created DESC";
		
		$rows = $this->db->GetAll($sql, $param);
		if ($set_cache) {
			if (!array_key_exists($user_id, $cache)) {
				$cache[$user_id] = array();
			}
			
			$cache[$user_id][$status] = $rows;
		}
		
		return $rows;
	}
	
	//FriendManagerへ移動
	/**
	 * 指定されたステータスのフレンド全員のdate_bringをリセットする
	 * 
	 * @param int $user_id
	 * @param int $status 0だったらstatusに関わらず全件取得
	 * @return true/Ethna::raiseError
	 */
	/*
	function resetFriendDateBring($user_id, $status)
	{
		if (!$status) {
			$status = 0;
		}
		
		$param = array($user_id);
		$sql = "UPDATE t_user_friend SET date_bring=NULL"
		     . " WHERE user_id = ?";
		if ($status != 0) {
			$param = array($user_id, $status);
			$sql .= " AND status = ?";
		}
		
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return true;
	}
	*/
	
}
?>
