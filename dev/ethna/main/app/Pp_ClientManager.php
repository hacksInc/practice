<?php
/**
 *  Pp_ClientManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_ClientManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_ClientManager extends Ethna_AppManager
{
	protected $db_m_r = null;
	
	/**
	 * 最新のアプリバージョンを取得する
	 */
	function getLatestAppVer($date)
	{
		$row = $this->getLatestRow($date);
		if (is_array($row) && array_key_exists('app_ver', $row)) {
			return array(
				'app_ver'    => $row['app_ver'],
				'date_start' => $row['date_start'],
			);
		} else {
			// ADOdbのGetRowは、結果がないと空文字列が返ってくるようなので、それに準じる
			return array();
		}
	}

	/**
	 * 最新のリソースバージョンを取得する
	 */
	function getLatestResVer($date)
	{
		$row = $this->getLatestRow($date);
		if (is_array($row) && array_key_exists('res_ver', $row)) {
			return array(
				'res_ver'				=> $row['res_ver'],
				'in_game_ver'			=> $row['in_game_ver'],
				'out_game_ver'			=> $row['out_game_ver'],
				'photo_ver'				=> $row['photo_ver'],
				'clear'					=> $row['clear'],
				'date_start'			=> $row['date_start'],
			);
		} else {
			// ADOdbのGetRowは、結果がないと空文字列が返ってくるようなので、それに準じる
			return array();
		}
	}
	
	/**
	 * 最新の行を取得する
	 */
	protected function getLatestRow($date)
	{
		static $cache = array();
	
		if (!array_key_exists($date, $cache)) {
			if ( is_null( $this->db_m_r ) ) {
				$this->db_m_r =& $this->backend->getDB( "m_r" );
			}
			
			$param = array($date);
			$sql = "SELECT *"
				 . " FROM m_client"
				 . " WHERE date_start <= ?"
				 . " ORDER BY date_start DESC"
				 . " LIMIT 1";
			$cache[$date] = $this->db_m_r->GetRow($sql, $param);
		}

		return $cache[$date];
	}
	
	/**
	 * 端末情報を取得する
	 * 
	 * @param int $user_id ユーザID
	 * @param int $period 期間(ym)
	 * @param bool $master マスターDBから取得するか
	 * @return string|null 内容(JSON) データが取得できなかった場合はnull
	 */
	function getUserDeviceInfoContent($user_id, $period = null, $master = true)
	{
		if ($period === null) {
			$period = $this->getUserDeviceInfoPeriod();
		}
		
		$param = array($period, $user_id);
		$sql = "SELECT content"
		     . " FROM t_user_device_info"
		     . " WHERE period = ?"
		     . " AND user_id = ?";
		
		$db_varname = $master ? 'db' : 'db_r';
		
		return $this->$db_varname->GetOne($sql, $param);
	}
	
	/**
	 * 端末情報をセットする
	 * 
	 * @param int $user_id ユーザID
	 * @param int $period 期間(ym)
	 * @param string $content 内容(JSON)
	 * @param int $ua User-Agent種別（Pp_UserManager::OS_～）
	 * @return true|Ethna_Error 成否
	 */
	function setUserDeviceInfoContent($user_id, $period = null, $content, $ua = null)
	{
		if ($period === null) {
			$period = $this->getUserDeviceInfoPeriod();
		}
		
		$current_content = $this->getUserDeviceInfoContent($user_id, $period, false);
		
		if (strlen($current_content) > 0) {
			if (strcmp($current_content, $content) === 0) {
				// OK
				return true;
			}
			
			$param = array($content, $period, $user_id);
			$sql = "UPDATE t_user_device_info SET content = ? WHERE period = ? AND user_id = ?";
		} else {
			if ($ua === null) {
				$user_m = $this->backend->getManager('User');
				$user_base = $user_m->getUserBase($user_id);
				$ua = $user_base['ua'];
			}
			
			$param = array($period, $user_id, $ua, $content);
			$sql = "INSERT INTO t_user_device_info(period, user_id, ua, content, date_created)"
			     . " VALUES(?, ?, ?, ?, NOW())";
		}
		
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return true;
	}

	/**
	 * 端末情報の期間を取得する
	 * 
	 * @param int $time 日時(UNIXタイムスタンプ)
	 * @return int 期間(ym)
	 */
	function getUserDeviceInfoPeriod($time = null)
	{
		if ($time === null) {
			$time = $_SERVER['REQUEST_TIME'];
		}
		
		return intval(date('ym', $time));
	}
}
?>
