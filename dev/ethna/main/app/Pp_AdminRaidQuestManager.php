<?php
/**
 *  Pp_AdminRaidQuestManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id: d4af361a99e2aaa95cedee2132d1ca3f10920c6b $
 */

/**
 *  Pp_AdminRaidQuestManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminRaidQuestManager extends Ethna_AppManager
{
	/**
	 * 最大のレイドクエストデータログの管理IDを取得する
	 * 
	 * @param boolean $from_master マスターDBから取得するか（true:マスターから, false:スレーブから）
	 * @return int 管理ID
	 */
	function getMaxLogRaidQuestId($from_master = false)
	{
		$db = ($from_master) ? $this->db : $this->db_r;
		$sql = "SELECT MAX(id) FROM log_raid_quest";
		
		return $db->GetOne($sql);
	}
}
?>
