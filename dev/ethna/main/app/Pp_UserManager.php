<?php
/**
 *  Pp_UserManager.php
 *
 *  主にゲーム関連のユーザー情報の処理
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Text/Password.php';
require_once 'Pp/Ngword.php';
require_once 'Pp/Password.php';

require_once 'Pp_MissionManager.php';
require_once 'Pp_ItemManager.php';
require_once 'Pp_PresentManager.php';

/**
 *  Pp_UserManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_UserManager extends Ethna_AppManager
{
	const RETRY_MAX = 10;					// リトライ回数

	const MIGRATE_ID_DEFAULT_LEN = 12;		// 引き継ぎIDデフォルト文字数
	const MIGRATE_PW_DEFAULT_LEN = 10;		// 引き継ぎパスワードデフォルト文字数
	const MIGRATE_PW_MIN_LEN     = 4;		// 引き継ぎパスワード最小文字数
	const MIGRATE_PW_MAX_LEN     = 20;		// 引き継ぎパスワード最大文字数

	// 引き継ぎID NG種別
	const MIGRATE_ID_NG_TYPE_NONE   = 0;	// 正常
	const MIGRATE_ID_NG_TYPE_LEN    = 1;	// 文字数が不正
	const MIGRATE_ID_NG_TYPE_CTYPE  = 2;	// 文字種別が不正
	const MIGRATE_ID_NG_TYPE_FORBID = 3;	// NGワード
	const MIGRATE_ID_NG_TYPE_EXIST  = 4;	// 重複（既に同名の引き継ぎIDが登録済み）

	// 引き継ぎパスワードNG種別
	const MIGRATE_PW_NG_TYPE_NONE  = 0;		// 正常
	const MIGRATE_PW_NG_TYPE_LEN   = 1;		// 文字数が不正
	const MIGRATE_PW_NG_TYPE_CTYPE = 2;		// 文字種別が不正
	const MIGRATE_PW_NG_TYPE_WEAK  = 3;		// 弱い文字列

	// ニックネームNG種別
	const NAME_NG_TYPE_NONE   = 0;			// 正常
	const NAME_NG_TYPE_LEN    = 1;			// 文字数が不正
	const NAME_NG_TYPE_CTYPE  = 2;			// 文字種別が不正
	const NAME_NG_TYPE_FORBID = 3;			// NGワード
	const NAME_NG_TYPE_EXIST  = 4;			// DB存在エラー（自分のニックネームが既に登録済み、または自分のユーザ未登録） */

	// OS種別
	const OS_IPHONE         = 1;
	const OS_ANDROID        = 2;
	const OS_IPHONE_ANDROID = 3;

	// ステータス
	const GAME_CTRL_STATUS_RUNNING             = 0;
	const GAME_CTRL_STATUS_MAINTENANCE_BEFORE  = 1;
	const GAME_CTRL_STATUS_MAINTENANCE         = 2;

	/** ユーザ属性 */
	const USER_ATTRIBUTE_USER     = 10;	//通常
	const USER_ATTRIBUTE_STAFF    = 21;	//開発スタッフ
	const USER_ATTRIBUTE_PARTNERS = 26;	//外部協力会社
	const USER_ATTRIBUTE_APPLE_REVIEW = 30; // Apple審査

	// コンストラクタで取得されないDBのインスタンス
	protected $db_cmn = null;	//	共通DB
	protected $db_cmn_r = null;	//	共通DB
	protected $db_m_r = null;	//	マスタデータDB

	// 唐之杜ミッション出現解放ミッションID
	const KARANOMORI_RELEASE_MISSION_ID = 10010501;	// このミッションIDに到達している必要がある

	// 月額課金上限
	// DBに定義するほどでもなさそうなので、変数で用意
	public $ma_purchase_max = array(
		0	=> -1,	// -1は上限なし
		1	=> 3000,
		2	=> 10000,
		3	=> 50000,
	);

	/**
	 * ユーザーデータの新規作成
	 *
	 * @param string $user_name ユーザー名
	 * @param int $device_type 端末種別（1:iPhone, 2:Android）
	 *
	 * @return array:ユーザー基本情報 | null:取得エラー
	 */
	function createUser( $user_name, $device_type )
	{
		//-------------------------------------------------------------------------------
		//	ユニーク情報を作成
		//-------------------------------------------------------------------------------
		// インストールパスワードを取得
		$install_pw = $this->newInstallPassword();
//error_log( "install_pw:$install_pw" );
		// 引き継ぎIDを取得
		$migrate_id = $this->getRandomMigrateId();
		if( is_null( $migrate_id ))
		{	// 取得エラー
			return Ethna::raiseError( 'cannot get random migrate_id', E_USER_ERROR );
		}

		// 引き継ぎパスワードを取得
		$migrate_pw = $this->getRandomMigratePassword();
		if( is_null( $migrate_pw ))
		{	// 取得エラー
			return Ethna::raiseError( 'cannot get random migrate_pw', E_USER_ERROR );
		}

		// サイコパスIDを取得
		$pp_id = $this->getRandomPpId();
		if( is_null( $pp_id ))
		{	// 取得エラー
			return Ethna::raiseError( 'cannot get random pp_id', E_USER_ERROR );
		}

		// ハッシュデータを作る
		$migrate_pw_hash = $this->hashMigratePassword( $pp_id, $migrate_pw );
		$install_pw_hash = $this->hashInstallPassword( $pp_id, $install_pw );

		//-------------------------------------------------------------------------------
		//	インスタンスがなければ取得
		//-------------------------------------------------------------------------------
		if( is_null( $this->db_m_r ))
		{
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
		if( is_null( $this->db_cmn ))
		{
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$character_m =& $this->backend->getManager( 'Character' );

		//-------------------------------------------------------------------------------
		//	ut_user_baseレコード追加
		//-------------------------------------------------------------------------------
		$param = array( $pp_id, $user_name, $device_type, $migrate_id, $migrate_pw_hash, $install_pw_hash );
		$sql = "INSERT INTO ut_user_base( pp_id, name, device_type, migrate_id, "
			 . "migrate_pw_hash, install_pw_hash, date_created ) "
			 . "VALUES( ?, ?, ?, ?, ?, ?, NOW())";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		//-------------------------------------------------------------------------------
		//	ut_user_stageレコードを追加
		//	（最初のステージ＝通常ステージの一番ステージIDの小さいもの）
		//-------------------------------------------------------------------------------
		// 最初のステージを取得
		$cache_key = "createUser__normal_first_stage";
		$stage_id = $cache_m->get( $cache_key, 3600 );
		if( empty( $stage_id ) || Ethna::isError( $stage_id ))
		{	// キャッシュから取得できなかった
			$param = array( Pp_MissionManager::STAGE_TYPE_NORMAL );
			$sql = "SELECT stage_id FROM m_stage WHERE type = ? ORDER BY stage_id LIMIT 1";
			$stage_id = $this->db_m_r->GetOne( $sql, $param );
			if( empty( $stage_id ))
			{	// エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}

			// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $stage_id );
		}

		// 取得したステージを追加
		$ret = $this->insertUserStage( $pp_id, $stage_id );
		if( $ret !== true )
		{	// エラー
			return $ret;
		}

		//-------------------------------------------------------------------------------
		// ut_user_areaレコードを追加
		//-------------------------------------------------------------------------------
		$cache_key = "createUser__normal_first_area";
		$area_data = $cache_m->get( $cache_key, 3600 );
		if( empty( $data ) || Ethna::isError( $data ))
		{	// キャッシュから取得できなかった
			$param = array( $stage_id, Pp_MissionManager::AREA_TYPE_NORMAL );
			$sql = "SELECT area_id, area_stress_def FROM m_area WHERE stage_id = ? AND type = ? ORDER BY area_no LIMIT 1";
			$area_data = $this->db_m_r->GetRow( $sql, $param );
			if( empty( $area_data ))
			{	// エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}

			// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $area_data );
		}

		$ret = $this->insertUserArea( $pp_id, $area_data['area_id'], $area_data['area_stress_def'] );
		if( $ret !== true )
		{	// エラー
			return $ret;
		}

		//-------------------------------------------------------------------------------
		//	ut_user_gameレコード追加
		//-------------------------------------------------------------------------------
		// 最初のミッションを取得
		$cache_key = "createUser__normal_first_mission";
		$mission_id = $cache_m->get( $cache_key, 3600 );
		if( empty( $mission_id ) || Ethna::isError( $mission_id ))
		{	// キャッシュから取得できなかった
			$param = array( $area_data['area_id'], Pp_MissionManager::MISSION_TYPE_MAIN );
			$sql = "SELECT mission_id FROM m_mission WHERE area_id = ? AND type = ? ORDER BY mission_no LIMIT 1";
			$mission_id = $this->db_m_r->GetOne( $sql, $param );
			if( empty( $mission_id ))
			{	// エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}

			// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $mission_id );
		}

		// プレイヤーのキャラクターマスタ情報を取得
		$cm = $character_m->getMasterCharacter( Pp_CharacterManager::CHARACTER_ID_PLAYER );
		if( empty( $cm ))
		{	// マスタ情報取得エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		// 定時ストレスケア最終実行日時
		$now = time();		// 現在のタイムスタンプ
//		$last_fixed_stress_care = date( "Y-m-d H:i:s", $now - ( $now % ( 3600 * 6 )) );
		
		list( $year, $month, $day, $hour ) = explode( "-", date( "Y-m-d-H", $now ));
		$last_fixed_stress_care = date( "Y-m-d H:i:s", mktime(( floor( $hour / 6 ) * 6 ), 0, 0, $month, $day, $year ));
		
		$param = array(
			$pp_id, $cm['crime_coef_def'], $cm['body_coef_def'], $cm['intelli_coef_def'], $cm['mental_coef_def'],
		 	$last_fixed_stress_care, $mission_id
		);
		$sql = "INSERT INTO ut_user_game( pp_id, crime_coef, body_coef, intelli_coef, mental_coef, "
			 . "last_fixed_stress_care, mission_id, date_created ) "
			 . "VALUES( ?, ?, ?, ?, ?, ?, ?, NOW())";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		//-------------------------------------------------------------------------------
		//	ut_user_tutorialレコード追加
		//-------------------------------------------------------------------------------
		$param = array( $pp_id );
		$sql = "INSERT INTO ut_user_tutorial( pp_id ) VALUES( ? )";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		//-------------------------------------------------------------------------------
		//	ut_user_achievement_countレコード追加
		//-------------------------------------------------------------------------------
		$param = array( $pp_id );
		$sql = "INSERT INTO ut_user_achievement_count( pp_id ) VALUES( ? )";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		//-------------------------------------------------------------------------------
		//	ct_user_unitレコード追加
		//-------------------------------------------------------------------------------
		$unit = $this->config->get( 'unit_id' );
		$param = array( $pp_id, $migrate_id, $unit );
		$sql = "INSERT INTO ct_user_unit( pp_id, migrate_id, unit, date_created ) "
			 . "VALUES( ?, ?, ?, NOW())";
		if( !$this->db_cmn->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// ユニットの所属ユーザー数をカウント
		$param = array( $unit );
		$sql = "INSERT INTO ct_unit( unit, counter, date_created ) VALUES( ?, 1, NOW()) "
			 . "ON DUPLICATE KEY UPDATE counter = counter + 1";
		if( !$this->db_cmn->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		//-------------------------------------------------------------------------------
		//	ct_user_characterレコード追加
		//-------------------------------------------------------------------------------
		// あかねたんマスタ情報を取得
		$cm = $character_m->getMasterCharacter( Pp_CharacterManager::CHARACTER_ID_TSUNEMORI );
		if( empty( $cm ))
		{	// マスタ情報取得エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		// パラメータ初期値セット
		$columns = array(
			'crime_coef' => $cm['crime_coef_def'],
			'body_coef' => $cm['body_coef_def'],
			'intelli_coef' => $cm['intelli_coef_def'],
			'mental_coef' => $cm['mental_coef_def'],
		);
		// あかねたん追加
		$res = $character_m->insertUserCharacter( $pp_id, Pp_CharacterManager::CHARACTER_ID_TSUNEMORI, $columns );
		if( $res !== true )
		{	// あかねたん追加エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		//-------------------------------------------------------------------------------
		//	jmeter用データ追加
		//-------------------------------------------------------------------------------
		if( $this->af->getApp( 'stress_test_user' ))
		{
			$present_m =& $this->backend->getManager( "Present" );
			$item_m =& $this->backend->getManager( "Item" );
			$col_present = array(
				'comment_id'       => Pp_PresentManager::COMMENT_PRESENT,
				'present_category' => Pp_PresentManager::CATEGORY_ITEM,
				'present_value'    => Pp_ItemManager::ITEM_ID_PHOTO_FILM,
				'num'              => 1
			);
			// プレゼント・セラピー受診命令書・ほとひるむを各10個
			for( $i = 0; $i < 10; $i++ )
			{
				$present_id = $present_m->setUserPresent( $pp_id, 0, $col_present );
				if( Ethna::isError( $present_id ))
				{
					return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
				}
			}
			$result = $item_m->updateUserItem( $pp_id, Pp_ItemManager::ITEM_ID_PHOTO_FILM, 10 );
			if( $result !== true )
			{
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}
			$result = $item_m->updateUserItem( $pp_id, Pp_ItemManager::ITEM_ID_THERAPY_TICKET, 10 );
			if( $result !== true )
			{
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}

			// 犯罪係数を上限に、その他の係数を999に
			$param = array( $pp_id );
			$sql = "UPDATE ut_user_game SET crime_coef = 400, body_coef = 999, intelli_coef = 999, mental_coef = 999 WHERE pp_id = ?";
			if( !$this->db->execute( $sql, $param ))
			{
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}
		}

		//-------------------------------------------------------------------------------
		//	体験版用データ追加
		//-------------------------------------------------------------------------------
		$trial = $this->backend->config->get( 'trial' );
		if( !empty( $trial ))
		{
			// アイテムを最大にする
			$trial = $this->backend->config->set( 'trial', 0 );
			$this->updateUserItem( $pp_id, Pp_ItemManager::ITEM_ID_PHOTO_FILM, 24 );
			$this->updateUserItem( $pp_id, Pp_ItemManager::ITEM_ID_THERAPY_TICKET, 24 );
			$this->updateUserItem( $pp_id, Pp_ItemManager::ITEM_ID_RESERVE_DOMINATOR, 24 );
			$this->updateUserItem( $pp_id, Pp_ItemManager::ITEM_ID_DRONE, 24 );
			$trial = $this->backend->config->set( 'trial', 1 );

			// ステージ１を全て解放
			$mission_m =& $this->backend->getManager( 'Mission' );
			$a = $mission_m->getMasterAreaListAssocByStageId( $stage_id );
			if( empty( $a ))
			{	// エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}
			foreach( $a as $aid => $row )
			{
				if( $aid == $area_data['area_id'] )
				{	// 既に追加済みのレコード
					continue;
				}
				if( $row['type'] != Pp_MissionManager::AREA_TYPE_NORMAL )
				{	// 通常エリア以外
					continue;
				}

				$ret = $this->insertUserArea( $pp_id, $aid, $row['area_stress_def'] );
				if( $ret !== true )
				{	// エラー
					return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
				}
			}
			$m = $mission_m->getMasterMissionListAssocByAreaIds( array_keys( $a ));
			if( empty( $m ))
			{	// エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}
			foreach( $m as $mid => $row )
			{
				if( $row['type'] != Pp_MissionManager::MISSION_TYPE_MAIN )
				{
					continue;
				}

				$ret = $this->addUserMissionResultCount( $pp_id, $mid, 3 );
				if( $ret !== true )
				{	// エラー
					return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
				}
			}

			// あかねちゃんグレードアップ
			$columns = array(
				'body_coef' => $cm['body_coef_def'] + 50,
				'intelli_coef' => $cm['intelli_coef_def'] + 50,
				'mental_coef' => $cm['mental_coef_def'] + 50,
			);
			$character_m->updateUserCharacter( $pp_id, Pp_CharacterManager::CHARACTER_ID_TSUNEMORI, $columns );

			// 宜野座を解放
			$cm = $character_m->getMasterCharacter( Pp_CharacterManager::CHARACTER_ID_GINOZA );
			if( empty( $cm ))
			{	// マスタ情報取得エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}
			// パラメータ初期値セット
			$columns = array(
				'crime_coef' => $cm['crime_coef_def'],
				'body_coef' => $cm['body_coef_def'] + 50,
				'intelli_coef' => $cm['intelli_coef_def'] + 50,
				'mental_coef' => $cm['mental_coef_def'] + 50,
			);
			$res = $character_m->insertUserCharacter( $pp_id, Pp_CharacterManager::CHARACTER_ID_GINOZA, $columns );
			if( $res !== true )
			{
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}

			// ユーザーグレードアップ
			$cm = $character_m->getMasterCharacter( Pp_CharacterManager::CHARACTER_ID_PLAYER );
			$columns = array(
				'body_coef' => $cm['body_coef_def'] + 30,
				'intelli_coef' => $cm['intelli_coef_def'] + 30,
				'mental_coef' => $cm['mental_coef_def'] + 30
			);
			$res = $this->updateUserGame( $pp_id, $columns );
		}

		//-------------------------------------------------------------------------------
		//	戻り値を作成
		//-------------------------------------------------------------------------------
		$data = array(
			'pp_id' => $pp_id,						// サイコパスID
			'install_pw' => ( string )$install_pw,	// インストールパスワード
			'migrate_id' => ( string )$migrate_id,	// 引き継ぎID
			'migrate_pw' => ( string )$migrate_pw,	// 引き継ぎPW
			'unit' => $unit							// 所属ユニット
		);

		return $data;
	}

	/**
	 * 引継ぎIDのチェック
	 * prepareで通常引き継ぎか旧ユーザーか判別するため、処理をmigrateUserから切り分けた
	 */
	function isMigrateUser ( $migrate_id, $migrate_pw )
	{
		// migrate_idから該当データを取得
		$param = array( $migrate_id );
		$sql = "SELECT * FROM ut_user_base WHERE migrate_id = ?";
		$user = $this->db->GetRow( $sql, $param );

		// 対象がないかエラーなら引継ぎ失敗
		if ( !$user || Ethna::isError( $user ) ) return Ethna::raiseError( 'migrate_id not match', E_USER_ERROR );

		// 引継ぎパスワードの書式がおかしかったらエラー
		if ( !$this->isValidMigratePasswordFormat( $migrate_pw ) ) return Ethna::raiseError( 'migrate_id is unjust', E_USER_ERROR );

		// ハッシュが合わなければエラー
		//if ( !$this->hashMigratePassword( $user['pp_id'], $migrate_pw ) ) return Ethna::raiseError( 'migrate_hash not match', E_USER_ERROR );
		if ( $this->hashMigratePassword( $user['pp_id'], $migrate_pw ) != $user['migrate_pw_hash']) return Ethna::raiseError( 'migrate_hash not match', E_USER_ERROR );

		return true;
	}

	/**
	 * ユーザー引継ぎ処理
	 * ユーザー引継ぎを行うとインストールハッシュが更新される
	 * これは旧端末からの起動を防ぐ（＝インストールハッシュ変更）ため
	 * 引継ぎコードはユーザーごとに固定なので、再発行はしない
	 */
	function migrateUser ( $pp_id, $device_type, $migrate_id, $migrate_pw )
	{
		// 所属ユニットを取得
		$unit = $this->config->get( 'unit_id' );

		// インストールパスワードを取得
		$install_pw = $this->newInstallPassword();

		// ハッシュデータを作る
		$install_pw_hash = $this->hashInstallPassword( $pp_id, $install_pw );

		// ut_user_baseのハッシュ部分を更新
		$param = array( $device_type, $install_pw_hash, $pp_id );
		$sql = "UPDATE ut_user_base SET device_type = ?, install_pw_hash = ?, date_modified = NOW() WHERE pp_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 更新ができたらキャッシュをクリア
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_user_base__$pp_id" );

		// 返す値
		$data = array(
			'pp_id'			=> $pp_id,	// サイコパスID
			'install_pw'	=> $install_pw,		// インストールパスワード
			'migrate_id'	=> $migrate_id,		// 引き継ぎID
			'migrate_pw'	=> $migrate_pw,		// 引き継ぎPW
			'unit'			=> $unit			// 所属ユニット
		);

		return $data;
	}

	/**
	 * ケイブ版ポータルからのユーザー専用の処理
	 * 上記ユーザーはpp_idのみ端末にあって、install_pw、migrate_id、migrate_pwがない状態
	 * なので、初回アクセス時にここを更新してやる必要がある
	 */
	function updateCavePortalUser ( $pp_id )
	{
		// インストールパスワードを取得
		$install_pw = $this->newInstallPassword();

		// 引き継ぎIDを取得
		$migrate_id = $this->getRandomMigrateId();
		if( is_null( $migrate_id ))
		{	// 取得エラー
			return Ethna::raiseError( 'cannot get random migrate_id', E_USER_ERROR );
		}

		// 引き継ぎパスワードを取得
		$migrate_pw = $this->getRandomMigratePassword();
		if( is_null( $migrate_pw ))
		{	// 取得エラー
			return Ethna::raiseError( 'cannot get random migrate_pw', E_USER_ERROR );
		}

		// ハッシュデータを作る
		$migrate_pw_hash = $this->hashMigratePassword( $pp_id, $migrate_pw );
		$install_pw_hash = $this->hashInstallPassword( $pp_id, $install_pw );

		// ut_user_baseのハッシュ部分を更新
		$param = array( $install_pw_hash, $migrate_id, $migrate_pw_hash, $pp_id );
		$sql = "UPDATE ut_user_base SET install_pw_hash = ?, migrate_id = ?, migrate_pw_hash = ?, date_modified = NOW() WHERE pp_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 返す値
		$data = array(
			'pp_id'			=> $pp_id,	// サイコパスID
			'install_pw'	=> $install_pw,		// インストールパスワード
			'migrate_id'	=> $migrate_id,		// 引き継ぎID
			'migrate_pw'	=> $migrate_pw,		// 引き継ぎPW
		);

		return $data;
	}

	/**
	 * インストールパスワードの生成
	 *
	 * @return int インストールパスワード文字列
	 */
	protected function newInstallPassword()
	{
		$plain = Text_Password::create( 64, 'unpronounceable' );
		$hex = unpack( 'H*', $plain );
		return $hex[1];
	}

	/**
	 * ランダムなサイコパスIDを取得する
	 *
	 * @return int サイコパスID
	 */
	protected function getRandomPpId()
	{
		// 所属ユニットのサイコパスID割り当て範囲を取得
		$ppid_range = $this->config->get( 'ppid_range' );

		$n = self::RETRY_MAX;
		while( $n > 0 )
		{
			// 範囲内からサイコパスIDをランダムで取得
			$pp_id = mt_rand( $ppid_range['min'], $ppid_range['max'] );

			// 既に使われている？
			$ub = $this->getUserBase( $pp_id );
			if( is_array( $ub ) && ( count( $ub ) === 0 ))
			{	// 空きサイコパスID
				return $pp_id;
			}
			$n--;
		}
		return null;
	}

	/**
	 * ランダムな引き継ぎIDを取得する
	 *
	 * @return string 引き継ぎID文字列
	 */
	protected function getRandomMigrateId()
	{
		$n = self::RETRY_MAX;
		while( $n > 0 )
		{
			// アルファベット小文字でランダムに生成
			$migrate_id = Text_Password::create( self::MIGRATE_ID_DEFAULT_LEN,
				'unpronounceable', 'abcdefghijklmnopqrstuvwxyz' );

			// 重複チェック
			if( $this->isUnusedMigrateId( $migrate_id ))
			{	// 未使用の引き継ぎID
				return $migrate_id;
			}
			$n--;
		}
		return null;
	}

	/**
	 * 引き継ぎIDの重複チェック
	 *
	 * @return boolean true:未使用のID, false:使用済のID
	 */
	protected function isUnusedMigrateId( $migrate_id )
	{
		if( is_null( $this->db_cmn ))
		{
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}

		$param = array( $migrate_id );
		$sql = "SELECT pp_id FROM ct_user_unit WHERE migrate_id = ?";
		$data = $this->db_cmn->GetRow( $sql, $param );
		if( $data === false )
		{	// 取得エラー
			return false;
		}
		if( !empty( $data ))
		{	// データがあるので既に使われている
			return false;
		}
		return true;
	}

	/**
	 * 引き継ぎパスワードを取得する
	 *
	 * @return string 引き継ぎパスワード文字列
	 */
	function getRandomMigratePassword()
	{
		$n = self::RETRY_MAX;
		while( $n > 0 )
		{
			$migrate_pw = Text_Password::create( self::MIGRATE_PW_DEFAULT_LEN,
				'unpronounceable', '0123456789' );

			// フォーマットチェック
			if( $this->isValidMigratePasswordFormat( $migrate_pw ) === true )
			{	// 問題なし
				return $migrate_pw;
			}
			$n--;
		}
		return null;
	}

	/**
	 * インストールパスワードのチェック
	 */
	function isValidInstallPassword ( $pp_id, $install_pw )
	{
//error_log( 'pp_id: '.$pp_id );
		$base = $this->getUserBase( $pp_id );
		if ( !$base || Ethna::isError( $base ) ) {
			return false;
		}


//error_log( 'install_pw: '.$install_pw );
		if ($base['install_pw_hash'] == $this->hashInstallPassword( $pp_id, $install_pw ) ) {
			return true;
		}

		return false;
	}

	/**
	 * 引き継ぎパスワードの書式チェック
	 *
	 * @return string 引き継ぎパスワード文字列
	 */
	function isValidMigratePasswordFormat( $migrate_pw )
	{
		$ng_type = $this->getMigratePasswordNgType( $migrate_pw );
		return ( $ng_type == self::MIGRATE_PW_NG_TYPE_NONE ) ? true : false;
	}

	/**
	 * 引き継ぎパスワードの書式チェック
	 *
	 * @return string 引き継ぎパスワード文字列
	 */
	function getMigratePasswordNgType( $migrate_pw )
	{
		$len = strlen( $migrate_pw );
		if(( $len < self::MIGRATE_PW_MIN_LEN )||( self::MIGRATE_PW_MAX_LEN < $len ))
		{	// 文字列の長さが不正
			return self::MIGRATE_PW_NG_TYPE_LEN;
		}
		if( preg_match('/[^a-zA-Z0-9\-_]/', $migrate_pw ))
		{	// 文字種別が不正
			return self::MIGRATE_PW_NG_TYPE_CTYPE;
		}
		if( Pp_Password::isWeakPassword( $migrate_pw ))
		{	// 強度不足
			return self::MIGRATE_PW_NG_TYPE_WEAK;
		}
		return self::MIGRATE_PW_NG_TYPE_NONE;
	}

	/**
	 * インストールパスワードをハッシュする
	 *
	 * @param int $pp_id サイコパスID
	 * @param string $install_pw インストールパスワード
	 *
	 * @return string ハッシュ文字列
	 */
	protected function hashInstallPassword( $pp_id, $install_pw )
	{
		// Saltの内、アプリケーションサーバ内にだけ保持する部分
		$salt_app = 'uGf2D5kI';

		$salt = $salt_app . $pp_id;
		$hash = sha1( $install_pw . $salt );

		return $hash;
	}

	/**
	 * 引き継ぎパスワードをハッシュする
	 *
	 * @param int $pp_id サイコパスID
	 * @param string $migrate_pw インストールパスワード
	 *
	 * @return string ハッシュ文字列
	 */
	protected function hashMigratePassword( $pp_id, $migrate_pw )
	{
		// Saltの内、アプリケーションサーバ内にだけ保持する部分
		$salt_app = 'C6yi42m9';

		$salt = $salt_app . $pp_id;
		$hash = sha1( $migrate_pw . $salt );

		return $hash;
	}

	/**
	 * 新規にエリアを解放させる（エリアとミッションのユーザー情報をDBに追加する）
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $area_id エリアID
	 * @param int $area_stress_def エリアストレス初期値（なければ内部で取得する）
	 *
	 * @return true:正常終了, null:エラー
	 */
	function releaseNewArea( $pp_id, $area_id, $area_stress_def = null )
	{
//error_log("***************** releaseNewArea: {$pp_id}, {$area_id}, {$area_stress_def}");
		$mission_m =& $this->backend->getManager( "Mission" );
		if( is_null( $area_stress_def ))
		{	// エリアストレス値がなければマスタから取得
//error_log("***************** area_stress_def empty.");
			$m = $mission_m->getMasterArea( $area_id );
			if( empty( $m ))
			{
				return null;
			}
			$area_stress_def = $m['area_stress_def'];
		}

		// エリア情報を新規に作成
//error_log("***************** insertUserArea");
		$ret = $this->insertUserArea( $pp_id, $area_id, $area_stress_def );
		if( $ret !== true )
		{	// エラー
//error_log("***************** error");
			return null;
		}

		// エリアの最初のミッションのマスタ情報
//error_log("***************** getMasterFirstMissionByAreaId: ".$area_id);
		$mis = $mission_m->getMasterFirstMissionByAreaId( $area_id );
		if( empty( $mis ))
		{	// エラー
//error_log("***************** error");
			return null;
		}

		// ミッション情報を新規に作成
//error_log("***************** insertUserMission: ".$area_id);
		$res = $this->insertUserMission( $pp_id, $mis['mission_id'] );
		if( $res !== true )
		{	// エラー
//error_log("***************** error");
			return null;
		}
//error_log("***************** ok");
		return true;
	}

	//================================================================================================
	//		ut_user_base に関する処理
	//================================================================================================
	/**
	 * ユーザー基本情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 *
	 * @return array:ユーザー基本情報 | null:取得エラー
	 */
	function getUserBase( $pp_id )
	{
		if( empty( $pp_id ))
		{	// サイコパスIDがなければ取得エラー
			return null;
		}

		// memcacheから取得してみる
		$cache_key = "ut_user_base__$pp_id";
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 1 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// 取得できない場合はDBから取得
		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_base WHERE pp_id = ?";
		$data = $this->db->GetRow( $sql, $param );

		if( !empty( $data ))
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}
	/**
	 * getUserBaseByMigrateId
	 *
	 * migrate_idからユーザー情報を取得。
	 * 使う箇所が引き継ぎ位しかないのでキャッシュはしない
	 */
	function getUserBaseByMigrateId ( $migrate_id, $dsn = "db_r" )
	{
		if ( empty( $migrate_id ) ) return null;

		$param = array( $migrate_id );
		$sql = "SELECT * FROM ut_user_base WHERE migrate_id = ?";

		return $this->$dsn->GetRow( $sql, $param );
	}

	/**
	 * ユーザー基本情報の更新
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $columns 更新するカラムとデータの配列（カラム名 => データ）
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし):false | Ethna_Errorオブジェクト:更新エラー
	 */
	function updateUserBase( $pp_id, $columns )
	{
		if( empty( $pp_id ) || !is_array( $columns ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		// 主キーが更新されるといかんので更新内容から削除
		unset( $columns['pp_id'] );

		// DB更新
		$str_set = array();
		$param = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = ?";
			$param[] = $v;
		}
		$param[] = $pp_id;
		$sql = "UPDATE ut_user_base SET ".implode( ',', $str_set )." WHERE pp_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 更新エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 更新ができたらキャッシュをクリア
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_user_base__$pp_id" );

		return true;
	}

	//================================================================================================
	//		ut_user_game に関する処理
	//================================================================================================
	/**
	 * ユーザーゲーム情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 *
	 * @return array:ユーザーゲーム情報 | null:取得エラー
	 */
	function getUserGame( $pp_id )
	{
		if( empty( $pp_id ))
		{	// サイコパスIDがなければ取得エラー
			return null;
		}

		// memcacheから取得してみる
		$cache_key = "ut_user_game__$pp_id";
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// 取得できない場合はDBから取得
		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_game WHERE pp_id = ?";
		$data = $this->db->GetRow( $sql, $param );

		if( !empty( $data ))
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * ユーザーゲーム情報の更新
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $columns 更新するカラムとデータの配列（カラム名 => データ）
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし) | Ethna_Errorオブジェクト:更新エラー
	 */
	function updateUserGame( $pp_id, $columns )
	{
		if( empty( $pp_id ) || !is_array( $columns ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		// 主キーが更新されるといかんので更新内容から削除
		unset( $columns['pp_id'] );

		// DB更新
		$str_set = array();
		$param = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = ?";
			$param[] = $v;
		}
		$param[] = $pp_id;
		$sql = "UPDATE ut_user_game SET ".implode( ',', $str_set )." WHERE pp_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// 更新エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 更新ができたらキャッシュをクリア
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_user_game__$pp_id" );

		return true;
	}

	/**
	 * 次の定時ストレスケア情報の取得
	 *
	 * @param
	 *
	 * @return array 定時ストレスケア情報
	 */
	function getNextFixedStressCare()
	{
		// 定時ストレスケア時刻テーブル（'00:00','06:00','12:00','18:00'の４つ）
		$today = mktime( 0, 0, 0 );		// 今日を基準にテーブルを作成
		$fixed_stress_care_time = array(
			0 => $today, 1 => ( $today + ( 6 * 3600 )), 2 => ( $today + ( 12 * 3600 )), 3 => ( $today + ( 18 * 3600 ))
		);

		// 次の定時ストレスケアの時刻を取得
		$now = time();		// 現在のタイムスタンプを取得
		$index = 0;
		$timestamp = 0;
		foreach( $fixed_stress_care_time as $idx => $t )
		{
			if( $now < $t )
			{	// まだ実行されていない定時ストレスケア時刻
				$index = $idx;
				$timestamp = $t;
				break;
			}
		}
		if( $index === 0 )
		{	// どれでもなければ今日の定時ストレスケアは全て終了している
			$index = 0;						// 明日の一番最初
			$timestamp = $today + 86400;	// 今日の１日後（つまり明日）
		}

		$res = array(
			'index' => $index,					// １日の何番目の定時ストレスケアか？（１～４）
			'next_timestamp' => $timestamp,		// 次の定時ストレスケアのタイムスタンプ
			'base_timestamp' => $now			// 検索の基準となったタイムスタンプ
		);

		return $res;
	}

	/**
	 * ログイン関連情報を更新
	 *
	 * @param int pp_id サイコパスID
	 * @param boolean is_boot true:起動時のログインチェック処理, false:起動時以外のログインチェック処理
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし) | Ethna_Errorオブジェクト:更新エラー
	 */
	function updateUserGameLogin( $pp_id, $is_boot = true )
	{
		$ug = $this->getUserGame( $pp_id );
		if( empty( $ug ))
		{	// ないはずはないのでエラー
			return null;
		}

		$last_dt = date( 'Y-m-d', strtotime( $ug['last_login'] ));	// 前回ログインの日付
		$dt = date( 'Y-m-d', $_SERVER['REQUEST_TIME'] );			// 今回ログインの日付
		if( $last_dt == $dt )
		{	// 日付が同じなら当日再ログイン
			$str = "today_login = today_login + 1,";
			$is_first = false;
		}
		else if(( strtotime( $dt ) - strtotime( $last_dt )) == 86400 )
		{	// 前日ログインしているので連続ログイン
			$str = "cont_login = cont_login + 1, today_login = 1,";
			$is_first = true;
		}
		else
		{	// 当日再ログインでも前日からの連続ログインでもない
			$str = "cont_login = 1, today_login = 1,";
			$is_first = true;
		}
		if(( $is_boot == true )||( $is_first == true ))
		{
			$param = array( date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ), $pp_id );
			$sql = "UPDATE ut_user_game SET ".$str." last_login = ? WHERE pp_id = ?";
			if( !$this->db->execute( $sql, $param ))
			{	// 更新エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}
		}

		// 今日最初のログインならログイン回数を加算
		if( $is_first == true )
		{
			// ut_user_achievement_countのログイン回数
			$columns = array( 'login' => 1 );
			$ret = $this->addUserAchievementCount( $pp_id, $columns );
			if( $ret !== true )
			{	// 更新エラー
				return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
			}
		}

		// 更新ができたらキャッシュをクリア
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_user_game__$pp_id" );

		return true;
	}

	//================================================================================================
	//		ut_user_ingame に関する処理
	//================================================================================================
	/**
	 * ユーザーInGame情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 *
	 * @return array:ユーザーInGameゲーム情報 | null:取得エラー
	 */
	function getUserIngame( $pp_id )
	{
		if( empty( $pp_id ))
		{	// サイコパスIDがなければ取得エラー
			return null;
		}

		// 取得できない場合はDBから取得
		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_ingame WHERE pp_id = ?";
		return $this->db_r->GetRow( $sql, $param );
	}

	/**
	 * ユーザーInGame情報の更新（レコードがなければ自動で追加される）
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $columns 更新するカラムとデータの配列（カラム名 => データ）
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし):false | Ethna_Errorオブジェクト:更新エラー
	 */
	function updateUserIngame( $pp_id, $columns )
	{
		if( empty( $pp_id ) || !is_array( $columns ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		$param = array( $pp_id,
			$columns['play_id'], $columns['mission_id'], $columns['accompany_character_id'], $columns['hazard_flag'], $columns['karanomori_report_flag'],
			$columns['crime_coef_pl'], $columns['crime_coef_pl_after'], $columns['hazard_diff_pl'],
			$columns['crime_coef_sp'], $columns['crime_coef_sp_after'], $columns['hazard_diff_sp'],
			$columns['play_id'], $columns['mission_id'], $columns['accompany_character_id'], $columns['hazard_flag'], $columns['karanomori_report_flag'],
			$columns['crime_coef_pl'], $columns['crime_coef_pl_after'], $columns['hazard_diff_pl'],
			$columns['crime_coef_sp'], $columns['crime_coef_sp_after'], $columns['hazard_diff_sp']
		);
		$sql = "INSERT INTO ut_user_ingame( pp_id, play_id, mission_id, accompany_character_id, hazard_flag, karanomori_report_flag, "
			 . "crime_coef_pl, crime_coef_pl_after, hazard_diff_pl, crime_coef_sp, crime_coef_sp_after, hazard_diff_sp, date_created ) "
			 . "VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()) "
			 . "ON DUPLICATE KEY UPDATE play_id = ?, mission_id = ?, accompany_character_id = ?, hazard_flag = ?, karanomori_report_flag = ?, "
			 . "crime_coef_pl = ?, crime_coef_pl_after = ?, hazard_diff_pl = ?, crime_coef_sp = ?, crime_coef_sp_after = ?, hazard_diff_sp = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return true;
	}

	//================================================================================================
	//		ut_user_stage に関する処理
	//================================================================================================
	/**
	 * ユーザーステージ情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $stage_id ステージID
	 *
	 * @return true:正常終了 | Ethna_Errorオブジェクト:エラー
	 */
	function getUserStage( $pp_id, $stage_id )
	{
		$param = array( $pp_id, $stage_id );
		$sql = "SELECT * FROM ut_user_stage WHERE pp_id = ? AND stage_id = ?";
		return $this->db_r->GetRow( $sql, $param );
	}

	/**
	 * ユーザーステージ情報の取得（複数）
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $stage_id ステージID
	 *
	 * @return true:正常終了 | Ethna_Errorオブジェクト:エラー
	 */
	function getUserStageList( $pp_id, $dsn = "db_r" )
	{
		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_stage WHERE pp_id = ?";
		return $this->$dsn->GetAll( $sql, $param );
	}

	/**
	 * ユーザーステージ情報の追加
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $stage_id ステージID
	 *
	 * @return true:正常終了 | Ethna_Errorオブジェクト:エラー
	 */
	function insertUserStage( $pp_id, $stage_id )
	{
		$param = array( $pp_id, $stage_id );
		$sql = "INSERT INTO ut_user_stage( pp_id, stage_id, karanomori_report, date_created ) "
			 . "VALUES( ?, ?, 0, NOW())";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * ユーザーステージ情報の更新
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $stage_id ステージID
	 * @param array $columns 更新するカラムとデータの配列（カラム名 => データ）
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし):false | Ethna_Errorオブジェクト:更新エラー
	 */
	function updateUserStage( $pp_id, $stage_id, $columns )
	{
		if( empty( $pp_id ) || empty( $stage_id ) || !is_array( $columns ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		$param = array();
		$str_set = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = ?";
			$param[] = $v;
		}
		$param[] = $pp_id;
		$param[] = $stage_id;

		$sql = "UPDATE ut_user_stage SET ".implode( ',', $str_set )." WHERE pp_id = ? AND stage_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return true;
	}

	//================================================================================================
	//		ut_user_area に関する処理
	//================================================================================================
	/**
	 * ユーザーエリア情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $area_id エリアID
	 * @param string $dsn DSN文字列
	 *
	 * @return array:ユーザーエリア情報 | null:エラー
	 */
	function getUserArea( $pp_id, $area_id, $dsn = "db_r" )
	{
		$param = array( $pp_id, $area_id );
		$sql = "SELECT * FROM ut_user_area WHERE pp_id = ? AND area_id = ?";
		return $this->$dsn->GetRow( $sql, $param );
	}

	/**
	 * ユーザーエリア情報の取得（複数同時取得版）
	 *
	 * @param int $pp_id サイコパスID（指定がない場合は全データ）
	 * @param array $area_ids エリアIDの配列
	 * @param string $dsn DSN文字列
	 *
	 * @return array:ユーザーエリア情報の配列 | null:エラー
	 */
	function getUserAreaList( $pp_id, $area_ids, $dsn = "db_r" )
	{
		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_area WHERE pp_id = ?";
		if( !empty( $area_ids ))
		{
			$str_in = array();
			foreach( $area_ids as $area_id )
			{
				$param[] = $area_id;
				$str_in[] = '?';
			}
			$sql .= " AND area_id IN ( ".implode( ',', $str_in )." )";
		}
		return $this->$dsn->GetAll( $sql, $param );
	}

	/**
	 * ユーザーエリア情報の取得（複数同時取得版）
	 *
	 * @param int $pp_id サイコパスID（指定がない場合は全データ）
	 * @param array $area_ids エリアIDの配列
	 * @param string $dsn DSN文字列
	 *
	 * @return array:ユーザーエリア情報の配列 | null:エラー
	 */
	function getUserAreaListAssoc( $pp_id, $area_ids, $dsn = "db_r" )
	{
		$param = array( $pp_id );
		$sql = "SELECT ua.area_id, ua.* FROM ut_user_area as ua WHERE ua.pp_id = ?";
		if( !empty( $area_ids ))
		{
			$str_in = array();
			foreach( $area_ids as $area_id )
			{
				$param[] = $area_id;
				$str_in[] = '?';
			}
			$sql .= " AND ua.area_id IN ( ".implode( ',', $str_in )." )";
		}
		return $this->$dsn->db->GetAssoc( $sql, $param );
	}


	/**
	 * ユーザーエリア情報の追加
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $area_id エリアID
	 * @param int $area_stress_def エリアストレス初期値
	 *
	 * @return true:正常終了 | Ethna_Errorオブジェクト:エラー
	 */
	function insertUserArea( $pp_id, $area_id, $area_stress_def = 1 )
	{
		$param = array( $pp_id, $area_id, $area_stress_def );
		$sql = "INSERT INTO ut_user_area( pp_id, area_id, area_stress, status, date_created ) "
			 . "VALUES( ?, ?, ?, 0, NOW())";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * ユーザーエリア情報の更新
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $area_id エリアID
	 * @param array $columns 更新するカラムとデータの配列（カラム名 => データ）
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし):false | Ethna_Errorオブジェクト:更新エラー
	 */
	function updateUserArea( $pp_id, $area_id, $columns )
	{
		if( empty( $pp_id ) || empty( $area_id ) || !is_array( $columns ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		$param = array();
		$str_set = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = ?";
			$param[] = $v;
		}
		$param[] = $pp_id;
		$param[] = $area_id;

		$sql = "UPDATE ut_user_area SET ".implode( ',', $str_set )." WHERE pp_id = ? AND area_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return true;
	}

	//================================================================================================
	//		ut_user_mission に関する処理
	//================================================================================================
	/**
	 * ユーザーミッション情報のミッション結果回数をカウントする（レコードがなければ自動で追加される）
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $mission_id ミッションID
	 * @param int $result_type 処理結果番号（1:BEST, 2:NORMAL, 3:FAIL）
	 *
	 * @return true:正常終了 | Ethna_Errorオブジェクト:エラー
	 */
	function addUserMissionResultCount( $pp_id, $mission_id, $columns )
	{
		$param_insert = array( $pp_id, $mission_id );
		$param_update = array();
		$str_insert = array();
		$str_update = array();
		$val_insert = array( '?', '?');
		foreach( $columns as $k => $v )
		{
			$str_insert[] = "$k";
			$val_insert[] = "?";
			$param_insert[] = $v;
			$str_update[] = "$k = $k + ?";
			$param_update[] = $v;
		}
		$param = array_merge( $param_insert, $param_update );
		$sql = "INSERT INTO ut_user_mission( pp_id, mission_id, ".implode( ",", $str_insert ).", date_created ) "
			 . "VALUES( ".implode( ",", $val_insert ).", NOW()) "
			 . "ON DUPLICATE KEY UPDATE ".implode( ",", $str_update );
		/*
		$column = array( 1 => 'fail', 2 => 'normal_clear', 3 => 'best_clear' );
		$column_name = $column[$result_type];

		$param = array( $pp_id, $mission_id );
		$sql = "INSERT INTO ut_user_mission( pp_id, mission_id, $column_name, date_created ) VALUES( ?, ?, 1, NOW()) "
			 . "ON DUPLICATE KEY UPDATE $column_name = $column_name + 1";
		*/
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * ユーザーミッション情報の追加
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $mission_id ミッションID
	 *
	 * @return true:正常終了 | Ethna_Errorオブジェクト:エラー
	 */
	function insertUserMission( $pp_id, $mission_id )
	{
		$param = array( $pp_id, $mission_id );
		$sql = "INSERT INTO ut_user_mission( pp_id, mission_id, date_created ) VALUES( ?, ?, NOW())";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	/**
	 * ユーザーミッション情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $mission_id ミッションID
	 * @param string $dsn DSN文字列
	 *
	 * @return array:ユーザーミッション情報 | null:エラー
	 */
	function getUserMission( $pp_id, $mission_id, $dsn = "db_r" )
	{
		$param = array( $pp_id, $mission_id );
		$sql = "SELECT * FROM ut_user_mission WHERE pp_id = ? AND mission_id = ?";
		return $this->$dsn->GetRow( $sql, $param );
	}

	/**
	 * ユーザーミッション情報の取得（複数同時取得版）
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $mission_ids ミッションIDの配列（指定がない場合は存在する全データ）
	 * @param string $dsn DSN文字列
	 *
	 * @return array:ユーザーミッション情報の配列 | null:エラー
	 */
	function getUserMissionList( $pp_id, $mission_ids = array(), $dsn = "db_r" )
	{
		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_mission WHERE pp_id = ?";
		if( !empty( $mission_ids ))
		{
			$str_in = array();
			foreach( $mission_ids as $mission_id )
			{
				$param[] = $mission_id;
				$str_in[] = '?';
			}
			$sql .= " AND mission_id IN ( ".implode( ',', $str_in )." )";
		}
		return $this->$dsn->GetAll( $sql, $param );
	}

	/**
	 * ユーザーミッション情報の取得（複数同時取得版）
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $mission_ids ミッションIDの配列（指定がない場合は存在する全データ）
	 * @param string $dsn DSN文字列
	 *
	 * @return array:ユーザーミッション情報の配列 | null:エラー
	 */
	function getUserMissionListAssoc( $pp_id, $mission_ids = array(), $dsn = "db_r" )
	{
		$param = array( $pp_id );
		$sql = "SELECT um.mission_id, um.* FROM ut_user_mission as um WHERE pp_id = ?";
		if( !empty( $mission_ids ))
		{
			$str_in = array();
			foreach( $mission_ids as $mission_id )
			{
				$param[] = $mission_id;
				$str_in[] = '?';
			}
			$sql .= " AND um.mission_id IN ( ".implode( ',', $str_in )." )";
		}
		return $this->$dsn->db->GetAssoc( $sql, $param );
	}

	/**
	 * 指定のユーザーミッション情報の結果種別レコード数の取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $result_type 結果種別（1:BEST, 2:NORMAL, 3:FAIL）
	 * @param array $mission_ids ミッションIDの配列（指定がない場合は存在する全データの合計）
	 * @param string $dsn DSN文字列
	 *
	 * @return array:ユーザーミッション情報のクリア数の配列 | null:エラー
	 */
	function getUserMissionResultRecordCount( $pp_id, $result_type, $mission_ids = array(), $dsn = "db_r" )
	{
		$colname = array(
			Pp_MissionManager::RESULT_TYPE_BEST   => 'best_clear',
			Pp_MissionManager::RESULT_TYPE_NORMAL => 'normal_clear',
			Pp_MissionManager::RESULT_TYPE_FAIL   => 'fail'
		);

		$param = array( $pp_id );
		$sql = "SELECT count( ".$colname[$result_type]." ) as count FROM ut_user_mission "
			 . "WHERE pp_id = ? ";
		if( !empty( $mission_ids ))
		{
			$str_in = array();
			foreach( $mission_ids as $mission_id )
			{
				$param[] = $mission_id;
				$str_in[] = '?';
			}
			$sql .= "AND mission_id IN ( ".implode( ',', $str_in )." ) ";
		}
		$sql .= "AND ".$colname[$result_type]." > 0";

		return $this->$dsn->GetOne( $sql, $param );
	}

	/**
	 * クリア済みミッションIDの一覧を取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param string $dsn DSN文字列
	 *
	 * @return array:クリア済みミッションIDの配列 | null:エラー
	 */
	function getClearedMissionIdList( $pp_id, $dsn = "db_r" )
	{
		$param = array( $pp_id );
		$sql = "SELECT mission_id FROM ut_user_mission "
			 . "WHERE pp_id = ? AND ( best_clear > 0 OR normal_clear > 0 ) "
			 . "ORDER BY mission_id";
		$data = $this->$dsn->GetAll( $sql, $param );
		if( $data === false )
		{
			return null;
		}
		$buff = array();
		foreach( $data as $v )
		{
			$buff[] = $v['mission_id'];
		}
		return $buff;
	}

	/**
	 * 唐之杜ミッション抽選処理が解放されているかをチェック
	 *
	 * @param int $pp_id サイコパスID
	 *
	 * @return true:解放済み | false:未解放
	 */
	function isReleasedKaranomori( $pp_id )
	{
		if( empty( $pp_id ))
		{
			return false;
		}

		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_key = "isReleasedKaranomori__".$pp_id;
		$data = $cache_m->get( $cache_key, 3600 );

		if( $data !== 1 )
		{	// キャッシュから取得できなかった
			$res = $this->getUserMission( $pp_id, self::KARANOMORI_RELEASE_MISSION_ID );

			// 取得したデータをキャッシュする
			$data = empty( $res ) ? 0 : 1;
			if( $data )
			{
				$cache_m->set( $cache_key, 1 );
			}
		}
		return (( $data === 1 ) ? true : false );
	}

	//================================================================================================
	//		ut_user_achievement_base_count に関する処理
	//================================================================================================
	function insertUserAchievementBaseCount( $pp_id, $ach_id )
	{
		$ach_m =& $this->backend->getManager( 'Achievement' );
		$cond_master = $ach_m->getMasterAchievementConditionListAssoc();

		$idx = $cond_master[$ach_id]['type'];
		if( $idx <= 16 )
		{	// 全ミッション共通
			$count = $this->getUserAchievementCount( $pp_id, "db" );
		}
		else
		{	// 指定ミッションのみ
			$count = $this->getUserMission( $pp_id, $cond_master[$ach_id]['cond_mission_id'], "db" );
		}

		// 参照カラムを取得
		if(( $idx == 4 )||( $idx == 19 ))
		{
			$cond_time = $cond_master[$ach_id]['cond_time'];
			$col_name = $ach_m->COLUMN_TABLE[$idx][$cond_time];
		}
		else
		{
			$col_name = $ach_m->COLUMN_TABLE[$idx];
		}

		$cnt = ( empty( $count )) ? 0 : $count[$col_name];
		$param = array( $pp_id, $ach_id, $col_name, $cnt );
		$sql = "INSERT INTO ut_user_achievement_base_count( pp_id, ach_id, column_name, base_count, date_created ) "
			 . "VALUES( ?, ?, ?, ?, NOW())";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// キャッシュをクリア
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_user_achievement_base_count__".$pp_id );
		$cache_m->clear( "ut_user_achievement_base_count__".$pp_id."__".$ach_id );

		return true;
	}

	/**
	 * ユーザー実績基準情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $ach_id 勲章ID
	 *
	 * @return array:ユーザー実績情報 | null:エラー
	 */
	function getUserAchievementBaseCount( $pp_id, $ach_id = null )
	{
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );

		if( is_null( $ach_id ))
		{	// 勲章IDの指定がなければ全取得
			// memcacheから取得してみる
			$cache_key = "ut_user_achievement_base_count__".$pp_id;
			$cache_data = $cache_m->get( $cache_key, 3600 );
			if( $cache_data && !Ethna::isError( $cache_data ))
			{	// キャッシュから取得できた
				return $cache_data;
			}

			$param = array( $pp_id );
			$sql = "SELECT a.ach_id, a.* FROM ut_user_achievement_base_count as a WHERE a.pp_id = ?";
			$data = $this->db->db->GetAssoc( $sql, $param );
		}
		else
		{	// 勲章IDの指定あり
			// memcacheから取得してみる
			$cache_key = "ut_user_achievement_base_count__".$pp_id."__".$ach_id;
			$cache_data = $cache_m->get( $cache_key, 3600 );
			if( $cache_data && !Ethna::isError( $cache_data ))
			{	// キャッシュから取得できた
				return $cache_data;
			}

			$param = array( $pp_id, $ach_id );
			$sql = "SELECT * FROM ut_user_achievement_base_count WHERE pp_id = ? AND ach_id = ?";
			$data = $this->db->GetRow( $sql, $param );
		}

		if( !empty( $data ))
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * ユーザー実績基準情報からの差分実績を取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param string $dsn DSN文字列
	 *
	 * @return array:ユーザー実績情報 | null:エラー
	 */
	function getUserAchievementCountBaseDiff( $pp_id, $dsn = "db_r" )
	{
		$ach_m =& $this->backend->getManager( 'Achievement' );

		// 獲得している勲章を取得
		$rank = $this->getUserAchievementRank( $pp_id );
		$got_ach_ids = array();
		if( !empty( $rank ))
		{
			foreach( $rank as $row )
			{
				$got_ach_ids[] = $row['ach_id'];
			}
		}

		// 解放済みの勲章のうち、未獲得の勲章IDを取得
		$ach_ids = $ach_m->getNextAchIds( $got_ach_ids );

		// ユーザーミッション情報を取得
		$user_missions = $this->getUserMissionListAssoc( $pp_id, array(), $dsn );

		// 実績情報を取得
		$total_count = $this->getUserAchievementCount( $pp_id, $dsn );	// 累計
		$base = $this->getUserAchievementBaseCount( $pp_id );			// 差分基準

		// 勲章条件マスタを取得
		$cond_master = $ach_m->getMasterAchievementConditionListAssoc();

		// 基準カウントからの差分実績を取得
		$data = array();
		foreach( $ach_ids as $ach_id )
		{
			$type = $cond_master[$ach_id]['type'];
			if( $type <= 16 )
			{	// 全ミッション共通
				if( isset( $base[$ach_id] ))
				{	// 差分基準がある
					$base_count = $base[$ach_id]['base_count'];
					$data[$ach_id] = $total_count[$base[$ach_id]['column_name']] - $base_count;
				}
				else
				{	// 差分基準がない
					if( is_array( $ach_m->COLUMN_TABLE[$type] ))
					{	// 条件が残り時間
						$cond_time = $cond_master[$ach_id]['cond_time'];
						$col_name = $ach_m->COLUMN_TABLE[$type][$cond_time];
					}
					else
					{	// 残り時間以外の条件
						$col_name = $ach_m->COLUMN_TABLE[$type];
					}
					$data[$ach_id] = $total_count[$col_name];
				}
			}
			else
			{	// 指定ミッションのみ
				$mission_id = $cond_master[$ach_id]['cond_mission_id'];		// 対象のミッションID
				if(( isset( $base[$ach_id] ))&&( isset( $user_missions[$mission_id] )))
				{	// 差分基準がある
					$base_count = $base[$ach_id]['base_count'];
					$data[$ach_id] = $user_missions[$mission_id][$base[$ach_id]['column_name']] - $base_count;
				}
				else
				{	// 差分基準がない
					if( is_array( $ach_m->COLUMN_TABLE[$type] ))
					{	// 条件が残り時間
						$cond_time = $cond_master[$ach_id]['cond_time'];
						$col_name = $ach_m->COLUMN_TABLE[$type][$cond_time];
					}
					else
					{	// 残り時間以外の条件
						$col_name = $ach_m->COLUMN_TABLE[$type];
					}
					$data[$ach_id] = ( isset( $user_missions[$mission_id] )) ? $user_missions[$mission_id][$col_name] : 0;
				}
			}
		}
		return $data;
	}

	//================================================================================================
	//		ut_user_achievement_count に関する処理
	//================================================================================================
	/**
	 * ユーザー実績情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param string $dsn DSN文字列
	 *
	 * @return array:ユーザー実績情報 | null:エラー
	 */
	function getUserAchievementCount( $pp_id, $dsn = "db_r" )
	{
		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_achievement_count WHERE pp_id = ?";
		return $this->$dsn->GetRow( $sql, $param );
	}

	/**
	 * ユーザー実績情報の加算
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $columns 加算するカラムと増減値の配列（カラム名 => 増減値）
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし):false | Ethna_Errorオブジェクト:更新エラー
	 */
	function addUserAchievementCount( $pp_id, $columns )
	{
		if( empty( $pp_id ) || !is_array( $columns ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		$param = array();
		$str_set = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = $k + ?";
			$param[] = $v;
		}
		$param[] = $pp_id;

		$sql = "UPDATE ut_user_achievement_count SET ".implode( ',', $str_set )." WHERE pp_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return true;
	}

	/**
	 * ユーザー実績情報の更新
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $columns 更新するカラムとデータの配列（カラム名 => データ）
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし):false | Ethna_Errorオブジェクト:更新エラー
	 */
	function updateUserAchievementCount( $pp_id, $columns )
	{
		if( empty( $pp_id ) || !is_array( $columns ))
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}

		$param = array();
		$str_set = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = ?";
			$param[] = $v;
		}
		$param[] = $pp_id;

		$sql = "UPDATE ut_user_achievement_count SET ".implode( ',', $str_set )." WHERE pp_id = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return true;
	}

	//================================================================================================
	//		ut_user_achievement_rank に関する処理
	//================================================================================================
	/**
	 * ユーザー勲章情報取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $ach_id 勲章ID
	 *
	 * @return array:ユーザー勲章情報 | null:取得エラー
	 */
	function getUserAchievementRank( $pp_id, $ach_id = null )
	{
		if( empty( $pp_id ))
		{	// サイコパスIDがなければ取得エラー
			return null;
		}

		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		if( is_null( $ach_id ))
		{	// 勲章IDの指定がなければ全勲章取得
			// memcacheから取得してみる
			$cache_key = "ut_user_achievement_rank__".$pp_id;
			$cache_data = $cache_m->get( $cache_key, 3600 );
			if( $cache_data && !Ethna::isError( $cache_data ))
			{	// キャッシュから取得できた
				return $cache_data;
			}

			$param = array( $pp_id );
			$sql = "SELECT * FROM ut_user_achievement_rank WHERE pp_id = ?";
			$data = $this->db->GetAll( $sql, $param );
		}
		else
		{	// 勲章IDの指定あり
			// memcacheから取得してみる
			$cache_key = "ut_user_achievement_rank__".$pp_id."__".$ach_id;
			$cache_data = $cache_m->get( $cache_key, 3600 );
			if( $cache_data && !Ethna::isError( $cache_data ))
			{	// キャッシュから取得できた
				return $cache_data;
			}

			// 取得できなければDBから取得
			$param = array( $pp_id, $ach_id );
			$sql = "SELECT * FROM ut_user_achievement_rank WHERE pp_id = ? AND ach_id = ?";
			$data = $this->db->GetRow( $sql, $param );
		}

		if( !empty( $data ))
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * ユーザー勲章情報の追加
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $ach_id 勲章ID
	 *
	 * @return true:正常終了 | Ethna_Errorオブジェクト:エラー
	 */
	function insertUserAchievementRank( $pp_id, $ach_id )
	{
		$param = array( $pp_id, $ach_id );
		$sql = "INSERT INTO ut_user_achievement_rank( pp_id, ach_id, date_created ) "
			 . "VALUES( ?, ?, NOW())";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 更新ができたらキャッシュをクリア
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_user_achievement_rank__".$pp_id );
		$cache_m->clear( "ut_user_achievement_rank__".$pp_id."__".$ach_id );

		return true;
	}

	//================================================================================================
	//		ut_user_item に関する処理
	//================================================================================================
	/**
	 * アイテム情報を取得する
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $item_id アイテムID
	 * @param string $dsn DSN情報
	 * @return array 取得データ
	 */
	function getUserItem ( $pp_id, $item_id, $dsn = "db" )
	{
		$param = array( $pp_id, $item_id );
		$sql = "SELECT * FROM ut_user_item WHERE pp_id = ? AND item_id = ?";
		$result = $this->$dsn->GetRow( $sql, $param );

		if ( Ethna::isError( $result ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->$dsn->db->ErrorNo(), $this->$dsn->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return $result;
	}

	/**
	 * アイテム一覧を取得する
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $dsn DSN文字列
	 * @return array
	 */
	function getUserItemList ( $pp_id, $dsn = "db" )
	{
		$param = array( $pp_id );
		$sql = "SELECT ui.item_id AS id, ui.* FROM ut_user_item ui WHERE ui.pp_id = ? AND ui.num > 0 ORDER BY ui.item_id ASC";
		$result = $this->$dsn->db->GetAssoc( $sql, $param );

		if ( Ethna::isError( $result ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->$dsn->db->ErrorNo(), $this->$dsn->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return $result;
	}

	/**
	 * アイテムを増減させる（0～上限まで）
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $item_id アイテムID
	 * @param int $num 増減値
	 * @return bool|object 処理結果
	 */
	function updateUserItem ( $pp_id, $item_id, $num )
	{
		$trial = $this->backend->config->get( 'trial' );
		if( !empty( $trial ))
		{	// 体験版だと減らない
			$num = 0;
		}

		// 増減しない場合
		if ( $num == 0 ) {
			return true;
		}

		$item_m =& $this->backend->getManager( "Item" );

		$item = $this->getUserItem( $pp_id, $item_id, "db" );
		$m_item = $item_m->getMasterItem( $item_id );

		$num_new = $num;

		//データがある
		if (( !$item )&&( $num < 0 )) return false;		// アイテムを持っていないのにエラーの場合はエラー

		// INSERT UPDATE
		$param = array( $pp_id, $item_id, $num_new, $num, $m_item['maximum'], $num, $num, $m_item['maximum'] );
		$sql = "INSERT INTO ut_user_item( pp_id, item_id, num, date_created, date_modified ) "
			 . "VALUES( ?, ?, ?, NOW(), NOW() ) ON DUPLICATE KEY UPDATE num = IF((( num + ? ) <= ? ), IF((( num + ? ) < 0 ), 0, ( num + ? )), ? ), date_modified = NOW()";
		if ( !$this->db->execute( $sql, $param ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, $this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return true;
	}

	//================================================================================================
	//		ut_user_tutorial に関する処理
	//================================================================================================
	/**
	 * チュートリアル情報取得
	 */
	function getUserTutorial ( $pp_id, $dsn = "db" )
	{
		$param = array( $pp_id );
		$sql = "SELECT pp_id, CONV( flag0, 2, 10 ) AS flag FROM ut_user_tutorial WHERE pp_id = ?";

		return $this->$dsn->GetRow( $sql, $param );
	}

	/**
	 * チュートリアル情報更新
	 */
	function updateUserTutorial ( $pp_id, $tutorial_id )
	{
		$param = array( $pp_id, $tutorial_id, $tutorial_id );
		$sql = "INSERT INTO ut_user_tutorial( pp_id, flag0 ) VALUES( ?, ? ) ON DUPLICATE KEY UPDATE flag0 = ?";
		if ( !$this->db->execute( $sql, $param ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
										$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return true;
	}


	//================================================================================================
	//		ut_user_device_info に関する処理
	//================================================================================================
	/**
	 * 端末情報更新
	 */
	function updateUserDeviceInfo( $pp_id, $columns )
	{
		$period = date( "ym" );
		$ua = $columns['ua'];
		$content = json_encode( $columns );
		$param = array( $period, $pp_id, $ua, $content, $content );
		$sql = "INSERT INTO ut_user_device_info( period, pp_id, ua, content, date_created, date_modified ) "
			 . "VALUES( ?, ?, ?, ?, NOW(), NOW()) "
			 . "ON DUPLICATE KEY UPDATE content = ?";
		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	//================================================================================================
	//		ut_user_voting に関する処理
	//================================================================================================
	/**
	 * 投票ポイント情報取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param string $dsn DSN文字列
	 */
	function getUserVoting( $pp_id, $dsn = "db_r" )
	{
		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_voting WHERE pp_id = ?";
		$result = $this->$dsn->GetRow( $sql, $param );
		if ( Ethna::isError( $result ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->$dsn->db->ErrorNo(), $this->$dsn->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return $result;
	}

	/**
	 * 投票ポイント加算
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $point ポイント増減値（消費の場合はマイナス値）
	 * @return bool|object 処理結果
	 */
	function addUserVotingPoint( $pp_id, $point )
	{
		if( $point > 0 )
		{	// ポイント加算
			$param = array( $pp_id, $point, $point, $point, $point );
			$sql = "INSERT INTO ut_user_voting( pp_id, point, total_point, date_created, date_modified ) "
				 . "VALUES( ?, ?, ?, NOW(), NOW()) "
				 . "ON DUPLICATE KEY UPDATE point = point + ?, total_point = total_point + ?";
		}
		else if( $point < 0 )
		{	// ポイント消費
			$param = array( $point, $pp_id );
			$sql = "UPDATE ut_user_voting SET point = point + ? WHERE pp_id = ?";
		}
		else
		{	// ポイント変動なし
			return true;
		}

		if( !$this->db->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	//================================================================================================
	//		ut_user_flag に関する処理
	//================================================================================================
	/**
	 * フラッグ情報一覧を取得
	 * 
	 * @param int $pp_id サイコパスID
	 * @param string $dsn 接続インスタンス名（デフォルトはスレーブ）
	 * @return array データ配列（キーはflag_id）
	 */
	function getUserFlagList ( $pp_id, $dsn = "db_r" )
	{
		$param = array( $pp_id );
		$sql = "SELECT flag_id AS id, t.* FROM ut_user_flag t WHERE pp_id = ?";
		
		return $this->$dsn->db->getAssoc( $sql, $param );
	}
	
	/**
	 * フラッグ情報を取得
	 * 
	 * @param int $pp_id サイコパスID
	 * @param int $flag_id フラッグID
	 * @param string $dsn 接続インスタンス名（デフォルトはスレーブ）
	 * @return array データ配列
	 */
	function getUserFlag ( $pp_id, $flag_id, $dsn = "db_r" )
	{
		$param = array( $pp_id, $flag_id );
		$sql = "SELECT * FROM ut_user_flag t WHERE pp_id = ? AND flag_id = ?";
		
		return $this->$dsn->GetRow( $sql, $param );
	}
	
	/**
	 * フラッグ情報を追加（存在するなら上書き）
	 * 
	 * @param int $pp_id サイコパスID
	 * @param int $flag_id フラッグID
	 * @param int $value フラッグ値（デフォルトは1）
	 * @param string $memo メモ（デフォルトはnull）
	 * @return bool 処理結果
	 */
	function insertUserFlag ( $pp_id, $flag_id, $value = 1, $memo = null )
	{
		$param = array( $pp_id, $flag_id, $value, $memo, $value, $memo );
		$sql = "INSERT INTO ut_user_flag( pp_id, flag_id, value, memo, date_created ) VALUES( ?, ?, ?, ?, NOW() ) ON DUPLICATE KEY UPDATE value = ?, memo = ?";
		
		return $this->db->execute( $sql, $param );
	}

	//================================================================================================
	//		その他（ユーティリティとか）
	//================================================================================================
	/**
	 *	ゲーム管理情報を取得
	 */
	function getGameCtrl()
	{
		$sql = "SELECT * FROM ut_game_ctrl WHERE id = 1";
		return $this->db_r->GetRow( $sql );
	}
	/**
	 * メンテナンス用コントロールDBを更新する
	 */
	function updGameCtrl($columns)
	{
		// 更新
		$param = array($columns['status'],$columns['btf'],$columns['date_start'],$columns['date_end']);
		$sql = "UPDATE ut_game_ctrl SET status = ?, btf = ?, date_start = ?, date_end = ? WHERE id = 1";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		/*多人数が同じ値を同時に書き込む可能性があるからaffected_rowsは確認しない
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		*/
		return true;
	}
	function insGameCtrl($columns)
	{
		// INSERT
		$param = array($columns['date_start'],$columns['date_end']);
		$sql = "INSERT INTO ut_game_ctrl(id, status, btf, date_start, date_end) VALUES(1, 0, 0, ?, ?)";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
}
