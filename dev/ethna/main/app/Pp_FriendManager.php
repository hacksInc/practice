<?php
/**
 *  Pp_FriendManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'array_column.php';

/**
 *  Pp_FriendManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_FriendManager extends Ethna_AppManager
{
	/*
	status=1・2はフレンド上限数に含める
	status=3・4はフレンド上限数に含めない
	*/
	/** ステータス：申請中 */
	const STATUS_REQUEST_S = 1;
	
	/** ステータス：承認済（フレンド状態） */
	const STATUS_FRIEND    = 2;
	
	/** ステータス：申請を受けている */
	const STATUS_REQUEST_R = 3;
	
	/** ステータス：ブロック（非フレンド状態） */
	const STATUS_BLOCK     = 4;
	
	/**
	 * フレンド情報を取得する
	 * 
	 * @param int $user_id
	 * @param int $friend_id
	 * @return array
	 */
	function getUserFriend($user_id, $friend_id)
	{
		$param = array($user_id, $friend_id);
		$sql = "SELECT * FROM t_user_friend"
		     . " WHERE user_id = ? AND friend_id = ?";
		
        $unit_m =& $this->backend->getManager('Unit');
        return $unit_m->GetRowMultiUnit($sql, $param, NULL, false);
	}
	
	/**
	 * フレンド一覧を取得する
	 * 
	 * @param int $user_id
	 * @param int $status 0だったらstatusに関わらず全件取得
	 * @param bool $get_cache キャッシュから取得するか …キャッシュは関数内のstatic変数
	 * @param bool $set_cache キャッシュにセットするか
	 * @return array
	 */
	function getFriendList($user_id, $status, $get_cache = true, $set_cache = true)
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
		$sql = "SELECT * FROM t_user_friend"
		     . " WHERE user_id = ?";
		if ($status != 0) {
			$param = array($user_id, $status);
			$sql .= " AND status = ?";
		}
		$sql .= " ORDER BY date_created DESC";


		
        $unit_m =& $this->backend->getManager('Unit');
        $rows = $unit_m->GetAllMultiUnit($sql, $param, NULL, false);

		if ($set_cache) {
			if (!array_key_exists($user_id, $cache)) {
				$cache[$user_id] = array();
			}
			
			$cache[$user_id][$status] = $rows;
		}
		
		return $rows;
	}

	/**
	 * フレンドID一覧を取得する
	 * 
	 * @param int $user_id
	 * @param int $status 0だったらstatusに関わらず全件取得
	 * @param bool $get_cache キャッシュから取得するか …キャッシュは関数内のstatic変数
	 * @param bool $set_cache キャッシュにセットするか
	 * @return array
	 */
	function getFriendIdList($user_id, $status, $get_cache = true, $set_cache = true)
	{
		$rows = $this->getFriendList($user_id, $status, $get_cache, $set_cache);
		if (is_array($rows)) {
			return array_column($rows, 'friend_id');
		} else {
			return $rows;
		}
	}

	/**
	 * フレンド情報をセットする
	 * 
	 * @param int $user_id
	 * @param int $friend_id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserFriend($user_id, $friend_id, $columns)
	{
		//ステータスが未指定
		if (!isset($columns['status'])) {
			return Ethna::raiseError("status none", E_USER_ERROR);
		}

        $unit_m =& $this->backend->getManager('Unit');
        $unit = $unit_m->cacheGetUnitFromUserId($user_id);

		// UPDATE実行
		$param = array_values($columns);
		$param[] = $user_id;
		$param[] = $friend_id;
		$sql = "UPDATE t_user_friend SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE user_id = ? AND friend_id = ?";

        $ret = $unit_m->executeForUnit($unit, $sql, $param, false);
        if ($ret->ErrorNo) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
                $ret->ErrorNo, $ret->ErrorMsg, __FILE__, __LINE__);
		}

		// 影響した行数を確認
		$affected_rows = $ret->affected_rows;
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		if ($affected_rows == 0) {
			// INSERT実行
			$param = array($user_id, $friend_id, $columns['status']);
			$sql = "INSERT INTO t_user_friend(user_id, friend_id, status, date_created)"
				 . " VALUES(?, ?, ?, NOW())";
			if (!$unit_m->executeForUnit($unit, $sql, $param, false)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
                    $ret->ErrorNo, $ret->ErrorMsg, __FILE__, __LINE__);
			}
		}
		return true;
	}
	
	/**
	 * フレンド数を取得する（statusがSTATUS_FRIEND以下）
	 * 
	 * @param int $user_id
	 * @return int
	 */
	function countUserFriend($user_id)
	{
	//	$param = array($user_id, self::STATUS_FRIEND);
		$param = array($user_id, self::STATUS_REQUEST_R);//申請を受けている分も数に加える
		$sql = "SELECT COUNT(*) AS cnt FROM t_user_friend"
		     . " WHERE user_id = ? AND status <= ?";
		
        $unit_m =& $this->backend->getManager('Unit');
        $unit = $unit_m->cacheGetUnitFromUserId($user_id);
		return intval($unit_m->GetOneMultiUnit($sql, $param, $unit, false));
	}
	
	/**
	 * フレンドステータス毎の数を取得する
	 * 
	 * @param int $user_id
	 * @param int $status
	 * @return int
	 */
	function countUserFriendStatus($user_id, $status)
	{
		$param = array($user_id, $status);
		$sql = "SELECT COUNT(*) AS cnt FROM t_user_friend"
		     . " WHERE user_id = ? AND status = ?";
		
        $unit_m =& $this->backend->getManager('Unit');
        return intval($unit_m->GetOneMultiUnit($sql, $param, NULL, false));
	}
	
	/**
	 * フレンド情報を削除する
	 * 
	 * @param int $user_id
	 * @param int $friend_id
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function deleteUserFriend($user_id, $friend_id)
	{
		$param = array($user_id, $friend_id);
		$sql = "DELETE FROM t_user_friend"
		     . " WHERE user_id = ? AND friend_id = ?";
		
        $unit_m =& $this->backend->getManager('Unit');
        $unit = $unit_m->cacheGetUnitFromUserId($user_id);

        $ret = $unit_m->executeForUnit($unit, $sql, $param, false);
        if ($ret->ErrorNo) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
                $ret->ErrorNo, $ret->ErrorMsg, __FILE__, __LINE__);
		}

        // 影響した行数を確認
		$affected_rows = $ret->affected_rows;
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		return true;
	}
	
	/**
	 * チケット送信日時をリセットする
	 * 
	 * @param int $user_id
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
     * @deprecated
	 */
	function resetUserFrienfSendTicket($user_id)
	{
		// UPDATE実行
		$param = array($user_id);
		$sql = "UPDATE t_user_friend SET date_send_ticket = NULL"
			 . " WHERE user_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
	
	/**
	 * チケット送信日時をセットする
	 * 
	 * @param int $user_id
	 * @param datetime $date_send
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
     * @deprecated
	 */
	function setUserFrienfSendTicket($user_id, $date_send)
	{
		// UPDATE実行
		$param = array($date_send, $user_id, self::STATUS_FRIEND);
		$sql = "UPDATE t_user_friend SET date_send_ticket = ?"
			 . " WHERE user_id = ? AND status = ? AND date_send_ticket IS NULL";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
	
	/**
	 * チケットを送っていないフレンドの数を取得する
	 * 
	 * @param int $user_id
	 * @return int
     * @deprecated
	 */
	function countUserFriendNotSendTicket($user_id)
	{
		$param = array($user_id, self::STATUS_FRIEND);
		$sql = "SELECT COUNT(*) AS cnt FROM t_user_friend"
		     . " WHERE user_id = ? AND status = ? AND date_send_ticket IS NULL";
        $unit_m =& $this->backend->getManager('Unit');
        return $unit_m->GetOneMultiUnit($sql, $param, NULL, false);
	}
	
	/**
	 * チケットを送っていないフレンドID一覧を取得する
	 * 
	 * @param int $user_id
	 * @return array
     * @deprecated
	 */
	function getUserFriendNotSendTicketList($user_id)
	{
		$param = array($user_id, self::STATUS_FRIEND);
		$sql = "SELECT friend_id FROM t_user_friend"
		     . " WHERE user_id = ? AND status = ? AND date_send_ticket IS NULL";
        $unit_m =& $this->backend->getManager('Unit');
        return $unit_m->GetOneMultiUnit($sql, $param, NULL, false);
	}
	
	//AdminFriendManagerから移動
	/**
	 * 指定されたステータスのフレンド全員のdate_bringをリセットする
	 * 
	 * @param int $user_id
	 * @param int $status 0だったらstatusに関わらず全件取得
	 * @return true/Ethna::raiseError
	 */
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
		/*
		$ret = $this->db->execute($sql, $param);
		if (!$ret) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		*/
		//ユニット化対応
		$unit_m =& $this->backend->getManager('Unit');
		$unit = $unit_m->cacheGetUnitFromUserId($user_id);
		$ret = $unit_m->executeForUnit($unit, $sql, $param, false);
		if ($ret->ErrorNo) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$ret->ErrorNo, $ret->ErrorMsg, __FILE__, __LINE__);
		}
		
		return true;
	}
	


	/**
	 * フレンド一覧のリーダーモンスターのデータを取得する
	 * 
	 * @param int $user_id
	 * @param int $status
	 * @return array
	 */
	function getFriendLeaderList($user_id, $status = self::STATUS_FRIEND)
	{
		//フレンドID一覧を取得
		$friend_list = $this->getFriendList($user_id, $status);
		$friend_id_list = array();
		$friend_status_list = array();
		foreach($friend_list as $key => $row) {
			$friend_id_list[] = $row['friend_id'];
			$friend_status_list[($row['friend_id'])] = $row['status'];
		}
		$monster_m =& $this->backend->getManager('Monster');
		$friend_leader_list = $monster_m->getActiveLeaderList(
			$friend_id_list
		);
		if ($friend_leader_list == null) {
			$friend_leader_list = array();
		} else {
			foreach($friend_leader_list as $key => $row) {
				$friend_leader_list[$key]['friend_status'] = $friend_status_list[($friend_leader_list[$key]['user_id'])];
			}
		}
		return $friend_leader_list;
	}

}
?>
