<?php
/**
 *  Pp_AdminRaidSearchManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id: d4af361a99e2aaa95cedee2132d1ca3f10920c6b $
 */

require_once 'Pp_RaidSearchManager.php';

/**
 *  Pp_AdminRaidSearchManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminRaidSearchManager extends Pp_RaidSearchManager
{
	/** 最後に処理したレイドクエストデータログの管理IDを保存するファイルのパス */
	protected $last_raid_quest_id_file = null;
	
	/**
	 * 一時データカウンタ値書き込みバッファ
	 * 
	 * @var array $counter_w[search_code:clock_type][clock] = counter 
	 */
	protected $counter_w = array(); 
	
	/**
	 * DB接続(pp-ini.phpの'dsn'で定義したDBにadminユーザーで接続)
	 * 
	 * コンストラクタでは生成されないので、明示的に$this->backend->getAdminDBしてから使用すること
	 */
	protected $db_admin = null;
	
	/**
	 *  コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);
		
		$tmp_dir = $this->backend->ctl->getDirectory('tmp');
		$this->last_raid_quest_id_file = $tmp_dir . '/admin_raid_search_last_raid_quest_id';
	}

	/**
	 * 最後に処理したレイドクエストデータログの管理IDを保存するファイルのパスを取得する
	 * 
	 * @return string パス
	 */
	function getLastRaidQuestIdFile()
	{
		return $this->last_raid_quest_id_file;
	}

	/**
	 * レイドクエストデータログに基づいて一時データを更新するバッチ処理
	 * 
	 * @param int $id_min 取得対象とする最小の管理ID（端点含む）
	 * @param int $id_max 取得対象とする最大の管理ID（端点含まない）
	 * @param string $date_from 取得対象とする生成日時の最小値（端点含む）（Y-m-d H:i:s形式）
	 * @param string $date_to 取得対象とする生成日時の最大値（端点含まない）（Y-m-d H:i:s形式）
	 * @return int 処理した最後の管理ID
	 */
	function renewTmpDataAfterQuestInsertOnBatch($id_min, $id_max, $date_from, $date_to)
	{
		$quest_list = $this->getQuestListForSearchTmpData($id_min, $id_max, $date_from, $date_to);
		if (!is_array($quest_list) || empty($quest_list)) {
			return null;
		}

		$last_id = null;
		foreach ($quest_list as $row) {
			$last_id = $row['id'];
			
			if ($row['date_created'] < $date_from) {
				echo "Skipping " . $row['id'] . ".\n";
				continue;
			}
			
			if (!isset($row['search_code'])) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ': search_code is not set. party_id=[' . $row['party_id'] . ']');
				$this->backend->logger->log(LOG_CRIT, 'search_code is not set. party_id=[' . $row['party_id'] . ']');
				continue;
			}

			$columns = array(
				'party_id'     => $row['party_id'],
				'date_created' => $row['date_created'],
				'search_code'  => $row['search_code']
			);
			
			echo "Processing " . $row['id'] . ".\n";
			$ret = $this->renewQuestTmpDataAfterQuestInsert($columns);
			if ($ret !== true) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ': renewTmpDataAfterQuestInsert failed.');		
				$this->backend->logger->log(LOG_CRIT, 'renewTmpDataAfterQuestInsert failed.');
			}
		}
		
		$this->flushSetTmpRaidSearchCounterDiff();
		
		$this->backend->logger->log(LOG_INFO, "last_id[%s]", $last_id);

		return $last_id;
	}
	
	/**
	 * フレンドがいるパーティの検索用一時データを追加する（バッチ使用）
	 * 
	 * @param int $time_from いつ以降（以上）を処理対象とするか（UNIXタイムスタンプ）
	 * @param int $time_to いつまで（未満）を処理対象とするか（UNIXタイムスタンプ）
	 */
	function addPartyFriendTmpDataOnBatch($time_from, $time_to)
	{
		$key = $this->getFriendQueueCacheKey();

	    $cache =& Ethna_CacheManager::getInstance('memcache');
//		$queue = new Pp_MemcachedPseudoQueue($this->config->get('memcache_host'), $this->config->get('memcache_port'));
		$queue = new Pp_MemcachedPseudoQueue($cache->memcache);
		$queue->initializeReceivingCounter($key, array(
			'time_from' => $time_from,
			'time_to'   => $time_to,
		));
		
		while ($data = $queue->receive($key)) {
			$party_id = $data[0];
			$user_id  = $data[1];
			$status   = $data[2];
			$disconn  = $data[3];
			$rclock   = $data[4];
			echo "party_id[{$party_id}] user_id[{$user_id}] status[{$status}] disconn[{$disconn}] rclock[{$rclock}]\n";
			
			// フレンドのユーザーIDごとにキャッシュへセット
			$friend_m = $this->backend->getManager('Friend');
			$friend_list = $friend_m->getFriendList($user_id, 0);

			$is_ok = true;
			if (is_array($friend_list)) foreach ($friend_list as $row) {
				$friend_id = $row['friend_id'];
				echo "friend_id[{$friend_id}]\n";

				$ret = $this->setPartyFriendCache($party_id, $friend_id, $status, $disconn, $rclock);
				if (!$ret) {
					echo "ERROR: party_id[{$party_id}] friend_id[{$friend_id}] status[{$status}] rclock[{$rclock}]\n";
				}
			}
		}
	}
	
	/**
	 * 検索一時データをTRUNCATEする
	 * 
	 * @return boolean 成否
	 */
	function truncateTmpRaidSearchTables()
	{
		if (!$this->db_admin) {
			$this->db_admin =& $this->backend->getAdminDB();
		}
	
		$tables = array_merge(
			array_values($this->DATA_TABLE_NAME),
			array_values($this->COUNTER_TABLE_NAME)
		);

		$is_ok = true;
		foreach ($tables as $table) {
			$sql = "TRUNCATE TABLE $table";
			
			if (!$this->db_admin->execute($sql)) {
				error_log(sprintf("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", 
						$this->db_admin->db->ErrorNo(), $this->db_admin->db->ErrorMsg(), __FILE__, __LINE__));
				Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_DB_QUERY, 
						$this->db_admin->db->ErrorNo(), $this->db_admin->db->ErrorMsg(), __FILE__, __LINE__);
				$is_ok = false;
			}
		}
		
		return $is_ok;
	}
	
	/**
	 * 検索一時データのカウンタテーブルから無効なな古いデータを削除する
	 * 
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $limit 最大削除件数
	 * @param int $now 現在日時（UNIXタイムスタンプ）
	 * @param int $expire_minutes 何分前までのデータを削除するか（端点含まない）
	 * @param boolean $from_master 削除対象をマスターDBから取得するか（true:マスターから, false:スレーブから）
	 * @return mixed 成功の場合：削除した件数, 失敗の場合：false
	 */
	function deleteExpiredTmpRaidSearchCounter($clock_type, $limit = null, $now = null, $expire_minutes = null, $from_master = false)
	{
		if ($limit === null) {
			$limit = 1000;
		}
		
		if ($now === null) {
			$now = $_SERVER['REQUEST_TIME'];
		}
		
		if ($expire_minutes === null) {
			$expire_minutes = Pp_RaidSearchManager::MAX_BACK_MINUTES;
		}
		
		$db = $from_master ? $this->db : $this->db_r;
		
		$table = $this->COUNTER_TABLE_NAME[$clock_type];
		$clock_now = $this->convertTimeToClock($now);
		$clock_min = $clock_now - $expire_minutes;
		$param = array($clock_min, $limit);
		$sql = "SELECT search_code, clock FROM $table WHERE clock < ? LIMIT ?";

		$cnt = 0;
		$is_ok = true;
		$rows = $db->GetAll($sql, $param);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$search_code = $row['search_code'];
				$clock       = $row['clock'];

				// 削除する
				$ret = $this->deleteTmpRaidSearchCounter($search_code, $clock_type, $clock);
				if ($ret === true) {
					$cnt++;
				} else {
					$is_ok = false;
				}
			}
		}
		
		return $is_ok ? $cnt : false;
	}
	
	/**
	 * 検索一時データ生成用にクエストリストを取得する
	 * 
	 * @param int $id_min 取得対象とする最小の管理ID（端点含む）
	 * @param int $id_max 取得対象とする最大の管理ID（端点含まない）
	 * @param string $date_from 取得対象とする生成日時の最小値（端点含む）（Y-m-d H:i:s形式）
	 * @param string $date_to 取得対象とする生成日時の最大値（端点含まない）（Y-m-d H:i:s形式）
	 * @return array  連想配列（キーはid, party_id, date_created, search_code）の配列
	 *                 ただしsearch_code があるのは$date_from以後の行のみ
	 */
	protected function getQuestListForSearchTmpData($id_min, $id_max, $date_from, $date_to)
	{
        $raid_party_m  = $this->backend->getManager('AdminRaidParty');
		
		// 出撃情報を取得する
		$param = array($id_min, $id_max, $date_to);
		$sql = "SELECT id, party_id, date_created FROM log_raid_quest"
		     . " WHERE id >= ? AND id < ? AND date_created < ? ORDER BY id";
		
		$quest_list = $this->db_r->GetAll($sql, $param);
		if (!is_array($quest_list) || empty($quest_list)) {
			return null;
		}

		// パーティ情報の付加が必要なパーティIDを判別する
		$party_id_list = array();
		foreach ($quest_list as $row) {
			if ($row['date_created'] < $date_from) {
				continue;
			}
			
			$party_id_list[] = $row['party_id'];
		}
		
		// パーティ情報を取得する
		$party_assoc = null;
		if (!empty($party_id_list)) {
			$party_assoc = $raid_party_m->getPartyAssoc(
					$party_id_list, array('party_id', 'entry_passwd', 'difficulty', 'play_style', 'force_elimination', 'status', 'dungeon_id'));
		}
		
		if (empty($party_assoc)) {
			$party_assoc = array();
		}
		
		// 出撃情報にパーティ情報を付加する
		foreach ($quest_list as $i => $row) {
			if (!isset($party_assoc[$row['party_id']])) {
				continue;
			}
			
			$party = $party_assoc[$row['party_id']];
			
			$quest_list[$i]['search_code'] = $this->getSearchCode($party['entry_passwd'], $party['difficulty'], $party['play_style'], $party['force_elimination'], $party['status'], $party['dungeon_id']);
		}
		
		return $quest_list;
	}
	
	/**
	 * レイドクエスト一時データを出撃後に更新する
	 * 
	 * @param array $columns カラム内容　キーは party_id, search_code, date_created が必須
	 * @return bool 成否
	 */
	protected function renewQuestTmpDataAfterQuestInsert($columns)
	{
		// 引数チェック＆取得
		foreach (array(
			'party_id', 'search_code', 'date_created'
		) as $colname) {
			if (!array_key_exists($colname, $columns)) {
				$this->backend->logger->log(LOG_INFO, 
						"Invalid args. colname[%s]", $colname);
				return false;
			}
			
			$$colname = $columns[$colname];
		}
		
		$clock = $this->convertDateToClock($date_created);
		$time = strtotime($date_created);
		
		// DBへ保存
		$existing_data = $this->getTmpRaidSearch($party_id, self::CLOCK_TYPE_SALLY);
		if (empty($existing_data)) {
			$this->backend->logger->log(LOG_INFO,
					"calling insertTmpRaidSearch. party_id[%s] clock[%s] search_code[%s]",
					$party_id, $clock, $search_code);
			
			$ret = $this->insertTmpRaidSearch($party_id, self::CLOCK_TYPE_SALLY, $clock, $search_code);
			if ($ret !== true) {
				$this->backend->logger->log(LOG_ERR,
						"insertTmpRaidSearch failed. party_id[%s] clock[%s] search_code[%s]",
						$party_id, $clock, $search_code);
			
				return false;
			}
			
			$ret = $this->prepareIncrementTmpRaidSearchCounter($search_code, self::CLOCK_TYPE_SALLY, $clock);
			if ($ret !== true) {
				return false;
			}
		} else {
			$this->backend->logger->log(LOG_INFO,
					"calling updateTmpRaidSearch. party_id[%s] clock[%s] search_code[%s]",
					$party_id, $clock, $search_code);

			$ret = $this->updateTmpRaidSearch($party_id, self::CLOCK_TYPE_SALLY, $clock, $search_code);
			if ($ret !== true) {
				$this->backend->logger->log(LOG_ERR,
						"updateTmpRaidSearch failed. party_id[%s] clock[%s] search_code[%s]",
						$party_id, $clock, $search_code);

				return false;
			}
			
			if (($search_code != $existing_data['search_code']) ||
				($clock       != $existing_data['clock'])
			) {
				$ret = $this->prepareDecrementTmpRaidSearchCounter(
						$existing_data['search_code'], 
						self::CLOCK_TYPE_SALLY, 
						$existing_data['clock']);
				if ($ret !== true) {
					error_log('ERROR:' . __FILE__ . ':' . __LINE__);		
					return false;
				}
			
				$ret = $this->prepareIncrementTmpRaidSearchCounter($search_code, self::CLOCK_TYPE_SALLY, $clock);
				if ($ret !== true) {
					error_log('ERROR:' . __FILE__ . ':' . __LINE__);		
					return false;
				}
			}
		}
		
		// 最新キャッシュへセットする
		$this->setTmpRaidSearchLatestCache($search_code, self::CLOCK_TYPE_SALLY, $party_id, $time);
		
		return true;
	}
	
	/**
	 * レイド一時検索カウンタのインクリメントを準備する
	 * 
	 * このオブジェクト内の変数に保持する。
	 * 後でflushSetTmpRaidSearchCounterDiffを呼ぶとDBへ書きこまれる。
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock 時刻（UNIXTIMEを60で割った値）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function prepareIncrementTmpRaidSearchCounter($search_code, $clock_type, $clock)
	{
		return $this->prepareSetTmpRaidSearchCounterDiff($search_code, $clock_type, $clock, 1);
	}

	/**
	 * レイド一時検索カウンタのデクリメントを準備する
	 * 
	 * このオブジェクト内の変数に保持する。
	 * 後でflushSetTmpRaidSearchCounterDiffを呼ぶとDBへ書きこまれる。
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock 時刻（UNIXTIMEを60で割った値）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function prepareDecrementTmpRaidSearchCounter($search_code, $clock_type, $clock)
	{
		return $this->prepareSetTmpRaidSearchCounterDiff($search_code, $clock_type, $clock, -1);
	}
	
	/**
	 * 差分の値を指定してレイド一時検索カウンタを増減させる準備をする
	 * 
	 * このオブジェクト内の変数に保持する。
	 * 後でflushSetTmpRaidSearchCounterDiffを呼ぶとDBへ書きこまれる。
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock 時刻（UNIXTIMEを60で割った値）
	 * @param int $number 増減値　増加させる場合は正の値、減少させる場合は負の値
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function prepareSetTmpRaidSearchCounterDiff($search_code, $clock_type, $clock, $number)
	{
		$this->backend->logger->log(LOG_INFO,
				"search_code[%s] clock_type[%s] clock[%s] number[%s]",
				$search_code, $clock_type, $clock, $number);
		
		$key = $this->encodeCounterKey($search_code, $clock_type);
		if (!isset($this->counter_w[$key])) {
			$this->counter_w[$key] = array();
		}
		
		if (!isset($this->counter_w[$key][$clock])) {
			$this->counter_w[$key][$clock] = 0;
		}
		
		$this->counter_w[$key][$clock] += $number;
		
		return true;
	}

	/**
	 * 準備されたレイド一時検索カウンタ値をDBへ流し込む
	 * 
	 * prepareIncrementTmpRaidSearchCounter, 
	 * prepareDecrementTmpRaidSearchCounter, 
	 * prepareSetTmpRaidSearchCounterDiff
	 * で準備された値をDBへ書き込む
	 * @return boolean 成否
	 */
	protected function flushSetTmpRaidSearchCounterDiff()
	{
		$ok = true;
		foreach ($this->counter_w as $key => $assoc) {
			list($search_code, $clock_type) = $this->decodeCounterKey($key);
			
			foreach ($assoc as $clock => $number) {
				$this->backend->logger->log(LOG_INFO,
						"calling setTmpRaidSearchCounterDiff. search_code[%s] clock_type[%s] clock[%s] number[%s]",
						$search_code, $clock_type, $clock, $number);

				$ret = $this->setTmpRaidSearchCounterDiff($search_code, $clock_type, $clock, $number);
				if ($ret !== true) {
					$this->backend->logger->log(LOG_ERR,
							"setTmpRaidSearchCounterDiff failed. search_code[%s] clock_type[%s] clock[%s] number[%s]",
							$search_code, $clock_type, $clock, $number);

					$ok = false;
				}
			}
		}
		
		$this->counter_w = array();
		
		return $ok;
	}
	
	/**
	 * カウンタ値の連想配列用キーをエンコードする
	 * 
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別
	 * @return string キー
	 */
	protected function encodeCounterKey($search_code, $clock_type)
	{
		return $this->getCounterKey($search_code, $clock_type);
	}
	
	/**
	 * カウンタ値の連想配列用キーをデコードする
	 * 
	 * @param string $key キー
	 * @param array array(パーティ検索コード, 時刻種別)
	 */
	protected function decodeCounterKey($key)
	{
		return explode(':', $key);
	}

	/**
	 * パーティの所属ユーザーのフレンドごとの検索用キャッシュをセットする
	 * 
	 * cronのバッチでキューからユーザーIDなどを取得して、フレンド毎の一時データとしてMemcacheへセットする
	 * @param int $party_id パーティID
	 * @param int $friend_id フレンドのユーザーID
	 * @param int $status パーティメンバーステータス（Pp_RaidPartyManager::MEMBER_STATUS_～）
	 * @param int $disconn 切断状況
	 * @param int $rclock 丸めたクロック値（roundTimeByFriendMinutes関数の値）
	 * @return bool 成否
	 */
	protected function setPartyFriendCache($party_id, $friend_id, $status, $disconn, $rclock)
	{
        $raid_party_m = $this->backend->getManager('RaidParty');
		
		$ttl = self::FRIEND_MINUTES * 60;
		
	    $cache =& Ethna_CacheManager::getInstance('memcache');
		$memcached = $cache->memcache;

		$key = $this->getFriendCacheKey($friend_id, $rclock);
		
		$data_str = $memcached->get($key);
		if ($data_str) {
			$data_arr = explode(',', $data_str);
		} else {
			$data_arr = array();
		}
		
		$idx = array_search($party_id, $data_arr);
		if (($disconn == 1) || 
			($status == Pp_RaidPartyManager::MEMBER_STATUS_BREAK)
		) {
			if ($idx !== false) {
				unset($data_arr[$idx]);
			}
		} else {
			if ($idx === false) {
				$data_arr[] = $party_id;
			}
		}
		
		$value = implode(',', $data_arr);

		$this->backend->logger->log(LOG_INFO, "key[%s] value[%s]", $key, $value);
		
		$ret = $memcached->set($key, $value, $ttl);
		if (!$ret) {
			$this->backend->logger->log(LOG_ERR, "setPartyFriendCache failed. key[%s] value[%s]", $key, $value);
		}
		
		return $ret;
	}
}
?>