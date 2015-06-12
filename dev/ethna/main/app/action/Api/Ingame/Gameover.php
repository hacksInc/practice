<?php
/**
 *	Api/Ingame/Gameover.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once "Pp_MissionManager.php";

/**
 *	api_ingame_gameover Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiIngameGameover extends Pp_ApiActionForm
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
		'c'
	);
}

/**
 *	api_ingame_gameover action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiIngameGameover extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_ingame_gameover Action.
	 *
	 *	@access public
	 *	@return string	  forward name(null: success.
	 *								  false: in case you want to exit.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{
			return 'error_400';
		}
		return null;
	}

	/**
	 *	api_ingame_gameover action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		$play_id = $this->af->get( 'play_id' );										// プレイID
		$api_transaction_id = $this->getApiTransactionId();							// トランザクションID
		$ingame_result = array();
		$ingame_result['status'] = $this->af->get( 'status' );						// クリアステータス
		$ingame_result['zone'] = $this->af->get( 'zone' );							// プレイヤーが最後にいたゾーン番号
		$ingame_result['paralyzer'] = $this->af->get( 'paralyzer' );				// パラライザー執行数
		$ingame_result['eliminator'] = $this->af->get( 'eliminator' );				// エリミネーター執行数
		$ingame_result['decomposer'] = $this->af->get( 'decomposer' );				// デコンポーザー執行数
		$ingame_result['life'] = $this->af->get( 'life' );							// 終了時の残りライフ割合
		$ingame_result['paralyzer_boss'] = $this->af->get( 'paralyzer_boss' );		// BOSSをパラライザーで倒した数
		$ingame_result['eliminator_boss'] = $this->af->get( 'eliminator_boss' );	// BOSSをエリミネーターで倒した数
		$ingame_result['decomposer_boss'] = $this->af->get( 'decomposer_boss' );	// BOSSをデコンポーザーで倒した数
		$ingame_result['remain_time'] = $this->af->get( 'remain_time' );			// 終了時の残り時間
		$ingame_result['persuasion'] = $this->af->get( 'persuasion' );				// [説得]での交渉成功数
		$ingame_result['reprimand'] = $this->af->get( 'reprimand' );				// [叱責]での交渉成功数
		$ingame_result['warning'] = $this->af->get( 'warning' );					// [警告]での交渉成功数

		if(empty($pp_id))
		{
			//$pp_id = 918297118;
			$pp_id = 916150486;
			$play_id = 'hogehogehoge';
			$api_transaction_id = time();
			$mission_id = 10010101;

			$ingame_result['status'] = 0;
			$ingame_result['zone'] = 1;
			$ingame_result['paralyzer'] = 0;
			$ingame_result['eliminator'] = 0;
			$ingame_result['decomposer'] = 0;
			$ingame_result['life'] = 0;
			$ingame_result['paralyzer_boss'] = 0;
			$ingame_result['eliminator_boss'] = 0;
			$ingame_result['decomposer_boss'] = 0;
			$ingame_result['remain_time'] = 0;
			$ingame_result['persuasion'] = 0;
			$ingame_result['reprimand'] = 0;
			$ingame_result['warning'] = 0;

		}

		// マネージャのインスタンスを取得
		$user_m =& $this->backend->getManager( 'User' );
		$character_m =& $this->backend->getManager( 'Character' );
		$transaction_m =& $this->backend->getManager( 'Transaction' );
		$mission_m =& $this->backend->getManager( 'Mission' );
		$photo_gacha_m =& $this->backend->getManager( 'PhotoGacha' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );

		// 定時ストレスケア処理を実行（多重処理チェックの前にやること！）
		$res = $character_m->stressCare( $pp_id, $api_transaction_id );
		if( Ethna::isError( $res ))
		{
			$this->backend->logger->log( LOG_ERR, 'fixed stress care error.' );
			$this->af->setApp( 'status_detail_code', SDC_FIXED_STRESS_CARE_ERROR, true );
			return 'error_500';
		}

		// 多重処理防止チェック
		$json = $transaction_m->getResultJson( $api_transaction_id );
		if( !empty( $json ))
		{	// 既に一度処理している
			$this->backend->logger->log( LOG_INFO, 'Found api_transaction_id.' );
			$temp = json_decode( $json, true );

			if( $res === true )
			{	// 定時ストレスケア処理で更新があった場合、処理結果を更新
				$user_game = $user_m->getUserGame( $pp_id );
				if( empty( $user_game ))
				{
					$this->backend->logger->log( LOG_ERR, 'Not found user_game. pp_id='.$pp_id );
					$this->af->setApp( 'status_detail_code', SDC_USER_NONEXISTENCE, true );
					return 'error_500';
				}
				$user_character = $character_m->getUserCharacterAssoc( $pp_id );
				if( empty( $user_character ))
				{
					$this->backend->logger->log( LOG_ERR, 'Not found user_character. pp_id='.$pp_id );
					$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
					return 'error_500';
				}

				// 定時ストレスケア後の値で上書き
				$temp['modify_user_base']['crime_coef'] = $user_game['crime_coef'];
				$temp['modify_user_base']['ex_stress_care'] = $user_game['ex_stress_care'];
				foreach( $temp['support_character'] as $k => $row )
				{
					$chara_id = $row['character_id'];
					$temp['support_character'][$k]['crime_coef'] = $user_character[$chara_id]['crime_coef'];
					$temp['support_character'][$k]['ex_stress_care'] = $user_character[$chara_id]['ex_stress_care'];
				}

				// 上書きした値でトランザクション情報を更新する
				$result_json = json_encode( $temp );
				$res = $transaction_m->registTransaction( $pp_id, $api_transaction_id, $result_json );
				if( $res !== true )
				{	// 記録エラー
					$this->backend->logger->log( LOG_ERR, 'transaction update error.' );
					$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
					return 'error_500';
				}
			}

			foreach( $temp as $k => $v )
			{
				$this->af->setApp( $k, $v, true );
			}
			return 'api_json_encrypt';
		}

		// InGame開始前の保持データを取得
		$before_data = $user_m->getUserIngame( $pp_id );
		if( empty( $before_data ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'getUserIngame() error.' );
			return 'error_500';
		}

		// ミッションマスタ情報を取得
		$mission_master = $mission_m->getMasterMission( $before_data['mission_id'] );
		if( empty( $mission_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'getMasterMission()' );
			return 'error_500';
		}

		// 実行ミッションが所属するエリアのマスタ情報を取得
		$area_master = $mission_m->getMasterArea( $mission_master['area_id'] );
		if( empty( $area_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'getMasterArea()' );
			return 'error_500';
		}

		// 同行サポートキャラの情報を取得
		$support_chara = $character_m->getUserCharacter( $pp_id, $before_data['accompany_character_id'] );
		if( empty( $support_chara ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'getUserCharacter() error.' );
			return 'error_500';
		}

		// ユーザーゲーム情報を取得
		$user_game = $user_m->getUserGame( $pp_id );
		if( empty( $user_game ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'getUserGame() error.' );
			return 'error_500';
		}

		// 全サポートキャラ情報を取得
		$character = $character_m->getUserCharacter( $pp_id );
		if( empty( $character ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'getUserCharacter() error.' );
			return 'error_500';
		}

		// 最新のマップ情報を取得
		$map_list = $this->_getMapList( $pp_id, $user_m, $mission_m );
		if( empty( $map_list ))
		{
			$this->backend->logger->log( LOG_ERR, '_getMapList() error.' );
			return 'error_500';
		}
		list( $stage_list, $area_list, $mission_list ) = $map_list;

		// 利用可能ガチャ情報を取得
		$cleared_mission_ids = $user_m->getClearedMissionIdList( $pp_id );		// クリア済みミッションIDの取得
		if( is_null( $cleared_mission_ids ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'getClearedMissionIdList() error.' );
			return 'error_500';
		}
		if( !empty( $cleared_mission_ids ))
		{
			$gacha_info = $photo_gacha_m->getMasterPhotoGachaAvailable( $cleared_mission_ids );
		}

		// チュートリアル情報を取得
		$user_tuto = $user_m->getUserTutorial( $pp_id );
		if( empty( $user_tuto ))
		{	// 取得エラー
			return 'error_500';
		}

		//-------------------------------------------------------------------
		//		プレイIDチェック
		//-------------------------------------------------------------------
		if( $before_data['play_id'] != $play_id )
		{	// プレイIDが異なる（最新のプレイIDではない）
			$this->backend->logger->log( LOG_ERR, 'Ingame/GameOver: play_id error!!' );
			$this->af->setApp( 'status_detail_code', SDC_MISSION_PLAYID_INVALID, true );
			return 'error_500';
		}

		// DBトランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		$this->backend->logger->log( LOG_INFO, '@@@@ GameOver.php : database update.' );

		try
		{
			//-------------------------------------------------------------------
			//		 KPIとかログの処理
			//-------------------------------------------------------------------
			// ミッション結果履歴
			$columns = array(
				'pp_id' => $pp_id,
				'api_transaction_id' => $api_transaction_id,
				'play_id' => $play_id,
				'mission_id' => $before_data['mission_id'],
				'result_type' => Pp_MissionManager::RESULT_TYPE_RETIRE,
				'status' => $ingame_result['status'],
				'zone' => $ingame_result['zone']
			);
			$res = $logdata_m->logIngameResult( $columns );

			//-------------------------------------------------------------------
			//		クライアントへの返却データを作成
			//-------------------------------------------------------------------
			// 処理結果をトランザクション情報として記録する
			$buff = array();

			// ミッションの結果
			$mission_result = array(
				'mission_id' => ( int )$before_data['mission_id'],						// プレイしたミッションID
				'unlock_stage_id' => 0,													// 新規解放ステージID
				'unlock_area_id' => 0,													// 新規解放エリアID
				'unlock_mission_id' => 0,												// 新規解放ミッションID
				'unlock_support_id' => 0,												// 解放サポートキャラID
				'clear_type' => Pp_MissionManager::RESULT_TYPE_RETIRE,					// クリア種別
				'first_clear' => 0,														// 初クリアか？
				'get_photo_id' => 0,													// 獲得したフォトID
				'karanomori_status' => Pp_MissionManager::KARANOMORI_STATUS_NONE,		// 唐之杜調査結果
				'p_hazard_diff' => ( int )$before_data['hazard_diff_pl'],				// サイコハザード発生中と未発生での犯罪係数上昇値差分
				's_hazard_diff' => ( int )$before_data['hazard_diff_sp'],				// サイコハザード発生中と未発生での犯罪係数上昇値差分
				'p_crime_coef_before' => ( int )$before_data['crime_coef_pl'],			// 更新前のプレイヤー犯罪係数
				'p_crime_coef_after' => ( int )$before_data['crime_coef_pl_after'],		// 更新後のプレイヤー犯罪係数
				'p_body_coef_before' => ( int )$user_game['body_coef'],					// 更新前のプレイヤー身体係数
				'p_body_coef_after' => ( int )$user_game['body_coef'],					// 更新後のプレイヤー身体係数
				'p_intelli_coef_before' => ( int )$user_game['intelli_coef'],			// 更新前のプレイヤー知能係数
				'p_intelli_coef_after' => ( int )$user_game['intelli_coef'],			// 更新後のプレイヤー知能係数
				'p_mental_coef_before' => ( int )$user_game['mental_coef'],				// 更新前のプレイヤー心的係数
				'p_mental_coef_after' => ( int )$user_game['mental_coef'],				// 更新後のプレイヤー心的係数
				'support_charcter_id' => ( int )$before_data['accompany_character_id'],	// 同行したサポートキャラID
				's_crime_coef_before' => ( int )$before_data['crime_coef_sp'],			// 更新前のサポートキャラ犯罪係数
				's_crime_coef_after' => ( int )$before_data['crime_coef_sp_after'],		// 更新後のサポートキャラ犯罪係数
				's_body_coef_before' => ( int )$support_chara['body_coef'],				// 更新前のサポートキャラ身体係数
				's_body_coef_after' => ( int )$support_chara['body_coef'],				// 更新後のサポートキャラ身体係数
				's_intelli_coef_before' => ( int )$support_chara['intelli_coef'],		// 更新前のサポートキャラ知能係数
				's_intelli_coef_after' => ( int )$support_chara['intelli_coef'],		// 更新後のサポートキャラ知能係数
				's_mental_coef_before' => ( int )$support_chara['mental_coef'],			// 更新前のサポートキャラ心的係数
				's_mental_coef_after' => ( int )$support_chara['mental_coef']			// 更新後のサポートキャラ心的係数
			);
			$buff['mission_result'] = $mission_result;

			// 全サポートキャラクター情報
			$support_character = array();
			foreach( $character as $v )
			{
				$support_character[] = array(
					'character_id' => ( int )$v['character_id'],		// キャラクターID
					'crime_coef' => ( int )$v['crime_coef'],			// 犯罪係数
					'body_coef' => ( int )$v['body_coef'],				// 身体係数
					'intelli_coef' => ( int )$v['intelli_coef'],		// 知能係数
					'mental_coef' => ( int )$v['mental_coef'],			// 心的係数
					'ex_stress_care' => ( int )$v['ex_stress_care']		// 臨時ストレスケア回数
				);
			}
			$buff['support_character'] = $support_character;

			// ユーザーゲーム情報差分
			$modify_user_base = array(
				'crime_coef' => ( int )$user_game['crime_coef'],		// 犯罪係数
				'body_coef' => ( int )$user_game['body_coef'],			// 身体係数
				'intelli_coef' => ( int )$user_game['intelli_coef'],	// 知能係数
				'mental_coef' => ( int )$user_game['mental_coef'],		// 心的係数
				'ex_stress_care' => ( int )$user_game['ex_stress_care']	// 臨時ストレスケア回数
			);
			$buff['modify_user_base'] = $modify_user_base;

			// InGame結果
			$avg = 0;
			foreach( $stage_list as $row )
			{
				if( $row['stage_id'] == $area_master['stage_id'] )
				{
					$avg = $row['ave_area_stress'];
					break;
				}
			}
			$ingame = array(
				'paralyzer' => $ingame_result['paralyzer'],				// パラライザー執行数
				'eliminator' => $ingame_result['eliminator'],			// エリミネーター執行数
				'decomposer' => $ingame_result['decomposer'],			// ドミネーター執行数
				'stage_id' => $area_master['stage_id'],					// 実行したミッションのステージID
				'ave_area_stress' => $avg								// 実行したミッションが所属するエリアのエリア平均ストレス値
			);
			$buff['ingame_result'] = $ingame;

			// 利用可能ガチャリスト
			$gacha_list = array();
			if( !empty( $gacha_info ))
			{
				foreach( $gacha_info as $v )
				{
					$gacha_list[] = array(
						'gacha_id' => $v['gacha_id'],	// ガチャID
						'stage_id' => $v['stage_id'],	// 対象ステージID
						'type' => $v['type'],			// ガチャ種別
						'price' => $v['price']			// 価格（使用するフォトフィルムの数）
					);
				}
			}
			$buff['gacha_list'] = $gacha_list;

			// チュートリアル情報
			$user_tutorial = array( 'tutorial' => $user_tuto['flag'] );
			$buff['user_tutorial'] = $user_tutorial;

			$buff['stage_list'] = $stage_list;
			$buff['area_list'] = $area_list;
			$buff['mission_list'] = $mission_list;

			$result_json = json_encode( $buff );		// JSON文字列にする
			$res = $transaction_m->registTransaction( $pp_id, $api_transaction_id, $result_json );
			if( $res !== true )
			{	// 記録エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			$this->af->setApp( 'mission_result', $mission_result, true ); 
			$this->af->setApp( 'support_character', $support_character, true ); 
			$this->af->setApp( 'modify_user_base', $modify_user_base, true ); 
			$this->af->setApp( 'gacha_list', $gacha_list, true ); 
			$this->af->setApp( 'user_tutorial', $user_tutorial, true ); 
			$this->af->setApp( 'stage_list', $stage_list, true ); 
			$this->af->setApp( 'area_list', $area_list, true ); 
			$this->af->setApp( 'mission_list', $mission_list, true ); 
			$this->af->setApp( 'ingame_result', $ingame, true ); 

			$db->commit();
		}
		catch( Exception $e )
		{	// 例外発生
			$db->rollback();		// 更新をロールバックする
			return 'error_500';
		}

		return 'api_json_encrypt';
	}

	/**
	 * 最新のマップ情報を取得
	 *
	 * @param array $pp_id サイコパスID
	 * @param array $user_m Pp_UserManagerのインスタンス
	 * @param array $misson_m Pp_MissionManagerのインスタンス
	 *
	 * @return array ステージ情報,エリア情報,ミッション情報の配列
	 */
	private function _getMapList( $pp_id, $user_m, $mission_m )
	{
		// ステージマスタリストの取得
		$ms = $mission_m->getMasterStageList();
		if( empty( $ms ))
		{	// 取得エラー
			return null;
		}

		// 最新の全ユーザーステージ情報を取得
		$us = $user_m->getUserStageList( $pp_id );
		if( empty( $us ))
		{	// 取得エラー
			return null;
		}

		// エリアマスタリストの取得
		$ma = $mission_m->getMasterAreaList();
		if( empty( $ma ))
		{	// 取得エラー
			return null;
		}

		// 最新の全ユーザーエリア情報を取得（唐之杜エリアは除外）
		$area_ids = array();
		foreach( $ma as $area_id => $row )
		{
			if( $row['type'] == Pp_MissionManager::AREA_TYPE_KARANOMORI )
			{	// 唐之杜エリアは除外
				continue;
			}
			$area_ids[] = $area_id;
		}
		$ua = $user_m->getUserAreaList( $pp_id, $area_ids );
		if( empty( $ua ))
		{
			return null;
		}

		// 唐之杜ミッション以外のミッションマスタ情報を取得
		$mm = $mission_m->getMasterMissionListAssocByAreaIds( $area_ids );
		$mission_ids = array_keys( $mm );

		// 最新の全ユーザーミッション情報を取得（唐之杜ミッションは除外）
		$um = $user_m->getUserMissionList( $pp_id, $mission_ids );
		if( empty( $um ))
		{
			return null;
		}

		// エリアストレス平均値の取得
		$area_stress_avg = $mission_m->getAverageAreaStressAssoc( $pp_id, "db" );

		// データを整形
		$mission_list = array();
		foreach( $um as $row )
		{
			$mission_id = $row['mission_id'];
			$mission_list[] = array(
				'mission_id' => $mission_id,
				'area_id' => $mm[$mission_id]['area_id']
			);
		}

		$area_list = array();
		foreach( $ua as $row )
		{
			$area_id = $row['area_id'];
			$area_list[] = array(
				'area_id' => $area_id,
				'stage_id' => $ma[$area_id]['stage_id'],
				'type' => $ma[$area_id]['type'],
				'area_stress' => $row['area_stress'],
				'psychohazard' => $row['status']
			);
		}

		$stage_list = array();
		$karanomori_buff = array();
		foreach( $us as $row )
		{
			$stage_id = $row['stage_id'];
			if( !array_key_exists( $stage_id, $karanomori_buff ))
			{
				$karanomori_buff[$stage_id] = explode( ',', $ms[$stage_id]['karanomori_find_prob'] );
			}

			$avg = ( isset( $area_stress_avg[$stage_id] )&&( !is_null( $area_stress_avg[$stage_id] ))) ? $area_stress_avg[$stage_id] : 0;
			$investigate_rate = ( $row['karanomori_report'] == 0 ) ? 0 : $karanomori_buff[$stage_id][$row['karanomori_report']-1];
			$stage_list[] = array(
				'stage_id' => $stage_id,
				'ave_area_stress' => $avg,
				'investigate_rate' => $investigate_rate
			);
		}
		return array( $stage_list, $area_list, $mission_list );
	}

}
