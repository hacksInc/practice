<?php
/**
 *  Pp_InviteManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'array_column.php';

/**
 *  Pp_InviteManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_InviteManager extends Ethna_AppManager
{
	/** ステータス：未付与 */
	const STATUS_NOT_GRANT  = 0;
	
	/** ステータス：付与済 */
	const STATUS_GRANTED    = 1;
	
	/** ステータス：不可(不可上限) */
	const STATUS_IMPOSSIBLE = 2;
	
	/** タイプ：被招待者 */
	const TYPE_GUEST = 1;
	
	/** タイプ：招待者 */
	const TYPE_INVITED = 2;
	
	const DIST_STATUS_START = 0;	//配布開始
	const DIST_STATUS_STOP  = 1;	//配布中止
	
	// t_user_invite は user_id, friend_id, invite_mng_id がプライマリキー
	// 複数同時実施キャンペーンに対応するため
	
	/**
	 * フレンド情報を取得する
	 * (invite_mng_idの数だけ)
	 * 
	 * @param int $user_id
	 * @param int $friend_id
	 * @return array
	 */
	function getUserInvite($user_id, $friend_id)
	{
		$param = array($user_id, $friend_id);
		$sql = "SELECT * FROM t_user_friend"
		     . " WHERE user_id = ? AND friend_id = ?";
		
        $unit_m =& $this->backend->getManager('Unit');
      //return $unit_m->GetRowMultiUnit($sql, $param, NULL, false);
        return $unit_m->GetAllMultiUnit($sql, $param, NULL, false);
	}

	/**
	 * フレンド一覧を取得する
	 * 
	 * @param int $user_id
	 * @param int $status 0だったらstatusに関わらず全件取得
	 * @param int $type
	 * @return array
	 */
	function getUserInviteListUser($user_id, $status, $type)
	{
		$param = array($user_id, $type);
		$sql = "SELECT * FROM t_user_invite"
		     . " WHERE user_id = ? AND type = ?";
		if ($status != 0) {
			$param = array($user_id, $type, $status);
			$sql .= " AND status = ?";
		}
		
        $unit_m =& $this->backend->getManager('Unit');
        $rows = $unit_m->GetAllMultiUnit($sql, $param, NULL, false);
		
		return $rows;
	}
	function getUserInviteListFriend($friend_id, $status, $type)
	{
		$param = array($friend_id, $type);
		$sql = "SELECT * FROM t_user_invite"
		     . " WHERE friend_id = ? AND type = ?";
		if ($status != 0) {
			$param = array($friend_id, $type, $status);
			$sql .= " AND status = ?";
		}
		
        $unit_m =& $this->backend->getManager('Unit');
        $rows = $unit_m->GetAllMultiUnit($sql, $param, NULL, false);
		
		return $rows;
	}

	/**
	 * フレンド情報をセットする
	 * 
	 * @param int $user_id
	 * @param int $friend_id
	 * @param int $invite_mng_id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserInvite($user_id, $friend_id, $invite_mng_id, $columns)
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
		$param[] = $invite_mng_id;
		$sql = "UPDATE t_user_invite SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE user_id = ? AND friend_id = ? AND invite_mng_id = ?";

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
			$param = array($user_id, $friend_id, $invite_mng_id, $columns['status'], $columns['type']);
			$sql = "INSERT INTO t_user_invite(user_id, friend_id, invite_mng_id, status, type, date_created)"
				 . " VALUES(?, ?, ?, ?, ?, NOW())";
			if (!$unit_m->executeForUnit($unit, $sql, $param, false)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
                    $ret->ErrorNo, $ret->ErrorMsg, __FILE__, __LINE__);
			}
		}
		return true;
	}

	/**
	 * フレンド数を取得する
	 * 
	 * @param int $user_id
	 * @param int $invite_mng_id
	 * @param int $status
	 * @param int $type
	 * @return int
	 */
	function countUserInvite($user_id, $invite_mng_id, $status, $type)
	{
		$param = array($user_id, $invite_mng_id, $status, $type);
		$sql = "SELECT COUNT(*) AS cnt FROM t_user_invite"
		     . " WHERE user_id = ? AND invite_mng_id = ? AND status = ? AND type = ?";
		
        $unit_m =& $this->backend->getManager('Unit');
        $unit = $unit_m->cacheGetUnitFromUserId($user_id);
		return intval($unit_m->GetOneMultiUnit($sql, $param, $unit, false));
	}

	/**
	 * フレンド情報を削除する（多分、使わないと思う）
	 * 
	 * @param int $user_id
	 * @param int $friend_id
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function deleteUserInvite($user_id, $friend_id)
	{
		$param = array($user_id, $friend_id);
		$sql = "DELETE FROM t_user_invite"
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
	 * 招待報酬配布管理情報を取得する
	 * 
	 * @param int $invite_mng_id
	 * @return array
	 */
	function getInviteMng($invite_mng_id)
	{
		$param = array($invite_mng_id);
		$sql = "SELECT * FROM t_invite_distribution"
		     . " WHERE invite_mng_id = ?";
		
		return $this->db->GetRow($sql, $param);
	}
	/**
	 * 配布待ちステータスの招待報酬配布管理情報を取得する
	 * 
	 * @return array
	 */
	function getInviteMngWait()
	{
		$param = array(self::DIST_STATUS_START, date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
		$sql = "SELECT * FROM t_invite_distribution"
		     . " WHERE status = ? AND date_start <= ? ORDER BY date_start ASC";
		
		return $this->db->GetRow($sql, $param);
	}
	
	/**
	 * 招待報酬配布管理情報一覧を取得する
	 * 
	 * @return array
	 */
	function getInviteMngList( $offset = 0, $limit = 10)
	{
		$param = array( $offset, $limit );
		$sql = "SELECT * FROM t_invite_distribution ORDER BY invite_mng_id DESC LIMIT ?, ?";
		$result = $this->db->execute( $sql, $param );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return $this->db->GetAll($sql, $param);
	}
	
	/**
	 * 期間内でstatus=0(有効)の招待報酬配布管理情報一覧を取得する
	 * 仮に数件該当したら全て返す
	 * 
	 * @return array
	 */
	function getInviteMngListTerm( )
	{
		$param = array(self::DIST_STATUS_START, date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) );
		$sql = "SELECT * FROM t_invite_distribution WHERE status = ? AND date_start <= ? AND date_end > ? ORDER BY invite_mng_id ASC";
		$result = $this->db->execute( $sql, $param );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return $this->db->GetAll($sql, $param);
	}
	
	/**
	 * 招待報酬配布管理情報をセットする
	 * 
	 * @param int $invite_mng_id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setInviteMng($invite_mng_id, $columns)
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array_values($columns);
		$param[] = $invite_mng_id;
		$sql = "UPDATE t_invite_distribution SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE invite_mng_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		if ($affected_rows == 0) {
			// INSERT実行
			$param = array($columns['date_start'], $columns['date_end'], $columns['invite_max'], $columns['g_dist_type'], $columns['g_item_id'], $columns['g_lv'], $columns['g_number'], $columns['g_dist_user_cnt'], $columns['i_dist_type'], $columns['i_item_id'], $columns['i_lv'], $columns['i_number'], $columns['i_dist_user_cnt'], $columns['i_dist_user_total'], $columns['status'], $columns['account_reg'], $columns['account_upd'], date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) );
			$sql = "INSERT INTO t_invite_distribution(date_start, date_end, invite_max, g_dist_type, g_item_id, g_lv, g_number, g_dist_user_cnt, i_dist_type, i_item_id, i_lv, i_number, i_dist_user_cnt, i_dist_user_total, status, account_reg, account_upd, date_created)"
				 . " VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			if (!$this->db->execute($sql, $param)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}
		return true;
	}
	function insertInviteMng($invite_mng_id, $columns)
	{
		// INSERT実行
		$param = array($columns['date_start'], $columns['date_end'], $columns['invite_max'], $columns['g_dist_type'], $columns['g_item_id'], $columns['g_lv'], $columns['g_number'], $columns['g_dist_user_cnt'], $columns['i_dist_type'], $columns['i_item_id'], $columns['i_lv'], $columns['i_number'], $columns['i_dist_user_cnt'], $columns['i_dist_user_total'], $columns['status'], $columns['account_reg'], $columns['account_upd'], date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) );
		$sql = "INSERT INTO t_invite_distribution(date_start, date_end, invite_max, g_dist_type, g_item_id, g_lv, g_number, g_dist_user_cnt, i_dist_type, i_item_id, i_lv, i_number, i_dist_user_cnt, i_dist_user_total, status, account_reg, account_upd, date_created)"
			 . " VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
	function updateInviteMng($invite_mng_id, $columns)
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array_values($columns);
		$param[] = $invite_mng_id;
		$sql = "UPDATE t_invite_distribution SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE invite_mng_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}
	function incInviteMngGCnt($invite_mng_id)
	{
		// UPDATE実行
		$param = array($invite_mng_id);
		$sql = "UPDATE t_invite_distribution SET g_dist_user_cnt=g_dist_user_cnt+1"
			 . " WHERE invite_mng_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
	function incInviteMngICnt($invite_mng_id)
	{
		// UPDATE実行
		$param = array($invite_mng_id);
		$sql = "UPDATE t_invite_distribution SET i_dist_user_cnt=i_dist_user_cnt+1"
			 . " WHERE invite_mng_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
	function incInviteMngITotal($invite_mng_id)
	{
		// UPDATE実行
		$param = array($invite_mng_id);
		$sql = "UPDATE t_invite_distribution SET i_dist_user_total=i_dist_user_total+1"
			 . " WHERE invite_mng_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	// 招待報酬管理情報に登録し、そのレコードの管理IDを取得する
	function insertInviteMngReturningInviteMngId( $columns )
	{
		$ret = $this->insertInviteMng( Pp_InviteManager::ID_NEW_PRESENT, $columns );
		if( !$ret || Ethna::isError( $ret ))
		{
			return 0;
		}
		$sql = "SELECT LAST_INSERT_ID()";
		return $this->db->GetOne( $sql );
	}

	// 指定された全管理IDの配布を中止する
	function abortInviteMngMulti( $invite_mng_ids )
	{
		foreach( $invite_mng_ids as $invite_mng_id )
		{
			$param[] = $invite_mng_id;
			$where_invite_mng_id_in[] = '?';
		}
		$sql = "UPDATE t_invite_distribution SET status = ".self::DIST_STATUS_STOP." "
			 . "WHERE invite_mng_id IN (".implode( ',', $where_invite_mng_id_in ).")";
		if( !$this->db->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

}
?>
