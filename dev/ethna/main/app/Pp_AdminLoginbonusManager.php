<?php
/**
 *  Pp_AdminLoginbonusManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_LoginbonusManager.php';

/**
 *  Pp_AdminLoginbonusManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminLoginbonusManager extends Pp_LoginbonusManager
{
	/**
	 * ログインボーナスのマスタデータを全件取得する
	 * 
	 * @return ログインボーナスマスタ情報の連想配列
	 *         該当なければnull
	 */
	function getLoginbonusAll()
	{
		$lang = $this->config->get('lang');
		$sql = "SELECT login_bonus_id, name_$lang AS name, date_start, date_end, date_created, account_reg, date_modified, account_upd FROM m_login_bonus ORDER BY login_bonus_id";
		$result = $this->db_r->execute( $sql );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
		}
		//レコードが無かったらnullを返す
		if ( $result->RecordCount() == 0 ) {
			return null;
		}
		return $result->GetAll();
	}

	/**
	 * ログインボーナスのマスタデータをID指定で１件取得する
	 * 
	 * @param int $login_bonus_id
	 * @return ログインボーナスマスタ情報1件の連想配列
	 *         該当なければnull
	 */
	function getLoginbonusId($login_bonus_id)
	{
		$lang = $this->config->get('lang');
		$param = array($login_bonus_id);
		$sql = "SELECT login_bonus_id, name_$lang AS name, date_start, date_end, date_created, account_reg, date_modified, account_upd FROM m_login_bonus WHERE login_bonus_id = ?";
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
	 * ログインボーナスのマスタデータを保存する
	 * 
	 * @param array $columns
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setLoginBonus($columns)
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array($columns['date_start'], $columns['date_end'], $columns['name'], $columns['account_upd'], $columns['login_bonus_id']);
		$sql = "UPDATE m_login_bonus SET "
			 . "date_start = ?, date_end = ?, name_ja = ?, account_upd = ?"
			 . " WHERE login_bonus_id = ?";
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
			$param = array($columns['login_bonus_id'], $columns['date_start'], $columns['date_end'], $columns['name'], $columns['account_reg']);
			$sql = "INSERT INTO m_login_bonus("
				 . "login_bonus_id, date_start, date_end, name_ja, account_reg, date_created)"
				 . " VALUES(?, ?, ?, ?, ?, NOW())";
			if (!$this->db->execute($sql, $param)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}
		return true;
	}
	function insertLoginBonus($columns)
	{
		// INSERT実行
		$param = array($columns['login_bonus_id'], $columns['date_start'], $columns['date_end'], $columns['name'], $columns['account_reg']);
		$sql = "INSERT INTO m_login_bonus("
			 . "login_bonus_id, date_start, date_end, name_ja, account_reg, date_created)"
			 . " VALUES(?, ?, ?, ?, ?, NOW())";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
	function updateLoginBonus($columns)
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array($columns['date_start'], $columns['date_end'], $columns['name'], $columns['account_upd'], $columns['login_bonus_id']);
		$sql = "UPDATE m_login_bonus SET "
			 . "date_start = ?, date_end = ?, name_ja = ?, account_upd = ?"
			 . " WHERE login_bonus_id = ?";
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

	/**
	 * ログインボーナスアイテムのマスタデータを保存する
	 * 
	 * @param array $columns
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setLoginBonusItem($columns)
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array_values($columns);
		unset($param['login_bonus_id']);
		unset($param['stamp']);
		$param[] = $columns['login_bonus_id'];
		$param[] = $columns['stamp'];
		$sql = "UPDATE m_login_bonus_item SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE login_bonus_id = ? AND stamp = ?";
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
			$param = array_values($columns);
			$sql = "INSERT INTO m_login_bonus_item(" . implode(",", array_keys($columns))
				 . ", date_created)"
				 . " VALUES(" . str_repeat("?,", count($columns)) . "NOW())";
			if (!$this->db->execute($sql, $param)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}
		return true;
	}
	function insertLoginBonusItem($columns)
	{
		// INSERT実行
		$param = array_values($columns);
		$sql = "INSERT INTO m_login_bonus_item(" . implode(",", array_keys($columns))
			 . ", date_created)"
			 . " VALUES(" . str_repeat("?,", count($columns)) . "NOW())";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
	function updateLoginBonusItem($columns)
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array_values($columns);
		unset($param['login_bonus_id']);
		unset($param['stamp']);
		$param[] = $columns['login_bonus_id'];
		$param[] = $columns['stamp'];
		$sql = "UPDATE m_login_bonus_item SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE login_bonus_id = ? AND stamp = ?";
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

}
?>
