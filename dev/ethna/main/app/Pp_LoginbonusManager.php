<?php
/**
 *  Pp_LoginbonusManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_LoginbonusManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LoginbonusManager extends Ethna_AppManager
{


	/**
	 * 現在の日時を取得する（TODO:将来的にはユーザごとにデバッグ用日付や国の設定を見て調整することになるかも）
	 * 
	 * @param type $user_id
	 * @return date
	 */
	public function getNowDate($user_id)
	{
	//	$nowdate = date( "Y-m-d H:i:s" );
		$nowdate = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		return( $nowdate );
	}

	/**
	 * ユーザログインボーナス情報
	 * @var array $user_loginbonus[ユーザID] = t_user_login_bonusのレコード情報 
	 */
	protected static $userloginbonus_init = array(
		'login_bonus_id'      =>    0,  // ログインボーナスID
		'stamp'               =>    0,  // 押した回数（ログインボーナスIDが変わったらリセット）
		'sheet'               =>    0,  // 押した枚数（ログインボーナスIDが変わったらリセット）
		'date_last_login'     => NULL,  // 最終ログイン日時
	);

	/**
	 * ユーザログインボーナス情報を取得する
	 * 
	 * @param int $user_id
	 * @return array ユーザログインボーナス情報1件の連想配列
	 *         エラー時はfalse
	 */
	function getUserLoginbonus($user_id)
	{
		$param = array($user_id );
		$sql = "SELECT * FROM t_user_login_bonus WHERE user_id = ?";
		$result = $this->db->execute( $sql, $param );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		//レコードが存在しない場合
		if ( $result->RecordCount() == 0 ){
			$this->user_loginbonus[$user_id] = self::$userloginbonus_init;
		} else {
			$this->user_loginbonus[$user_id] = $result->FetchRow();
		}
		
		return $this->user_loginbonus[$user_id];
	}
	
	/**
	 * ユーザログインボーナス情報を保存する
	 * 
	 * @param int $user_id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserLoginbonus($user_id, $columns)
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array_values($columns);
		$param[] = $user_id;
		$sql = "UPDATE t_user_login_bonus SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE user_id = ?";
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
			$param = array($user_id, $columns['login_bonus_id'], $columns['stamp'], $columns['sheet'], $columns['date_last_login']);
			$sql = "INSERT INTO t_user_login_bonus(user_id, login_bonus_id, stamp, sheet, date_last_login, date_created)"
				 . " VALUES(?, ?, ?, ?, ?, NOW())";
			if (!$this->db->execute($sql, $param)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}
		return true;
	}

	/**
	 * ログインボーナスのマスタデータを取得する
	 * 
	 * @param date $date_bonus(YYYY-mm-dd) 取得する日付（この日を含む期間のデータを取得してくる）
	 * @return ログインボーナスマスタ情報1件の連想配列
	 *         該当なければnull
	 */
	function getLoginbonus($date_bonus)
	{
		$lang = $this->config->get('lang');
		$param = array($date_bonus, $date_bonus);
		$sql = "SELECT login_bonus_id, name_$lang AS name, date_start, date_end FROM m_login_bonus WHERE date_start <= ? AND date_end >= ? LIMIT 1";//念のため１件に限定しておく
		$result = $this->db_r->execute( $sql, $param );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
		}
		//レコードが無かったらnullを返す
		if ( $result->RecordCount() == 0 ) {
			return null;
		}
		return $result->FetchRow();
	}

	/**
	 * ログインボーナスのアイテムデータを取得する
	 * 
	 * @param int $login_bonus_id 取得するログインボーナスIDが（このIDのデータを取得してくる）
	 * @return ログインボーナスマスタ情報1件の連想配列
	 *         該当なければnull
	 */
	function getLoginbonusItem($login_bonus_id)
	{
		$param = array($login_bonus_id);
		$sql = "SELECT * FROM m_login_bonus_item WHERE login_bonus_id = ? ORDER BY stamp";

		$result = $this->db_r->execute( $sql, $param );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
		}
		//レコードが無かったらnullを返す
		if ( $result->RecordCount() == 0 ) {
			return null;
		}
		return $result->GetArray();
	}

}
?>
