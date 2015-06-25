<?php
/**
 *  Pp_RaidSearchManager.php
 *
 *  @see        \\Cave.net\全社共有\プロジェクト\SP12-004\プログラマ\レイド\レイドパーティ検索\raid_search_README.html
 *  @see        \\Cave.net\全社共有\プロジェクト\SP12-004\プログラマ\レイド\レイドパーティ検索\レイドパーティ検索一時データの流れ.pdf
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id: d4af361a99e2aaa95cedee2132d1ca3f10920c6b $
 */

require_once 'array_column.php';
require_once 'Pp/MemcachedPseudoQueue.php';
require_once 'Pp_RaidManager.php';

/**
 *  Pp_RaidSearchManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RaidSearchManager extends Pp_RaidManager
{
	/** 時刻種別：パーティ作成 */
	const CLOCK_TYPE_PARTY = 1;
	
	/** 時刻種別：出撃 */
	const CLOCK_TYPE_SALLY = 2;
	
	/** さかのぼって参照する最大の分数 */
	const MAX_BACK_MINUTES = 20;
	
	/** フレンドを参照するパーティ作成日時を区切る時間の長さ（分） */
	const FRIEND_MINUTES = 5;
	
	/** 難易度の最大桁数（10進） */
	const DIFFICULTY_DIGITS_MAX = 1;
	
	/** プレイスタイルの最大桁数（10進） */
	const PLAY_STYLE_DIGITS_MAX = 1;
	
	/** 強制退室設定の最大桁数（10進） */
	const FORCE_ELIMINATION_DIGITS_MAX = 1;
	
	/** パーティステータスの最大桁数（10進） */
	const PARTY_STATUS_DIGITS_MAX = 2;
	
	/** ダンジョンIDの最大桁数（10進） */
	const DUNGEON_ID_DIGITS_MAX = 4;
	
	/** 結果キャッシュインデックス数 */
	const RESULT_CACHE_KEY_IDX_NUM = 10;
	
	/** 結果キャッシュlifetime（秒） */
	const RESULT_CACHE_LIFETIME = 5;
	
	/** 最新パーティキャッシュlifetime（秒） */
	const LATEST_CACHE_LIFETIME = 20; // 20 は LATEST_CACHE_STEP × 5 の意
	
	/** 最新パーティキャッシュを区切る時間単位（秒） */
	const LATEST_CACHE_STEP = 4;

	/** 最新パーティキャッシュ区切り記号 */
	const LATEST_CACHE_DELIMITER = ',';
	
	/** 一時データテーブル名 */
	protected $DATA_TABLE_NAME = array(
		self::CLOCK_TYPE_PARTY => 'tmp_raid_search_party',
		self::CLOCK_TYPE_SALLY => 'tmp_raid_search_quest',
	);

	/** 一時カウンタテーブル名 */
	protected $COUNTER_TABLE_NAME = array(
		self::CLOCK_TYPE_PARTY => 'tmp_raid_search_party_counter',
		self::CLOCK_TYPE_SALLY => 'tmp_raid_search_quest_counter',
	);

	/**
	 * 一時データカウンタ値バッファ
	 * 
	 * @var array $counter[search_code:clock_type][clock] = counter 
	 */
	protected $counter = array();
	
	/**
	 * パーティ情報のキャッシュ
	 * 
	 * @var array $party_cache[party_id]['date_created'] = パーティ作成日時 
	 */
	protected $party_cache = array();
	
	/**
	 * 検索コード用パラメータの桁数が妥当か
	 * 
	 * @param int $value パラメータ値
	 * @param type $digits 桁数（10進）
	 * @return bool 真偽
	 */
	static function isValidSearchCodeParamDigits($value, $digits)
	{
		return ((0 <= $value) && ($value < pow(10, $digits)));
	}

	/**
	 * レイドパーティ管理テーブルINSERT後に検索用一時データを更新する
	 * 
	 * ・APIで都度実行する事
	 * ・t_raid_partyへのINSERTと同トランザクション内で呼ぶ事
	 * @param array $columns カラム内容　キーは party_id, dungeon_id, difficulty, member_num, member_limit, status, force_elimination, play_style, entry_passwd, date_created が必須
	 * @return bool 成否
	 */
	function renewTmpDataAfterPartyInsert($columns)
	{
		$raid_party_m = $this->backend->getManager('RaidParty'); // const PARTY_STATUS_～ の参照用にPp_RaidPartyManagerクラスが必要

		// 引数チェック＆取得
		foreach (array(
			'party_id', 'dungeon_id', 'difficulty', 'member_num', 'member_limit', 'status', 'force_elimination', 'play_style', 'entry_passwd', 'date_created'
		) as $colname) {
			if (!array_key_exists($colname, $columns)) {
				$this->backend->logger->log(LOG_ERR, 'Invalid args. ' . var_export($columns, true));
				return false;
			}
			
			$$colname = $columns[$colname];
		}
		
		$this->backend->logger->log(LOG_INFO, 'Valid args. ' . var_export($columns, true));
		
		$clock = $this->convertDateToClock($date_created);
		$time = strtotime($date_created);
		$search_code = $this->getSearchCode($entry_passwd, $difficulty, $play_style, $force_elimination, $status, $dungeon_id);

		// 検索対象外チェック
		if (//(!empty($entry_passwd)) ||                             // 入室パスワードが存在する
			($member_num >= $member_limit) ||                      // メンバー数上限に達している
			($member_num <= 0) ||                                  // メンバーがいない
			($status == Pp_RaidPartyManager::PARTY_STATUS_BREAKUP) // 解散状態
		) {
			$search_code = 0; // 検索で出てこないようにする
		}
		
		// DBへ保存
		$ret = $this->insertTmpRaidSearch($party_id, self::CLOCK_TYPE_PARTY, $clock, $search_code);
		if ($ret !== true) {
			return false;
		}
		
		$ret = $this->incrementTmpRaidSearchCounter($search_code, self::CLOCK_TYPE_PARTY, $clock);
		if ($ret !== true) {
			return false;
		}
		
		// このPHPオブジェクト内のキャッシュへ保存
		$this->party_cache[$party_id] = array(
			'date_created' => $date_created,
		);
		
		// 最新キャッシュへセットする
		$this->setTmpRaidSearchLatestCache($search_code, self::CLOCK_TYPE_PARTY, $party_id, $time);
		
		return true;
	}
	
	/**
	 * レイドパーティ管理テーブルUPDATE後に一時データを更新する
	 * 
	 * ・APIで都度実行する事
	 * ・MySQLトランザクションを張っていない状態で呼んでも可
	 * @param array $columns UPDATE後カラム内容　キーは party_id, dungeon_id, difficulty, member_num, member_limit, status, force_elimination, play_style, entry_passwd, date_created が必須
	 * @return bool 成否
	 */
	function renewTmpDataAfterPartyUpdate($columns)
	{
		$raid_party_m = $this->backend->getManager('RaidParty'); // const PARTY_STATUS_～ の参照用にPp_RaidPartyManagerクラスが必要
		
		// 引数チェック＆取得
		foreach (array(
			'party_id', 'dungeon_id', 'difficulty', 'member_num', 'member_limit', 'status', 'force_elimination', 'play_style', 'entry_passwd', 'date_created'
		) as $colname) {
			if (!array_key_exists($colname, $columns)) {
				$this->backend->logger->log(LOG_ERR, 'Invalid args. ' . var_export($columns, true));
				return false;
			}
			
			$$colname = $columns[$colname];
		}

		$this->backend->logger->log(LOG_INFO, 'Valid args. ' . var_export($columns, true));
		
		$clock = $this->convertDateToClock($date_created);
		$search_code = $this->getSearchCode($entry_passwd, $difficulty, $play_style, $force_elimination, $status, $dungeon_id);
		
		// 検索対象外チェック
		if (//(!empty($entry_passwd)) ||                             // 入室パスワードが存在する
			($member_num >= $member_limit) ||                      // メンバー数上限に達している
			($member_num <= 0) ||                                  // メンバーがいない
			($status == Pp_RaidPartyManager::PARTY_STATUS_BREAKUP) // 解散状態
		) {
			$search_code = 0; // 検索で出てこないようにする
		}
		
		// 存在しなかったらINSERT用の処理を行う
		$existing_data = $this->getTmpRaidSearch($party_id, self::CLOCK_TYPE_PARTY);
		if (empty($existing_data)) {
			return $this->renewTmpDataAfterPartyInsert($columns);
		}
		
		// DBへ保存
		$ret = $this->updateTmpRaidSearch($party_id, self::CLOCK_TYPE_PARTY, $clock, $search_code);
		if ($ret !== true) {
			return false;
		}
		
		if (($search_code != $existing_data['search_code']) ||
			($clock       != $existing_data['clock'])
		) {
			$ret = $this->decrementTmpRaidSearchCounter(
					$existing_data['search_code'], 
					self::CLOCK_TYPE_PARTY, 
					$existing_data['clock']);
			if ($ret !== true) {
				return false;
			}

			$ret = $this->incrementTmpRaidSearchCounter($search_code, self::CLOCK_TYPE_PARTY, $clock);
			if ($ret !== true) {
				return false;
			}
			
			if ($search_code != $existing_data['search_code']) {
				$ret = $this->renewQuestTmpDataAfterPartyUpdate(array(
					'party_id'    => $party_id,
					'search_code' => $search_code,
				));
				if ($ret !== true) {
					return false;
				}
			}
		}
		
		// このPHPオブジェクト内のキャッシュへ保存
		$this->party_cache[$party_id] = array(
			'date_created' => $date_created,
		);
		
		return true;
	}
	
	/**
	 * レイドパーティメンバーテーブルINSERT後に検索用一時データを更新する
	 * 
	 * ・APIで都度実行する事
	 * ・MySQLトランザクションを張っていない状態で呼んでも可
	 * @param array $columns カラム内容　キーは party_id, user_id, status, disconn が必須
	 * @return bool 成否
	 */
	function renewTmpDataAfterPartyMemberInsert($columns)
	{
		return $this->renewTmpDataAfterPartyMemberUpdate($columns);
	}
	
	/**
	 * レイドパーティメンバーテーブルUPDATE後に検索用一時データを更新する
	 * 
	 * ・APIで都度実行する事
	 * ・MySQLトランザクションを張っていない状態で呼んでも可
	 * @param array $columns カラム内容　キーは party_id, user_id, status, disconn が必須
	 * @param string $party_date_created パーティ作成日時(Y-m-d H:i:s) 省略可
	 * @return bool 成否
	 */
	function renewTmpDataAfterPartyMemberUpdate($columns, $party_date_created = null)
	{
		// 引数チェック＆取得
		foreach (array(
			'party_id', 'user_id', 'status', 'disconn',
		) as $colname) {
			if (!array_key_exists($colname, $columns)) {
				$this->backend->logger->log(LOG_ERR, 'Invalid args. ' . var_export($columns, true) . ' ' . var_export($party_date_created, true));
				return false;
			}
			
			$$colname = $columns[$colname];
		}

		$this->backend->logger->log(LOG_INFO, 'Valid args. ' . var_export($columns, true) . ' ' . var_export($party_date_created, true));
		
		// パーティ作成日時が指定されていない場合
		if (!$party_date_created) {
			if (isset($this->party_cache[$party_id]) && isset($this->party_cache[$party_id]['date_created'])) {
				// キャッシュから取得
				$party_date_created = $this->party_cache[$party_id]['date_created'];
			} else {
				// DBから取得
				$raid_party_m = $this->backend->getManager('RaidParty');
				$party = $raid_party_m->getParty($party_id);
				
				if (is_array($party) && isset($party['date_created'])) {
					$party_date_created = $party['date_created'];
				}
			}
		}
		
		if (!$party_date_created) {
			$this->backend->logger->log(LOG_ERR, 'No party_date_created.');
			return true; // 一時的なスレーブ遅延かもしれないので、この関数は正常終了扱いにする。
		}
		
		if (!$this->isInFriendTimeWindow(strtotime($party_date_created))) {
			return true;
		}
		
		$this->renewPartyFriend($party_id, $user_id, $status, $disconn, $party_date_created);
		
		return true;
	}
	
	/**
	 * フレンドがいるパーティID一覧を取得する
	 * 
	 * @param int $user_id ユーザーID
	 * @param int $limit 最大取得件数
	 * @param int $rclock 丸めたクロック値（roundTimeByFriendMinutes関数の値） 省略可
	 * @param array $search_code_list 検索コードの配列
	 * @param array $exclude_party_id_list 除外するパーティIDの配列
	 * @return array パーティID一覧
	 */
	function getFriendPartyIdList($user_id, $limit, $rclock = null, $search_code_list = null, $exclude_party_id_list = null)
	{
		if ($rclock === null) {
			$rclock = $this->roundTimeByFriendMinutes($_SERVER['REQUEST_TIME']);
		}
		
//		$cache = new Memcached();
//		$cache->addServer($this->config->get('memcache_host'), $this->config->get('memcache_port'));
	    $cache =& Ethna_CacheManager::getInstance('memcache');
		$memcached = $cache->memcache;
		
		// memcacheから取得
		$key = $this->getFriendCacheKey($user_id, $rclock);
		$buf = $memcached->get($key);
		$this->backend->logger->log(LOG_INFO, 'key=[' . $key . '] buf=[' . $buf . ']');
		
		if (!$buf || !is_string($buf)) {
			$rand_list = array();
		} else {
			// memcacheデータをパースしてパーティIDを取り出す
			$party_id_list = explode(',', $buf);
			
			// 最大件数以内に収まるようにランダムにパーティIDを決定
			$cnt = count($party_id_list);
			$offset = mt_rand(0, $cnt - 1);
			
			$rand_list = array();
			for ($i = 0; $i < $cnt; $i++) {
				$rand_key = ($i + $offset) % $cnt;
				$rand_party_id = $party_id_list[$rand_key];
				
				if (!empty($exclude_party_id_list) &&
					in_array($rand_party_id, $exclude_party_id_list)
				) {
					continue;
				}
				
				if (!empty($search_code_list) && 
					!$this->isPartyIdMatchSearchCodeList($rand_party_id, $search_code_list)
				) {
					continue;
				}
				
				$rand_list[] = $rand_party_id;
				if (count($rand_list) >= $limit) {
					break;
				}
			}
		}
		
		return $rand_list;
	}
	
	/**
	 * パーティは検索コードに合致するか
	 * 
	 * @param int $party_id パーティID
	 * @param array $search_code_list 検索コードの配列
	 * @return boolean true:合致する, false:合致しない
	 */
	protected function isPartyIdMatchSearchCodeList($party_id, $search_code_list)
	{
		$tmp = $this->getTmpRaidSearch($party_id, self::CLOCK_TYPE_PARTY, false);
		if (empty($tmp)) {
			return false;
		}
		
		$is_match = in_array($tmp['search_code'], $search_code_list);

		return $is_match;
	}
	
	/**
	 * レイドクエスト一時データをパーティ更新後に更新する
	 * 
	 * @param array $columns カラム内容　キーは party_id, search_code が必須
	 * @return bool 成否
	 */
	protected function renewQuestTmpDataAfterPartyUpdate($columns)
	{
		// 引数チェック＆取得
		foreach (array(
			'party_id', 'search_code'
		) as $colname) {
			if (!array_key_exists($colname, $columns)) {
				return false;
			}
			
			$$colname = $columns[$colname];
		}
		
		// DB更新が必要かチェック
		$existing_data = $this->getTmpRaidSearch($party_id, self::CLOCK_TYPE_SALLY);
		if (empty($existing_data)) {
			return true;
		}
		
		if ($search_code == $existing_data['search_code']) {
			return true;
		}
		
		$clock = $this->convertDateToClock($existing_data['date_created']);
		
		// DBへ保存
		$ret = $this->updateTmpRaidSearch($party_id, self::CLOCK_TYPE_SALLY, $clock, $search_code);
		if ($ret !== true) {
			return false;
		}
			
		$ret = $this->decrementTmpRaidSearchCounter($existing_data['search_code'], self::CLOCK_TYPE_SALLY, $existing_data['clock']);
		if ($ret !== true) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);		
			return false;
		}

		$ret = $this->incrementTmpRaidSearchCounter($search_code, self::CLOCK_TYPE_SALLY, $clock);
		if ($ret !== true) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);		
			return false;
		}
		
		return true;
	}
	
	/**
	 * パーティ検索コードの取得
	 * 
	 * @param string $login_passwd 入室パスワード（一応0or1でも大丈夫なようにはなっているが）
	 * @param int $difficulty 難易度
	 * @param int $play_style プレイスタイル（1:初心者熱烈歓迎, 2:マイペースで, 3:トップを目指す！）
	 * @param int $force_elimination 強制退室設定（0:OFF, 1:ON）
	 * @param int $party_status パーティステータス（1:準備中, 2:出撃中, 3:解散）
	 * @param int $dungeon_id ダンジョンID
	 * 
	 * @return 検索コード
	 */
	function getSearchCode( $login_passwd, $difficulty, $play_style, $force_elimination, $party_status, $dungeon_id )
	{
		$auto_login = ( empty( $login_passwd ) === true ) ? 0 : 1;
		
		$code_str = $auto_login
		          . sprintf("%0" . self::FORCE_ELIMINATION_DIGITS_MAX . "d", $force_elimination)
		          . sprintf("%0" . self::DIFFICULTY_DIGITS_MAX        . "d", $difficulty)
		          . sprintf("%0" . self::PLAY_STYLE_DIGITS_MAX        . "d", $play_style)
		          . sprintf("%0" . self::PARTY_STATUS_DIGITS_MAX      . "d", $party_status)
		          . sprintf("%0" . self::DUNGEON_ID_DIGITS_MAX        . "d", $dungeon_id);
		$code = intval($code_str, 10);
		
		return $code;
	}
	
	/**
	 * 一時データカウンタ値をDBからロードする
	 * 
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock_from いつからの時刻をロードするか（端点含む）
	 * @param int $clock_to いつまでの時刻をロードするか（端点含まない）
	 */
	function loadCounter($search_code, $clock_type, $clock_from, $clock_to)
	{
		$table = $this->COUNTER_TABLE_NAME[$clock_type];
		
		$param = array($search_code, $clock_from, $clock_to);
		$sql = "SELECT clock, counter FROM $table"
		     . " WHERE search_code = ? AND clock >= ? AND clock < ?";
		
		$key = $this->getCounterKey($search_code, $clock_type);
		$this->counter[$key] = array();
		
		$result =& $this->db_unit1_r->query($sql, $param);
		while ($row = $result->FetchRow()) {
			$this->counter[$key][$row['clock']] = $row['counter'];
		}
	}

	/**
	 * 時刻でレイド検索一時データのランダムなリストを取得する
	 * 
	 * 事前にloadCounter関数でカウンタ値をロードしておく必要があるので注意
	 * @param array $search_code_list パーティ検索コードの配列
	 * @param array $clock_type_list 時刻種別（1:パーティ作成, 2:出撃）の配列
	 * @param int $clock_from いつからの時刻を取得対象とするか（端点含む）
	 * @param int $clock_to いつまでの時刻を取得対象とするか（端点含まない）
	 * @param int $num 取得件数
	 * @param array exclude_party_id_list 除外するパーティIDの配列
	 * @return array 連想配列（カラム名がキー）の配列
	 */
	function getTmpRaidSearchRandomListByMultiClockType($search_code_list, $clock_type_list, $clock_from, $clock_to, $num, $exclude_party_id_list = array())
	{
		$debug_args = func_get_args();
		$this->backend->logger->log(LOG_DEBUG, 'getTmpRaidSearchRandomListByMultiClockType. args[%s]', str_replace("\n", "", var_export($debug_args, true)));
		
		shuffle($clock_type_list);
		
		$list = array();
		foreach ($clock_type_list as $clock_type) {
			$list_tmp = $this->getTmpRaidSearchRandomListByClock(
					$search_code_list, 
					$clock_type, $clock_from, $clock_to, $num,
					array_merge($exclude_party_id_list, array_column($list, 'party_id')));
			if (is_array($list_tmp) && !empty($list_tmp)) {
				$list = array_merge($list, $list_tmp);
			}
		}
		
		if (count($list) > $num) {
			$list = array_slice($list, 0, $num);
		}
		
		return $list;
	}
	
	/**
	 * 一時データカウンタ値をDBからロードする（複数のパーティ検索コードに対応）
	 * 
	 * @param array $search_code_list パーティ検索コードの配列
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock_from いつからの時刻をロードするか（端点含む）
	 * @param int $clock_to いつまでの時刻をロードするか（端点含まない）
	 * @param int $delete_flg 無効な古いデータを削除するか
	 */
	function loadCounterMulti($search_code_list, $clock_type, $clock_from, $clock_to, $delete_flg = true)
	{
		$table = $this->COUNTER_TABLE_NAME[$clock_type];

//		$delete_cnt = 0; // 削除回数カウンタ

//		$clock_now = $this->convertTimeToClock($_SERVER['REQUEST_TIME']);
//		$clock_min = $clock_now - Pp_RaidSearchManager::MAX_BACK_MINUTES;
		
		$param = $search_code_list;
		$sql = "SELECT search_code, clock, counter FROM $table"
		     . " WHERE search_code IN(" . str_repeat('?,', count($param) - 1) . "?)";

		$result =& $this->db_r->query($sql, $param);
		while ($row = $result->FetchRow()) {
			$search_code = $row['search_code'];
			$clock       = $row['clock'];

//			// 削除対象の場合は削除する
//			if (($clock < $clock_min) && 
//			    ($delete_cnt == 0) && // 削除するのは最初の1件のみ
//			    $delete_flg
//			) {
//				$this->deleteTmpRaidSearchCounter($search_code, $clock_type, $clock);
//				$delete_cnt++;
//				continue;
//			}

			// 時刻が範囲外の場合は取り込まない
			if (!(($clock_from <= $clock) && ($clock < $clock_to))) {
				continue;
			}

			// このオブジェクト内の変数で保持する
			$key = $this->getCounterKey($search_code, $clock_type);
			if (!isset($this->counter[$key])) {
				$this->counter[$key] = array();
			}

			$this->counter[$key][$clock] = $row['counter'];
		}
	}
	
	/**
	 * 時刻でレイド検索一時データのランダムなリストを取得する
	 * 
	 * 事前にloadCounter関数でカウンタ値をロードしておく必要があるので注意
	 * @param array $search_code_list パーティ検索コードの配列
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock_from いつからの時刻を取得対象とするか（端点含む）
	 * @param int $clock_to いつまでの時刻を取得対象とするか（端点含まない）
	 * @param int $num 取得件数
	 * @param array exclude_party_id_list 除外するパーティIDの配列
	 * @return array 連想配列（カラム名がキー）の配列
	 */
	function getTmpRaidSearchRandomListByClock($search_code_list, $clock_type, $clock_from, $clock_to, $num, $exclude_party_id_list = array())
	{
		$debug_args = func_get_args();
		$this->backend->logger->log(LOG_DEBUG, 'getTmpRaidSearchRandomListByClock. args[%s]', var_export($debug_args, true));
		
		$exclude_party_id_num = count($exclude_party_id_list);
		
		shuffle($search_code_list);
		
		// 取得結果バッファ
		$parties = array();
		
		// パーティ検索コードでループ
		$limit = $num + $exclude_party_id_num; // 次回にLIMIT句で指定する値
		foreach ($search_code_list as $search_code) {
			// LIMIT句の準備
			$counter = $this->getCounter($search_code, $clock_type, $clock_from, $clock_to);
			if (!$counter) {
				continue;
			}

			$max = $counter - $limit; // offset値の取り得る最大値
			if ($max > 0) {
				$offset = mt_rand(0, $max);
			} else {
				$offset = 0;
			}

			// DBから取得
			$tmp_parties = $this->getTmpRaidSearchListByClock(
					$search_code, $clock_type, $clock_from, $clock_to, $offset, $limit);
			if (empty($tmp_parties)) {
				continue;
			}

			foreach ($tmp_parties as $tmp_party) {
				// 指定されたパーティIDは除外する
				if (in_array($tmp_party['party_id'], $exclude_party_id_list)) {
					continue;
				}

				$parties[] = $tmp_party;

				// 十分な件数を取得できていたら終了
				$limit -= 1;
				if ($limit <= $exclude_party_id_num) {
					break 2;
				}
			} // end of foreach 1
		} // end of foreach 2
		
		return $parties;
	}
	
	/**
	 * 日時の範囲でレイド検索一時データの最新リスト（パーティIDの配列）を取得する
	 * 
	 * パーティIDを$num_examine個取得して、その中からランダムに$num_take個を取得する。
	 * @param array $search_code_list パーティ検索コードの配列
	 * @param array $clock_type_list 時刻種別（1:パーティ作成, 2:出撃）の配列
	 * @param int $num_examine 取得候補として検証するパーティ数
	 * @param int $num_take 取得するパーティ数
	 * @param int $time_from いつからの時刻を取得対象とするか(UNIXTIME)（端点含む）
	 * @param int $time_to いつまでの時刻を取得対象とするか(UNIXTIME)（端点含まない）
	 * @return array パーティID一覧
	 */
	function getLatestPartyIdListByTimeRange($search_code_list, $clock_type_list, $num_examine, $num_take, $time_from = null, $time_to = null)
	{
		$debug_args = func_get_args();
		$this->backend->logger->log(LOG_DEBUG, 'getLatestPartyIdListByTimeRange. args[%s]', var_export($debug_args, true));
		
		if ($time_to === null) {
			$time_to = $_SERVER['REQUEST_TIME'] + self::LATEST_CACHE_STEP;
		}
		
		$time_to = $this->roundTimeByLatestCacheStep($time_to);
		
		if ($time_from === null) {
			$time_from = $time_to - self::LATEST_CACHE_LIFETIME;
		}
		
		$time_from = $this->roundTimeByLatestCacheStep($time_from);
		
		$party_id_assoc = array();
		
		shuffle($search_code_list);
		shuffle($clock_type_list);
		
		for ($time = $time_to - self::LATEST_CACHE_STEP; $time >= $time_from; $time -= self::LATEST_CACHE_STEP) {
			$list = $this->getLatestPartyIdList($search_code_list, $clock_type_list, $num_examine, $num_take, $time);
			if (empty($list)) {
				$this->backend->logger->log(LOG_DEBUG, 'Empty list. time[%d]', $time);
				continue;
			}

			if (!is_array($list)) {
				$this->backend->logger->log(LOG_WARNING, 'Invalid list. time[%d]', $time);
				continue;
			}

			foreach ($list as $party_id) {
				$party_id_assoc[$party_id] = true;

				if (count($party_id_assoc) >= $num_take) {
					break 2;
				}
			} // end of loop 1
		} // end of loop 2
		
		$party_id_list = array_keys($party_id_assoc);

		$this->backend->logger->log(LOG_DEBUG, 'getLatestPartyIdListByTimeRange party_id_list[%s]', str_replace("\n", "", var_export($party_id_list, true)));
		
		return $party_id_list;
	}
	
	/**
	 * パーティ検索API戻り値用のパーティ一覧情報を取得する
	 * 
	 * @param array $party_id_list パーティIDの配列
	 * @param array $friend_leader_list フレンド一覧のリーダーモンスターのデータ（Pp_FriendManager::getFriendLeaderListの戻り値）
	 * @return array 連想配列（キーは'party_id', 'user_id', 'status', 'play_style', 'user_name', 'user_rank', 'leader_mons_id', 'leader_mons_lv'）の配列
	 */
	function getPartyListForSearchResponse($party_id_list, $friend_leader_list)
	{
		$raidquest_m =& $this->backend->getManager('RaidQuest');
		$raidparty_m =& $this->backend->getManager('RaidParty');
		$monster_m  =& $this->backend->getManager('Monster');

		$friend_assoc = array(); // フレンド判定用連想配列  $friend_assoc[フレンドのユーザーID] = true
		if (is_array($friend_leader_list)) {
			foreach ($friend_leader_list as $row) {
				$friend_assoc[$row['user_id']] = true;
			}
		}
		
		//ダンジョンのマスタデータを全件取得
		$dungeon_master = $raidquest_m->cacheGetMasterDungeon( false );
		//ダンジョン名のリストを生成
		$dungeon_name = array();
		foreach ($dungeon_master as $dm) {
			$dungeon_name[($dm['dungeon_id'])] = $dm['name'];
		}
		
		$sql = "SELECT party_id AS id, t.* FROM t_raid_party t WHERE party_id IN("
			 . str_repeat("?,", count($party_id_list) - 1) . "?)";
		
		$party_assoc = $this->db_unit1_r->db->GetAssoc($sql, $party_id_list);
		
		$party_list = array();
		foreach ($party_id_list as $tmp_party_id) {
			if (isset($party_assoc[$tmp_party_id])) {
				$party_list[] = $party_assoc[$tmp_party_id];
			}
		}
		
		$user_id_list = array_column($party_list, 'master_user_id');
		
		$party_info_list = array();
		
		if (!empty($user_id_list)) {
			//アクティブなリーダーモンスター及びユーザー情報を取得する
			$leader_list = $monster_m->getActiveLeaderList(
				$user_id_list
			);
			//ユーザIDをキーに再生成する
			$leaders = array();
			foreach($leader_list as $key => $row) {
				$leaders[($row['user_id'])] = $row;
			}
			
			foreach ($party_list as $tmp_party) {
				$party_info = array();
				$dungeon_info = array();
				$user_info = array();

				$party_id = $tmp_party['party_id'];
				$user_id = $tmp_party['master_user_id'];
				$party_member = $raidparty_m->getPartyMember( $party_id, $user_id );

				// メンバーがいなかったら取得対象外
				if (empty($party_member)) {
					$this->backend->logger->log(LOG_WARNING, 'No member. party_id[%s] user_id[%s]', $party_id, $user_id);
					continue;
				}
				
				// 退室以上のステータスなら取得対象外
				if ($party_member['status'] >= Pp_RaidPartyManager::MEMBER_STATUS_BREAK) {
					// OK
					$this->backend->logger->log(LOG_INFO, 'Status break. party_id[%s] user_id[%s]', $party_id, $user_id);
					continue;
				}

				// リーダーがいない場合は取得対象外
				if (!isset($leaders[$user_id])) {
					$this->backend->logger->log(LOG_WARNING, 'No leader. party_id[%s] user_id[%s]', $party_id, $user_id);
					continue;
				}
				
				//該当のパーティ情報を取得
//				$party_data = $raidparty_m->getParty( $party_id, false, false );
				$party_data = $tmp_party;
				$party_info = $raidparty_m->convertPartyInfoForApiResponse($party_data);
				//ダンジョン情報生成
				$dungeon_info = $raidquest_m->getDungeonInfoExForApiResponse($party_data, $dungeon_name[$party_data['dungeon_id']]);
				$user_info = $raidparty_m->convertUserInfoForApiResponse($party_data, $party_member, $leaders[$user_id]);

				// フレンド情報を取得
				$is_friend = isset($friend_assoc[$user_id]) ? 1 : 0;

				//データを追加
				$party_info_list[] = array(
//					'data_type'    => 1,
					'party_info'   => $party_info,
					'dungeon_info' => $dungeon_info,
					'user_info'    => $user_info,
					'is_friend'    => $is_friend,
				);
			}
		}
		
		return $party_info_list;
	}
	
	/**
	 * 日付を検索一時データ用クロック値へ変換する
	 * 
	 * @param string $date 日付(Y-m-d H:i:s)
	 * @return int クロック値（UNIXTIMEを60で割った値）
	 */
	function convertDateToClock($date)
	{
		$time = strtotime($date);
		
		return $this->convertTimeToClock($time);
	}
	
	/**
	 * UNIXTIME値を検索一時データ用クロック値へ変換する
	 * 
	 * @param int $time UNIXTIME値
	 * @return int クロック値（UNIXTIMEを60で割った値）
	 */
	function convertTimeToClock($time)
	{
		return floor($time / 60);
	}

	/**
	 * UNIXTIME値をフレンドがいるパーティの検索での保持期間で丸める
	 * 
	 * @param int $time UNIXTIME値
	 * @return int 丸めたクロック値
	 */
	function roundTimeByFriendMinutes($time)
	{
		$clock = $this->convertTimeToClock($time);
		$rclock = $clock - ($clock % self::FRIEND_MINUTES);
		
		return $rclock;
	}
	
	/**
	 * UNIXTIME値を最新パーティキャッシュを区切る時間単位（秒）で丸める
	 * 
	 * @param int $time UNIXTIME値
	 * @return int 丸めたUNIXTIME値
	 */
	function roundTimeByLatestCacheStep($time)
	{
		$rtime = $time - ($time % self::LATEST_CACHE_STEP);
		
		return $rtime;
	}

	/**
	 * フレンドがいるパーティの検索対象となる時間窓の範囲内か判別する
	 * 
	 * @param int $time_created パーティ作成日時(UNIXTIME)
	 * @param int $time_now     現在日時(UNIXTIME) 省略可
	 * @return bool true:範囲内, false:範囲外
	 */
	function isInFriendTimeWindow($time_created, $time_now = null)
	{
		if ($time_now === null) {
			$time_now = $_SERVER['REQUEST_TIME'];
		}
		
		$clock1 = $this->roundTimeByFriendMinutes($time_created);
		$clock2 = $this->roundTimeByFriendMinutes($time_now);
		
		return ($clock1 == $clock2);
	}

	/**
	 * 結果キャッシュ用キーを取得する
	 * 
	 * @param int $play_style プレイスタイル  0:指定なし, 1以上:Pp_RaidPartyManager::PLAY_STYLE_～
	 * @param int $dungeon_id ダンジョンID  0:指定なし, 1以上:ダンジョンID
	 * @param int $dungeon_rank ダンジョンランク  0:指定なし, 1以上:Pp_RaidQuestManager::DIFFICULTY_～
	 * @param int $party_status パーティーの準備状況  0:指定なし, 1以上:Pp_RaidPartyManager::PARTY_STATUS_～
	 * @param int $auto_entry 自動入室フラグ  0:手動入室,1:自動入室
	 * @param array $force_elimination_list 強制退室設定の配列  0:OFF, 1:ON
	 * @param int $rank ユーザーのランク
	 * @param int $idx キー複数保持用インデックス番号  0以上  省略可
	 * @return string キー
	 */
	function getResultCacheKey($play_style, $dungeon_id, $dungeon_rank, $party_status, $auto_entry, $force_elimination_list, $rank, $retry_cnt, $idx = null)
	{
		if ($idx === null) {
			$idx = mt_rand(0, self::RESULT_CACHE_KEY_IDX_NUM - 1);
		}
		
		$retry_flg = $this->getRetryFlg($retry_cnt);
		
		$key = basename(BASE) . 'raid_search_result_'
		     . $play_style . ':' . $dungeon_id . ':' . $dungeon_rank . ':'
			 . $party_status . ':' . $auto_entry . ':'
			 . implode(',', $force_elimination_list) . ':' . $rank . ':' . $retry_flg . ':' . $idx;
		
		return $key;
	}
	
	/**
	 * 再検索フラグを取得する
	 * 
	 * @param int $retry_cnt 再検索カウンタ値
	 * @return bool true:再検索, false:初回検索
	 */
	function getRetryFlg($retry_cnt)
	{
		return ($retry_cnt > 0) ? 1 : 0;
	}
	
	/**
	 * 時刻を指定してレイド検索一時データのリストを取得する
	 * 
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock_from いつからの時刻を取得対象とするか（端点含む）
	 * @param int $clock_to いつまでの時刻を取得対象とするか（端点含まない）
	 * @param int $offset LIMIT句に指定するoffset値
	 * @param int $limit LIMIT句に指定するlimit値
	 * @return array 連想配列（カラム名がキー）の配列
	 */
	protected function getTmpRaidSearchListByClock($search_code, $clock_type, $clock_from, $clock_to, $offset, $limit)
	{
		$table = $this->DATA_TABLE_NAME[$clock_type];
		
		$param = array($search_code, $clock_from, $clock_to, $offset, $limit);
		$sql = "SELECT * FROM $table"
		     . " WHERE search_code = ? AND clock >= ? AND clock < ?"
		     . " LIMIT ?, ?";
		
		return $this->db_unit1_r->GetAll($sql, $param);
	}
	
	/**
	 * 一時データカウンタ値を取得する
	 * 
	 * あらかじめloadCounter関数でロードしておく必要があるので注意
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock_from いつからの時刻を取得対象とするか（端点含む）
	 * @param int $clock_to いつまでの時刻を取得対象とするか（端点含まない）
	 * @return int カウンタ値
	 */
	protected function getCounter($search_code, $clock_type, $clock_from, $clock_to = null)
	{
		if ($clock_to === null) {
			$clock_to = $clock_from + 1;
		}
		
		$key = $this->getCounterKey($search_code, $clock_type);
		
		$value = 0;
		for ($clock = $clock_from; $clock < $clock_to; $clock++) {
			if (isset($this->counter[$key]) && isset($this->counter[$key][$clock])) {
				$value += $this->counter[$key][$clock];
			}
		}
		
		return $value ? $value : null;
	}
	
	/**
	 * レイド一時検索カウンタをインクリメントする
	 * 
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock 時刻（UNIXTIMEを60で割った値）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function incrementTmpRaidSearchCounter($search_code, $clock_type, $clock)
	{
		return $this->setTmpRaidSearchCounterDiff($search_code, $clock_type, $clock, 1);
	}
	
	/**
	 * レイド一時検索カウンタをデクリメントする
	 * 
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock 時刻（UNIXTIMEを60で割った値）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function decrementTmpRaidSearchCounter($search_code, $clock_type, $clock)
	{
		return $this->setTmpRaidSearchCounterDiff($search_code, $clock_type, $clock, -1);
	}
	
	/**
	 * 差分の値を指定してレイド一時検索カウンタを増減させる
	 * 
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock 時刻（UNIXTIMEを60で割った値）
	 * @param int $number 増減値　増加させる場合は正の値、減少させる場合は負の値
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function setTmpRaidSearchCounterDiff($search_code, $clock_type, $clock, $number)
	{
		if ($search_code == 0) {
			return true;
		}
		
		if ($number == 0) {
			return true;
		}

		// 無効な古い時刻の場合は処理しない
		$clock_now = $this->convertTimeToClock($_SERVER['REQUEST_TIME']);
		$clock_min = $clock_now - Pp_RaidSearchManager::MAX_BACK_MINUTES;
		if ($clock < $clock_min) {
			// OK
			return true;
		}
		
		$table = $this->COUNTER_TABLE_NAME[$clock_type];
		
		// UPDATEを試みる
		$update_param = array($number, $search_code, $clock);
		$update_sql = "UPDATE $table SET counter = counter + ?"
		            . " WHERE search_code = ? AND clock = ?";
		if (!$this->db_unit1->execute($update_sql, $update_param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		// UPDATEで反映できなかったらINSERT
		$affected_rows = $this->db_unit1->db->affected_rows();
		if ($affected_rows == 0) {
			$insert_param = array($search_code, $clock, $number);
			$insert_sql = "INSERT INTO $table(search_code, clock, counter)"
			            . " VALUES(?, ?, ?)";
			if (!$this->db_unit1->execute($insert_sql, $insert_param)) { // INSERT失敗の場合
				// 同時に他のユーザーがINSERTしていた為に失敗した可能性があるので、再度UPDATEを試みる
				if (!$this->db_unit1->execute($update_sql, $update_param)) {
					return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
						$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
				}
			}
		}

		return true;
	}
	
	/**
	 * レイド一時検索カウンタを削除する
	 * 
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock 時刻（UNIXTIMEを60で割った値）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function deleteTmpRaidSearchCounter($search_code, $clock_type, $clock)
	{
		$table = $this->COUNTER_TABLE_NAME[$clock_type];

		$param = array($search_code, $clock);
		$sql = "DELETE FROM $table WHERE search_code = ? AND clock = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		return true;
	}

	/**
	 * レイド検索一時データテーブルへINSERTする
	 * 
	 * @param int $party_id パーティID
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock 時刻（UNIXTIMEを60で割った値）
	 * @param int $search_code パーティ検索コード
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function insertTmpRaidSearch($party_id, $clock_type, $clock, $search_code)
	{
		$table = $this->DATA_TABLE_NAME[$clock_type];

		$param = array($party_id, $clock, $search_code);
		$sql = "INSERT INTO $table(party_id, clock, search_code)"
		     . " VALUES(?, ?, ?)";
		
		if (!$this->db_unit1->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return true;
	}
	
	/**
	 * レイド検索一時データテーブルをUPDATEする
	 * 
	 * @param int $party_id パーティID
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $clock 時刻（UNIXTIMEを60で割った値）
	 * @param int $search_code パーティ検索コード
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function updateTmpRaidSearch($party_id, $clock_type, $clock, $search_code)
	{
		$table = $this->DATA_TABLE_NAME[$clock_type];
		
		$param = array($clock, $search_code, $party_id);
		$sql = "UPDATE $table SET clock = ?, search_code = ?"
		     . " WHERE party_id = ?";
		
		if (!$this->db_unit1->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return true;
	}
	
	/**
	 * パーティ検索API戻り値用のアクティブリーダー情報を取得する
	 * 
	 * getPartyListForSearchResponse関数から呼ばれるサブルーチン
	 * @see Pp_MonsterManager::getActiveLeaderList
	 */
	protected function getActiveLeaderListForSearchResponse($user_id_list)
	{
		if (!is_array($user_id_list) || (count($user_id_list) == 0)) {
			return null;
		}
		
        $base_sql = "SELECT b.user_id, b.name, b.rank, m.monster_id"
		     . " FROM t_user_base b, t_user_team t, t_user_monster m"
		     . " WHERE b.user_id = t.user_id"
		     . " AND b.active_team_id = t.team_id"
		     . " AND t.user_monster_id = m.user_monster_id"
		     . " AND t.leader_flg = 1"
             . " AND b.user_id IN(";
		
        $unit_m = $this->backend->getManager('Unit');
        $unit_user_list = $unit_m->cacheGetUnitFromUserIdList($user_id_list);

        $ary = array();
        foreach($unit_user_list as $unit => $user_ids) {
            $sql = $base_sql
                . str_repeat("?,", count($user_ids) - 1) . "?)";

            $rows = $unit_m->getAllSpecificUnit($sql, $user_ids, $unit, false);
            $ary = array_merge($ary, $rows);
        }
		
		$monster_m = $this->backend->getManager('Monster');
		$monster_id_list = array_column($ary, 'monster_id');
		$master_monster_assoc = $monster_m->getMasterMonsterAssoc($monster_id_list);
		
		$colname = 'name_' . $this->config->get('lang');
		foreach ($ary as $i => $row) {
			$ary[$i]['monster_name'] = $master_monster_assoc[$row['monster_id']][$colname];
		}

        return $ary;
	}

	/**
	 * レイド検索一時データを取得する
	 * 
	 * @param int $party_id パーティID
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param boolean $from_master マスターDBから取得するか（true:マスターから, false:スレーブから）
	 * @return array 連想配列（カラム名がキー）
	 */
	protected function getTmpRaidSearch($party_id, $clock_type, $from_master = true)
	{
		$table = $this->DATA_TABLE_NAME[$clock_type];
		
		$param = array($party_id);
		$sql = "SELECT * FROM $table WHERE party_id = ?";

		$db = ($from_master) ? $this->db_unit1 : $this->db_unit1_r;
		
		return $db->GetRow($sql, $param);
	}
	
	/**
	 * カウンタ値の連想配列用キーを取得する
	 * 
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別
	 * @return string キー
	 */
	protected function getCounterKey($search_code, $clock_type)
	{
		return $search_code . ':' . $clock_type;
	}
	
	/**
	 * フレンドがいるパーティ検索用のmemcacheキーを取得する
	 * 
	 * @param int $friend_id フレンドのユーザーID
	 * @param int $rclock 丸めたクロック値（roundTimeByFriendMinutes関数の値）
	 * @return string キー
	 */
	protected function getFriendCacheKey($friend_id, $rclock)
	{
		return basename(BASE) . 'raid_search_friend' . $friend_id . '_' . $rclock;
	}
	
	/**
	 * フレンドがいるパーティ検索用の一時データを更新する
	 * 
	 * この関数で行うのはキューへセットする所まで。
	 * キューから取り出して一時データを更新するのは別バッチ（CLIのraid_search_friend_addアクション）で行う。
	 * @param int $party_id パーティID
	 * @param int $user_id ユーザーID
	 * @param int $status パーティメンバーステータス（Pp_RaidPartyManager::MEMBER_STATUS_～）
	 * @param int $disconn 切断状況
	 * @param string $party_date_created パーティ作成日時(Y-m-d H:i:s)
	 * @return void
	 */
	protected function renewPartyFriend($party_id, $user_id, $status, $disconn, $party_date_created)
	{
		$rclock = $this->roundTimeByFriendMinutes(strtotime($party_date_created));
		
//		$queue = new Pp_MemcachedPseudoQueue(
//				$this->config->get('memcache_host'), 
//				$this->config->get('memcache_port'));
	    $cache =& Ethna_CacheManager::getInstance('memcache');
		$queue = new Pp_MemcachedPseudoQueue($cache->memcache);
		
		$key = $this->getFriendQueueCacheKey();
		
		$queue->send($key, array($party_id, $user_id, $status, $disconn, $rclock));
	}
	
	/**
	 * フレンドがいるパーティ検索用の一時データを更新する為のキューに使用するキー名を取得する
	 * 
	 * @return string キー
	 */
	protected function getFriendQueueCacheKey()
	{
		$key = basename(BASE) . 'RaidSearchFriendQueue';
		return $key;
	}
	
	/**
	 * レイド検索一時データの最新リストキャッシュへセットする
	 * 
	 * @param int $search_code パーティ検索コード
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $party_id パーティID
	 * @param int $time 日時(UNIXTIME)（省略可）
	 * @return void
	 */
	protected function setTmpRaidSearchLatestCache($search_code, $clock_type, $party_id, $time = null)
	{
		if ($time === null) {
			$time = $_SERVER['REQUEST_TIME'];
		}
		
		$time = $this->roundTimeByLatestCacheStep($time);
		
	    $cache =& Ethna_CacheManager::getInstance('memcache');
		
		$key = $this->getLatestCacheKey($search_code, $clock_type, $time);
		$value = $party_id . self::LATEST_CACHE_DELIMITER;
		
		$cache->prependOrAdd($key, $value);

		$this->backend->logger->log(LOG_DEBUG, 'setTmpRaidSearchLatestCache. key[%s] value[%s]', $key, $value);
	}
	
	/**
	 * レイド検索一時データの最新リスト（パーティIDの配列）を取得する
	 * 
	 * パーティIDを$num_examine個取得して、その中からランダムに$num_take個を取得する。
	 * @param array $search_code_list パーティ検索コードの配列
	 * @param array $clock_type_list 時刻種別（1:パーティ作成, 2:出撃）の配列
	 * @param int $num_examine 取得候補として検証するパーティ数
	 * @param int $num_take 取得するパーティ数
	 * @param int $time いつの時刻を取得対象とするか(UNIXTIME)
	 * @return array パーティID一覧
	 */
	protected function getLatestPartyIdList($search_code_list, $clock_type_list, $num_examine, $num_take, $time)
	{
		$debug_args = func_get_args();
		$this->backend->logger->log(LOG_DEBUG, 'getLatestPartyIdList. args[%s]', var_export($debug_args, true));
		
	    $cache =& Ethna_CacheManager::getInstance('memcache');
		$memcached = $cache->memcache;
		
		$keys = array();
		foreach ($search_code_list as $search_code) {
			foreach ($clock_type_list as $clock_type) {
				$keys[] = $this->getLatestCacheKey($search_code, $clock_type, $time);
			}
		}
		
		$party_id_list = array();
		
		$values = $memcached->getMulti($keys);
		if (is_array($values)) {
			foreach ($keys as $key) {
				if (!isset($values[$key])) {
//					$this->backend->logger->log(LOG_DEBUG, 'Key not set. key[%s]', $key);
					continue;
				}
				
				$cnt = 0;
				$tok = strtok($values[$key], self::LATEST_CACHE_DELIMITER);
				while ($tok !== false) {
					if (in_array($tok, $party_id_list)) {
						$this->backend->logger->log(LOG_WARNING, 'Key exists. key[%s]', $key);
						continue;
					}
					
					$party_id_list[] = $tok;
					$cnt++;
					if ($cnt >= $num_examine) {
						break 1;
					}
					
					$tok = strtok(self::LATEST_CACHE_DELIMITER);
				} // end of loop 1
			} // end of loop 2
		}
		
		if (count($party_id_list) > $num_take) {
			$rand_ret = Pp_Util::arrayRandValues($party_id_list, $num_take);
			
			if ($num_take > 1) {
				$party_id_list = $rand_ret;
			} else {
				if ($rand_ret !== null) {
					$party_id_list = array($rand_ret);
				} else {
					$party_id_list = array();
				}
			}
		}
		
		$this->backend->logger->log(LOG_DEBUG, 'getLatestPartyIdList party_id_list[%s]', str_replace("\n", "", var_export($party_id_list, true)));
		
		return $party_id_list;
	}

	/**
	 * 最新リスト用キャッシュキーを取得する
	 * 
	 * @param array $search_code_list パーティ検索コードの配列
	 * @param int $clock_type 時刻種別（1:パーティ作成, 2:出撃）
	 * @param int $time いつの時刻を取得対象とするか(UNIXTIME)
	 * @return string キー
	 */
	protected function getLatestCacheKey($search_code, $clock_type, $time)
	{
		$key = basename(BASE) . 'RaidSearchLatest' . $search_code . $clock_type . $time;

		// $this->backend->logger->log(LOG_DEBUG, 'getLatestCacheKey. key[%s]', $key);
		
		return $key;
	}
}
?>