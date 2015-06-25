<?php
/**
 *  Pp_SlotManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_SlotManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_SlotManager extends Ethna_AppManager
{
	const SETTING_DEFAULT   = 4;	//通常設定
	const SETTING_MINIMUM   = 1;	//最低設定
	const SETTING_MAXIMUM   = 6;	//最大設定
	
	/**
	 * 期間中の設定値を取得する
	 * 
	 * @param date $date_setting(YYYY-mm-dd) 取得する日付（この日を含む期間のデータを取得してくる）
	 * @return 設定値
	 *         該当なければデフォルト値（SETTING_DEFAULT:4）
	 */
	function getSlotSetting($date_setting)
	{
		$param = array($date_setting, $date_setting);
		$sql = "SELECT * FROM m_slot_setting WHERE date_start <= ? AND date_end >= ? LIMIT 1";//念のため１件に限定しておく

		$result = $this->db_r->execute( $sql, $param );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
		}
		//レコードが無かったらnullを返す
		if ( $result->RecordCount() == 0 ) {
			return self::SETTING_DEFAULT;
		}
		$row = $result->FetchRow();
		$value = $row['slot_level'];
		//念のため上下限値チェック
		if ($value < self::SETTING_MINIMUM) $value = self::SETTING_MINIMUM;
		if ($value > self::SETTING_MAXIMUM) $value = self::SETTING_MAXIMUM;
		return $value;
	}



}
?>
