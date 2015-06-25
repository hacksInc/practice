<?php
/**
 *  Pp_TransactionManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_TransactionManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_TransactionManager extends Ethna_AppManager
{
	/**
	 * トランザクション情報の記録
	 *
	 * @param int $pp_id サイコパスID
	 * @param string $api_transaction_id トランザクションID
	 * @param string $json 処理結果JSON文字列
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし) | Ethna_Errorオブジェクト:更新エラー
	 */
	function registTransaction( $pp_id, $api_transaction_id, $json )
	{
		$param = array( $pp_id, $api_transaction_id, $json, $api_transaction_id, $json );
		$sql = "INSERT INTO ut_transaction( pp_id, api_transaction_id, result_json, date_created ) "
			 . "VALUES( ?, ?, ?, NOW()) "
			 . "ON DUPLICATE KEY UPDATE api_transaction_id = ?, result_json = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 更新された行数をチェック
		if( $this->db->db->affected_rows() == 0 )
		{
			return false;
		}

		return true;
	}

	/**
	 * トランザクションIDから処理結果JSON文字列を取得
	 *
	 * @param string $api_transaction_id トランザクションID
	 *
	 * @return string:処理結果JSON文字列 | null 処理エラー
	 */
	function getResultJson( $api_transaction_id )
	{
		$param = array( $api_transaction_id );
		$sql = "SELECT result_json FROM ut_transaction WHERE api_transaction_id = ?";
		return $this->db->GetOne( $sql, $param );
	}
}
?>
