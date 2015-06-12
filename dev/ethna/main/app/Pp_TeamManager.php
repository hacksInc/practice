<?php
/**
 *  Pp_TeamManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_TeamManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_TeamManager extends Ethna_AppManager
{
	/**
	 * フレンド（助っ人）枠の場所の初期値
	 */
	const INITIAL_HELPER_POSITION = 5;
	
	/**
	 * リーダーの場所の初期値
	 */
	const INITIAL_LEADER_POSITION = 1;
	
	/**
	 * user_monster_idについて、
	 * 通常のユーザ自身の所持モンスターではなく、
	 * フレンド（助っ人）の所持モンスターが使用されることをあらわす値
	 * 
	 * このマネージャではなく、MonsterManagerに置く方がいいかも？
	 */
	const USER_MONSTER_ID_HELPER = -2;
	
	/**
	 * モンスターがいないことをあらわす値
	 */
	const USER_MONSTER_ID_EMPTY = -1;
	
	/**
	 * 最大チーム数
	 * 
	 * team_idは0始まりなので、取り得るteam_idは、この値未満
	 */
	const MAX_TEAM_NUM = 5;
	
	/**
	 * 場所の最大値
	 * 
	 * positionは1始まりなので、取り得るposition_idは、この値以下
	 */
	const MAX_POSITION = 5;
	
	/**
	 * 場所を初期化する
	 * 
	 * @param int $user_id
	 * @return bool|object 成功時:true, 失敗時:Ethnaエラーオブジェクト
	 */
	function initPosition($user_id)
	{
//		$sql = "INSERT INTO t_user_team(user_id, team_id, position, user_monster_id, leader_flg, date_created)"
//			 . " VALUES(?, ?, ?, ?, ?, NOW())";

//		for ($team_id = 0; $team_id < 5; $team_id++) {//TODO 5が直値だが…
//			for ($position = 1; $position <= 5; $position++) {
//				$user_monster_id = ($position == self::INITIAL_HELPER_POSITION) ? self::USER_MONSTER_ID_HELPER
//																				: self::USER_MONSTER_ID_EMPTY;
//				$leader_flg = ($position == self::INITIAL_LEADER_POSITION) ? 1 : 0;
//				$param = array($user_id, $team_id, $position, $user_monster_id, $leader_flg);
			
//				if (!$this->db->execute($sql, $param)) {
//					return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
//							$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
//				}
//			}
//		}

		$sql = "INSERT INTO t_user_team(user_id, team_id, position, user_monster_id, leader_flg, date_created)"
				. " VALUES";

		$params = array();
		$values_queries = array();

		for ($team_id = 0; $team_id < self::MAX_TEAM_NUM; $team_id++) {
			for ($position = 1; $position <= self::MAX_POSITION; $position++) {
				$user_monster_id = ($position == self::INITIAL_HELPER_POSITION) ? self::USER_MONSTER_ID_HELPER
				: self::USER_MONSTER_ID_EMPTY;
				$leader_flg = ($position == self::INITIAL_LEADER_POSITION) ? 1 : 0;

				$param = array($user_id, $team_id, $position, $user_monster_id, $leader_flg);

				$params = array_merge($params, $param);
				$values_queries[] = '(?, ?, ?, ?, ?, NOW())';

			}
		}

		$query = 'INSERT INTO t_user_team(user_id, team_id, position, user_monster_id, leader_flg, date_created)'
				. ' VALUES' . implode(',', $values_queries);

		if (!$this->db->execute($query, $params)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return true;
	}
	/**
	 * ユーザチーム情報一覧を取得する
	 * 
	 * @param int $user_id ユーザID
	 * @return array ユーザチーム情報の連想配列の配列
	 */
	function getUserTeamList($user_id)
	{
		static $list = null;
		if ($list === null) {
			$param = array($user_id);
			$sql = "SELECT team_id, position, user_monster_id, leader_flg"
				 . " FROM t_user_team WHERE user_id = ?";
		
			$list = $this->db->GetAll($sql, $param);
		}
		
		return $list;
	}
	
	/**
	 * API応答用のユーザチーム情報一覧を取得する
	 */
	function getUserTeamListForApiResponse($user_id)
	{
		$list = $this->getUserTeamList($user_id);
		
		$tmp_assoc = array();
		foreach ($list as $row) {
			$team_id = $row['team_id'];
			$position = $row['position'];
			
			if (!isset($tmp_assoc[$team_id])) {
				$tmp_assoc[$team_id] = array(
					'team_id' => $team_id,
				);
			}
			
			$tmp_assoc[$team_id]["pos$position"] = $row['user_monster_id'];
			
			if ($row['leader_flg'] == 1) {
//				$tmp_assoc[$team_id]['leader'] = $position;
				$tmp_assoc[$team_id]['leader'] = $position - 1;
			}
		}
		
		$compact = array();
		foreach ($tmp_assoc as $tmp) {
			$compact[] = $tmp;
		}
		
		return $compact;
	}

	/**
	 * ユーザチームコストを取得する
	 * 
	 * @param int $user_id ユーザID
	 * @param int $team_id チームID
	 * @return array ユーザチームの連想配列の配列
	 */
	function getUserTeamCost($user_id, $team_id)
	{
		static $list = null;
		if ($list === null) {
			$param = array($user_id, $team_id);
			$sql = "SELECT SUM(mm.cost) AS cost"
				 . " FROM t_user_team tut, t_user_monster tum, m_monster mm"
				 . " WHERE tut.user_id = ? AND tut.team_id = ? AND tum.user_monster_id = tut.user_monster_id AND tum.monster_id = mm.monster_id";
			$ret = $this->db->GetOne($sql, $param);
		}
		
		return $ret;
	}

	/**
	 * ユーザチームを取得する
	 * 
	 * @param int $user_id ユーザID
	 * @param int $team_id チームID
	 * @return array ユーザチームの連想配列の配列
	 */
	function getUserTeam($user_id, $team_id)
	{
		static $list = null;
		if ($list === null) {
			$param = array($user_id, $team_id);
            $sql = "SELECT "
                 . " tum.user_monster_id,"
                 . " tum.monster_id,"
                 . " mm.name_ja as monster_name,"
                 . " mm.m_rare as rare,"
                 . " tum.exp,"
                 . " tum.lv,"
                 . " mm.hp as hp,"
                 . " mm.max_hp as max_hp,"
                 . " mm.attack as attack,"
                 . " mm.max_attack as max_attack,"
                 . " mm.max_lv as max_lv,"
                 . " tum.hp_plus,"
                 . " tum.attack_plus,"
                 . " tum.heal_plus,"
                 . " tum.skill_lv,"
                 . " tut.leader_flg,"
                 . " tut.position"
				 . " FROM t_user_team tut, t_user_monster tum, m_monster mm"
				 . " WHERE tut.user_id = ? AND tut.team_id = ? AND tum.user_monster_id = tut.user_monster_id AND tum.monster_id = mm.monster_id";
			$ret = $this->db->GetAll($sql, $param);
		}
		
		return $ret;
	}
	
	/**
	 * ユーザチーム情報をセットする
	 * 
	 * この関数は、ユーザID生成時にt_user_teamのデータも初期化されている前提で動作するので、
	 * 動作はUPDATEのみ。
	 * @param int $user_id
	 * @param int $team_id
	 * @param int $position
	 * @param int $user_monster_id
	 * @param int $leader_flg
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserTeam($user_id, $team_id, $position, $user_monster_id, $leader_flg = null)
	{
		$monster_m = $this->backend->getManager('Monster');
		if (($user_monster_id != self::USER_MONSTER_ID_HELPER) &&
		    ($user_monster_id != self::USER_MONSTER_ID_EMPTY) &&
		    !$monster_m->getUserMonsterEx($user_id, $user_monster_id)
		) {
			return Ethna::raiseError("Monster does not exists or owned another user. user_id[%d] user_monster_id[%s]", E_USER_ERROR, 
				$user_id, $user_monster_id
			);
		}
		
		$param = array($user_monster_id);
		$sql = "UPDATE t_user_team SET user_monster_id = ?";
		
		if ($leader_flg !== null) {
			$param[] = $leader_flg;
			$sql .= ", leader_flg = ?";
		}
		
		$param[] = $user_id;
		$param[] = $team_id;
		$param[] = $position;
		$sql .= " WHERE user_id = ? AND team_id = ? AND position = ?";
		
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		return true;
	}

	/**
	 * ユーザチーム情報一覧をセットする
	 * 
	 * @param int $user_id
	 * @param array $team_list ユーザチーム情報の連想配列の配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserTeamList($user_id, $team_list)
	{
		//TODO 引数チェック

		foreach ($team_list as $team) {
			$leader_position = null;
			for ($position = 1; $position <= 5; $position++) {
				if (isset($team['leader']) && (($team['leader'] + 1) == $position)) {
					$leader_position = $position;
				}
			}
			
			for ($position = 1; $position <= 5; $position++) {
				if (!isset($team["pos$position"])) {
					continue;
				}

				$user_monster_id = $team["pos$position"];

//				if (isset($team['leader']) && (($team['leader'] + 1) == $position)) {
				if ($leader_position === null) {
					$leader_flg = null;
				} else if ($leader_position == $position) {
					$leader_flg = 1;
				} else {
					$leader_flg = 0;
				}

				$ret = $this->setUserTeam($user_id, $team['team_id'], $position, $user_monster_id, $leader_flg);
				if (!$ret || Ethna::isError($ret)) {
					return $ret;
				}
			}
		}

		return true;
	}
	
	/**
	 * 初期チームマスタ情報一覧を取得する
	 * 
	 * @param int $types 御三家タイプ
	 * @return array 初期チームマスタ情報一覧
	 */
	function getMasterInitialTeamList($type)
	{
		$param = array($type);
		$sql = "SELECT * FROM m_initial_team WHERE type = ?";

		return $this->db_r->db->GetAll($sql, $param);
	}

    /**
	 * 対象モンスターを各チームのリーダーへセット
	 *
	 * この関数は、ユーザID生成時にt_user_teamのデータも初期化されている前提で動作するので、
	 * 動作はUPDATEのみ。
	 * @param int $user_id
	 * @param int $position
	 * @param int $user_monster_id
	 * @param int $min
	 * @param int $max
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserTeamLeader($user_id, $position, $user_monster_id, $min, $max)
	{
		$monster_m = $this->backend->getManager('Monster');
		if (($user_monster_id != self::USER_MONSTER_ID_HELPER)
		 && ($user_monster_id != self::USER_MONSTER_ID_EMPTY)
		 && !$monster_m->getUserMonsterEx($user_id, $user_monster_id)
		) {
			return Ethna::raiseError("Monster does not exists or owned another user. user_id[%d] user_monster_id[%s]", E_USER_ERROR,
					$user_id, $user_monster_id
			);
		}

		$param = array($user_monster_id, $user_id, $min, $max, $position);
		$sql = "UPDATE t_user_team SET user_monster_id = ? WHERE user_id = ? AND (team_id BETWEEN ? AND ?) AND position = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 5) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

    /**
	 * 指定のユーザーIDのアクティブチームの情報を取得する
	 *
	 * @param int $user_ids	取得するユーザーIDの配列
	 *
	 * @return array 指定ユーザーのアクティブチームの連想配列の配列
	 */
	function getUsersActiveTeamList( $user_ids )
	{
		if( empty( $user_ids ) === true )
		{
			return null;
		}
		$temp = array();
		$buff = array();
		for( $i = 0; $i < count( $user_ids ); $i++ )
		{
			$buff[$user_ids[$i]] = array();
			$temp[] = '?';
		}
		$s = implode( ',', $temp );
		$sql = "SELECT ut.user_id, ut.position, ut.leader_flg, um.monster_id, um.user_monster_id, "
			 . "um.exp, um.lv, um.skill_lv, um.badge_num, um.badges "
			 . "FROM t_user_base as ub, t_user_team as ut, t_user_monster as um "
			 . "WHERE ub.user_id = ut.user_id AND ub.active_team_id = ut.team_id "
			 . "AND ut.user_monster_id = um.user_monster_id AND ub.user_id IN ( $s )";
		$ret = $this->db->GetAll( $sql, $user_ids );
		if( empty( $ret ) === true )
		{	// 取得エラー
			return null;
		}

		// ユーザーID単位でまとめる
		foreach( $ret as $row )
		{
			$buff[$row['user_id']][] = $row;
		}
		return $buff;
	}

    /**
	 * 指定のユーザーIDのアクティブチームのフレンドポジション情報を取得する
	 *
	 * @param int $user_ids	取得するユーザーIDの配列
	 *
	 * @return array 指定ユーザーのアクティブチームの連想配列の配列
	 */
	function getUsersActiveTeamFriendPos( $user_ids )
	{
		if( empty( $user_ids ) === true )
		{
			return null;
		}
		$temp = array();
		$buff = array();
		for( $i = 0; $i < count( $user_ids ); $i++ )
		{
			$buff[$user_ids[$i]] = array();
			$temp[] = '?';
		}
		$s = implode( ',', $temp );
		$sql = "SELECT ut.user_id, ut.position "
			 . "FROM t_user_base AS ub, t_user_team AS ut "
			 . "WHERE ub.user_id = ut.user_id AND ub.active_team_id = ut.team_id "
			 . "AND ut.user_monster_id = -2 AND ub.user_id IN ( $s )";
		$ret = $this->db->GetAll( $sql, $user_ids );
		if( empty( $ret ) === true )
		{	// 取得エラー
			return null;
		}
		foreach( $ret as $row )
		{
			$buff[$row['user_id']] = $row;
		}
		return $buff;
	}
}
