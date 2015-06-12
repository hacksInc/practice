<?php
/**
 *  Pp_PeriodlogManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  期間別ログマネージャ
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PeriodlogManager extends Ethna_AppManager
{
	// AdminManagerにも同様の期間種別のConstがある……。定義する場所を失敗したかも。
	/** 期間種別：1時間単位 */
	const PERIOD_TYPE_HOURLY  = 1;
	
	/** 期間種別：1日単位 */
	const PERIOD_TYPE_DAILY   = 2;

	/** 期間種別：1ヶ月単位 */
	const PERIOD_TYPE_MONTHLY = 3;

	/** 期間種別：週単位（週は月曜日始まり～日曜日終わり） */
	const PERIOD_TYPE_WEEKLY = 4;

	/** 行動種別：アクティブユーザー扱いとなる行動 */
	const ACTION_TYPE_ACTIVE      = 1;
	
	/** 行動種別：魔法のメダル使用（メダル数で累計） */
	const ACTION_TYPE_PAYMENT_USE = 2;
	
	/** 行動種別：魔法のメダル使用（回数で累計） */
	const ACTION_TYPE_PAYMENT_USE_NUM = 4;
	
	/** 行動種別：使用合成素材数が蓄積する行動 */
	const ACTION_TYPE_SYNTHESIS_MATERIAL_NUM = 3;

	/**
	 * ログバッファ
	 * @var array $log_buffer = array(array(関数名, 引数の配列),   array(関数名, 引数の配列), ...)
	 */
	protected $log_buffer = array();
	
	/**
	 * DB接続(pp-ini.phpの'dsn_log'で定義したDB)
	 */
	protected $db_log = null;
	
	/**
	 * 期間別ユニークユーザーログを連続して記録する
	 * 
	 * 最終的な記録先へは出力せず、バッファリングするのみ
	 * @param int $user_id
	 * @param int $ua  User-Agent種別(1:iphone,2:android)（nullを渡されると自動取得）
	 * @param int $action_type
	 * @param array $period_type_array 期間種別の配列
	 * @param bool $break_flg 影響した行数が0の期間種別があった場合に処理を中断するか
	 * @return int|Ethna_Error 成功時：影響した行数  失敗時：Ethna_Error
	 */
	function logPeriodUserUniqueMultiPeriods($user_id, $ua, $action_type, $period_type_array, $break_flg = true)
	{
		$this->log_buffer[] = array(
			'flushPeriodUserUniqueMultiPeriods',
			func_get_args(),
		);
	}
	
	/**
	 * 期間別ユニークユーザーログを記録する
	 * 
	 * 最終的な記録先へは出力せず、バッファリングするのみ
	 * @param int $user_id
	 * @param int $ua  User-Agent種別(1:iphone,2:android)（nullを渡されると自動取得）
	 * @param int $action_type
	 * @param int $period_type
	 * @return int|Ethna_Error 成功時：影響した行数(0 or 1)  失敗時：Ethna_Error
	 */
	function logPeriodUserUnique($user_id, $ua, $action_type, $period_type)
	{
		$this->log_buffer[] = array(
			'flushPeriodUserUnique',
			func_get_args(),
		);
	}
	
	/**
	 * 期間別累積ユーザーログを記録する
	 * 
	 * 最終的な記録先へは出力せず、バッファリングするのみ
	 * @param int $user_id
	 * @param int $ua  User-Agent種別(1:iphone,2:android)（nullを渡されると自動取得）
	 * @param int $action_type
	 * @param int $period_type
	 * @param int $num_delta 件数の増分（省略すると1）
	 * @return int|Ethna_Error 成功時：影響した行数(取る得る値は1のみ)  失敗時：Ethna_Error
	 */
	function logPeriodUserAccumu($user_id, $ua, $action_type, $period_type, $num_delta = 1)
	{
		$this->log_buffer[] = array(
			'flushPeriodUserAccumu',
			func_get_args(),
		);
	}

	/**
	 * フラッシュ
	 * 
	 * バッファリングされたログをまとめて出力する。
	 * Pp_Plugin_Filter_TrackingのpostFilterから呼ぶようにしてあるので、
	 * 各アクションやマネージャから個別に呼ぶ必要は無い。
	 */
	function flush()
	{
		while ($buf = array_shift($this->log_buffer)) {
			call_user_func_array(array($this, $buf[0]), $buf[1]);
		}
	}
	
	/**
	 * 期間別ユニークユーザーログを連続してDB出力する
	 * 
	 * 引数はlogPeriodUserUniqueMultiPeriods関数と同じ
	 */
	protected function flushPeriodUserUniqueMultiPeriods($user_id, $ua, $action_type, $period_type_array, $break_flg = true)
	{
		$ret_total = 0;
		foreach ($period_type_array as $period_type) {
			$ret = $this->flushPeriodUserUnique($user_id, $ua, $action_type, $period_type);
			if (Ethna::isError($ret)) {
				return $ret;
			}

			if (($ret === 0) && $break_flg) {
				return $ret_total;
			}
			
			$ret_total += $ret;
		}
		
		return $ret_total;
	}
	
	/**
	 * 期間別ユニークユーザーログをDB出力する
	 * 
	 * 引数はlogPeriodUserUnique関数と同じ
	 */
	protected function flushPeriodUserUnique($user_id, $ua, $action_type, $period_type)
	{
		if (!$this->db_log) {
			$this->db_log =& $this->backend->getDB('log');
		}

		$date_start = $this->getDateStart($period_type);
		
		$param = array($date_start, $period_type, $action_type, $user_id);
		$sql = <<<EOD
SELECT date_start
FROM log_period_user_unique
WHERE date_start = ?
AND period_type = ?
AND action_type = ?
AND user_id = ?
EOD;
		$row = $this->db_log->GetRow($sql, $param);
		if (Ethna::isError($row)) {
			return Ethna::raiseError('flushPeriodUserUnique failed. ' . var_export(array($user_id, $ua, $action_type, $period_type), true), E_USER_ERROR);
		}
		
		if ($row) {
			// OK
			return 0;
		}
		
		if ($ua === null) {
			$base = $this->backend->getManager('User')->getUserBase($user_id);
			if ($base && is_array($base)) {
				$ua = $base['ua'];
			}
		}
		
		$param = array($date_start, $period_type, $action_type, $user_id, $ua);
		$sql = <<<EOD
INSERT INTO log_period_user_unique(date_start, period_type, action_type, user_id, ua)
VALUES (?, ?, ?, ?, ?)
EOD;
		if (!$this->db_log->execute($sql, $param)) {
			return Ethna::raiseError('flushPeriodUserUnique failed. ' . var_export(array($user_id, $ua, $action_type, $period_type), true), E_USER_ERROR);
		}

		$this->backend->logger->log(LOG_DEBUG, 
			'flushPeriodUserUnique executed. ' . var_export(array($user_id, $ua, $action_type, $period_type), true)
		);
		
		return 1;
	}

	/**
	 * 期間別累積ユーザーログをDB出力する
	 * 
	 * 引数はlogPeriodUserAccumu関数と同じ
	 */
	protected function flushPeriodUserAccumu($user_id, $ua, $action_type, $period_type, $num_delta = 1)
	{
		if (!$this->db_log) {
			$this->db_log =& $this->backend->getDB('log');
		}

		$date_start = $this->getDateStart($period_type);

		$param = array($num_delta, $date_start, $period_type, $action_type, $user_id);
		$sql = <<<EOD
UPDATE log_period_user_accumu
SET num = num + ?
WHERE date_start = ?
AND period_type = ?
AND action_type = ?
AND user_id = ?
EOD;
		if (!$this->db_log->execute($sql, $param)) {
			return Ethna::raiseError('flushPeriodUserAccumu failed. ' . var_export(array($user_id, $ua, $action_type, $period_type, $num_delta), true), E_USER_ERROR);
		}
		
		$affected_rows = $this->db_log->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError('flushPeriodUserAccumu failed. ' . var_export(array($user_id, $ua, $action_type, $period_type, $num_delta), true), E_USER_ERROR);
		}

		if ($affected_rows == 0) {
			if ($ua === null) {
				$base = $this->backend->getManager('User')->getUserBase($user_id);
				if ($base && is_array($base)) {
					$ua = $base['ua'];
				}
			}
		
			// INSERT実行
			$param = array($date_start, $period_type, $action_type, $user_id, $ua, $num_delta);
			$sql = <<<EOD
INSERT INTO log_period_user_accumu(date_start, period_type, action_type, user_id, ua, num)
VALUES (?, ?, ?, ?, ?, ?)
EOD;
			if (!$this->db_log->execute($sql, $param)) {
				return Ethna::raiseError('flushPeriodUserAccumu failed. ' . var_export(array($user_id, $ua, $action_type, $period_type, $num_delta), true), E_USER_ERROR);
			}
		}

		$this->backend->logger->log(LOG_DEBUG, 
			'flushPeriodUserAccumu executed. ' . var_export(array($user_id, $ua, $action_type, $period_type, $num_delta), true)
		);
		
		return 1;
	}
	
	/**
	 * 期間開始日時を取得する
	 * 
	 * @param int $period_type 期間種別
	 * @param int $request_time 期間内のUNIXタイムスタンプ（省略すると現在）
	 * @return string 期間開始日時(Y-m-d H:i:s)
	 */
	function getDateStart($period_type, $request_time = null)
	{
		if ($request_time === null) {
			$request_time = $_SERVER['REQUEST_TIME'];
		}

		$format = array(
			self::PERIOD_TYPE_HOURLY  => 'Y-m-d H',
			self::PERIOD_TYPE_DAILY   => 'Y-m-d',
			self::PERIOD_TYPE_MONTHLY => 'Y-m',
			self::PERIOD_TYPE_WEEKLY  => 'Y-m-d',
		);
		
		if ($period_type == self::PERIOD_TYPE_WEEKLY) {
			$request_time -= ((date('w', $request_time) + 6) % 7) * 86400; // 直近の月曜日までさかのぼる
		}
		
		$date_start = date($format[$period_type], $request_time);
		$date_start .= substr('0000-00-01 00:00:00', strlen($date_start));
		
		return $date_start;
	}
}
?>