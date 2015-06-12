<?php
/**
 *  Pp_MissionManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'array_column.php';

/**
 *  Pp_MissionManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_MissionManager extends Ethna_AppManager
{
	/** マップタイプ：基本 */
	const STAGE_TYPE_NORMAL = 1;
	/** マップタイプ：イベント */
	const STAGE_TYPE_EVENT  = 2;

	/** エリアタイプ：基本 */
	const AREA_TYPE_NORMAL  = 1;
	/** エリアタイプ：スペシャル */
	const AREA_TYPE_SPECIAL = 2;
	/** エリアタイプ：唐之杜ミッション用 */
	const AREA_TYPE_KARANOMORI		= 3;
	/** エリアタイプ：曜日限定 */
	const AREA_TYPE_MONDAY			= 11;
	const AREA_TYPE_TUESDAY			= 12;
	const AREA_TYPE_WEDNESDAY		= 13;
	const AREA_TYPE_THURSDAY		= 14;
	const AREA_TYPE_FRIDAY			= 15;
	const AREA_TYPE_SATURDAY		= 16;
	const AREA_TYPE_SUNDAY			= 17;
	/** エリアタイプ：土日限定 */
	const AREA_TYPE_HOLIDAY			= 18;
	/** エリアタイプ：期間限定 */
	const AREA_TYPE_LIMITED			= 20;
	/** エリアタイプ：デイリー（1日1回のみ） */
	const AREA_TYPE_DAILY			= 30;

	/** エリアタイプ：曜日＆時間限定 */
	const AREA_TYPE_MONDAY_00_03	= 31;
	const AREA_TYPE_MONDAY_03_06	= 32;
	const AREA_TYPE_MONDAY_06_09	= 33;
	const AREA_TYPE_MONDAY_09_12	= 34;
	const AREA_TYPE_MONDAY_12_15	= 35;
	const AREA_TYPE_MONDAY_15_18	= 36;
	const AREA_TYPE_MONDAY_18_21	= 37;
	const AREA_TYPE_MONDAY_21_24	= 38;
	const AREA_TYPE_TUESDAY_00_03	= 41;
	const AREA_TYPE_TUESDAY_03_06	= 42;
	const AREA_TYPE_TUESDAY_06_09	= 43;
	const AREA_TYPE_TUESDAY_09_12	= 44;
	const AREA_TYPE_TUESDAY_12_15	= 45;
	const AREA_TYPE_TUESDAY_15_18	= 46;
	const AREA_TYPE_TUESDAY_18_21	= 47;
	const AREA_TYPE_TUESDAY_21_24	= 48;
	const AREA_TYPE_WEDNESDAY_00_03	= 51;
	const AREA_TYPE_WEDNESDAY_03_06	= 52;
	const AREA_TYPE_WEDNESDAY_06_09	= 53;
	const AREA_TYPE_WEDNESDAY_09_12	= 54;
	const AREA_TYPE_WEDNESDAY_12_15	= 55;
	const AREA_TYPE_WEDNESDAY_15_18	= 56;
	const AREA_TYPE_WEDNESDAY_18_21	= 57;
	const AREA_TYPE_WEDNESDAY_21_24	= 58;
	const AREA_TYPE_THURSDAY_00_03	= 61;
	const AREA_TYPE_THURSDAY_03_06	= 62;
	const AREA_TYPE_THURSDAY_06_09	= 63;
	const AREA_TYPE_THURSDAY_09_12	= 64;
	const AREA_TYPE_THURSDAY_12_15	= 65;
	const AREA_TYPE_THURSDAY_15_18	= 66;
	const AREA_TYPE_THURSDAY_18_21	= 67;
	const AREA_TYPE_THURSDAY_21_24	= 68;
	const AREA_TYPE_FRIDAY_00_03	= 71;
	const AREA_TYPE_FRIDAY_03_06	= 72;
	const AREA_TYPE_FRIDAY_06_09	= 73;
	const AREA_TYPE_FRIDAY_09_12	= 74;
	const AREA_TYPE_FRIDAY_12_15	= 75;
	const AREA_TYPE_FRIDAY_15_18	= 76;
	const AREA_TYPE_FRIDAY_18_21	= 77;
	const AREA_TYPE_FRIDAY_21_24	= 78;
	const AREA_TYPE_SATURDAY_00_03	= 81;
	const AREA_TYPE_SATURDAY_03_06	= 82;
	const AREA_TYPE_SATURDAY_06_09	= 83;
	const AREA_TYPE_SATURDAY_09_12	= 84;
	const AREA_TYPE_SATURDAY_12_15	= 85;
	const AREA_TYPE_SATURDAY_15_18	= 86;
	const AREA_TYPE_SATURDAY_18_21	= 87;
	const AREA_TYPE_SATURDAY_21_24	= 88;
	const AREA_TYPE_SUNDAY_00_03	= 91;
	const AREA_TYPE_SUNDAY_03_06	= 92;
	const AREA_TYPE_SUNDAY_06_09	= 93;
	const AREA_TYPE_SUNDAY_09_12	= 94;
	const AREA_TYPE_SUNDAY_12_15	= 95;
	const AREA_TYPE_SUNDAY_15_18	= 96;
	const AREA_TYPE_SUNDAY_18_21	= 97;
	const AREA_TYPE_SUNDAY_21_24	= 98;

	/** ミッションタイプ：メイン */
	const MISSION_TYPE_MAIN = 1;
	/** ミッションタイプ：サブ */
	const MISSION_TYPE_SUB = 2;
	/** ミッションタイプ：イベント */
	const MISSION_TYPE_EVENT = 3;
	/** ミッションタイプ：インセンティブ */
	const MISSION_TYPE_INCENTIVE = 4;

	/** エリアステータス：通常 */
	const AREA_STATUS_NORMAL = 0;
	/** エリアステータス：サイコハザード */
	const AREA_STATUS_HAZARD = 1;

	/** InGame終了ステータス */
	const RESULT_STATUS_CLEAR    = 1;	// クリア
	const RESULT_STATUS_LIFE     = 2;	// ライフ０による失敗
	const RESULT_STATUS_BATTERY  = 3;	// ドミネーターバッテリー切れによる失敗
	const RESULT_STATUS_TIMEOVER = 4;	// 時間切れによる失敗
	const RESULT_STATUS_RETIRE   = 5;	// プレイヤーの判断によるリタイア

	/** リザルト処理種別 */
	const RESULT_TYPE_FAIL   = 1;		// ミッション失敗
	const RESULT_TYPE_NORMAL = 2;		// ノーマルクリア
	const RESULT_TYPE_BEST   = 3;		// ベストクリア
	const RESULT_TYPE_RETIRE = 4;		// リタイア

	/** 唐之杜調査結果 */
	const KARANOMORI_STATUS_NONE = 1;	// 唐之杜通信なし
	const KARANOMORI_STATUS_FIND = 2;	// 犯人発見
	const KARANOMORI_STATUS_LOST = 3;	// 犯人ロスト

	// サイコハザードが解除されるLV
	const PSYCHO_HAZARD_CANCEL_LV = 3;

	// コンストラクタで取得されないDBのインスタンス
	protected $db_m_r = null;

	/**
	 * キャッシュ名を取得する
	 *
	 * @param string $key
	 * @param array $array
	 * @return array
	 */
	function getCacheKey( $key, $id = '' )
	{
		if ( empty( $key ))
		{
			return null;
		}

		$cache_key = "";

		switch ( $key )
		{
			case "stage":
				$cache_key = "m_stage_list";
				break;
			case "area":
				$cache_key = "m_area_list";
				break;
			case "area_debug":
				$cache_key = "m_area_list_debug";
				break;
			case "mission":
				$cache_key = "m_mission_list";
				break;
			case "stage_id":
				$cache_key = "m_stage__{$id}";
				break;
			case "area_id":
				$cache_key = "m_area__{$id}";
				break;
			case "area_id_debug":
				$cache_key = "m_area_debug__{$id}";
				break;
			case "mission_id":
				$cache_key = "m_mission__{$id}";
				break;
			case "area_in_stage_id__assoc":
				$cache_key = "m_area_in_stage__assoc__{$id}";
				break;
			case "area_in_stage_id_debug__assoc":
				$cache_key = "m_area_in_stage_debug__assoc__{$id}";
				break;
			case "mission_in_area_id__assoc":
				$cache_key = "m_mission_in_area__assoc__{$id}";
				break;
			case "next":
				$cache_key = "stage_area_mission_next_info__{$id}";
				break;
			case "first_mission_in_area_id":
				$cache_key = "first_mission_in_area_id__{$id}";
				break;
			case "sp_area_release";
				$cache_key = "sp_area_release";
				break;
		}

		return $cache_key;
	}

	/**
	 * DBのインスタンスを生成
	 *
	 * @return null
	 */
	function set_db()
	{
		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
	}

	/**
	 * マップマスター情報一覧を取得する
	 *
	 * @return array
	 */
	function getMasterStageList()
	{
		// DBのインスタンスを生成
		$this->set_db();

		// memcacheから取得してみる
		$cache_key = $this->getCacheKey( "stage" );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$sql = "SELECT m.stage_id AS id, m.* FROM m_stage m";
		$data = $this->db_m_r->db->getAssoc( $sql );

		// 取得したデータをキャッシュする
		$cache_m->set( $cache_key, $data );

		return $data;
	}

	/**
	 * エリアマスター情報一覧を取得する
	 *
	 * @return array
	 */
	function getMasterAreaList()
	{
		// DBのインスタンスを生成
		$this->set_db();

		$is_dbg = $this->config->get( 'is_debug_user' );

		// memcacheから取得してみる
		$key = ( $is_dbg ) ? "area_debug" : "area";
		$cache_key = $this->getCacheKey( $key );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$sql = "SELECT m.area_id AS id, m.* FROM m_area m";
		if ( !$is_dbg ) {
			$sql .= " WHERE debug = 0";
		}
		$data = $this->db_m_r->db->getAssoc( $sql );

		// 取得したデータをキャッシュする
		$cache_m->set( $cache_key, $data );

		return $data;
	}

	/**
	 * ミッションマスター情報一覧を取得する
	 *
	 * @return array
	 */
	function getMasterMissionList()
	{
		// DBのインスタンスを生成
		$this->set_db();

		// memcacheから取得してみる
		$cache_key = $this->getCacheKey( "mission" );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$sql = "SELECT m.mission_id AS id, m.* FROM m_mission m";
		$data = $this->db_m_r->db->GetAssoc( $sql );

		// 取得したデータをキャッシュする
		$cache_m->set( $cache_key, $data );

		return $data;
	}

	/**
	 * マップマスター情報を取得する
	 *
	 * @param int $stage_id
	 * @return array マップマスター情報1件の連想配列
	 */
	function getMasterStage( $stage_id )
	{
		// DBのインスタンスを生成
		$this->set_db();

		static $pool = array();

		// memcacheから取得してみる
		$cache_key = $this->getCacheKey( "stage_id", $stage_id );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		if ( !isset( $pool[$stage_id] )) {
			$param = array( $stage_id );
			$sql = "SELECT * FROM m_stage WHERE stage_id = ?";
			$pool[$stage_id] = $this->db_m_r->GetRow( $sql, $param );

			// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $pool[$stage_id] );
		}

		return $pool[$stage_id];
	}

	/**
	 * エリアマスター情報を取得する
	 *
	 * @param int $area_id
	 * @return array エリアマスター情報1件の連想配列
	 */
	function getMasterArea( $area_id )
	{
		// DBのインスタンスを生成
		$this->set_db();

		$is_dbg = $this->config->get( 'is_debug_user' );

		static $pool = array();

		// memcacheから取得してみる
		$key = ( $is_dbg ) ? "area_id_debug" : "area_id";
		$cache_key = $this->getCacheKey( $key, $area_id );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$index = $area_id.'_'.$key;
		if ( !isset( $pool[$index] )) {
			$param = array( $area_id );
			$sql = "SELECT * FROM m_area WHERE area_id = ?";
			if ( !$is_dbg ) {
				$sql .= " AND debug = 0";
			}
			$pool[$index] = $this->db_m_r->GetRow( $sql, $param );

			// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $pool[$index] );
		}

		return $pool[$index];
	}

	/**
	 * ミッションマスター情報を取得する
	 *
	 * @param int $mission_id
	 * @return array ミッションマスター情報1件の連想配列
	 */
	function getMasterMission( $mission_id )
	{
		// DBのインスタンスを生成
		$this->set_db();

		static $pool = array();

		// memcacheから取得してみる
		$cache_key = $this->getCacheKey( "mission_id", $mission_id );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		if ( !isset( $pool[$mission_id] )) {
			$param = array( $mission_id );
			$sql = "SELECT * FROM m_mission WHERE mission_id = ?";
			$pool[$mission_id] = $this->db_m_r->GetRow( $sql, $param );

			// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $pool[$mission_id] );
		}

		return $pool[$mission_id];
	}

	/**
	 * 指定ステージに属するエリアマスター情報一覧を取得する
	 *
	 * @return array
	 */
	function getMasterAreaListAssocByStageId( $stage_id )
	{
		// DBのインスタンスを生成
		$this->set_db();

		$is_dbg = $this->config->get( 'is_debug_user' );

		static $pool = array();

		// memcacheから取得してみる
		$key = ( $is_dbg ) ? "area_in_stage_id_debug__assoc" : "area_in_stage_id__assoc";
		$cache_key = $this->getCacheKey( $key, $stage_id );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$param = array( $stage_id );
		$sql = "SELECT area_id, m_area.* FROM m_area WHERE stage_id = ?";
		if ( !$is_dbg ) {
			$sql .= " AND debug = 0";
		}
		$data = $this->db_m_r->db->GetAssoc( $sql, $param );

		// 取得したデータをキャッシュする
		if( !empty( $data ))
		{
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * 指定エリアに属するミッションマスター情報一覧を取得する
	 *
	 * @return array
	 */
	function getMasterMissionListAssocByAreaId( $area_id )
	{
		// DBのインスタンスを生成
		$this->set_db();

		static $pool = array();

		// memcacheから取得してみる
		$cache_key = $this->getCacheKey( "mission_in_area_id__assoc", $area_id );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$param = array( $area_id );
		$sql = "SELECT mission_id, m_mission.* FROM m_mission WHERE area_id = ?";
		$data = $this->db_m_r->db->GetAssoc( $sql, $param );

		// 取得したデータをキャッシュする
		if( !empty( $data ))
		{
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * 指定エリアに属するミッションマスター情報一覧を取得する（複数エリア指定版）
	 *
	 * @return array
	 */
	function getMasterMissionListAssocByAreaIds( $area_ids )
	{
		// DBのインスタンスを生成
		$this->set_db();

		$param = array();
		$where_in = array();
		foreach( $area_ids as $area_id )
		{
			$param[] = $area_id;
			$where_in[] = '?';
		}
		$sql = "SELECT mission_id, m_mission.* FROM m_mission "
			 . "WHERE area_id IN ( ".implode( ',', $where_in )." )";
		return $this->db_m_r->db->GetAssoc( $sql, $param );
	}

	/**
	 * 指定エリアに属する最初のミッションのマスター情報を取得する
	 *
	 * @return array
	 */
	function getMasterFirstMissionByAreaId( $area_id )
	{
		// DBのインスタンスを生成
		$this->set_db();

		static $pool = array();

		// memcacheから取得してみる
		$cache_key = $this->getCacheKey( "first_mission_in_area_id", $area_id );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$param = array( $area_id, $area_id );
		$sql = "SELECT * FROM m_mission "
			 . "WHERE area_id = ? AND mission_no = "
			 . "( SELECT MIN( mission_no ) as mission_no FROM m_mission WHERE area_id = ? )";
		$data = $this->db_m_r->GetRow( $sql, $param );

		// 取得したデータをキャッシュする
		if( !empty( $data ))
		{
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * イベントエリアマスタ情報を取得
	 *
	 * @return array
	 */
	function getMasterEventAreaListAssoc()
	{
		$tbl = array(
			0 => array(
				self::AREA_TYPE_SUNDAY_00_03, self::AREA_TYPE_SUNDAY_03_06, self::AREA_TYPE_SUNDAY_06_09, self::AREA_TYPE_SUNDAY_09_12,
				self::AREA_TYPE_SUNDAY_12_15, self::AREA_TYPE_SUNDAY_15_18, self::AREA_TYPE_SUNDAY_18_21, self::AREA_TYPE_SUNDAY_21_24
			),
			1 => array(
				self::AREA_TYPE_MONDAY_00_03, self::AREA_TYPE_MONDAY_03_06, self::AREA_TYPE_MONDAY_06_09, self::AREA_TYPE_MONDAY_09_12,
				self::AREA_TYPE_MONDAY_12_15, self::AREA_TYPE_MONDAY_15_18, self::AREA_TYPE_MONDAY_18_21, self::AREA_TYPE_MONDAY_21_24
			),
			2 => array(
				self::AREA_TYPE_TUESDAY_00_03, self::AREA_TYPE_TUESDAY_03_06, self::AREA_TYPE_TUESDAY_06_09, self::AREA_TYPE_TUESDAY_09_12,
				self::AREA_TYPE_TUESDAY_12_15, self::AREA_TYPE_TUESDAY_15_18, self::AREA_TYPE_TUESDAY_18_21, self::AREA_TYPE_TUESDAY_21_24
			),
			3 => array(
				self::AREA_TYPE_WEDNESDAY_00_03, self::AREA_TYPE_WEDNESDAY_03_06, self::AREA_TYPE_WEDNESDAY_06_09, self::AREA_TYPE_WEDNESDAY_09_12,
				self::AREA_TYPE_WEDNESDAY_12_15, self::AREA_TYPE_WEDNESDAY_15_18, self::AREA_TYPE_WEDNESDAY_18_21, self::AREA_TYPE_WEDNESDAY_21_24
			),
			4 => array(
				self::AREA_TYPE_THURSDAY_00_03, self::AREA_TYPE_THURSDAY_03_06, self::AREA_TYPE_THURSDAY_06_09, self::AREA_TYPE_THURSDAY_09_12,
				self::AREA_TYPE_THURSDAY_12_15, self::AREA_TYPE_THURSDAY_15_18, self::AREA_TYPE_THURSDAY_18_21, self::AREA_TYPE_THURSDAY_21_24
			),
			5 => array(
				self::AREA_TYPE_FRIDAY_00_03, self::AREA_TYPE_FRIDAY_03_06, self::AREA_TYPE_FRIDAY_06_09, self::AREA_TYPE_FRIDAY_09_12,
				self::AREA_TYPE_FRIDAY_12_15, self::AREA_TYPE_FRIDAY_15_18, self::AREA_TYPE_FRIDAY_18_21, self::AREA_TYPE_FRIDAY_21_24
			),
			6 => array(
				self::AREA_TYPE_SATURDAY_00_03, self::AREA_TYPE_SATURDAY_03_06, self::AREA_TYPE_SATURDAY_06_09, self::AREA_TYPE_SATURDAY_09_12,
				self::AREA_TYPE_SATURDAY_12_15, self::AREA_TYPE_SATURDAY_15_18, self::AREA_TYPE_SATURDAY_18_21, self::AREA_TYPE_SATURDAY_21_24
			)
		);

		// DBのインスタンスを生成
		$this->set_db();

		// 現在の日時を取得
		$date = getdate( time());		// 現在の日付情報を取得
		$weekday = $date['wday'];		// 曜日の取得（0:日...6:土）
		$hours = $date['hours'];		// 時間の取得

		$param = array();
		$in_type = array();
		switch( $weekday )
		{
			case 0:	// 日曜日
				$param[] = self::AREA_TYPE_SUNDAY;
				$param[] = self::AREA_TYPE_HOLIDAY;
				$in_type = '?, ?';
				break;
			case 1:	// 月曜日
				$param[] = self::AREA_TYPE_MONDAY;
				$in_type = '?';
				break;
			case 2:	// 火曜日
				$param[] = self::AREA_TYPE_TUESDAY;
				$in_type = '?';
				break;
			case 3:	// 水曜日
				$param[] = self::AREA_TYPE_WEDNESDAY;
				$in_type = '?';
				break;
			case 4:	// 木曜日
				$param[] = self::AREA_TYPE_THURSDAY;
				$in_type = '?';
				break;
			case 5:	// 金曜日
				$param[] = self::AREA_TYPE_FRIDAY;
				$in_type = '?';
				break;
			case 6:	// 土曜日
				$param[] = self::AREA_TYPE_SATURDAY;
				$param[] = self::AREA_TYPE_HOLIDAY;
				$in_type = '?, ?';
				break;
		}
		$param[] = $tbl[$weekday][((int)$hours/3)];	// 曜日・時間限定
		$param[] = self::AREA_TYPE_LIMITED;			// 期間限定
		$param[] = self::AREA_TYPE_DAILY;			// デイリー
		$in_type .= ', ?, ?, ?';

		$sql = "SELECT ma.area_id, ma.* FROM m_area as ma "
			 . "WHERE type IN ( {$in_type} ) AND date_start <= NOW() AND NOW() <= date_end";
		$data = $this->db_m_r->db->GetAssoc( $sql, $param );

		return $data;
	}

	/**
	 * 現在開催中のエリアIDを取得
	 *
	 * @return array
	 */
	function getHeldAreaIds()
	{
		// DBのインスタンスを生成
		$this->set_db();

		$is_dbg = $this->config->get( 'is_debug_user' );

		// 開催中の通常エリアのエリアIDを取得
		$sql = "SELECT area_id FROM m_area WHERE type < 10 AND date_start <= NOW() AND NOW() < date_end";
		if ( !$is_dbg ) {
			$sql .= " AND debug = 0";
		}

		$area_ids = $this->db_m_r->GetAll( $sql );

		// 開催中のイベントエリアマスタを取得
		$evt_area = $this->getMasterEventAreaListAssoc();

		$data = array();
		foreach( $area_ids as $row )
		{
			$data[] = $row['area_id'];
		}
		foreach( $evt_area as $area_id => $row )
		{
			$data[] = $area_id;
		}
		return $data;
	}

	/**
	 * マスターマップ情報にユーザマップ情報を付加した一覧を取得する
	 *
	 * @param int $pp_id
	 * @return array
	 */
	function getMasterStageByUserList( $pp_id )
	{
		$m_stage_list = $this->getMasterStageList();

		$param = array( $pp_id );
		$sql = "SELECT stage_id, karanomori_report"
			. " FROM ut_user_stage"
			. " WHERE pp_id = ?";
		$user_stage_assoc = $this->db_r->db->GetAssoc( $sql, $param );

		// エリアストレスの平均値を取得
		$average_area_stress_assoc = $this->getAverageAreaStressAssoc( $pp_id );

		// 配列結合
		foreach ( $m_stage_list as &$m_stage )
		{
			$m_stage['karanomori_report'] = $user_stage_assoc[$m_stage['stage_id']]['karanomori_report'];
			$m_stage['avg_area_stress'] = $average_area_stress_assoc[$m_stage['stage_id']]['avg_area_stress'];
		}

		return $m_stage_list;
	}

	/**
	 * マップ毎のエリアストレス平均の一覧を取得する
	 *
	 * 解放されていないエリア、スペシャルエリアは対象に含めない
	 * @param int $pp_id
	 * @return array
	 */
	function getAverageAreaStressAssoc( $pp_id, $dsn = "db_r" )
	{
		/*
		$m_area_list = $this->getMasterAreaList();

		$param = array( $pp_id );
		$where_stage_id_in = array();

		foreach ( $m_area_list as $m_area )
		{
			if ( $m_area['type'] == self::AREA_TYPE_NORMAL )
			{
				$param[] = $m_area['stage_id'];
				$where_stage_id_in[] = "?";
			}
		}

		$sql = "SELECT stage_id, avg(area_stress) as avg_area_stress"
			. " FROM ut_user_area"
			. " WHERE pp_id = ?"
			. " AND stage_id IN (" . implode(',', $where_stage_id_in) . ")"
			. " GROUP BY stage_id";
		$data = $this->db_r->db->GetAssoc( $sql, $param );
		*/

		$m_area_list = $this->getMasterAreaList();

		$param = array( $pp_id );
		$where_in = array();
		foreach( $m_area_list as $m_area )
		{
			if( $m_area['type'] != self::AREA_TYPE_NORMAL )
			{
				continue;
			}
			$param[] = $m_area['area_id'];
			$where_in[] = '?';
		}
		$sql = "SELECT area_id, area_stress FROM ut_user_area "
			 . "WHERE pp_id = ? AND area_id IN ( ".implode( ',', $where_in )." )";
		$u_area_list = $this->$dsn->GetAll( $sql, $param );
		if( $u_area_list === false )
		{
			return null;
		}
		$buff = array();
		if( !empty( $u_area_list ))
		{
			foreach( $u_area_list as $row )
			{
				$stage_id = $m_area_list[$row['area_id']]['stage_id'];
				if( !array_key_exists( $stage_id, $buff ))
				{
					$buff[$stage_id] = array(
						'area_stress_sum' => 0,
						'count' => 0
					);
				}
				$buff[$stage_id]['area_stress_sum'] += $row['area_stress'];
				$buff[$stage_id]['count']++;
			}
		}

		$data = array();
		if( !empty( $buff ))
		{
			foreach( $buff as $stage_id => $row )
			{
				$avg = ( float )( $row['area_stress_sum'] / $row['count'] );
//				$data[$stage_id] = array( 'avg_area_stress' => $avg );
				$data[$stage_id] = round( $avg, 1 );
			}
		}
		return $data;
	}

	/**
	 * マスターエリア情報にユーザエリア情報を付加した一覧を取得する
	 *
	 * @param int $pp_id
	 * @return array
	 */
	function getMasterAreaByUserList( $pp_id )
	{
		$m_area_list = $this->getMasterAreaList();

		$param = array( $pp_id );
		$sql = "SELECT area_id, area_stress, status"
			. " FROM ut_user_area"
			. " WHERE pp_id = ?";
		$user_area_assoc = $this->db_r->db->GetAssoc( $sql, $param );

		// 配列結合
		foreach ( $m_area_list as &$m_area )
		{
			foreach ( $user_area_assoc[$m_area['area_id']] as $key => $value )
			{
				$m_area[$key] = $user_area_assoc[$m_area['area_id']][$key];
			}
		}

		return $m_area_list;
	}

	/**
	 * マスターミッション情報にユーザミッション情報を付加した一覧を取得する
	 *
	 * @param int $pp_id
	 * @return array
	 */
	function getMasterMissionByUserList( $pp_id )
	{
		$m_mission_list = $this->getMastermissionList();

		$param = array( $pp_id );
		$sql = "SELECT mission_id, best_clear, normal_clear, fail"
			. " FROM ut_user_mission"
			. " WHERE pp_id = ?";
		$user_mission_assoc = $this->db_r->db->GetAssoc( $sql, $param );

		// 配列結合
		foreach ( $m_mission_list as &$m_mission )
		{
			foreach ( $user_mission_assoc[$m_mission['mission_id']] as $key => $value )
			{
				$m_mission[$key] = $user_mission_assoc[$m_mission['mission_id']][$key];
			}
		}

		return $m_mission_list;
	}

	/**
	 * ユーザミッション情報一覧を取得する
	 *
	 * @param int $pp_id
	 * @return array
	 */
	function getUserMissionList( $pp_id )
	{
		// DBのインスタンスを生成
		$this->set_db();

		$param = array( $pp_id );
		$sql = "SELECT mi.mission_id, a.stage_id, mi.area_id"
			. " FROM m_mission mi"
			. " LEFT JOIN m_area a ON mi.area_id = a.area_id";
		$m_mission_assoc = $this->db_m_r->db->GetAssoc( $sql, $param );

		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_mission WHERE pp_id = ?";
		$user_mission_list = $this->db_r->GetAll( $sql, $param );

		// 配列結合
		foreach ( $user_mission_list as &$user_mission )
		{
			foreach ( $m_mission_assoc[$user_mission['mission_id']] as $key => $value )
			{
				$user_mission[$key] = $m_mission_assoc[$user_mission['mission_id']][$key];
			}
		}

		return $user_mission_list;
	}

	/**
	 * エリアストレス平均値補正を取得
	 *
	 * @param float $avg エリアストレス平均値
	 * @return int エリアストレス平均値補正
	 */
	function getAreaStressAvgCorrection( $avg )
	{
		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		// テーブルでは下２桁が小数値なので100倍した値を引数にする
		$param = array( floor( $avg * 100 ));
		$sql = "SELECT correction_value FROM m_area_stress_avg_correction "
			 . "WHERE area_stress_avg >= ? ORDER BY area_stress_avg LIMIT 1";
		$value = $this->db_m_r->GetOne( $sql, $param );
		if( empty( $value ))
		{	// 取得できない場合は最大値を取得することにする
			$sql = "SELECT correction_value FROM m_area_stress_avg_correction "
				 . "ORDER BY area_stress_avg DESC LIMIT 1";
			$value = $this->db_m_r->GetOne( $sql, $param );
		}

		return $value;
	}

	/**
	 * 指定ミッションの次のステージ・エリア・ミッションを取得
	 *
	 * @param mission_id ミッションID
	 *
	 * @return array ステージID,エリアID,ミッションIDの配列
	 */
	function getNextMainMissionIdInfo( $mission_id )
	{
		// memcacheから取得してみる
		$cache_key = $this->getCacheKey( "next", $mission_id );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 1 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// 指定のミッションのマスタ情報を取得
		$m = $this->getMasterMission( $mission_id );
		if( empty( $m ))
		{	// 取得エラー
			return null;
		}
		if( $m['type'] == self::MISSION_TYPE_MAIN )
		{
			$stage_type = self::STAGE_TYPE_NORMAL;
		}
		else if( $m['type'] == self::MISSION_TYPE_EVENT )
		{
			$stage_type = self::STAGE_TYPE_EVENT;
		}
		else
		{	// ノーマルエリア／イベントエリアでなければ次のステージ・エリア・ミッション情報はない
			return array();
		}

		// 返却値
		$data = array();

		// 指定のミッションが所属するエリアマスタ情報を取得
		$a = $this->getMasterArea( $m['area_id'] );
		if( empty( $a ))
		{	// 取得エラー
			return null;
		}
		if(( $a['last'] == 0 )||( $m['last'] == 0 ))
		{	// 最終エリアではない
			$data['stage_id'] = $a['stage_id'];	// 現在のステージIDをセット
		}
		else
		{	// 最終エリアの最終ミッションなら次のステージを取得
			if( $stage_type == self::STAGE_TYPE_EVENT )
			{	// イベントエリアの場合は仕様として次のステージは存在しない
				return array();
			}

			$param = array( $a['stage_id'], $stage_type );
			$sql = "SELECT stage_id FROM m_stage WHERE stage_id > ? AND type = ? ORDER BY stage_id LIMIT 1";
			$stage_id = $this->db_m_r->GetOne( $sql, $param );
			if( $stage_id === false )
			{	// 取得エラー
				return null;
			}
			else if( empty( $stage_id ))
			{	// 次のステージはない
				return array();
			}
			$data['stage_id'] = $stage_id;		// 次のステージIDをセット
		}

		// 次のエリアIDを取得
		if( $m['last'] == 0 )
		{	// 最終ミッションでなければエリアは変わらず
			$data['area_id'] = $a['area_id'];
		}
		else
		{	// 最終ミッションなら次のエリアを取得
			$area_no = (( $data['stage_id'] == $a['stage_id'] )&&( $a['last'] == 0 )) ? ( $a['area_no'] + 1 ) : 1;
			$param = array( $data['stage_id'], $area_no, $a['type'] );
			$sql = "SELECT area_id FROM m_area WHERE stage_id = ? AND area_no = ? AND type = ?";
			$area_id = $this->db_m_r->GetOne( $sql, $param );
			if( empty( $area_id ))
			{	// 取得エラー
				return null;
			}
			$data['area_id'] = $area_id;			// 次のエリアIDをセット
		}

		// 次のミッションを取得
		$mission_no = ( $m['last'] == 0 ) ? ( $m['mission_no'] + 1 ) : 1;
		$param = array( $data['area_id'], $mission_no, $m['type'] );
		$sql = "SELECT mission_id FROM m_mission WHERE area_id = ? AND mission_no = ? AND type = ?";
		$mission_id = $this->db_m_r->GetOne( $sql, $param );
		if( empty( $mission_id ))
		{	// 取得エラー
			return null;
		}
		$data['mission_id'] = $mission_id;		// 次のミッションIDをセット

		// 取得したデータをキャッシュする
		$cache_m->set( $cache_key, $data );

		return $data;
	}

	/**
	 * スペシャルミッション解放マスタ取得
	 *
	 * @param 
	 *
	 * @return boolean|object
	 */
	function getMasterSpAreaRelease()
	{
		// memcacheから取得してみる
		$cache_key = $this->getCacheKey( "sp_area_release" );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// DBのインスタンスを生成
		$this->set_db();

		$sql = "SELECT * FROM m_sp_area_release";
		$res = $this->db_m_r->GetAll( $sql );
		if( !$res )
		{
			return null;
		}
		$data = array();
		if( !empty( $res ))
		{
			foreach( $res as $row )
			{
				$data[$row['area_id']] = explode( ',', $row['release_condition'] );
			}

			// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}


	/**
	 * スペシャルミッション解放チェック
	 *
	 * @param photo_id 新規獲得フォトID
	 *
	 * @return array 新規解放されたエリアの配列
	 */
	function checkSpAreaRelease( $pp_id, $photo_id )
	{
		$photo_m =& $this->backend->getManager( 'Photo' );
		$release_area = array();

		// 解放条件マスタ情報取得
		$data = $this->getMasterSpAreaRelease();
		if( empty( $data ))
		{	// 取得エラー
			return null;
		}

		foreach( $data as $area_id => $row )
		{
			if( !in_array( $photo_id, $row ))
			{	// 獲得フォトが解放条件にないエリアなら飛ばす
				continue;
			}

			// 他の条件フォトの所有チェック
			$user_photo = $photo_m->getUserPhotoByPhotoIds( $pp_id, $row );
			if( $user_photo == false )
			{	// 取得エラー
				return null;
			}

			// 条件フォトが全部揃っているならエリア解放
			if( count( $user_photo ) == count( $row ))
			{
				$release_area[] = $area_id;
			}
		}
		return $release_area;
	}

	/**
	 * ミッション終了報酬アイテムの取得
	 *
	 * @param mission_id ミッションID
	 * @param result_type ミッション結果
	 *
	 * @return boolean|array 終了報酬
	 */
	function getResultGivenItem( $mission_id, $result_type )
	{
		// DBのインスタンスを生成
		$this->set_db();

		$param = array( $mission_id, $result_type );
		$sql = "SELECT category, item_value, num FROM m_result_given_item "
			 . "WHERE mission_id = ? AND result_type = ? AND date_start <= NOW() AND NOW() <= date_end";
		$data = $this->db_m_r->GetAll( $sql, $param );
		if( !$data || Ethna::isError( $data ))
		{
			return false;
		}
		return $data;
	}
}

