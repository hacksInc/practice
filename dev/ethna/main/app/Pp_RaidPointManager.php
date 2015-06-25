<?php
/**
 *  Pp_RaidPointManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id:$
 */

require_once 'Pp_RaidManager.php';

/**
 *  Pp_RaidPointManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RaidPointManager extends Pp_RaidManager
{
	/** コンティニュー時レイドポイント消費量 */
	const CONTINUE_USE_POINT = 1;
	
	/**
	 * 保存期間（日）
	 * 
	 * 保存を保証する期間
	 * この日数を過ぎたデータは参照しなくなるので、物理削除しても問題ない
	 */
	const RETENTION_PERIOD = 90;
	
	/**
	 * レイド コンティニュー一時データをセットする
	 * 
	 * @param int $user_id ユーザID
	 * @param string $continue_id コンティニューID
	 * @param string $api_transaction_id トランザクションID
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	function setTmpContinue($user_id, $continue_id, $api_transaction_id)
	{
		// UPDATEを試みる
		$update_param = array($continue_id, $api_transaction_id, $user_id);
		$update_sql = "UPDATE tmp_raid_point_continue"
			        . " SET continue_id = ?, api_transaction_id = ?"
			        . " WHERE user_id = ?";
		if (!$this->db->execute($update_sql, $update_param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		// UPDATEで反映できなかったらINSERT
		if ($affected_rows == 0) {
			$insert_param = array($user_id, $continue_id, $api_transaction_id);
			$insert_sql = "INSERT INTO tmp_raid_point_continue(user_id, continue_id, api_transaction_id, date_created)"
			            . " VALUES(?, ?, ?, NOW())";
			if (!$this->db->execute($insert_sql, $insert_param)) { // INSERT失敗の場合
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}	
		
		return true;
	}
	
	/**
	 * レイド コンティニュー一時データを取得する
	 * 
	 * @param int $user_id ユーザID
	 * @return array tmp_raid_point_continueテーブルの1行に相当する連想配列
	 */
	function getTmpContinue($user_id)
	{
		$date_min = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - self::RETENTION_PERIOD * 86400);

		$param = array($user_id, $date_min);
		$sql = "SELECT * FROM tmp_raid_point_continue"
			 . " WHERE user_id = ? AND date_modified >= ?";
		
		return $this->db_r->getRow($sql, $param);
	}
}
?>