<?php
/**
 *  Pp_RaidQuestManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'eaccelerator_dummy.php';
require_once 'array_column.php';
require_once 'Pp_ItemManager.php';
require_once 'Pp_RaidManager.php';

/**
 *  Pp_RaidDungeonManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RaidQuestManager extends Pp_RaidManager
{
	// 難易度
	const DIFFICULTY_NONE     = 0;	// 指定なし（検索条件用の定義）
	const DIFFICULTY_BEGINNER = 1;	// 初級
	const DIFFICULTY_MIDDLE   = 2;	// 中級
	const DIFFICULTY_ADVANCED = 3;	// 上級
	const DIFFICULTY_EXTRA    = 4;	// 超級

	var $DIFFICULTY_NAME = array(
		self::DIFFICULTY_BEGINNER => '初級',
		self::DIFFICULTY_MIDDLE   => '中級',
		self::DIFFICULTY_ADVANCED => '上級',
		self::DIFFICULTY_EXTRA    => '超級',
	); 

	// クリア報酬カテゴリ
	const CLEAR_REWARD_CATEGORY_MVP    = 1;	// MVP報酬
	const CLEAR_REWARD_CATEGORY_1ST    = 2;	// パーティマスター初回クリア報酬
	const CLEAR_REWARD_CATEGORY_2ND    = 3;	// パーティマスター２回目以降クリア報酬
	const CLEAR_REWARD_CATEGORY_MEMBER = 4;	// パーティメンバー報酬
	const CLEAR_REWARD_CATEGORY_DROP   = 5;	// ドロップ報酬(ログ用に定義)

	// クエスト結果
	const QUEST_RESULT_CLEAR   = 1;		// クエストクリア
	const QUEST_RESULT_FAILURE = 2;		// クエスト失敗

	// 獲得報酬一時テーブルステータス
	const TMP_REWARD_STATUS_ENTRY       = 0;	// 登録のみ
	const TMP_REWARD_STATUS_DISTRIBUTED = 1;	// 配布済
	
	// ダンジョン
	const DUNGEON_NONE = 0;	// 指定なし（検索条件用の定義）

	// プレイデータ一時テーブルステータス
	const TMP_RESULT_STATUS_ENTRY       = 0;	// 登録のみ
	const TMP_RESULT_STATUS_REFLECT     = 1;	// 反映済

	/**
	 * 指定ダンジョンIDのダンジョンマスタ情報を取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * @param int $check_date 開始終了日時のチェック（true:チェックする, false:チェックしない）
	 * 
	 * @return ダンジョンマスタ情報
	 */
	function getMasterDungeonById( $dungeon_id, $check_date = false )
	{
		$param = array( $dungeon_id );
		//$sql = "SELECT * FROM m_raid_dungeon WHERE dungeon_id = ?";
		$lang = $this->config->get('lang');
		$sql = "SELECT *, name_$lang AS name FROM m_raid_dungeon WHERE dungeon_id = ?";
		if( $check_date === true )
		{	// 開始終了日時のチェックを行う
			$sql .= " AND date_begin <= NOW() AND NOW() < date_end";
		}
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * 指定ダンジョンIDのダンジョンマスタ情報を取得（dungeon_id複数指定ver）
	 * 
	 * @param array $dungeon_ids ダンジョンIDの配列
	 * @param int $check_date 開始終了日時のチェック（true:チェックする, false:チェックしない）
	 * 
	 * @return ダンジョンマスタ情報
	 */
	function getMasterDungeonByIds( $dungeon_ids, $check_date = false )
	{
		$param = array();
		$where_in = array();
		foreach( $dungeon_ids as $dungeon_id )
		{
			$param[] = $dungeon_id;
			$where_in[] = '?';
		}
		//$sql = "SELECT * FROM m_raid_dungeon "
		$lang = $this->config->get('lang');
		$sql = "SELECT *, name_$lang AS name FROM m_raid_dungeon "
			 . "WHERE dungeon_id IN (".implode( ',', $where_in ).") ";
		if( $check_date === true )
		{	// 開始終了日時のチェックを行う
			$sql .= " AND date_begin <= NOW() AND NOW() < date_end";
		}
		return $this->db_unit1_r->GetAll( $sql, $param );
	}
	
	/**
	 * 指定ダンジョンIDのゲリラスケジュールマスタ情報を取得（dungeon_id複数指定ver）
	 * 
	 * @param array $dungeon_ids ダンジョンIDの配列
	 * @param int $check_date 開始終了時刻のチェック（true:チェックする, false:チェックしない）
	 * 
	 * @return ゲリラスケジュールマスタ情報
	 */
	function getMasterGuerrillaScheduleByIds( $dungeon_ids, $check_time = false )
	{
		$param = array();
		$where_in = array();
		foreach( $dungeon_ids as $dungeon_id )
		{
			$param[] = $dungeon_id;
			$where_in[] = '?';
		}
		
		$sql = "SELECT dungeon_id, time_begin, time_end FROM m_raid_guerrilla_schedule "
			 . "WHERE dungeon_id IN (".implode( ',', $where_in ).") ";
		if( $check_time === true )
		{	// 開始終了時刻のチェックを行う
			$sql .= " AND time_begin <= CURRENT_TIME AND CURRENT_TIME < time_end";
		}
		return $this->db_r->GetAll( $sql, $param );
	}

	/**
	 * ダンジョンマスタ情報を取得
	 * 
	 * @param int $check_date 開始日＆終了日のチェック（true:チェックする, false:チェックしない）
	 * 
	 * @return ダンジョンマスタ情報
	 */
	function getMasterDungeon( $check_date = false )
	{
		//$sql = "SELECT * FROM m_raid_dungeon ";
		$lang = $this->config->get('lang');
		$sql = "SELECT *, name_$lang AS name FROM m_raid_dungeon ";
		if( $check_date === true )
		{	// 開始終了日時のチェックを行う
			$sql .= "WHERE date_begin <= NOW() AND NOW() < date_end ";
		}
		$sql .= "ORDER BY dungeon_id DESC";
		return $this->db_unit1_r->GetAll( $sql );
	}

	/**
	 * ダンジョンマスタ情報を取得（キャッシュ使用）
	 * 
	 * @param int $check_date 開始日＆終了日のチェック（true:チェックする, false:チェックしない）
	 * 
	 * @return ダンジョンマスタ情報
	 */
	function cacheGetMasterDungeon( $check_date = false )
	{
		static $cache_buf = array();
		
		$cache_type = ($check_date ? '1' : '0');
		
		if (isset($cache_buf[$cache_type])) {
			return $cache_buf[$cache_type];
		}
		
		$cache_key = basename(BASE) . 'MRaidDungeon' . $cache_type;
		$cache_ttl = 60;
		
		$cache_value = eaccelerator_get($cache_key);
		if (is_string($cache_value) && $cache_value) {
			$this->backend->logger->log(LOG_INFO, 'cache hit. [%s]', $cache_value);
			$master_dungeon = unserialize($cache_value);
		}

		if (!isset($master_dungeon) || !is_array($master_dungeon)) {
			$this->backend->logger->log(LOG_INFO, 'cache miss. cache_key=[%s]', $cache_key);

			$master_dungeon = $this->getMasterDungeon($check_date);

			if (!empty($master_dungeon)) {
				$ret = eaccelerator_put($cache_key, serialize($master_dungeon), $cache_ttl);
				if ($ret !== true) {
					$this->backend->logger->log(LOG_ERR, 'eaccelerator_put failed. cache_key=[%s]', $cache_key);
				}
				
				$cache_buf[$cache_type] = $master_dungeon;
			}
		}
		
		return $master_dungeon;
	}
	
	/**
	 * ゲリラスケジュールに合致するダンジョンマスタ情報を取得
	 * 
	 * @return ダンジョンマスタ情報
	 */
	function getMasterDungeonGuerrilla()
	{
		//$sql = "SELECT * FROM m_raid_dungeon "
		$lang = $this->config->get('lang');
		$sql = "SELECT *, name_$lang AS name FROM m_raid_dungeon "
			 . "WHERE dungeon_id IN ( "
			 . "  SELECT dungeon_id FROM m_raid_guerrilla_schedule "
			 . "  WHERE time_begin <= CURRENT_TIME AND CURRENT_TIME < time_end "
			 . ") AND date_begin <= NOW() AND NOW() <= date_end ";
		return $this->db_unit1_r->GetAll( $sql );
	}
	
	/**
	 * ゲリラ及び定常開催のスケジュールに合致するダンジョンマスタ情報を取得
	 * 
	 * 定常開催orゲリラの判別方法：
	 * m_raid_guerrilla_schedule に dungeon_id が存在するダンジョンはゲリラ、無ければ定常開催
	 * 定常開催のダンジョンの取得条件：
	 * ・m_raid_dungeon の開始日時～終了日時の期間内
	 * ゲリラのダンジョンの取得条件：
	 * ・m_raid_dungeon の開始日時～終了日時の期間内
	 * ・m_raid_guerrilla_schedule の開始時刻～終了時刻の期間内
	 * @return ダンジョンマスタ情報
	 */
	function getMasterDungeonMixed()
	{
		$dungeon_list = $this->getMasterDungeon(true);
		if (empty($dungeon_list)) {
			return $dungeon_list;
		}
		
		$dungeon_ids = array_column($dungeon_list, 'dungeon_id');
		$guerrilla_list = $this->getMasterGuerrillaScheduleByIds($dungeon_ids, false);
		
		$current_time = date('H:i:s', $_SERVER['REQUEST_TIME']);
		
		// ゲリラスケジュール情報を判定
		// $flags[dungeon_id] = 現在がゲリラスケジュールの開始時刻～終了時刻の範囲内の場合:true, 範囲外の場合:false
		// ※ゲリラスケジュールが存在しないダンジョンの場合は、この連想配列のキーが存在しない
		$flags = array();
		foreach ($guerrilla_list as $guerrilla) {
			$dungeon_id = $guerrilla['dungeon_id'];
			
			if (($guerrilla['time_begin']) <= $current_time && ($current_time < $guerrilla['time_end'])) {
				$flags[$dungeon_id] = true;
			} else if (!isset($flags[$dungeon_id])) {
				$flags[$dungeon_id] = false;
			}
		}

		// ゲリラスケジュール判定済みのダンジョンマスタ情報を生成
		$list = array();
		foreach ($dungeon_list as $dungeon) {
			$dungeon_id = $dungeon['dungeon_id'];
			
			if (isset($flags[$dungeon_id]) && !$flags[$dungeon_id]) {
				continue;
			}
			
			$list[] = $dungeon;
		}
		
		return $list;
	}

	/**
	 * 指定ダンジョンIDの現在の時刻に当てはまるゲリラスケジュール情報を取得
	 * 
	 * @return ゲリラスケジュールマスタ情報
	 */
	function getMasterDungeonGuerrillaActive( $dungeon_id )
	{
		$param = array( $dungeon_id );
		$sql = "SELECT * FROM m_raid_guerrilla_schedule "
			 . "WHERE dungeon_id = ? AND time_begin <= CURRENT_TIME AND CURRENT_TIME < time_end";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}
	
	/**
	 * 指定ダンジョンのスケジュールから現在時刻に合致する情報の終了時間を取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * 
	 * @return 終了時間
	 */
	function getDungeonGuerrillaScheduleTimeEndById( $dungeon_id )
	{
		$param = array( $dungeon_id );
		$sql = "SELECT time_end FROM m_raid_guerrilla_schedule "
			 . "WHERE dungeon_id = ? "
			 . "AND time_begin <= CURRENT_TIME AND CURRENT_TIME < time_end";
		return $this->db_unit1_r->GetOne( $sql, $param );
	}

	/**
	 * 指定ダンジョンID・難易度・ダンジョンLVのダンジョン詳細マスタ情報を取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 * 
	 * @return ダンジョン詳細マスタ情報
	 */
	function getMasterDungeonDetail( $dungeon_id, $difficulty, $dungeon_lv )
	{
		$param = array( $dungeon_id, $difficulty, $dungeon_lv );
		$sql = "SELECT * FROM m_raid_dungeon_detail WHERE dungeon_id = ? AND difficulty = ? AND dungeon_lv = ?";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * 指定ダンジョンIDの難易度リストを取得（ダンジョンLV=1のレコードを取得）
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * 
	 * @return ダンジョン詳細マスタ難易度リスト情報
	 */
	function getMasterDungeonDetailDifficList( $dungeon_id )
	{
		$param = array( $dungeon_id );
		$sql = "SELECT difficulty FROM m_raid_dungeon_detail WHERE dungeon_id = ? AND dungeon_lv = 1 ORDER BY difficulty";
		return $this->db_unit1_r->db->GetCol( $sql, $param );
	}

	/**
	 * 指定ダンジョンIDの指定難易度でリストを取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * 
	 * @return ダンジョン詳細マスタ難易度リスト情報
	 */
	function getMasterDungeonDetailListByDiffic( $dungeon_id, $difficulty )
	{
		$param = array( $dungeon_id, $difficulty );
		$sql = "SELECT dd.dungeon_lv AS id, dd.* FROM m_raid_dungeon_detail dd WHERE dd.dungeon_id = ? AND dd.difficulty = ? ORDER BY dd.dungeon_lv";
		return $this->db_unit1_r->db->GetAssoc( $sql, $param );
	}

	/**
	 * 指定ダンジョンの開放条件マスタ情報を取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * 
	 * @return 開放条件マスタ情報
	 */
	function getDungeonOpenConditionByIdDiffic( $dungeon_id, $difficulty )
	{
		$param = array( $dungeon_id, $difficulty );
		$sql = "SELECT * FROM m_raid_dungeon_open_condition WHERE dungeon_id = ? AND difficulty = ?";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * 指定ダンジョンの開放条件マスタ情報を取得（dungeon_id複数指定ver）
	 * 
	 * @param int $dungeon_ids ダンジョンIDの配列
	 * 
	 * @return 開放条件マスタ情報
	 */
	function getDungeonOpenConditionByIds( $dungeon_ids )
	{
		$param = array();
		$where_in = array();
		foreach( $dungeon_ids as $dungeon_id )
		{
			$param[] = $dungeon_id;
			$where_in[] = '?';
		}
		$sql = "SELECT * FROM m_raid_dungeon_open_condition "
			 . "WHERE dungeon_id IN (".implode( ',', $where_in ).")";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * 開放条件マスタ情報を取得
	 * 
	 * @return 開放条件マスタ情報
	 */
	function getDungeonOpenCondition()
	{
		$sql = "SELECT * FROM m_raid_dungeon_open_condition";
		return $this->db_unit1_r->GetAll( $sql );
	}

	/**
	 * ユーザーが指定のダンジョンへの出撃資格があるかをチェックする
	 * 
	 * @param int $user_id ユーザーID
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 * @param int $last_lv 最後のレベルか？(ここだけダンジョン詳細マスタの値を渡される)この中でマスタ取得したくなかったため
	 * 
	 * @return boolean true:選択可能, false:選択不可
	 */
	function checkDungeonQualified( $user_id, $dungeon_id, $difficulty, $dungeon_lv, $last_lv )
	{
		$raid_user_m = $this->backend->getManager( 'RaidUser' );

		// 解放条件を取得
		$open_condition = $this->getDungeonOpenConditionByIdDiffic( $dungeon_id, $difficulty );
		if( empty( $open_condition ) === false )
		{	// 解放条件あり
			foreach( $open_condition as $opncnd )
			{
				$cond_lv = 0;	//クリア済みレベル

				// 条件に合うクリアデータを取得する
				$clear = $raid_user_m->getUserDungeonClear( $user_id, $opncnd['cond_dungeon_id'], $opncnd['cond_difficulty'] );
				$cnd_lv = ( empty( $clear ) === false ) ? $clear['dungeon_lv'] : 0;	// クリア済レベル
				if( $opncnd['cond_dungeon_lv'] > $cnd_lv )
				{	// １つでも条件を満たしてないものがあれば解放しない
					return false;
				}
			}
		}
		//開放条件に関係なく、必ず自分のレベル+1でないとNGにする(最後のレベルだったら同じでもOK)
		//ユーザの該当ダンジョン・難易度を取得
		$clear = $raid_user_m->getUserDungeonClear( $user_id, $dungeon_id, $difficulty );
		$cnd_lv = ( empty( $clear ) === false ) ? $clear['dungeon_lv'] : 0;	// クリア済レベル
		//指定レベルがクリア済みレベル+1ならOK
		if ($dungeon_lv == $cnd_lv + 1) return true;
		//指定レベルがクリア済みレベルと同じでも最後のレベルならOK
		if ($dungeon_lv == $cnd_lv && $last_lv == 1) return true;
		return false;
	}

	/**
	 * 指定ダンジョンのクリア報酬マスタ情報を取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 * 
	 * @return クリア報酬マスタ情報
	 */
	function getMasterDungeonClearReward( $dungeon_id, $difficulty, $dungeon_lv )
	{
		$param = array( $dungeon_id, $difficulty, $dungeon_lv );
	//	$sql = "SELECT * FROM m_raid_dungeon_clear_reward WHERE dungeon_id = ? AND difficulty = ? AND dungeon_lv = ?";
		$sql = "SELECT * FROM m_raid_dungeon_clear_reward_multi WHERE dungeon_id = ? AND difficulty = ? AND dungeon_lv = ?";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * 指定ダンジョンのクリア報酬マスタ情報を報酬タイプ別にグループ化して取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 * 
	 * @return クリア報酬マスタの連想配列
	 */
	function getMasterDungeonClearRewardGrouping( $dungeon_id, $difficulty, $dungeon_lv )
	{
		$reward = $this->getMasterDungeonClearReward( $dungeon_id, $difficulty, $dungeon_lv );
		if( is_null( $reward ) === true )
		{
			return null;
		}
		$buff = array(
			'clear'  => array(),
			'mvp'    => array(),
			'first'  => array(),
			'second' => array()
		);
		if( empty( $reward ) === true )
		{
			return $buff;
		}

		foreach( $reward as $r )
		{
			// 宝箱の時はreward_idをアイテムIDに変換する
			if( $r['reward_type'] == 3 )
			{
				switch ( $r['reward_id'] )
				{
					case 1:	// ブロンズチケット
						$r['reward_id'] = Pp_ItemManager::ITEM_TICKET_GACHA_FREE;
						break;
					case 2:	// ゴールドチケット
						$r['reward_id'] = Pp_ItemManager::ITEM_TICKET_GACHA_RARE;
						break;
					case 3:	// 合成メダル
						$r['reward_id'] = Pp_ItemManager::ITEM_MEDAL_SYNTHESIS;
						break;
					case 4:	// マジカルメダル
						$r['reward_id'] = Pp_ItemManager::ITEM_MEDAL_MAGICAL;
						break;
					case 5:	// バッジ拡張
						$r['reward_id'] = Pp_ItemManager::ITEM_BADGE_EXPAND;
						break;
				}
			}
			$temp = array(
				'reward_type' => $r['reward_type'],
				'reward_id'   => ( int )$r['reward_id'],
				'lv'          => ( int )$r['lv'],
				'reward_num'  => ( int )$r['reward_num'],
				'badge_expand'=> ( int )$r['badge_expand'],
				'badges'      => $r['badges'],
			);
			switch( $r['category'] )
			{
				// MVP報酬
				case self::CLEAR_REWARD_CATEGORY_MVP:
					$buff['mvp'][] = $temp;
					break;
				// 初回クリア報酬
				case self::CLEAR_REWARD_CATEGORY_1ST:
					$buff['first'][] = $temp;
					break;
				// ２回目以降クリア報酬
				case self::CLEAR_REWARD_CATEGORY_2ND:
					$buff['second'][] = $temp;
					break;
				// メンバー報酬
				case self::CLEAR_REWARD_CATEGORY_MEMBER:
					$buff['clear'][] = $temp;
					break;
				default:
					break;
			}
		}
		return $buff;
	}

	/**
	 * 指定ダンジョンの敵マスタ情報を取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 * @param array $emeny_ids 取得したい敵IDの配列（NULLor空配列の場合はダンジョンの全ての敵情報を取得）
	 * 
	 * @return 敵マスタ情報
	 */
	function getMasterEnemy( $dungeon_id, $difficulty, $dungeon_lv, $enemy_ids = null )
	{
		$param = array( $dungeon_id, $difficulty, $dungeon_lv );
		$sql = "SELECT * FROM m_raid_enemy WHERE dungeon_id = ? AND difficulty = ? AND dungeon_lv = ?";
		if( empty( $enemy_ids ) === false )
		{	// 敵IDが指定されている
			$where_in = array();
			foreach( $enemy_ids as $enemy_id )
			{
				$where_in[] = '?';
				$param[] = $enemy_id;
			}
			$sql .= "AND enemy_id IN (".implode( ',', $where_in ).") ";
		}
		$sql .= "ORDER BY enemy_id";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * 指定ダンジョンのボス敵マスタ情報を取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 * 
	 * @return 敵マスタ情報
	 */
	function getMasterBossEnemy( $dungeon_id, $difficulty, $dungeon_lv )
	{
		$param = array( $dungeon_id, $difficulty, $dungeon_lv );
		$sql = "SELECT * FROM m_raid_enemy WHERE dungeon_id = ? AND difficulty = ? AND dungeon_lv = ? AND boss_flag = 1";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * 指定ダンジョンの敵ドロップマスタ情報を取得
	 * 
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 * @param array $emeny_ids 取得したい敵IDの配列（NULLor空配列の場合はダンジョンの全ての敵ドロップ情報を取得）
	 * 
	 * @return 敵ドロップマスタ情報
	 */
	function getMasterEnemyDrop( $dungeon_id, $difficulty, $dungeon_lv, $enemy_ids = null )
	{
		$param = array( $dungeon_id, $difficulty, $dungeon_lv );
		$sql = "SELECT * FROM m_raid_enemy_drop WHERE dungeon_id = ? AND difficulty = ? AND dungeon_lv = ? ";
		if( empty( $enemy_ids ) === false )
		{
			$where_in = array();
			foreach( $enemy_ids as $enemy_id )
			{
				$where_in[] = '?';
				$param[] = $enemy_id;
			}
			$sql .= "AND enemy_id IN (".implode( ',', $where_in ).") ";
		}
		$sql .= "ORDER BY enemy_id";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * クエストデータを記録する
	 * 
	 * @param array 記録する情報の連想配列
	 * 
	 */
	function setQuestData( $columns )
	{
		return $this->db_unit1->db->AutoExecute( 'log_raid_quest', $columns, 'INSERT' );
	}

	/**
	 * クエスト情報を取得する
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 *
	 * @return クエストデータ文字列
	 */
	function getQuest( $party_id, $sally_no )
	{
		$param = array( $party_id, $sally_no );
		$sql = "SELECT * FROM log_raid_quest WHERE party_id = ? AND sally_no = ?";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * 最新のクエスト情報を取得する
	 * 
	 * @param int $party_id パーティID
	 *
	 * @return クエストデータ文字列
	 */
	function getQuestNewest( $party_id )
	{
		$param = array( $party_id );
		$sql = "SELECT * FROM log_raid_quest WHERE party_id = ? ORDER BY id DESC LIMIT 1";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * クエストデータを取得する
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 *
	 * @return クエストデータ文字列
	 */
	function getQuestData( $party_id, $sally_no )
	{
		$param = array( $party_id, $sally_no );
		$sql = "SELECT quest_data FROM log_raid_quest WHERE party_id = ? AND sally_no = ?";
		return $this->db_unit1_r->GetOne( $sql, $param );
	}

	/**
	 * クエストの結果を記録する
	 * 
	 * @param array 記録する情報の連想配列
	 * 
	 */
	function logQuestResult( $columns )
	{
		if( isset( $columns['map_no'] ) === false )
		{
			$columns['map_no'] = 0;
		}
		if( isset( $columns['date_created'] ) === false )
		{
			$columns['date_created'] = date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] );
		}
		return $this->db_unit1->db->AutoExecute( 'log_raid_quest_result', $columns, 'INSERT' );
	}

	/**
	 * クエストの結果を取得する
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 *
	 * @return array 結果データ
	 */
	function getQuestResult( $party_id, $sally_no )
	{
		$param = array( $party_id, $sally_no );
		$sql = "SELECT * FROM log_raid_quest_result WHERE party_id = ? AND sally_no = ?";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * プレイ一時データを取得する
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 * @param int $user_id ユーザID
	 *
	 * @return array プレイデータ（複数レコードも有り得る）
	 */
	function getTmpRaidResult( $party_id, $sally_no, $user_id )
	{
		$param = array( $party_id, $sally_no, $user_id );
		$sql = "SELECT * FROM tmp_raid_result WHERE party_id = ? AND sally_no = ? AND user_id = ?";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * プレイ一時データを保存する
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 * @param int $user_id ユーザID
	 * @param array $columns 一時保存するプレイデータの配列
	 *
	 * @return array プレイデータ（複数レコードも有り得る）
	 */
	function insertTmpRaidResult( $party_id, $sally_no, $user_id, $columns)
	{
		$param = array_values($columns);
		$param[] = $party_id;
		$param[] = $sally_no;
		$param[] = $user_id;
		$sql = "INSERT INTO tmp_raid_result(" . implode(",", array_keys($columns))
			 . ", party_id, sally_no, user_id, date_created)"
			 . " VALUES(" . str_repeat("?,", count($columns)) . "?,?,?,NOW())";
		if (!$this->db_unit1->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] SQL[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), $sql, __FILE__, __LINE__);
		}

		// 影響した行数を確認
		$affected_rows = $this->db_unit1->db->affected_rows();
		if( $affected_rows != 1 )
		{
			return Ethna::raiseError( "tmp_raid_result rows[%d]", E_USER_ERROR, $affected_rows );
		}
		return true;
	}

	/**
	 * プレイ一時データを更新する
	 * 
	 * @param int $ids プレイ一時データの更新対象レコードの管理IDの配列
	 * @param array $columns 更新するカラム名と設定値の連想配列
	 * 
	 * @return int|Ethna_Error 成功時:更新した行数, 失敗時:Ethna_Error
	 */
	function updateTmpRaidResultByIds( $ids, $columns )
	{
		if( empty( $ids ) === true )
		{	// 更新対象なし
			return 0;
		}

		$param = array();
		$set_str = array();
		foreach( $columns as $k => $v )
		{
			$set_str[] = "$k = ?";
			$param[] = $v;
		}
		$param = array_merge( $param, $ids );
		$where_in = array();
		foreach( $ids as $k => $v )
		{
			$where_in[] = '?';
		}

		$sql = "UPDATE tmp_raid_result SET ".implode( ',', $set_str )." "
			 . "WHERE id IN ( ".implode( ',', $where_in )." )";
		if( !$this->db_unit1->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] SQL[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), $sql, __FILE__, __LINE__);
		}

		// 影響した行数を返す
		return $this->db_unit1->db->affected_rows();
	}

	/**
	 * 獲得報酬一時テーブルに情報を記録する
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 * @param int $enemy_idx 報酬をドロップした敵のインデックス
	 * @param int $user_id 報酬を獲得したユーザーID
	 * @param int $reward_type 報酬種別（1:モンスター, 2:鍵, 3:宝箱）
	 * @param int $reward_id 報酬ID（モンスターIDとかアイテムIDとか）
	 * @param int $lv モンスターLV
	 * @param int $reward_num 獲得数量
	 * @param int $badge_expand 初期拡張バッジ数
	 * @param string $badges 初期装備バッジ
	 * 
	 * @return 
	 */
	function insertTmpRaidReward( $party_id, $sally_no, $enemy_idx, $user_id, $reward_type, $reward_id, $lv, $reward_num, $badge_expand = 0, $badges = '' )
	{
		$param = array( $party_id, $sally_no, $enemy_idx, $user_id, $reward_type, $reward_id, $lv, $reward_num, $badge_expand, $badges );
		$sql = "INSERT INTO tmp_raid_reward( party_id, sally_no, enemy_idx, user_id, reward_type, reward_id, lv, reward_num, badge_expand, badges ) "
			 . "VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
		if( !$this->db_unit1->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] SQL[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), $sql, __FILE__, __LINE__);
		}

		// 影響した行数を確認
		$affected_rows = $this->db_unit1->db->affected_rows();
		if( $affected_rows != 1 )
		{
			return Ethna::raiseError( "tmp_raid_reward rows[%d]", E_USER_ERROR, $affected_rows );
		}
		return true;
	}

	/**
	 * 獲得報酬一時テーブルに指定の敵のドロップ情報が記録されてるかを調べる
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 * @param int $enemy_idx 報酬をドロップした敵のインデックス
	 * 
	 * @return true:登録済, false:未登録
	 */
	function isExistTmpRaidReward( $party_id, $sally_no, $enemy_idx )
	{
		$param = array( $party_id, $sally_no, $enemy_idx );
		$sql = "SELECT * FROM tmp_raid_reward "
			 . "WHERE party_id = ? AND sally_no = ? AND enemy_idx = ? "
			 . "LIMIT 1";
		$ret = $this->db_unit1_r->GetRow( $sql, $param );
		if( is_null( $ret ) === true )
		{	// SQL実行エラー
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return ( empty( $ret ) === true ) ? false : true;
	}

	/**
	 * 獲得報酬一時テーブルから登録情報を取得する
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 * @param array $user_ids 取得するユーザーIDの配列（空の場合は全ユーザーの情報を取得）
	 * @param array $status 取得するレコードのステータス（空の場合は全ステータスの情報を取得）
	 * 
	 * @return
	 */
	function getTmpRaidReward( $party_id, $sally_no, $user_ids = null, $status = null )
	{
		$param = array( $party_id, $sally_no );
		$sql = "SELECT * FROM tmp_raid_reward WHERE party_id = ? AND sally_no = ? ";
		if( empty( $user_ids ) === false )
		{
			$where_in = array();
			foreach( $user_ids as $u )
			{
				$where_in[] = '?';
				$param[] = $u;
			}
			$sql .= "AND user_id IN (".implode( ',', $where_in ).") ";
		}
		if( empty( $status ) === false )
		{
			$where_in = array();
			foreach( $status as $s )
			{
				$where_in[] = '?';
				$param[] = $s;
			}
			$sql .= "AND status IN (".implode( ',', $where_in ).") ";
		}
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * 獲得報酬一時テーブルの情報を更新する
	 * 
	 * @param int $ids 獲得報酬一時テーブルの更新対象レコードの管理IDの配列
	 * @param array $columns 更新するカラム名と設定値の連想配列
	 * 
	 * @return int|Ethna_Error 成功時:更新した行数, 失敗時:Ethna_Error
	 */
	function updateTmpRaidRewardByIds( $ids, $columns )
	{
		if( empty( $ids ) === true )
		{	// 更新対象なし
			return 0;
		}

		$param = array();
		$set_str = array();
		foreach( $columns as $k => $v )
		{
			$set_str[] = "$k = ?";
			$param[] = $v;
		}
		$param = array_merge( $param, $ids );
		$where_in = array();
		foreach( $ids as $k => $v )
		{
			$where_in[] = '?';
		}

		$sql = "UPDATE tmp_raid_reward SET ".implode( ',', $set_str )." "
			 . "WHERE id IN ( ".implode( ',', $where_in )." )";
		if( !$this->db_unit1->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}

		// 影響した行数を返す
		return $this->db_unit1->db->affected_rows();
	}
	
	/**
	 * ダンジョン情報をAPI戻り値用のフォーマット（付加情報付き）で取得する
	 * 
	 * 戻り値は以下の書式の連想配列
	 * <code>
	 * array (
	 * 　"dungeon_id" => ダンジョンID,
	 * 　"dungeon_name" => ダンジョン名,
	 * 　"dungeon_rank" => 難易度,
	 * 　"dungeon_lv" => ダンジョンレベル,
	 * 　"boss_name" => ボスモンスター名,
	 * 　"clear_reward" => array( // (クリア報酬)  
	 * 　　array(
	 * 　　　"reward_type" : 報酬タイプ,
	 * 　　　"reward_id" : 報酬ID,
	 * 　　　"reward_num" : 報酬数量,
	 * 　　),
	 *     …繰り返し
	 * 　),
	 * 　"mvp_reward" => array( // (MVP報酬)
	 * 　　array(
	 * 　　　"reward_type" : 報酬タイプ,
	 * 　　　"reward_id" : 報酬ID,
	 * 　　　"reward_num" : 報酬数量,
	 * 　　),
	 *     …繰り返し
	 * 　),
	 * )
	 * </code>
	 * @param array $party パーティ（t_raid_partyの1行に相当する連想配列）
	 * @param string $dungeon_name ダンジョン名
	 * @return array ダンジョン情報
	 */
	function getDungeonInfoExForApiResponse($party, $dungeon_name)
	{
		$item_m =& $this->backend->getManager('Item');
		
		$dungeon_info = $this->getDungeonInfoForApiResponse($party, $dungeon_name);
		
		//報酬リスト取得
		$reward_list = $this->getMasterDungeonClearReward( $party['dungeon_id'], $party['difficulty'], $party['dungeon_lv'] );
		$reward_clr = array();
		$reward_mvp = array();
		//カテゴリが配列のキーになってないからループさせて１レコードずつカテゴリの値を見て変数に代入していく
		foreach ($reward_list as $reward) {
			//クリア報酬マスタのデータを変えたので変換が必要
			$reward_id = $reward['reward_id'];
			//モンスター
			if ($reward['reward_type'] == 1) {
				$reward_type = 2;
			}
			//宝箱（アイテム等）
			if ($reward['reward_type'] == 3) {
				switch ($reward['reward_id']) {
					case 1://ブロンズチケット
						$reward_type = 1;
						$reward_id = Pp_ItemManager::ITEM_TICKET_GACHA_FREE;
						break;
					case 2://ゴールドチケット
						$reward_type = 1;
						$reward_id = Pp_ItemManager::ITEM_TICKET_GACHA_RARE;
						break;
					case 3://合成メダル
						$reward_type = 3;
						$reward_id = Pp_ItemManager::ITEM_MEDAL_SYNTHESIS;
						break;
					case 4://マジカルメダル
						$reward_type = 4;
						$reward_id = Pp_ItemManager::ITEM_MEDAL_MAGICAL;
						break;
					case 5:	// バッジ拡張
						$reward_type = 1;
						$reward_id = Pp_ItemManager::ITEM_BADGE_EXPAND;
						break;
				}
			}
			//報酬カテゴリに応じて代入する配列を分ける
			if ($reward['category'] == Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_MEMBER) {
				$reward_clr[] = array(	//複数対応←$reward_clr = array(
					'reward_type' => $reward['reward_type'],
					'reward_id'   => $reward_id,
					'reward_num'  => $reward['reward_num'],
			//		'badge_expand'=> $reward['badge_expand'],
			//		'badges'      => $reward['badges'],
				);
			}
			if ($reward['category'] == Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_MVP) {
				$reward_mvp[] = array(	//複数対応←$reward_mvp = array(
					'reward_type' => $reward['reward_type'],
					'reward_id'   => $reward_id,
					'reward_num'  => $reward['reward_num'],
			//		'badge_expand'=> $reward['badge_expand'],
			//		'badges'      => $reward['badges'],
				);
			}
		}

		$dungeon_info['clear_reward'] = $reward_clr;
		$dungeon_info['mvp_reward'] = $reward_mvp;

		return $dungeon_info;
	}
	
	/**
	 * ダンジョン情報をAPI戻り値用のフォーマットで取得する
	 * 
	 * 戻り値は以下の書式の連想配列
	 * <code>
	 * array (
	 * 　"dungeon_id" => ダンジョンID,
	 * 　"dungeon_name" => ダンジョン名,
	 * 　"dungeon_rank" => 難易度,
	 * 　"dungeon_lv" => ダンジョンレベル,
	 * 　"boss_name" => ボスモンスター名,
	 *   "limit_time" => 制限時間（秒）,
	 * )
	 * </code>
	 * @param array $party パーティ（t_raid_partyの1行に相当する連想配列）
	 * @param string $dungeon_name ダンジョン名
	 * @return array ダンジョン情報
	 */
	function getDungeonInfoForApiResponse($party, $dungeon_name)
	{
		$monster_m =& $this->backend->getManager('Monster');
		
		static $dungeon_detail_assoc = array();
		static $boss_enemy_assoc = array();
		static $monster_assoc = array();
		
		$assoc_key = $party['dungeon_id'] . '_' . $party['difficulty'] . '_' . $party['dungeon_lv'];
		
		if (!array_key_exists($assoc_key, $dungeon_detail_assoc)) {
			//ダンジョン詳細情報取得
			$dungeon_detail_assoc[$assoc_key] = $this->getMasterDungeonDetail($party['dungeon_id'], $party['difficulty'], $party['dungeon_lv']);
		}
		
		if (!array_key_exists($assoc_key, $boss_enemy_assoc)) {
			//ボスデータ取得
			$boss_enemy_tmp = $this->getMasterBossEnemy( $party['dungeon_id'], $party['difficulty'], $party['dungeon_lv'] );
			$boss_enemy_assoc[$assoc_key] = $boss_enemy_tmp;
			
			//モンスター情報取得
			$monster_id_tmp = $boss_enemy_tmp['monster_id'];
			if (!array_key_exists($monster_id_tmp, $monster_assoc)) {
				$monster_assoc[$monster_id_tmp] = $monster_m->getMasterMonster($monster_id_tmp);
			}
		}

		//ダンジョン情報生成準備
		$dungeon_detail = $dungeon_detail_assoc[$assoc_key];
		$boss_enemy = $boss_enemy_assoc[$assoc_key];
		$monster_data = $monster_assoc[$boss_enemy['monster_id']];
		
		if (is_array($dungeon_detail)) {
			$limit_time = $dungeon_detail['limit_time'];
		} else {
			$limit_time = null;
		}
		
		//ダンジョン情報生成
		$dungeon_info = array(
			'dungeon_id'   => $party['dungeon_id'],
			'dungeon_name' => $dungeon_name,
			'dungeon_rank' => $party['difficulty'],
			'dungeon_lv'   => $party['dungeon_lv'],
			'boss_name'    => $monster_data['name_ja'],
			'limit_time'   => $limit_time,
		);

		return $dungeon_info;
	}
}
?>
