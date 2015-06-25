<?php
/**
 *  Pp_RaidUserManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_RaidManager.php';

/**
 *  Pp_RaidUserManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RaidUserManager extends Pp_RaidManager
{
	// クリアログステータス
	const STATUS_MASTER       = 1;	// 出撃時パーティマスター
	const STATUS_PROXY_MASTER = 2;	// 途中からパーティマスター
	const STATUS_MEMBER       = 3;	// 通常メンバー

	/**
	 * ユーザー別ダンジョンクリアログの記録
	 * 
	 * @param int $user_id ユーザーID
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 * @param int $status ステータス（1:出撃時パーティマスター, 2:途中パーティマスター, 3:通常メンバー）
	 * @param int $mvp MVPか？(0:ちがうよ, 1:MVPだよ)
	 *
	 */
	function logUserDungeon( $user_id, $dungeon_id, $difficulty, $dungeon_lv, $status, $mvp )
	{
		$param = array( $user_id, $dungeon_id, $difficulty, $dungeon_lv, $status, $mvp );
		$sql = "INSERT INTO log_raid_user_dungeon( user_id, dungeon_id, difficulty, dungeon_lv, status, mvp, date_created )"
			 . "VALUES( ?, ?, ?, ?, ?, ?, NOW())";
		if( !$this->db_unit1->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 * ユーザー別ダンジョンクリアログを取得
	 * 
	 * @param int $user_id ユーザーID
	 * @param int $dungeon_id ダンジョンIDの配列（NULLor空配列のユーザーの全クリア情報を取得）
	 *
	 */
	function getUserDungeon( $user_id, $dungeon_ids = null )
	{
		$param = array( $user_id );
		$sql = "SELECT * FROM log_raid_user_dungeon WHERE user_id = ?";
		if( empty( $dungeon_ids ) === false )
		{
			$where_in = array();
			foreach( $dungeon_ids as $dungeon_id )
			{
				$where_in[] = '?';
				$param[] = $dungeon_id;
			}
			$sql .= " AND dungeon_id IN (".implode( ',',$where_in ).")";
		}
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * 直近○件のユーザー別ダンジョンクリアログを取得
	 * 
	 * @param int $user_id ユーザーID
	 * @param int $num 取得件数
	 *
	 */
	function getUserDungeonLast( $user_id, $num )
	{
		$param = array( $user_id, $num );
		$sql = "SELECT * FROM log_raid_user_dungeon WHERE user_id = ? "
			 . "ORDER BY id DESC LIMIT 0, ?";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * 直近○件のユーザー別ダンジョンクリアログを取得
	 * 
	 * @param array $user_ids ユーザーIDの配列
	 * @param int $num 取得件数
	 *
	 */
	function getUserDungeonLastUsers( $user_ids, $num )
	{
		$param = array();
		$where_user_id_in = array();
		foreach( $user_ids as $u )
		{
			$param[] = $u;
			$where_user_id_in[] = '?';
		}
		$param[] = $num;
		$sql = "SELECT * FROM log_raid_user_dungeon WHERE user_id IN (".implode( ',', $where_user_id_in ).") "
			 . "ORDER BY id DESC LIMIT 0, ?";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * ユーザー別ダンジョンクリア情報ログの記録
	 * 
	 * @param int $user_id ユーザーID
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 *
	 */
	function logUserDungeonClear( $user_id, $dungeon_id, $difficulty, $dungeon_lv )
	{
		// UPDATE実行
		$param = array( $dungeon_lv, $user_id, $dungeon_id, $difficulty );
		$sql = "UPDATE log_raid_user_dungeon_clear SET dungeon_lv = ?, date_created = NOW() WHERE user_id = ? AND dungeon_id = ? AND difficulty = ?";
		if( !$this->db_unit1->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db_unit1->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		if ($affected_rows == 0) {
			// INSERT実行
			$param = array( $user_id, $dungeon_id, $difficulty, $dungeon_lv );
			$sql = "INSERT INTO log_raid_user_dungeon_clear( user_id, dungeon_id, difficulty, dungeon_lv, date_created )"
				 . "VALUES( ?, ?, ?, ?, NOW())";
			if( !$this->db_unit1->execute( $sql, $param ))
			{
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}
		return true;
	}

	/**
	 * ユーザー別ダンジョンクリアログ情報を取得
	 * 
	 * @param int $user_id ユーザーID
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 *
	 */
	function getUserDungeonClear( $user_id, $dungeon_id, $difficulty )
	{
		$param = array( $user_id, $dungeon_id, $difficulty );
		$sql = "SELECT * FROM log_raid_user_dungeon_clear WHERE user_id = ? AND dungeon_id = ? AND difficulty = ?";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * ユーザー累計情報に値を加算
	 * 
	 * @param int user_id
	 * @param array (加算カラム名 => 加算値)の連想配列
	 *
	 */
	function sumUserTotal( $user_id, $columns )
	{
		// レコードの存在チェック（なければ自動で追加）
		$ret = $this->getUserTotal( $user_id );
		if( !$ret || Ethna::isError($ret))
		{	// 取得エラー
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}

		unset( $columns['user_id'] );	// 念のため設定されているといかんので無効にしておく

		$temp = array();
		$param = array();
		foreach( $columns as $k => $v )
		{
			$temp[] = "$k = $k + ?";
			$param[] = $v;
		}
		$param[] = $user_id;
		$sql = "UPDATE t_raid_user_total SET ".implode(',', $temp)." WHERE user_id = ?";
		if( !$this->db_unit1->execute( $sql, $param ))
		{	// 更新エラー
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 * ユーザー累計情報を取得
	 * 
	 * @param int user_id
	 *
	 */
	function getUserTotal( $user_id, $from_master = false )
	{
		$param = array( $user_id );
		$sql = "SELECT * FROM t_raid_user_total WHERE user_id = ?";
		if( $from_master === false )
		{
			$row = $this->db_unit1_r->GetRow( $sql, $param );
		}
		else
		{
			$row = $this->db_unit1->GetRow( $sql, $param );
		}
		if( is_array( $row ) === false )
		{	// エラー
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		else if( count( $row ) === 0 )
		{	// レコードがなければ新規にレコードを追加
			$ret = $this->addUserTotal( $user_id );
			if(!$ret || Ethna::isError($ret))
			{	// レコードの追加に失敗
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
			}
			else
			{	// 追加できたなら取得できるはずなのでもう一度取得してみる
				$row = $this->getUserTotal( $user_id, true );
			}
		}
		return $row;
	}

	/**
	 * ユーザー累計情報のレコードを追加
	 * 
	 * @param int user_id ユーザーID
	 * @param int sally 出撃回数初期値
	 * @param int win 勝利回数初期値
	 * @param int ranking_base_pt 獲得累計ベースランキングポイント初期値
	 * @param int ranking_dmg_pt 獲得累計ダメージランキングポイント初期値
	 *
	 */
	function addUserTotal( $user_id, $sally = 0, $win = 0, $ranking_base_pt = 0, $ranking_dmg_pt = 0 )
	{
		$param = array( $user_id, $sally, $win, $ranking_base_pt, $ranking_dmg_pt );
		$sql = "INSERT INTO t_raid_user_total( user_id, sally, win, ranking_base_pt, ranking_dmg_pt ) "
			 . "VALUES( ?, ?, ?, ?, ? )";
		if( !$this->db_unit1->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
}
?>
