<?php
/**
 *  Pp_AdminPresentManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_PresentManager.php';

/**
 *  Pp_AdminPresentManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminPresentManager extends Pp_PresentManager
{
	/**
	 * 論理削除保存期間（日）
	 *
	 * 削除済み(STATUS_DELETE)または受取済み(STATUS_RECEIVE)となったプレゼントデータを保存する日数
	 * この日数を過ぎたデータは物理削除する
	 */
	const SOFT_DELETE_RETENTION_PERIOD = 90;

	/**
	 * プレゼント一覧を取得する（ステータス問わず）
	 *
	 * @param int $pp_id
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	function getUserPresentListAnyStatus($pp_id, $offset = 0, $limit = 100)
	{
		$param = array( $pp_id, $offset, $limit );
		$sql = "SELECT p.*, u.name"
			. " FROM ct_user_present p LEFT JOIN ct_user_base u ON p.pp_id_from = u.pp_id"
			. " WHERE p.pp_id = ?"
			. " ORDER BY p.present_id DESC LIMIT ?, ?";

		return $this->db->GetAll($sql, $param);
	}

	/**
	 * プレゼントデータをクリーンアップ処理用にエクスポートする
	 *
	 * @param int $pp_id ユーザーID
	 * @param string $backup_dir バックアップ先ディレクトリ
	 * @param string $cleanup_uniq クリーンアップ処理のユニーク値（ファイル名に使用）
	 * @return boolean
	 */
	function exportUserPresentForTransactionCleanup($pp_id, $backup_dir, $cleanup_uniq)
	{
		$expire_time = $_SERVER['REQUEST_TIME'] - self::SOFT_DELETE_RETENTION_PERIOD * 86400;

		$admin_m = $this->backend->getManager('Admin');

		$table = 'ct_user_present';
		$datafile = "{$backup_dir}/{$table}_{$cleanup_uniq}.csv";
		$datafile_exists = file_exists($datafile);
		$tmpfile = BASE . "/tmp/transaction_cleanup_{$table}_{$cleanup_uniq}";

		// カラム名取得
		static $colnames = null;
		static $colnums = null;
		if (!$colnames) {
			//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . $table);
			$colnames = $admin_m->getFieldsFromTableDefinition($table);
			$colnums = array_flip($colnames);
		}
		//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($colnames, true));
		//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($colnums, true));

		// ファイルオープン
		$fp_tmp = fopen($tmpfile, 'w');
		if (!$fp_tmp) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

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
		$param = array(
			$pp_id,
			self::STATUS_RECEIVE,
			self::STATUS_DELETE,
			date('Y-m-d H:i:s', $expire_time),
		);
		$sql = "SELECT " . implode(',', $colnames)
			. " FROM ct_user_present"
			. " WHERE pp_id = ?"
			. " AND status IN(?, ?)"
			. " AND date_modified < ?";

		$adodb_fetch_mode_old = $this->db_r->db->SetFetchMode(ADODB_FETCH_NUM);
		$adodb_countrecs_old = $admin_m->setAdodbCountrecs(false);

		$result =& $this->db_r->query($sql, $param);
		while ($row = $result->FetchRow()) {
			if ($row[$colnums['present_mng_id']] != -1) {
				// present_mng_idが存在する（-1でない）場合は、お詫びや補填などで配布されたプレゼント
				// 当面、present_mng_idが存在する場合は、削除対象としない
				// TODO: データ量が増えすぎた場合は、t_present_distributionを参照して、指定期間終了していたら削除対象とするように改修すること
				continue;
			}

			// ファイル出力
			$line = $row[$colnums['present_id']] . ',' . $row[$colnums['pp_id']] . ',' . $row[$colnums['game_transaction_id']] . PHP_EOL;
			if (!fwrite($fp_tmp, $line)) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}

			if (!fputcsv($fp_data, $row)) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}
		}

		$admin_m->setAdodbCountrecs($adodb_countrecs_old);
		$this->db_r->db->SetFetchMode($adodb_fetch_mode_old);

		fclose($fp_tmp);
		fclose($fp_data);

		return $tmpfile;
	}
}

