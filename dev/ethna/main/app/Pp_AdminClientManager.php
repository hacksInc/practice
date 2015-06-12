<?php
/**
 *  Pp_AdminClientManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_ClientManager.php';

/**
 *  Pp_AdminClientManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminClientManager extends Pp_ClientManager
{
	/** デバイス情報ソート項目：割合昇順 */
	const DEVICE_INFO_SORT_ITEM_DEVICE_PERCENTAGE_ASC = 1;

	/** デバイス情報ソート項目：割合降順 */
	const DEVICE_INFO_SORT_ITEM_DEVICE_PERCENTAGE_DESC = 2;

	/** デバイス情報ソート項目：台数昇順 */
	const DEVICE_INFO_SORT_ITEM_DEVICE_COUNT_ASC = 3;

	/** デバイス情報ソート項目：台数降順 */
	const DEVICE_INFO_SORT_ITEM_DEVICE_COUNT_DESC = 4;
	
	/**
	 * DB接続(pp-ini.phpの'dsn_cmn'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn = null;
	
	/**
	 * DB接続(pp-ini.phpの'dsn_cmn_r'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn_r = null;
	
	/**
	 * 端末情報KPIデータを作成する
	 * 
	 * @param int $period 期間(ym)
	 * @param int $ua User-Agent種別（Pp_UserManager::OS_～）
	 * @return bool 成否
	 */
	function makeKpiDeviceInfo($period, $ua)
	{
		$admin_m = $this->backend->getManager('Admin');
		
		$admin_m->offSessionQueryCache();
		$admin_m->setSessionSqlBigSelectsOn(array('r'));
		
		$is_ok = true;
		
		// ユーザ毎のデータを取得
		$param = array($period, $ua);
		$sql = "SELECT content FROM t_user_device_info WHERE period = ? AND ua = ?";
		$adodb_countrecs_old = $admin_m->setAdodbCountrecs(false);
		$result =& $this->db_r->query($sql, $param);
		$admin_m->setAdodbCountrecs($adodb_countrecs_old);
		if (Ethna::isError($result)) {
			error_log(sprintf("ERROR: CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", 
					$this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__));
			Ethna::raiseError("ERROR: CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_DB_QUERY, 
					$this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
			return false;
		}
		
		// 数える
		$counter = array(); // $counter[長いキー] = 件数
		while ($row = $result->FetchRow()) {
			$device_info = json_decode($row['content'], true);
			if (!is_array($device_info)) {
				$device_info = array();
			}
			
			// JSON内の各項目をつなげた文字列を$counterのキーとして使う
			$long_key_parts = '';
			foreach (array('deviceModel', 'operatingSystem', 'systemMemorySize') as $key) {
				if (isset($device_info[$key])) {
					$value = $device_info[$key];
				} else {
					$value = 'unknown';
				}
				
				$long_key_parts[] = $value;
			}
			
			$long_key = implode(':', array_map('urlencode', $long_key_parts));
			
			if (!isset($counter[$long_key])) {
				$counter[$long_key] = 0;
			}
			
			$counter[$long_key] += 1;
		}
		
		// 結果を保存する
		if (!$this->db_cmn) {
			$this->db_cmn =& $this->backend->getDB('cmn');
		}
		
		$unit = $this->config->get('unit_id');
		foreach ($counter as $long_key => $device_count) {
			$param = array_map('urldecode', explode(':', $long_key));
			$param[] = $device_count;
			$param[] = $period;
			$param[] = $ua;
			$param[] = $unit;
			$sql = "INSERT INTO kpi_device_info(device_model, operating_system, system_memory_size, device_count, period, ua, unit, date_created)"
			     . " VALUES(?, ?, ?, ?, ?, ?, ?, NOW())";
			
			if (!$this->db_cmn->execute($sql, $param)) {
				error_log(sprintf("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", 
						$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__));
				Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_DB_QUERY, 
						$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
				$is_ok = false;
			}
		}
		
		return $is_ok;
	}
	
	/**
	 * 端末情報KPIデータリストを取得する
	 * 
	 * @param int $period 期間(ym)
	 * @param int $ua User-Agent種別（Pp_UserManager::OS_～） 問わない場合はnull
	 * @param string $order_by ORDER BY対象とするカラム名　'device_count'のみ指定可
	 * @param string $direction ORDER BYする方向 'desc'または'asc'を指定可
	 * @return array 端末情報KPIデータリスト  連想配列（キーはkpi_device_infoのカラム名）の配列
	 */
	function getKpiDeviceInfoList($period, $ua = null, $order_by = 'device_count', $direction = 'desc')
	{
		// 引数チェック
		if (($order_by != 'device_count')) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $order_by);
			return false;
		}
		
		if (($direction != 'asc') && ($direction != 'desc')) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $direction);
			return false;
		}
		
		// DBから取得
		if (!$this->db_cmn_r) {
			$this->db_cmn_r =& $this->backend->getDB('cmn_r');
		}
		
		$param = array($period);
		$sql = "SELECT device_model, operating_system, system_memory_size, SUM(device_count) AS device_count"
		     . " FROM kpi_device_info"
		     . " WHERE period = ?";
		
		if ($ua) {
			$param[] = $ua;
			$sql .= " AND ua = ?";
		}
		
		$sql .= " GROUP BY device_model, operating_system, system_memory_size"
			 . " ORDER BY {$order_by} $direction";

		return $this->db_cmn_r->GetAll($sql, $param);
	}

	/**
	 * 端末情報KPIデータリストを付加情報付きで取得する
	 * 
	 * 引数はgetKpiDeviceInfoList関数と同じ
	 * 戻り値はgetKpiDeviceInfoList関数の戻り値に、"device_percentage"を加えたもの
	 */
	function getKpiDeviceInfoListEx($period, $ua = null, $order_by = 'device_count', $direction = 'desc')
	{
		$list = $this->getKpiDeviceInfoList($period, $ua, $order_by, $direction);
		
		$sum = Util::arrayColumnSum($list, 'device_count');
		
		foreach ($list as $i => $row) {
			$list[$i]['device_percentage'] = sprintf("%.01f", 100 * $row['device_count'] / $sum);
		}
		
		return $list;
	}
}

?>