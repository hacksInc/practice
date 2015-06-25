<?php
/**
 *  Pp_CharacterManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_CharacterManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_CharacterManager extends Ethna_AppManager
{
	// キャラクターID
	const CHARACTER_ID_TSUNEMORI = 1001;	// 常守朱
	const CHARACTER_ID_GINOZA    = 1002;	// 宜野座伸元
	const CHARACTER_ID_MASAOKA   = 2001;	// 征陸智己
	const CHARACTER_ID_KAGARI    = 2002;	// 縢秀星
	const CHARACTER_ID_KUNIZUKA  = 2003;	// 六合塚弥生
	const CHARACTER_ID_KOUGAMI   = 2004;	// 狡噛慎也
	const CHARACTER_ID_PLAYER    = 11001;	// プレイヤー

	// キャラクタパラメータ（身体係数・知能係数・心的係数）の最大値・最小値
	const CHARACTER_PARAM_MAX = 9999;		// 最大値
	const CHARACTER_PARAM_MIN = 1;			// 最小値

	const EX_STRESS_CARE_DEF = 0;			// 臨時ストレスケア回数初期値

	protected $db_m_r;
	protected $db_logex;

	/**
	 * ユーザーキャラクター情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $character_id キャラクターID
	 *
	 * @return array:キャラクター情報 | null:取得エラー
	 */
	function getUserCharacter( $pp_id, $character_id = null )
	{
		if( empty( $pp_id ))
		{	// サイコパスIDがなければ取得エラー
			return null;
		}

		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		if( is_null( $character_id ))
		{	// キャラクターIDの指定がなければ全キャラ取得
			// memcacheから取得してみる
			$cache_key = "ut_user_character__".$pp_id;
			$cache_data = $cache_m->get( $cache_key, 3600 );
			if( $cache_data && !Ethna::isError( $cache_data ))
			{	// キャッシュから取得できた
				return $cache_data;
			}

			$param = array( $pp_id );
			$sql = "SELECT * FROM ut_user_character WHERE pp_id = ? ORDER BY character_id";
			$data = $this->db->GetAll( $sql, $param );
		}
		else
		{	// キャラクターIDの指定あり
			// memcacheから取得してみる
			$cache_key = "ut_user_character__".$pp_id."__".$character_id;
			$cache_data = $cache_m->get( $cache_key, 3600 );
			if( $cache_data && !Ethna::isError( $cache_data ))
			{	// キャッシュから取得できた
				return $cache_data;
			}

			// 取得できなければDBから取得
			$param = array( $pp_id, $character_id );
			$sql = "SELECT * FROM ut_user_character WHERE pp_id = ? AND character_id = ?";
			$data = $this->db->GetRow( $sql, $param );
		}

		if( !empty( $data ))
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * ユーザーキャラクター情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 *
	 * @return array:キャラクター情報 | null:取得エラー
	 */
	function getUserCharacterAssoc( $pp_id )
	{
		if( empty( $pp_id ))
		{	// サイコパスIDがなければ取得エラー
			return null;
		}

		$user_list = $this->getUserCharacter( $pp_id );

		$data = array();
		foreach( $user_list as $user )
		{
			$data[$user['character_id']] = $user;
		}

		return $data;
	}

	/**
	 * ユーザーキャラクター情報の更新
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $character_id 更新対象のキャラクターID
	 * @param array $columns 更新するカラムとデータの配列（カラム名 => データ）
	 *
	 * @return bool|object 正常終了(更新あり):true, 正常終了(更新なし):false, 更新エラー:Ethna_Errorオブジェクト
	 */
	function updateUserCharacter( $pp_id, $character_id, $columns )
	{
		if( empty( $pp_id ) || empty( $character_id ) || !is_array( $columns ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		// 主キーが更新されるといかんので更新内容から削除
		unset( $columns['pp_id'] );
		unset( $columns['character_id'] );

		// DBを更新（INSERT ON DUPLICATE KEY UPDATEを使う方がいいかな？）
		$str_set = array();
		$param = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = ?";
			$param[] = $v;
		}
		$param[] = $pp_id;
		$param[] = $character_id;
		$sql = "UPDATE ut_user_character SET ".implode( ',', $str_set )." WHERE pp_id = ? AND character_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 更新エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 更新ができたらキャッシュをクリア
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_user_character__$pp_id" );					// キャラ一覧のキャッシュ
		$cache_m->clear( "ut_user_character__".$pp_id."__".$character_id );	// 特定キャラのキャッシュ

		return true;
	}

	/**
	 * ユーザーキャラクター情報の新規追加
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $character_id 追加対象のキャラクターID
	 * @param array $columns 初期値を設定するカラムとデータの配列（カラム名 => データ）
	 *
	 * @return bool|object 正常終了:true, 正常終了（追加なし）:false, 更新エラー:Ethna_Errorオブジェクト
	 */
	function insertUserCharacter( $pp_id, $character_id, $columns = null )
	{
		if( empty( $pp_id ) || empty( $character_id ))
		{	// 追加対象の指定がない
			return false;
		}

		// DBに追加する
		$param = array( $pp_id, $character_id );
		$str_column = array( 'pp_id', 'character_id' );
		$str_values = array( '?', '?' );
		if( is_array( $columns ) && count( $columns ) > 0 )
		{	// 初期値設定があるならパラメータに追加
			foreach( $columns as $k => $v )
			{
				$str_column[] = $k;
				$str_values[] = '?';
				$param[] = $v;
			}
		}
		$sql = "INSERT INTO ut_user_character( ".implode( ',', $str_column ).", date_created ) "
			 . "VALUES( ".implode( ',', $str_values ).", NOW())";
		if( !$this->db->execute( $sql, $param ))
		{	// 追加エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 追加できたらキャッシュを削除
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_user_character__$pp_id" );		// キャラ一覧のキャッシュ

		return true;
	}

	/**
	 * キャラクター情報一覧を取得する
	 *
	 * @return array
	 */
	function getMasterCharacterList()
	{
		// memcacheから取得してみる
		$cache_key = "m_character_list";
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		$sql = "SELECT * FROM m_character";
		$data = $this->db_m_r->GetAll( $sql );

		// 取得したデータをキャッシュする
		$cache_m->set( $cache_key, $data );

		return $data;
	}

	/**
	 * キャラクター情報一覧を取得する
	 *
	 * @return array
	 */
	function getMasterCharacterAssoc()
	{
		// memcacheから取得してみる
		$cache_key = "m_character_assoc";
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		$sql = "SELECT character_id, m.* FROM m_character m";
		$data = $this->db_m_r->db->GetAssoc( $sql );

		// 取得したデータをキャッシュする
		$cache_m->set( $cache_key, $data );

		return $data;
	}

	/**
	 * キャラクターマスタ情報を取得
	 *
	 * @param int $character_id
	 *
	 * @return array:キャラクター情報 | null:取得エラー
	 */
	function getMasterCharacter( $character_id )
	{
		if( empty( $character_id ))
		{	// キャラIDの指定がない
			return null;
		}

		// memcacheから取得してみる
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_key = "m_character__".$character_id;
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		// キャッシュになければDBから取得
		$param = array( $character_id );
		$sql = "SELECT * FROM m_character WHERE character_id = ?";
		$data = $this->db_m_r->GetRow( $sql, $param );

		if( !empty( $data ))
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * ミッションIDから解放されるキャラクターIDを取得する（解放キャラがいない場合は空の配列が返る）
	 *
	 * @param int $mission_id ミッションID
	 *
	 * @return array:キャラクター情報 | null:取得エラー
	 */
	function getMasterCharacterByReleaseMissionId( $mission_id )
	{
		if( empty( $mission_id ))
		{	// ミッションIDの指定がない
			return null;
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		$param = array( $mission_id );
		$sql = "SELECT * FROM m_character WHERE release_mission_id = ?";
		$data = $this->db_m_r->GetRow( $sql, $param );

		return $data;
	}

	/**
	 * 定時ストレスケア
	 *
	 * 全キャラクターの犯罪係数を犯罪係数下限値まで減少させる
	 * @param int $pp_id サイコパスID
	 * @param int $api_transaction_id トランザクションID
	 *
	 * @return bool|object 正常終了(更新あり):true, 正常終了(更新なし):false, 更新エラー:Ethna_Errorオブジェクト
	 */
	function stressCare( $pp_id, $api_transaction_id )
	{
		if( empty( $pp_id ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		// インスタンスを取得していないなら取得
		if( is_null( $this->db_m_r ))
		{
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
		if( is_null( $this->db_logex ))
		{
			$this->db_logex =& $this->backend->getDB( 'logex' );
		}

		$user_m =& $this->backend->getManager( 'User' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$user_game = $user_m->getUserGame( $pp_id );

		// 定時ストレスケアが実行可能かどうかチェック
		$now = time();
		$stress_care_count = floor(( $now - strtotime( $user_game['last_fixed_stress_care'] )) / 21600 );

		// 実行不可
		if( $stress_care_count <= 0 )
		{
			return false;
		}

		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );

		// パラメータ変動ログ出力用に変更前と変更後のパラメータの値が必要なので生成してあげる
		$data = array();

		// サポートキャラマスタ
		$m_character_assoc = $this->getMasterCharacterAssoc();

		// 定時ストレスケア最終実行日時
		list( $year, $month, $day, $hour ) = explode( "-", date( "Y-m-d-H", $now ));
		$last_fixed_stress_care = date( "Y-m-d H:i:s", mktime(( floor( $hour / 6 ) * 6 ), 0, 0, $month, $day, $year ));

		// サポートキャラリスト
		$user_character_list = $this->getUserCharacter( $pp_id );

		// DBトランザクション開始
		$transaction = ( $this->db->db->transCnt == 0 ) ? true : false; // この処理内でトランザクション開始するか？
		if( $transaction )
		{	// トランザクション開始
			$this->db->begin();
			$this->db_logex->begin();
		}

		// プレイヤーキャラ
		$param = array();
		$param[] = $m_character_assoc[self::CHARACTER_ID_PLAYER]['crime_coef_lower_limit'];
		$param[] = self::EX_STRESS_CARE_DEF;
		$param[] = $last_fixed_stress_care;
		$param[] = $pp_id;
		$sql = "UPDATE ut_user_game SET"
			. " crime_coef = ?,"
			. " ex_stress_care = ?,"
			. " last_fixed_stress_care = ?"
			. " WHERE pp_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 更新エラー
			if( $transaction === true )
			{	// ロールバックする
				$this->db->rollback();
				$this->db_logex->rollback();
			}
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// パラメータ変動履歴ログを記録
		$columns = array(
			'pp_id' => $pp_id,										// サイコパスID
			'api_transaction_id' => $api_transaction_id,			// トランザクションID
			'processing_type' => 'A01',								// 処理コード
			'character_id' => self::CHARACTER_ID_PLAYER,			// 臨時ストレスケアを実行したキャラクターID
			'crime_coef' => $m_character_assoc[self::CHARACTER_ID_PLAYER]['crime_coef_lower_limit'],	// 犯罪係数
			'crime_coef_prev' => $user_game['crime_coef'],			// 犯罪係数（変動前）
			'body_coef' => $user_game['body_coef'],					// 身体係数（変動前と同じ）
			'body_coef_prev' => $user_game['body_coef'],			// 身体係数（変動前）
			'intelli_coef' => $user_game['intelli_coef'],			// 知能係数（変動前と同じ）
			'intelli_coef_prev' => $user_game['intelli_coef'],		// 知能係数（変動前）
			'mental_coef' => $user_game['mental_coef'],				// 心的係数（変動前と同じ）
			'mental_coef_prev' => $user_game['mental_coef'],		// 心的係数（変動前）
			'ex_stress_care' => self::EX_STRESS_CARE_DEF,			// 臨時ストレスケア回数（変動前と同じ）
			'ex_stress_care_prev' => $user_game['ex_stress_care']	// 臨時ストレスケア回数（変動前）
		);
		$res = $logdata_m->logCharacter( $columns );

		$cache_m->clear( "ut_user_game__{$pp_id}" );

		// サポートキャラ
		foreach( $user_character_list as $user_character )
		{
			$param = array();
			$param[] = $m_character_assoc[$user_character['character_id']]['crime_coef_lower_limit'];
			$param[] = self::EX_STRESS_CARE_DEF;
			$param[] = $pp_id;
			$param[] = $user_character['character_id'];

			$sql = "UPDATE ut_user_character SET"
				. " crime_coef = ?,"
				. " ex_stress_care = ?"
				. " WHERE pp_id = ?"
				. " AND character_id = ?";
			if( !$this->db->execute( $sql, $param ))
			{	// 更新エラー
				if( $transaction === true )
				{	// ロールバックする
					$this->db->rollback();
					$this->db_logex->rollback();
				}
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}

			// パラメータ変動履歴ログを記録
			$columns = array(
				'pp_id' => $pp_id,											// サイコパスID
				'api_transaction_id' => $api_transaction_id,				// トランザクションID
				'processing_type' => 'A01',									// 処理コード
				'character_id' => $user_character['character_id'],			// 臨時ストレスケアを実行したキャラクターID
				'crime_coef' => $m_character_assoc[$user_character['character_id']]['crime_coef_lower_limit'],	// 犯罪係数
				'crime_coef_prev' => $user_character['crime_coef'],			// 犯罪係数（変動前）
				'body_coef' => $user_character['body_coef'],				// 身体係数（変動前と同じ）
				'body_coef_prev' => $user_character['body_coef'],			// 身体係数（変動前）
				'intelli_coef' => $user_character['intelli_coef'],			// 知能係数（変動前と同じ）
				'intelli_coef_prev' => $user_character['intelli_coef'],		// 知能係数（変動前）
				'mental_coef' => $user_character['mental_coef'],			// 心的係数（変動前と同じ）
				'mental_coef_prev' => $user_character['mental_coef'],		// 心的係数（変動前）
				'ex_stress_care' => self::EX_STRESS_CARE_DEF,				// 臨時ストレスケア回数（変動前と同じ）
				'ex_stress_care_prev' => $user_character['ex_stress_care']	// 臨時ストレスケア回数（変動前）
			);
			$res = $logdata_m->logCharacter( $columns );

			$cache_m->clear( "ut_user_character__{$pp_id}__".$user_character['character_id'] );
		}

		$cache_m->clear( "ut_user_character__{$pp_id}" );

		if( $transaction === true )
		{	// コミットする
			$this->db->commit();
			$this->db_logex->commit();
		}

		return true;
	}

	/**
	 * 臨時ストレスケア
	 *
	 * 指定キャラクターの犯罪係数とその他パラメータを減少させる
	 * 犯罪係数の現在値が下限の場合は使用不可能
	 * @param int $pp_id サイコパスID
	 * @param int $character_id キャラクターID
	 *
	 * @return bool|object 正常終了(更新あり):true, 正常終了(更新なし):false, 更新エラー:Ethna_Errorオブジェクト
	 */
	function exStressCare( $pp_id, $character_id )
	{
		if( empty( $pp_id ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		$user_m =& $this->backend->getManager( 'User' );
		$user_game = $user_m->getUserGame( $pp_id );

		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );

		// サポートキャラマスタ
		$m_character = $this->getMasterCharacter( $character_id );

		// プレイヤーキャラ
		if( $character_id == self::CHARACTER_ID_PLAYER )
		{
			// 犯罪係数の下限チェック
			if ( $user_game['crime_coef'] <= $m_character['crime_coef_lower_limit'] )
			{
				return false;
			}

			// 犯罪係数減少量
			$crime_coef_value = $this->exStressCareCrimeCoefValue( $user_game, $m_character );
			// 能力値減少量
			$body_coef_value = $this->exStressCareParameterValue( $user_game, $m_character, "body_coef", $crime_coef_value );
			$intelli_coef_value = $this->exStressCareParameterValue( $user_game, $m_character, "intelli_coef", $crime_coef_value );
			$mental_coef_value = $this->exStressCareParameterValue( $user_game, $m_character, "mental_coef", $crime_coef_value );

			$user_game['crime_coef'] -= $crime_coef_value;
			$user_game['body_coef'] -= $body_coef_value;
			$user_game['intelli_coef'] -= $intelli_coef_value;
			$user_game['mental_coef'] -= $mental_coef_value;
			$user_game = $this->checkParameterLimit( $user_game, $m_character );

			$param = array();
			$param[] = $user_game['crime_coef'];
			$param[] = $user_game['body_coef'];
			$param[] = $user_game['intelli_coef'];
			$param[] = $user_game['mental_coef'];
			$param[] = $pp_id;
			$sql = "UPDATE ut_user_game SET"
				. " crime_coef = ?,"
				. " body_coef = ?,"
				. " intelli_coef = ?,"
				. " mental_coef = ?,"
				. " ex_stress_care = ex_stress_care + 1"
				. " WHERE pp_id = ?";
			if( !$this->db->execute( $sql, $param ))
			{	// 更新エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}

			$cache_m->clear( "ut_user_game__{$pp_id}" );
		}
		else
		{
			// サポートキャラ
			$user_character = $this->getUserCharacter( $pp_id, $character_id );

			// 犯罪係数の下限チェック
			if ( $user_character['crime_coef'] <= $m_character['crime_coef_lower_limit'] )
			{
				return false;
			}

			// 犯罪係数減少量
			$crime_coef_value = $this->exStressCareCrimeCoefValue( $user_character, $m_character );
			// 能力値減少量
			$body_coef_value = $this->exStressCareParameterValue( $user_character, $m_character, "body_coef", $crime_coef_value );
			$intelli_coef_value = $this->exStressCareParameterValue( $user_character, $m_character, "intelli_coef", $crime_coef_value );
			$mental_coef_value = $this->exStressCareParameterValue( $user_character, $m_character, "mental_coef", $crime_coef_value );

			$user_character['crime_coef'] -= $crime_coef_value;
			$user_character['body_coef'] -= $body_coef_value;
			$user_character['intelli_coef'] -= $intelli_coef_value;
			$user_character['mental_coef'] -= $mental_coef_value;
			$user_character = $this->checkParameterLimit( $user_character, $m_character );

			$param = array();
			$param[] = $user_character['crime_coef'];
			$param[] = $user_character['body_coef'];
			$param[] = $user_character['intelli_coef'];
			$param[] = $user_character['mental_coef'];
			$param[] = $pp_id;
			$param[] = $character_id;

			$sql = "UPDATE ut_user_character SET"
				. " crime_coef = ?,"
				. " body_coef = ?,"
				. " intelli_coef = ?,"
				. " mental_coef = ?,"
				. " ex_stress_care = ex_stress_care + 1"
				. " WHERE pp_id = ?"
				. " AND character_id = ?";
			if( !$this->db->execute( $sql, $param ))
			{	// 更新エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}

			$cache_m->clear( "ut_user_character__{$pp_id}__{$character_id}" );
			$cache_m->clear( "ut_user_character__{$pp_id}" );
		}

		return true;
	}

	/**
	 * 臨時ストレスケアの犯罪係数減少量
	 *
	 * @param array $user_character ユーザキャラクター情報（ユーザゲーム情報）
	 * @param array $m_character キャラクターマスタ情報
	 *
	 * @return array:犯罪係数減少値 | null:取得エラー
	 */
	function exStressCareCrimeCoefValue( $user_character, $m_character )
	{
		if( empty( $user_character['crime_coef'] ) || empty( $m_character['crime_coef_lower_limit'] ))
		{	// 犯罪係数現在値と下限値がなければ取得エラー
			return null;
		}

		$result = round( 0.5 * ( $user_character['crime_coef'] - $m_character['crime_coef_lower_limit'] ));

		// 上限下限
		if( $result < self::CHARACTER_PARAM_MIN )
		{
			$result = self::CHARACTER_PARAM_MIN;
		}
		else if( $result > self::CHARACTER_PARAM_MAX )
		{
			$result = self::CHARACTER_PARAM_MAX;
		}

		return $result;
	}

	/**
	 * 臨時ストレスケアの能力値減少量
	 *
	 * @param array $user_character ユーザキャラクター情報（ユーザゲーム情報）
	 * @param array $m_character キャラクターマスタ情報
	 * @param string $coef_name パラメータのカラム名
	 * @param string $crime_coef_value 臨時ストレスケア犯罪係数減少量
	 *
	 * @return array:犯罪係数減少値 | null:取得エラー
	 */
	function exStressCareParameterValue( $user_character, $m_character, $coef_name, $crime_coef_value )
	{
		//if( empty( $crime_coef ) || empty( $crime_coef_lower_limit ))
		//{	// 犯罪係数現在値と下限値がなければ取得エラー
		//	return null;
		//}

		if( $user_character['ex_stress_care'] < 7)
		{
			$f = 2 * $user_character['ex_stress_care'] + 2;
		}
		else
		{
			$f = 15;
		}

		$result = ceil( 0.02 * $f * $m_character[$coef_name] * $crime_coef_value / ( $m_character['crime_coef_upper_limit'] - $m_character['crime_coef_lower_limit'] ));

		// 上限下限
		if( $result < self::CHARACTER_PARAM_MIN )
		{
			$result = self::CHARACTER_PARAM_MIN;
		}
		else if( $result > self::CHARACTER_PARAM_MAX )
		{
			$result = self::CHARACTER_PARAM_MAX;
		}

		return $result;
	}

	/**
	 * 能力値の上限下限チェック
	 *
	 * @param array $user_character ユーザキャラクター情報（ユーザゲーム情報）
	 * @param array $m_character キャラクターマスタ情報
	 *
	 * @return array:犯罪係数減少値 | null:取得エラー
	 */
	function checkParameterLimit( $user_character, $m_character )
	{
		foreach(array("crime_coef", "body_coef", "intelli_coef", "mental_coef") as $coef_name)
		{
			if ( $coef_name == "crime_coef" )
			{
				// 犯罪係数
				if( $user_character[$coef_name] < $m_character['crime_coef_lower_limit'] )
				{
					$user_character[$coef_name] = $m_character['crime_coef_lower_limit'];
				}
				else if( $user_character[$coef_name] > $m_character['crime_coef_upper_limit'] )
				{
					$user_character[$coef_name] = $m_character['crime_coef_upper_limit'];
				}
			}
			else
			{
				// 他能力値
				if( $user_character[$coef_name] < self::CHARACTER_PARAM_MIN )
				{
					$user_character[$coef_name] = self::CHARACTER_PARAM_MIN;
				}
				else if( $user_character[$coef_name] > self::CHARACTER_PARAM_MAX )
				{
					$user_character[$coef_name] = self::CHARACTER_PARAM_MAX;
				}
			}
		}

		return $user_character;
	}
}
