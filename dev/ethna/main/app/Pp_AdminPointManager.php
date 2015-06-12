<?php
/**
 *  Pp_AdminPointManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id: d4af361a99e2aaa95cedee2132d1ca3f10920c6b $
 */

require_once 'Pp_PointManager.php';

/**
 *  Pp_AdminPointManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminPointManager extends Pp_PointManager
{
	/**
	 * ポイント管理トランザクションデータをクリーンアップ処理用にエクスポートする
	 * 
	 * @param string $game_transaction_id ゲームトランザクションID
	 * @param int $user_id ユーザID
	 * @param string $backup_dir バックアップ先ディレクトリ
	 * @param string $cleanup_uniq クリーンアップ処理のユニーク値（ファイル名に使用）
	 * @return boolean|object 成功時:true, 失敗時:falseまたはEthna_Errorオブジェクト
	 */
	function exportPointTransactionForTransactionCleanup($game_transaction_id, $user_id, $backup_dir, $cleanup_uniq)
	{
		$admin_m = $this->backend->getManager('Admin');
		
		$table = 't_point_transaction';
		$datafile = "{$backup_dir}/{$table}_{$cleanup_uniq}.csv";
		$datafile_exists = file_exists($datafile);
		
		// カラム名取得
		static $colnames = null;
		static $colnums = null;
		if (!$colnames) {
			$colnames = $admin_m->getFieldsFromTableDefinition($table);
			$colnums = array_flip($colnames);
		}

		// ファイルオープン
		$fp_data = fopen($datafile, 'a');
		if (!$fp_data) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		// カラム名をファイル出力
		if (!$datafile_exists) {
			fputcsv($fp_data, $colnames);
		}

		// データ取得
		$param = array($game_transaction_id);
		$sql = "SELECT " . implode(',', $colnames)
		     . " FROM t_point_transaction"
		     . " WHERE game_transaction_id = ?";
		
		if ($user_id) {
			$param[] = $user_id;
			$sql .= " AND user_id = ?";
		}
		
		$adodb_fetch_mode_old = $this->db_r->db->SetFetchMode(ADODB_FETCH_NUM);
		
		if ($row = $this->db_r->GetRow($sql, $param)) {
			// ファイル出力
			if (!fputcsv($fp_data, $row)) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
		}
		
		$this->db_r->db->SetFetchMode($adodb_fetch_mode_old);

		fclose($fp_data);
		
		return true;
	}
	
	/**
	 * トランザクションを削除する
	 * 
	 * @param string $game_transaction_id ゲームトランザクションID
	 * @param int $user_id ユーザID
	 * @return boolean|object 成功時:true, 失敗時:falseまたはEthna_Errorオブジェクト
	 */
	function deleteTransaction($game_transaction_id, $user_id = null)
	{
		$param = array($game_transaction_id);
		$sql = "DELETE FROM t_point_transaction"
		     . " WHERE game_transaction_id = ?";
		
		if ($user_id) {
			$param[] = $user_id;
			$sql .= " AND user_id = ?";
		}
		
//error_log('DEBUG:' . basename(__FILE__) . ':' . __LINE__ . ':' . implode(",", $param));
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		return true;
	}
}
?>