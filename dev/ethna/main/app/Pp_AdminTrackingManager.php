<?php
/**
 *  Pp_TrackingManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_TrackingManager.php';

/**
 *  ユーザトラッキングマネージャ（管理画面用）
 * 
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminTrackingManager extends Pp_TrackingManager
{
	/**
	 * DB接続(pp-ini.phpの'dsn_log_r'で定義したDB)
	 */
	protected $db_log_r = null;
	
	function getTrackingLogListFromUserId($user_id, $offset = 0, $limit = 100)
	{
		if (!$this->db_log_r) {
			$this->db_log_r =& $this->backend->getDB('log_r');
		}

		$param = array($user_id, $offset, $limit);
		$sql = <<<EOD
SELECT *
FROM log_tracking
WHERE user_id = ?
ORDER BY id DESC LIMIT ?, ?
EOD;
		
		return $this->db_log_r->GetAll($sql, $param);
	}
}
?>