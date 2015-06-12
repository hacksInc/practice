<?php
/**
 *  Pp_ItemManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CharacterManager.php';

/**
 *  Pp_ItemManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_ItemManager extends Ethna_AppManager
{
	protected $db_m_r;

	// ITEM_ID＝アイテム種別
	const ITEM_ID_PHOTO_FILM		= 1001;	// フォトフィルム
	const ITEM_ID_THERAPY_TICKET	= 1002;	// セラピー受診命令書
	const ITEM_ID_RESERVE_DOMINATOR	= 1003;	// 予備ドミネーター
	const ITEM_ID_DRONE				= 1004;	// 巡査ドローン
	const ITEM_ID_PORTAL_POINT		= 1005;	// ポータルポイント？
	const ITEM_ID_PHOTO				= 1006;	// フォト

	// アイテム名
	var $ITEM_ID_OPTIONS = array(
		self::ITEM_ID_PHOTO_FILM => 'フォトフィルム',
		self::ITEM_ID_THERAPY_TICKET => 'セラピー受診命令書',
		self::ITEM_ID_RESERVE_DOMINATOR => '予備ドミネーター',
		self::ITEM_ID_DRONE => '巡査ドローン',
		self::ITEM_ID_PORTAL_POINT => 'ポータルポイント',
		self::ITEM_ID_PHOTO => 'フォト',
	);

    /**
     * アイテムマスターから指定の情報を取得
	 *
	 * @param int $item_id 指定アイテムID
	 * @return array 取得データ
     */
	function getMasterItem ( $item_id )
	{
		$list = $this->getMasterItemList();

		return $list[$item_id];
	}

	/**
	 * ショップマスターの全件を取得
	 *
	 * @return array 取得データ
	 */
	function getMasterItemList ()
	{
		// キャッシュが存在していればそれを返す
		$cache_key = "m_item_" . __FUNCTION__;
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ) )
		{	// キャッシュから取得できた
			return $cache_data;
		}

		if ( !$this->db_m_r ) {
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		$sql = "SELECT m.item_id AS id, m.* FROM m_item m ORDER BY item_id ASC";
		$result = $this->db_m_r->query( $sql );
		if ( Ethna::isError( $result ) )
		{
			Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, $this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			return false;
		}

		$list = $result->GetAssoc();

		if( count( $list ) > 0 )
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $list );
		}

		return $list;
	}

	/**
	 * アイテム情報を取得する（ラッパー）
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $item_id アイテムID
	 * @param int $master 取得先
	 * @return array 取得データ
	 */
	function getUserItem ( $pp_id, $item_id, $dsn = "db_r" )
	{
		$user_m =& $this->backend->getManager( "User" );

		return $user_m->getUserItem( $pp_id, $item_id, $dsn );
	}

	/**
	 * アイテム所持数が上限を超えるか？
	 *
	 * @param int $pp_id
	 * @param int $item_id
	 * @param int $num_add 増やす個数
	 * @return bool 真偽
	 */
	function isUserItemNumOutbalance ( $pp_id, $item_id, $num_add = 0 )
	{
		// 個数チェックは常にマスターを参照
		$user_item = $this->getUserItem( $pp_id, $item_id, true );
		$num = $user_item ? $user_item['num'] : 0;

		if ( $num_add ) {
			$num += $num_add;
		}

		$master_item = $this->getMasterItem( $item_id );

		return ( $num > $master_item['maximum'] );
	}

	/**
	 * アイテム一覧を取得する（ラッパー）
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $dsn DSN文字列
	 * @return array
	 */
	function getUserItemList ( $pp_id, $dsn = "db_r" )
	{
		$user_m =& $this->backend->getManager( "User" );

		return $user_m->getUserItemList( $pp_id, $dsn );
	}

	/**
	 * アイテムを増減させる（0～上限まで）（ラッパー）
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $item_id アイテムID
	 * @param int $num 増減値
	 * @return bool|object 処理結果
	 */
	function updateUserItem ( $pp_id, $item_id, $num )
	{
		$user_m =& $this->backend->getManager( "User" );

		return $user_m->updateUserItem( $pp_id, $item_id, $num );
	}

	/**
	 * アイテムの使用
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $item_id アイテムID
	 * @param array $item_data_array アイテム必要データ（空の場合もある）
	 * @param int $num 消費個数
	 * @param array &$result_data 処理後のデータ（リファレンス渡し）
	 * @return bool 処理結果
	 */
	function useItem ( $pp_id, $item_id, $item_data_array = array(), $num = 1, &$result_data = null, &$result_code = SDC_HTTP_500 )
	{
		// 消費個数がおかしい場合は処理しない（デュープに繋がるので）
		if ( $num <= 0 ) return true;

		switch ( $item_id ) {
			case self::ITEM_ID_RESERVE_DOMINATOR:	// 予備ドミネーター
				// 予備ドミネーターはInGameデータの回復？
				// アイテムを消費した＝回復したを返せばよい？
				// とりあえず、個数のみ減らすだけ。ここでは処理しない
				break;

			case self::ITEM_ID_PHOTO_FILM:	// フォトフィルム
				// フォトフィルムは直接消費できない。ガチャ機能でのみ消費するため、ここではエラーを返す
				return false;
				break;

			case self::ITEM_ID_THERAPY_TICKET:	// セラピー受診命令書
				// 対象キャラクターの犯罪係数を減少（下限値に再設定）
				$character_m =& $this->backend->getManager( "Character" );

				$m_chara = $character_m->getMasterCharacter( $item_data_array['chara_id'] );

				// もしマスターデータが取得できないかエラー吐いたら終了
				if ( !$m_chara || Ethna::isError( $m_chara ) ) {
					return false;
				}

				// 受信者のパラメータを取得
				if( $item_data_array['chara_id'] == Pp_CharacterManager::CHARACTER_ID_PLAYER )
				{
					$user_m =& $this->backend->getManager( "User" );
					$result = $user_m->getUserGame( $pp_id );
				}
				else
				{
					$result = $character_m->getUserCharacter( $pp_id, $item_data_array['chara_id'] );
				}
				if ( !$result || Ethna::isError( $result ) ) {
					return false;
				}

				// 既に犯罪係数が下限値の場合は実行できない
				if ( $result['crime_coef'] <= $m_chara['crime_coef_lower_limit'] )
				{
					$result_code = SDC_THERAPY_ORDER_PARAM_ERROR;
					return false;
				}

				// 犯罪係数を下限値に更新
				$columns = array(
					"crime_coef" => $m_chara['crime_coef_lower_limit'],
				);
				if( $item_data_array['chara_id'] == Pp_CharacterManager::CHARACTER_ID_PLAYER )
				{
					$result = $user_m->updateUserGame( $pp_id, $columns );
				}
				else
				{
					$result = $character_m->updateUserCharacter( $pp_id, $item_data_array['chara_id'], $columns );
				}
				if ( !$result || Ethna::isError( $result ) ) {
					return false;
				}

				$result_data = array(
					"chara_id"		=> $item_data_array['chara_id'],
					"crime_coef"	=> $m_chara['crime_coef_lower_limit']
				);
				break;

			case self::ITEM_ID_DRONE:	// 巡査ドローン
				// 対象エリアのエリアストレスを1レベル分減少
				$mission_m =& $this->backend->getManager( "Mission" );
				$user_m =& $this->backend->getManager( "User" );


				$area = $user_m->getUserArea( $pp_id, $item_data_array['area_id'], "db" );

				$area_status = $area['status'];
				if ( $area['area_stress'] < 0 ) {
					// 下限値は0
					$result_code = SDC_MISSION_AREA_STRESS_SHORTAGE;
					return false;
				} else if (( $area['status'] == Pp_MissionManager::AREA_STATUS_HAZARD )&&
					(( $area['area_stress'] - 1 ) <= Pp_MissionManager::PSYCHO_HAZARD_CANCEL_LV )) {
					// サイコハザード解除
					$area_status = Pp_MissionManager::AREA_STATUS_NORMAL;
				}

				$columns = array(
					"area_stress" => ( $area['area_stress'] - 1 ),
					"status" => $area_status
				);
				$result = $user_m->updateUserArea( $pp_id, $item_data_array['area_id'], $columns );

				if ( !$result || Ethna::isError( $result ) ) {
					return false;
				}

				$m_area = $mission_m->getMasterArea( $item_data_array['area_id'] );
				$ave_area_stress = $mission_m->getAverageAreaStressAssoc( $pp_id, "db" );

				$result_data = array(
					"area_id"			=> $item_data_array['area_id'],
					"area_stress"		=> $area['area_stress'] - 1,
					"status"			=> $area_status,
					"stage_id"			=> $m_area['stage_id'],
					"ave_area_stress"	=> ( isset( $ave_area_stress[$m_area['stage_id']] ) ? $ave_area_stress[$m_area['stage_id']] : 0 ),
				);
				break;
		}

		// アイテム個数の減少
		$result = $this->updateUserItem( $pp_id, $item_id, $num * -1 );

		if ( !$result || Ethna::isError( $result ) ) {
			return false;
		}

		return true;
	}
}
?>
