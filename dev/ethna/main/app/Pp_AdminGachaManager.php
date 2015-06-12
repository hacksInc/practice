<?php
/**
 *  Pp_AdminGachaManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_GachaManager.php';

/**
 *  Pp_AdminGachaManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminGachaManager extends Pp_GachaManager
{
	/**
	 * レアガチャのガチャリスト管理情報を全件取得する
	 *
	 * @param int $gacha_id ガチャID
	 */
	function getRareGachaAllListInfo()
	{
		$sql = "SELECT * FROM m_gacha_list WHERE type >= 3 ORDER BY gacha_id DESC";

		return $this->db_r->GetAll($sql);
	}

}
?>
