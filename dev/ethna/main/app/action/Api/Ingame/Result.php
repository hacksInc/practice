<?php
/**
 *	Api/Ingame/Result.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_ingame_result Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiIngameResult extends Pp_ApiActionForm
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
 *	api_ingame_result action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiIngameResult extends Pp_ApiActionClass
{
	// エリアストレスの最大値・最小値
	const AREA_STRESS_MAX = 10;		// 最大値
	const AREA_STRESS_MIN = 0;		// 最小値

	/**
	 *	preprocess of api_ingame_result Action.
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
	 *	api_ingame_result action implementation.
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
		$ingame_result['battery'] = $this->af->get( 'battery' );					// 終了時の残りドミネーターバッテリー割合
		$ingame_result['life'] = $this->af->get( 'life' );							// 終了時の残りライフ割合
		$ingame_result['paralyzer_boss'] = $this->af->get( 'paralyzer_boss' );		// BOSSをパラライザーで倒した数
		$ingame_result['eliminator_boss'] = $this->af->get( 'eliminator_boss' );	// BOSSをエリミネーターで倒した数
		$ingame_result['decomposer_boss'] = $this->af->get( 'decomposer_boss' );	// BOSSをデコンポーザーで倒した数
		$ingame_result['remain_time'] = $this->af->get( 'remain_time' );			// 終了時の残り時間
		$ingame_result['persuasion'] = $this->af->get( 'persuasion' );				// [説得]での交渉成功数
		$ingame_result['reprimand'] = $this->af->get( 'reprimand' );				// [叱責]での交渉成功数
		$ingame_result['warning'] = $this->af->get( 'warning' );					// [警告]での交渉成功数

		// マネージャのインスタンスを取得
		$user_m =& $this->backend->getManager( 'User' );
		$character_m =& $this->backend->getManager( 'Character' );
		$mission_m =& $this->backend->getManager( 'Mission' );
		$photo_m =& $this->backend->getManager( 'Photo' );
		$photo_gacha_m =& $this->backend->getManager( 'PhotoGacha' );
		$transaction_m =& $this->backend->getManager( 'Transaction' );
		$achievement_m =& $this->backend->getManager( 'Achievement' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$present_m =& $this->backend->getManager( 'Present' );
		$kpi_m = $this->backend->getManager( 'Kpi' );
		$raid_m = $this->backend->getManager( 'Raid' );

		// ここでログインのチェックを行う（当日再ログインはスルーされる）
		$ret = $user_m->updateUserGameLogin( $pp_id, false );
		if( $ret !== true )
		{	// 更新エラー
			$this->backend->logger->log( LOG_ERR, 'error: updateUserGameLogin( '.$pp_id.', false )' );
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// 定時ストレスケア処理を実行（トランザクションチェックの前にやること！）
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
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getUserIngame()' );
			return 'error_500';
		}

		// ミッションマスタ情報を取得
		$mission_master = $mission_m->getMasterMission( $before_data['mission_id'] );
		if( empty( $mission_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getMasterMission()' );
			return 'error_500';
		}

		// 実行ミッションが所属するエリアのマスタ情報を取得
		$area_master = $mission_m->getMasterArea( $mission_master['area_id'] );
		if( empty( $area_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getMasterArea()' );
			return 'error_500';
		}

		// 実行ミッションが所属するエリアが所属するステージのマスタ情報を取得
		$stage_master = $mission_m->getMasterStage( $area_master['stage_id'] );
		if( empty( $stage_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getMasterStage()' );
			return 'error_500';
		}

		// ユーザー基本情報を取得
		$user_base = $user_m->getUserBase( $pp_id );
		if( empty( $user_base ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getUserBase()' );
			return 'error_500';
		}

		// ユーザーゲーム情報を取得
		$user_game = $user_m->getUserGame( $pp_id );
		if( empty( $user_game ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getUserGame()' );
			return 'error_500';
		}

		// ユーザーミッション情報を取得
		$user_mission = $user_m->getUserMission( $pp_id, $mission_master['mission_id'] );
		if( $user_mission === false )
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getUserMission()' );
			return 'error_500';
		}

		// ユーザーステージ情報を取得
		$user_stage = $user_m->getUserStage( $pp_id, $area_master['stage_id'] );
		if( $user_stage === false )
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getUserStage()' );
			return 'error_500';
		}

		// 同行サポートキャラの情報を取得
		$support_chara = $character_m->getUserCharacter( $pp_id, $before_data['accompany_character_id'] );
		if( empty( $support_chara ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getUserCharacter()' );
			return 'error_500';
		}

		// 勲章グループマスタを取得
		$ach_group_master = $achievement_m->getMasterAchievementGroupListAssoc();
		if( empty( $ach_group_master ))
		{
			return 'error_500';
		}

		// 勲章条件マスタのリストを取得
		$group_ids = array_keys( $ach_group_master );
		$ach_cond_master = $achievement_m->getMasterAchievementConditionByGroupIdAssoc( $group_ids );
		if( empty( $ach_cond_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: getMasterAchievementConditionByGroupIdAssoc()' );
			return 'error_500';
		}

		// 同行サポートキャラのマスタ情報を取得
		$support_chara_master = $character_m->getMasterCharacter( $before_data['accompany_character_id'] );
		if( empty( $support_chara_master ))
		{	// 取得エラー
			return 'error_500';
		}

		// チュートリアル情報を取得
		$user_tuto = $user_m->getUserTutorial( $pp_id );
		if( empty( $user_tuto ))
		{	// 取得エラー
			return 'error_500';
		}

		// 唐之杜ミッションかどうか
		$is_karanomori_area = ( $area_master['type'] == Pp_MissionManager::AREA_TYPE_KARANOMORI ) ? true : false;

		//-------------------------------------------------------------------
		//		プレイIDチェック
		//-------------------------------------------------------------------
		if( $before_data['play_id'] != $play_id )
		{	// プレイIDが異なる（最新のプレイIDではない）
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: play_id error!!' );
			$this->af->setApp( 'status_detail_code', SDC_MISSION_PLAYID_INVALID, true );
			return 'error_500';
		}

		//-------------------------------------------------------------------
		//		リザルト処理種別の取得（BEST/NORMAL/FAILのどれよ？）
		//-------------------------------------------------------------------
		$result_type = $this->_getResultType( $mission_master, $ingame_result );
		if( is_null( $result_type ))
		{	// BEST条件フォーマットエラー
			$this->backend->logger->log( LOG_ERR, 'Ingame/Result: _getResultType()' );
			return 'error_500';
		}

		//-------------------------------------------------------------------
		//		新規解放マップチェック
		//-------------------------------------------------------------------
		$result_first_clear = false;
		$next_info = null;
		if( $result_type !== Pp_MissionManager::RESULT_TYPE_FAIL )
		{	// ミッション成功
			if(( empty( $user_mission ))||(( $user_mission['best_clear'] == 0 )&&( $user_mission['normal_clear'] == 0 )))
			{	// レコードがない、もしくはBEST/NORMALでの終了回数が０なら初クリア
				$next_info = $mission_m->getNextMainMissionIdInfo( $before_data['mission_id'] );	// 次のミッション情報
				if( is_null( $next_info ) ||( $next_info === false ))
				{	// 取得エラー
					return 'error_500';
				}
			}
		}

		if( empty( $next_info ))
		{	// 新規解放なし
			$next_info = array( 'stage_id' => 0, 'area_id' => 0, 'mission_id' => 0 );
		}
		else
		{	// 新規解放あり
			if( $next_info['stage_id'] == $stage_master['stage_id'] )
			{	// 新規にステージが解放されたわけじゃない
				$next_info['stage_id'] = 0;
			}
			if( $next_info['area_id'] == $area_master['area_id'] )
			{	// 新規にエリアが解放されたわけじゃない
				$next_info['area_id'] = 0;
			}
		}

		//-------------------------------------------------------------------
		//		通算でこのミッションが初めてのクリアかどうかのフラグ
		//-------------------------------------------------------------------
		$first_clear = false;
		if(( $result_type == Pp_MissionManager::RESULT_TYPE_NORMAL )||( $result_type == Pp_MissionManager::RESULT_TYPE_BEST ))
		{
			if( empty( $user_mission ))
			{	// レコードがなければ初クリア
				$first_clear = true;
			}
			else if(( $user_mission['normal_clear'] == 0 )&&( $user_mission['best_clear'] == 0 ))
			{	// NORMALもBESTも０回なら初クリア
				$first_clear = true;
			}
		}

		//-------------------------------------------------------------------
		//		結果種別毎に初めての終了かのフラグ
		//-------------------------------------------------------------------
		if( $is_karanomori_area === true )
		{	// 唐之杜ミッションの場合は常にfalse
			$result_first_clear = false;
		}
		else if( empty( $user_mission ))
		{	// ミッションの結果レコードがなければどれも初めて
			$result_first_clear = true;
		}
		else
		{	// 結果レコードがある
			$idx_tbl = array(
				Pp_MissionManager::RESULT_TYPE_FAIL => 'fail',
				Pp_MissionManager::RESULT_TYPE_NORMAL => 'normal_clear',
				Pp_MissionManager::RESULT_TYPE_BEST => 'best_clear'
			);
			if( $user_mission[$idx_tbl[$result_type]] == 0 )
			{	// レコードの回数が０かどうかで判断
				$result_first_clear = true;
			}
		}

		//-------------------------------------------------------------------
		//		パラメータ変動
		//-------------------------------------------------------------------
		if( $result_type === Pp_MissionManager::RESULT_TYPE_FAIL )
		{	// FAILの場合はパラメータ変動なし
			//$this->backend->logger->log( LOG_DEBUG, 'FAIL!FAIL!FAIL!' );

			$user_game_after = array(
				'body_coef' => $user_game['body_coef'],
				'intelli_coef' => $user_game['intelli_coef'],
				'mental_coef' => $user_game['mental_coef']
			);
			$support_chara_after = array(
				'body_coef' => $support_chara['body_coef'],
				'intelli_coef' => $support_chara['intelli_coef'],
				'mental_coef' => $support_chara['mental_coef']
			);
		}
		else
		{	// NORMAL,BESTならパラメータ変動あり
			//$this->backend->logger->log( LOG_DEBUG, 'NORMAL or BEST' );

			$s = ( $result_type === Pp_MissionManager::RESULT_TYPE_BEST ) ? 'variations_best' : 'variations_normal';

			// プレイヤーの変動後のパラメータ取得
			$variation = $mission_master[$s.'_pl'];
			$user_game_after = $this->_getAfterParam( $user_game, $variation, null, $mission_master['difficulty'] );
			if( empty( $user_game_after ))
			{	// 取得エラー
				return 'error_500';
			}

			// 同行サポートキャラの変動後のパラメータ取得
			$variation = $mission_master[$s.'_sp'];
			$support_chara_after = $this->_getAfterParam(
				$support_chara, $variation, $support_chara_master, $mission_master['difficulty']
			);
		}

		//-------------------------------------------------------------------
		//		解放サポートキャラチェック
		//-------------------------------------------------------------------
		$unlock_support_id = 0;		// 今回解放されるサポートキャラID（0:解放キャラなし）
		if( $result_type !== Pp_MissionManager::RESULT_TYPE_FAIL )
		{	// FAIL以外なら解放チェック
			$release_chara_master = $character_m->getMasterCharacterByReleaseMissionId( $before_data['mission_id'] );
			if( is_null( $release_chara_master )||( $release_chara_master === false ))
			{	// 取得エラー
				return 'error_500';
			}
			if( count( $release_chara_master ) > 0 )
			{	// 終了したミッションで解放されるキャラがいる
				$temp = $character_m->getUserCharacter( $pp_id, $release_chara_master['character_id'] );
				if( is_null( $temp )||( $temp === false ))
				{	// 取得エラー
					return 'error_500';
				}
				else if( empty( $temp ))
				{	// ユーザーキャラクター情報にレコードがなければ未解放
					$unlock_support_id = $release_chara_master['character_id'];
				}
			}
		}

		//-------------------------------------------------------------------
		//		エリアストレス上昇チェック
		//-------------------------------------------------------------------
		// 【エリアストレス＆サイコハザード仕様確認！】
		// ・エリアストレス値は0～10の11段階
		// ・エリアストレス上昇抽選処理はBESTとNORMALの時のみ実行
		// ・サイコハザード発生中はエリアストレス上昇抽選処理は実行しない（クリアしたエリアのストレス減少のみ）
		// ・サイコハザードは同一ステージ内のエリアでは同時発生しない
		// ・エリアストレス上昇抽選処理中にサイコハザードが発生した場合はそこで抽選処理は終了、その後のエリアは処理しない（…いいんスか？）
		$area_update_buff = array();		// エリアストレス変動値格納バッファ

		if(( $result_type !== Pp_MissionManager::RESULT_TYPE_FAIL )||( $is_karanomori_area == false ))
		{	// 唐之杜ミッション以外のBEST/NORMALのみ処理を行う
			if( $before_data['hazard_flag'] == 1 )
			{	// サイコハザード発生中
				// プレイしたミッションが所属するエリアのユーザーエリア情報を取得
				$ua = $user_m->getUserArea( $pp_id, $area_master['area_id'] );

				$area_update_buff[$ua['area_id']] = array();

				// ステージIDを渡しておく
				$area_update_buff[$ua['area_id']]['stage_id'] = $area_master['stage_id'];

				// エリアストレス減少
				$area_update_buff[$ua['area_id']]['area_stress_prev'] = $ua['area_stress'];
				$area_update_buff[$ua['area_id']]['area_stress'] = $ua['area_stress'] - 1;

				// サイコハザード解除？
				$area_update_buff[$ua['area_id']]['status_prev'] = $ua['status'];
				if( $area_update_buff[$ua['area_id']]['area_stress'] <= Pp_MissionManager::PSYCHO_HAZARD_CANCEL_LV )
				{	// サイコハザード解除
					$area_update_buff[$ua['area_id']]['status'] = Pp_MissionManager::AREA_STATUS_NORMAL;
				}
				else
				{	// サイコハザード継続
					$area_update_buff[$ua['area_id']]['status'] = Pp_MissionManager::AREA_STATUS_HAZARD;
				}
			}
			else
			{	// サイコハザード未発生
				// 同一ステージに所属するエリアの一覧を取得
				$area_master_list = $mission_m->getMasterAreaListAssocByStageId( $area_master['stage_id'] );
				if( empty( $area_master_list ))
				{	// 取得エラー
					return 'error_500';
				}

				// 所属エリアのうち、解放済み（ユーザーエリア情報が存在する）エリアを取得
				$temp = array();
				foreach( $area_master_list as $area_id => $am )
				{
					if( $am['type'] == Pp_MissionManager::AREA_TYPE_NORMAL )
					{	// 通常エリアのみが対象
						$temp[] = $area_id;
					}
				}

				if( !empty( $temp ))
				{	// 解放済みの通常エリアがある
					$user_area_list = $user_m->getUserAreaListAssoc( $pp_id, $temp );
					if( empty( $user_area_list ))
					{	// 取得エラー
						return 'error_500';
					}

					// エリアストレス上昇抽選処理を実行し、ユーザーエリア情報の更新分を取得
					$area_update_buff = $this->_checkAreaStress( $area_master['area_id'], $user_area_list, $area_master_list );

					// 変更前の値をセット
					foreach( $area_update_buff as $area_id => $v )
					{
						$area_update_buff[$area_id]['area_stress_prev'] = $user_area_list[$area_id]['area_stress'];
						$area_update_buff[$area_id]['status_prev'] = $user_area_list[$area_id]['status'];
					}
				}
			}
		}

		//-------------------------------------------------------------------
		//		唐之杜調査結果発表！！
		//-------------------------------------------------------------------
		if(( $result_type === Pp_MissionManager::RESULT_TYPE_FAIL )||( $is_karanomori_area == true ))
		{	// ミッション失敗の時は通信がなかったことにする
			$karanomori_status = Pp_MissionManager::KARANOMORI_STATUS_NONE;
		}
		else
		{
			if( $before_data['karanomori_report_flag'] == 0 )
			{	// ミッション開始前に唐之杜通信がなかった
				$karanomori_status = Pp_MissionManager::KARANOMORI_STATUS_NONE;
			}
			else
			{	// ミッション開始前に唐之杜通信があった
				if( empty( $stage_master['karanomori_find_prob'] ))
				{	// 確率がない？
					return 'error_500';
				}
				$prob = explode( ',', $stage_master['karanomori_find_prob'] );
				$r = mt_rand( 1, 100 );
				if( $r <= $prob[$user_stage['karanomori_report']] )
				{	// 犯人発見！
					$karanomori_status = Pp_MissionManager::KARANOMORI_STATUS_FIND;
				}
				else
				{	// 犯人ロスト
					$karanomori_status = Pp_MissionManager::KARANOMORI_STATUS_LOST;
				}
			}
		}

		// DBトランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------------
			//		データベースの更新
			//-------------------------------------------------------------------
			// 2015/03/31~04/06の投票イベント仮対応（とりあえず期間とかはマスタテーブルを作らずに対応）
			$now = $_SERVER['REQUEST_TIME'];
			//if(( strtotime( '2015-03-31 15:00:00' ) <= $now )&&( $now <= strtotime( '2015-04-06 15:00:00' )))
			if(( strtotime( '2015-03-20 15:00:00' ) <= $now )&&( $now <= strtotime( '2015-04-06 15:00:00' )))
			{
				if( $result_type !== Pp_MissionManager::RESULT_TYPE_FAIL )
				{
					$point = ( int )$ingame_result['paralyzer'] + ( int )$ingame_result['eliminator'] + ( int )$ingame_result['decomposer'];
					$res = $user_m->addUserVotingPoint( $pp_id, $point );
					if( $res !== true )
					{	// 追加エラー
						$err_msg = "error: addUserVotingPoint( {$pp_id}, {$point} )";
						$err_code = SDC_DB_ERROR;
						throw new Exception( $err_msg, $err_code );
					}

					$res = $raid_m->addRaidTotal( 1, $point );
					if( $res !== true )
					{	// 追加エラー
						$err_msg = "error: addRaidTotal( 1, {$point} )";
						$err_code = SDC_DB_ERROR;
						throw new Exception( $err_msg, $err_code );
					}
				}
			}

			// 新規解放サポートキャラ
			if( $unlock_support_id !== 0 )
			{	// 今回解放されるサポートキャラあり
				// 各パラメータはキャラの初期値を設定
				$columns = array(
					'crime_coef' => $release_chara_master['crime_coef_def'],
					'body_coef' => $release_chara_master['body_coef_def'],
					'intelli_coef' => $release_chara_master['intelli_coef_def'],
					'mental_coef' => $release_chara_master['mental_coef_def']
				);
				// 解放されるサポートキャラを追加
				$res = $character_m->insertUserCharacter( $pp_id, $release_chara_master['character_id'], $columns );
				if( $res !== true )
				{	// 追加エラー
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
			}

			// ユーザーパラメータ更新
			$columns = array(
				'body_coef' => $user_game_after['body_coef'],
				'intelli_coef' => $user_game_after['intelli_coef'],
				'mental_coef' => $user_game_after['mental_coef']
			);
			if( $next_info['mission_id'] != 0 )
			{	// 進行ミッションが更新された
				$columns['mission_id'] = $next_info['mission_id'];
				$next_mission_id = $next_info['mission_id'];
			}
			else
			{	// 更新されていない
				$next_mission_id = $user_game['mission_id'];
			}

			$res = $user_m->updateUserGame( $pp_id, $columns );
			if( $res !== true )
			{	// 更新エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// サポートキャラパラメータ更新
			$columns = array(
				'body_coef' => $support_chara_after['body_coef'],
				'intelli_coef' => $support_chara_after['intelli_coef'],
				'mental_coef' => $support_chara_after['mental_coef']
			);
			$res = $character_m->updateUserCharacter( $pp_id, $support_chara_master['character_id'], $columns );
			if( $res !== true )
			{	// 更新エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// ユーザー実績情報の更新
			$is_mission_designate = false;	// 指定ミッションか？
			$clear_time = ( $mission_master['time_limit'] / 1000 ) - $ingame_result['remain_time'];	// クリアまでにかかった時間（秒）
			$res = $this->_updateUserAchievementCount(
				$pp_id, $before_data['accompany_character_id'], $ingame_result, $result_type, $clear_time, $is_mission_designate, $user_m );
			if( $res !== true )
			{	// 更新エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// ユーザーエリア情報の更新
			if( !empty( $area_update_buff ))
			{	// 更新情報があるなら
				foreach( $area_update_buff as $area_id => $v )
				{
					$columns = array( 'area_stress' => $v['area_stress'], 'status' => $v['status'] );
					$res = $user_m->updateUserArea( $pp_id, $area_id, $columns );
					if( $res !== true )
					{	// 更新エラー
						$err_msg = '';
						$err_code = SDC_DB_ERROR;
						throw new Exception( $err_msg, $err_code );
					}
				}
			}

			// ユーザーミッション情報更新
			$columns = array(
				'paralyzer' => $ingame_result['paralyzer'],		// パラライザー執行数
				'eliminator' => $ingame_result['eliminator'],	// エリミネーター執行数
				'decomposer' => $ingame_result['decomposer'],	// デコンポーザー執行数
				'persuasion' => $ingame_result['persuasion'],	// [説得]での交渉成功数
				'reprimand' => $ingame_result['reprimand'],		// [叱責]での交渉成功数
				'warning' => $ingame_result['warning']			// [警告]での交渉成功数
			);
			if( $result_type === Pp_MissionManager::RESULT_TYPE_BEST )
			{	// BESTクリア
				$columns['best_clear'] = 1;
			}
			else if( $result_type === Pp_MissionManager::RESULT_TYPE_NORMAL )
			{	// NORMALクリア
				$columns['normal_clear'] = 1;
			}
			else
			{	// FAIL
				$columns['fail'] = 1;
			}
			if(( $result_type === Pp_MissionManager::RESULT_TYPE_BEST )||( $result_type === Pp_MissionManager::RESULT_TYPE_NORMAL ))
			{
				if( $clear_time <= 600 )
				{	// 10分以内クリア
					$columns['clear_10min'] = 1;
					if( $clear_time <= 540 )
					{	// 9分以内クリア
						$columns['clear_9min'] = 1;
						if( $clear_time <= 480 )
						{	// 8分以内クリア
							$columns['clear_8min'] = 1;
							if( $clear_time <= 420 )
							{	// 7分以内クリア
								$columns['clear_7min'] = 1;
								if( $clear_time <= 360 )
								{	// 6分以内クリア
									$columns['clear_6min'] = 1;
									if( $clear_time <= 300 )
									{	// 5分以内クリア
										$columns['clear_5min'] = 1;
									}
								}
							}
						}
					}
				}
			}
			$res = $user_m->addUserMissionResultCount( $pp_id, $mission_master['mission_id'], $columns );
			if( $res !== true )
			{	// 更新エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// ユーザーステージ情報更新（唐之杜調査回数）
			if( $karanomori_status != Pp_MissionManager::KARANOMORI_STATUS_NONE )
			{	// 唐之杜通信があった場合のみ
				$karanomori_report_prev = $user_stage['karanomori_report'];				// 更新前の値を保持
				if( $karanomori_status == Pp_MissionManager::KARANOMORI_STATUS_FIND )
				{	// 犯人発見！
					$karanomori_report_next = 0;
				}
				else if( $karanomori_status == Pp_MissionManager::KARANOMORI_STATUS_LOST )
				{	// 犯人ロスト…(´；ω；`)ｳｩｩ
					$karanomori_report_next = $user_stage['karanomori_report'] + 1;
				}
				$columns = array( 'karanomori_report' => $karanomori_report_next );
				$res = $user_m->updateUserStage( $pp_id, $stage_master['stage_id'], $columns );
				if( $res !== true )
				{
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
			}

			// 新規エリア・ステージ解放
			if(( $next_info['stage_id'] != 0 )&&( $next_info['stage_id'] != $area_master['stage_id'] ))
			{	// 新しいステージが解放された
				$res = $user_m->insertUserStage( $pp_id, $next_info['stage_id'] );
				if( $res !== true )
				{	// エラー
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
			}

			if(( $next_info['area_id'] != 0 )&&( $next_info['area_id'] != $area_master['area_id'] ))
			{	// 新しいエリアが解放された
				$temp = $mission_m->getMasterArea( $next_info['area_id'] );
				if( empty( $temp ))
				{	// 取得エラー
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
				$res = $user_m->insertUserArea( $pp_id, $next_info['area_id'], $temp['area_stress_def'] );
				if( $res !== true )
				{	// 追加エラー
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
			}

			if( $next_info['mission_id'] != 0 )
			{	// 新しいミッションが解放された
				$res = $user_m->insertUserMission( $pp_id, $next_info['mission_id'] );
				if( $res !== true )
				{	// 追加エラー
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
			}

			// フォト獲得チェック（必ずユーザーミッション情報更新の後にやること！）
			$photo_id = 0;
			$photo_lv = 0;
			$release_sp_area = null;
			if( empty( $user_mission )||
			   (( $result_type == Pp_MissionManager::RESULT_TYPE_BEST )&&( $user_mission['best_clear'] == 0 ))||
			   (( $result_type == Pp_MissionManager::RESULT_TYPE_NORMAL )&&( $user_mission['normal_clear'] == 0 ))||
			   (( $result_type == Pp_MissionManager::RESULT_TYPE_FAIL )&&( $user_mission['fail'] == 0 )))
			{
				$mission_master_list = $mission_m->getMasterMissionListAssocByAreaId( $area_master['area_id'] );
				if( empty( $mission_master_list ))
				{	// 取得エラー
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
				$mission_ids = array_keys( $mission_master_list );	// ミッションIDを配列にする

				$type_table = array(
					Pp_MissionManager::RESULT_TYPE_BEST => 'best',
					Pp_MissionManager::RESULT_TYPE_NORMAL => 'normal',
					Pp_MissionManager::RESULT_TYPE_FAIL => 'fail'
				);

				// エリア内ミッションの対象結果処理別の回数０以上のレコード数を取得
				$cnt = $user_m->getUserMissionResultRecordCount( $pp_id, $result_type, $mission_ids, 'db' );
				if( $cnt == $area_master['condition_'.$type_table[$result_type]] )
				{	// 規定クリア数になったのでフォトを獲得
					$photo_id = $area_master['photo_id_'.$type_table[$result_type]];

					// 獲得したフォトのレベルが最大かどうか
					$lv_max_photo = $photo_m->getUserPhotoMaxLvByPhotoIds( $pp_id, array( $photo_id ));
					if( empty( $lv_max_photo ))
					{	// 獲得したフォトは最大レベルではない
						// フォト所有情報を更新
						$res = $photo_m->addUserPhoto( $pp_id, array( $photo_id ));
						if( $res !== true )
						{	// エラー
							$err_msg = '';
							$err_code = SDC_DB_ERROR;
							throw new Exception( $err_msg, $err_code );
						}
						// 更新後の所有情報を取得
						$photo_info = $photo_m->getUserPhoto( $pp_id, $photo_id, true );
						if( !$photo_info )
						{	// 取得エラー
							$error_detail = "getUserPhoto() error!: pp_id={$pp_id}, photo_id={$photo_id}";
							throw new Exception( $error_detail, SDC_DB_ERROR );
						}
						$photo_lv = $photo_info['photo_lv'];	// 獲得後のフォトLV
					}
					else
					{	// 既に最大レベル
						// プレゼントBOXにフォトフィルムを１枚送る
						$columns = array(
							'comment_id'       => Pp_PresentManager::COMMENT_PRESENT,	// 運営からのプレゼンツ！？
							'present_category' => Pp_PresentManager::CATEGORY_ITEM,		// プレゼントのカテゴリ
							'present_value'    => Pp_ItemManager::ITEM_ID_PHOTO_FILM,	// フォトフィルムのアイテムID
							'num'              => 1	// 配布数（１枚でいいのか？）
						);
						$photo_film_present_id = $present_m->setUserPresent(
							$pp_id,
							0,
							$columns
						);
						if( Ethna::isError( $photo_film_present_id ))
						{	// 更新エラー
							$error_detail = "setUserPresent() error!: pp_id=$pp_id, photo_id=$photo_id";
							throw new Exception( $error_detail, SDC_DB_ERROR );
						}
						$photo_lv = Pp_PhotoManager::PHOTO_LV_MAX;	// 獲得後のフォトLVは最大のままで
					}

					// スペシャルエリア解放チェック
					if( $photo_lv == 1 )
					{	// 獲得したフォトが新規の場合のみ
						$release_sp_area = $mission_m->checkSpAreaRelease( $pp_id, $photo_id );
						if( is_null( $release_sp_area ))
						{	// エラー
							$error_detail = "checkSpAreaRelease() error!: pp_id=$pp_id, photo_id=$photo_id";
							throw new Exception( $error_detail, SDC_DB_ERROR );
						}
						if( !empty( $release_sp_area ))
						{	// 解放エリアあり
							foreach( $release_sp_area as $area_id )
							{
								$ret = $user_m->releaseNewArea( $pp_id, $area_id );
								if( $ret !== true )
								{
									$error_detail = "releaseNewArea() error!: pp_id=$pp_id, area_id=$area_id";
									throw new Exception( $error_detail, SDC_DB_ERROR );
								}
							}
						}
					}
				}
			}

			// 勲章獲得チェック（ユーザー実績情報が更新されてから実行すること！）
			$ach_rank = $user_m->getUserAchievementRank( $pp_id );
			if( is_null( $ach_rank )||( $ach_rank === false ))
			{	// 取得エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}
			$ach_count_diff = $user_m->getUserAchievementCountBaseDiff( $pp_id, "db" );		// 最新のユーザー実績情報を取得
			$get_ach_ids = $this->_checkAchievement( $ach_cond_master, $ach_count_diff, $ach_rank, $achievement_m );
			if( !empty( $get_ach_ids ))
			{	// 新規獲得あり
				$ach_present_ids = array();
				foreach( $get_ach_ids as $ach_id )
				{
					// 獲得したことにより新しく解放される勲章があるか？
					$release_ach_id = $achievement_m->getReleaseAchId( $ach_id );
					if( Ethna::isError( $release_ach_id ))
					{
						$err_msg = "getReleaseAchId() error!: pp_id={$pp_id}, ach_id={$ach_id}";
						$err_code = SDC_DB_ERROR;
						throw new Exception( $err_msg, $err_code );
					}
					if( !empty( $release_ach_id ))
					{	// 新しく解放される勲章があるよ
						$res = $user_m->insertUserAchievementBaseCount( $pp_id, $release_ach_id );
						if( $res !== true )
						{
							$err_msg = "insertUserAchievementBaseCount() error!: pp_id={$pp_id}, release_ach_id={$release_ach_id}";
							$err_code = SDC_DB_ERROR;
							throw new Exception( $err_msg, $err_code );
						}
					}

					// 同じグループの次のランクの勲章があるか？
					$next_achieve = $achievement_m->getMasterAchievementConditionNextRank( $ach_id );
					if( !empty( $next_achieve ))
					{	// あるなら次のランクを解放
						$res = $user_m->insertUserAchievementBaseCount( $pp_id, $next_achieve['ach_id'] );
						if( $res !== true )
						{
							$err_msg = "insertUserAchievementBaseCount() error!: pp_id={$pp_id}, next_ach_id=".$next_achieve['ach_id'];
							$err_code = SDC_DB_ERROR;
							throw new Exception( $err_msg, $err_code );
						}
					}

					// ユーザー勲章情報を追加
					$res = $user_m->insertUserAchievementRank( $pp_id, $ach_id );
					if( $res !== true )
					{	// 追加エラー
						$err_msg = "insertUserAchievementRank() error!: pp_id=$pp_id, ach_id=$ach_id";
						$err_code = SDC_DB_ERROR;
						throw new Exception( $err_msg, $err_code );
					}

					// 勲章獲得報酬をプレゼントBOXへ
					$columns = array(
						'comment_id'       => Pp_PresentManager::COMMENT_ACHIEVEMENT,		// 勲章獲得報酬っす！
						'present_category' => $ach_cond_master[$ach_id]['reward_category'],	// 報酬のカテゴリ
						'present_value'    => $ach_cond_master[$ach_id]['reward_id'],		// ブツのID
						'num'              => $ach_cond_master[$ach_id]['reward_num']		// 配布数
					);
					$present_id = $present_m->setUserPresent(
						$pp_id,
						0,
						$columns
					);
					if( Ethna::isError( $present_id ))
					{	// 更新エラー
						$error_detail = "setUserPresent() error!: pp_id=$pp_id, ach_id=$ach_id";
						throw new Exception( $error_detail, SDC_DB_ERROR );
					}
					$ach_present_ids[$ach_id] = $present_id;
				}
			}

			// 最新の全サポートキャラ情報を取得（全サポートキャラの情報が更新されてから実行すること！）
			$character = $character_m->getUserCharacter( $pp_id );
			if( empty( $character ))
			{	// 取得エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// 最新のユーザーミッション情報を取得（ユーザーミッション情報更新後に実行すること！）
			$user_mission_new = $user_m->getUserMission( $pp_id, $before_data['mission_id'], 'db' );	// マスターから取得
			if( empty( $user_mission_new ))
			{	// 取得エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// 最新のユーザーゲーム情報を取得（ユーザーゲーム情報更新後に実行すること！）
			$user_game_new = $user_m->getUserGame( $pp_id );
			if( empty( $user_game_new ))
			{	// 取得エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// 最新のマップ情報を取得（ステージ・エリア・ミッション関連の更新完了後に実行すること）
			$map_list = $this->_getMapList( $pp_id, $user_m, $mission_m );
			if( empty( $map_list ))
			{
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}
			list( $stage_list, $area_list, $mission_list ) = $map_list;

			// 最新プレゼントBOX情報を取得（勲章獲得チェック完了後に実行すること）
			$ret = $present_m->deleteMaxOverUserPresent( $pp_id );
			if(( $ret === false )||( Ethna::isError( $ret )))
			{
				$err_msg = 'deleteMaxOverUserPresent()';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}
			$ret = $present_m->deleteExpiredUserPresent( $pp_id );
			if( Ethna::isError( $ret ))
			{
				$err_msg = 'deleteExpiredUserPresent()';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}
			$present_list = $present_m->getUserPresentList( $pp_id );
			if( is_null( $present_list ) || ( $present_list === false ))
			{
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// 利用可能ガチャリストを取得
			$cleared_mission_list = $user_m->getClearedMissionIdList( $pp_id, "db" );
			if( is_null( $cleared_mission_list ))
			{
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}
			$gacha_info = null;
			if( !empty( $cleared_mission_list ))
			{
				$gacha_info = $photo_gacha_m->getMasterPhotoGachaAvailable( $cleared_mission_list );
			}

			// 各勲章グループの次の獲得すべき勲章IDのリストを取得
			$achieve_medal = array();
			$ach_count_diff = $user_m->getUserAchievementCountBaseDiff( $pp_id, "db" );		// 最新のユーザー実績情報を取得
			if( !empty( $ach_count_diff ))
			{
				$now_timestamp = $_SERVER['REQUEST_TIME'];
				foreach( $ach_count_diff as $ach_id => $count )
				{
					if( !isset( $ach_cond_master[$ach_id] ))
					{
						continue;
					}
					$cond = $ach_cond_master[$ach_id];
					$group_id = $cond['ach_group_id'];
					$group = $ach_group_master[$group_id];

					if(( empty( $group['date_end'] ))||( $group['date_end'] == '0000-00-00 00:00:00' ))
					{	// 期限なし
						$date_end = '9999-12-31 23:59:59';
						$remain = -1;
					}
					else
					{	// 期限あり
						$date_end = $group['date_end'];
						$remain = strtotime( $date_end ) - $now_timestamp;
					}

					$achieve_medal[] = array(
						'ach_id' => $ach_id,
						'ach_group_id' => $group_id,
						'category' => $group['category'],
						'condition' => $cond['cond_value'],
						'limit_time' => $date_end,
						'remain' => $remain,
						'achieve_count' => $ach_count_diff[$ach_id],
						'next_ach_id' => 0	// ここは０固定でセット
					);
				}
			}

			// 最後に参照勲章情報を取得した日時以降に取得した勲章の一覧を取得
			if( empty( $user_game['last_achievement_view'] ) || $user_game['last_achievement_view'] == '0000-00-00 00:00:00' )
			{	// 一度も参照していない
				$view_timestamp = strtotime( $user_game['date_created'] );	// レコード作成日時で代用する
			}
			else
			{	// 過去に参照したことがある
				$view_timestamp = $user_game['last_achievement_view'];		// 参照日時をセット
			}
			$temp = $achievement_m->getAchievementNewComplete( $pp_id, $view_timestamp, "db" );
			if( $temp === false )
			{
				$this->backend->logger->log( LOG_ERR, 'getAchievementNewComplete(): pp_id='.$pp_id.', date='.$user_game['last_achievement_view'] );
				return 'error_500';
			}
			$complete_medal = array();
			foreach( $temp as $v )
			{
				$ach_id = $v['ach_id'];
				if( !isset( $ach_cond_master[$ach_id] ))
				{
					continue;
				}
				$cond_data = $ach_cond_master[$ach_id];
				$group_data = $ach_group_master[$cond_data['ach_group_id']];
				$next = $achievement_m->getMasterAchievementConditionNextRank( $ach_id );
				$complete_medal[] = array(
					'ach_id' => $ach_id,
					'ach_group_id' => $cond_data['ach_group_id'],
					'category' => $group_data['category'],
					'condition' => $cond_data['cond_value'],
					'next_ach_id' => (( empty( $next )) ? 0 : $next['ach_id'] )
				);
			}

			$new_medal = array();

			//-------------------------------------------------------------------
			//		 KPIとかログの処理
			//-------------------------------------------------------------------
			// ミッション結果履歴
			$columns = array(
				'pp_id' => $pp_id,
				'api_transaction_id' => $api_transaction_id,
				'play_id' => $play_id,
				'mission_id' => $before_data['mission_id'],
				'paralyzer' => (( $result_type === Pp_MissionManager::RESULT_TYPE_FAIL ) ? 0 : $ingame_result['paralyzer'] ),
				'eliminator' => (( $result_type === Pp_MissionManager::RESULT_TYPE_FAIL ) ? 0 : $ingame_result['eliminator'] ),
				'decomposer' => (( $result_type === Pp_MissionManager::RESULT_TYPE_FAIL ) ? 0 : $ingame_result['decomposer'] ),
				'result_type' => $result_type,
				'status' => $ingame_result['status'],
				'zone' => $ingame_result['zone']
			);
			$res = $logdata_m->logIngameResult( $columns );

			// ユーザーステージ情報変動履歴
			if( $karanomori_status != Pp_MissionManager::KARANOMORI_STATUS_NONE )
			{	// 唐之杜通信があったらステージ情報変動履歴を更新
				$columns = array(
					'pp_id' => $pp_id,
					'api_transaction_id' => $api_transaction_id,
					'karanomori_report' => $karanomori_report_next,
					'karanomori_report_prev' => $karanomori_report_prev
				);
				$res = $logdata_m->logStage( $columns );
			}

			// ユーザーエリア情報変動履歴
			if( !empty( $area_update_buff ))
			{
				foreach( $area_update_buff as $area_id => $v )
				{
					$columns = array(
						'pp_id' => $pp_id,
						'api_transaction_id' => $api_transaction_id,
						'processing_type' => 'E01',
						'area_id' => $area_id,
						'area_stress' => $v['area_stress'],
						'area_stress_prev' => $v['area_stress_prev'],
						'status' => $v['status'],
						'status_prev' => $v['status_prev']
					);
					$res = $logdata_m->logArea( $columns );
				}
			}

			// フォト獲得履歴
			if( $photo_id > 0 )
			{
				$columns = array(
					'pp_id' => $pp_id,
					'api_transaction_id' => $api_transaction_id,
					'processing_type' => 'D01',								// 処理コード
					'photo_id' => $photo_id,
					'photo_lv' => $photo_lv
				);
				$res = $logdata_m->logPhoto( $columns );
			}

			if( isset( $photo_film_present_id ))
			{	// 獲得フォトLV最大でのフォトフィルムのプレゼント情報を記録
				$columns = array(
					'pp_id' => $pp_id,
					'api_transaction_id' => $api_transaction_id,			// トランザクションID
					'processing_type' => 'C02',								// 処理コード
					'present_id' => $photo_film_present_id,					// プレゼントID
					'present_category' => Pp_PresentManager::CATEGORY_ITEM,	// 配布物カテゴリ
					'present_value' => Pp_ItemManager::ITEM_ID_PHOTO_FILM,	// 配布物ID
					'num' => 1,												// 配布数
					'status' => Pp_PresentManager::STATUS_NEW,				// ステータス
					'comment_id' => Pp_PresentManager::COMMENT_PRESENT		// 配布コメント
				);
				$res = $logdata_m->logPresent( $columns );
			}

			if( !empty( $get_ach_ids ))
			{	// 勲章獲得報酬のプレゼント情報を記録
				foreach( $get_ach_ids as $ach_id )
				{
					$columns = array(
						'pp_id' => $pp_id,
						'api_transaction_id' => $api_transaction_id,				// トランザクションID
						'processing_type' => 'C01',									// 処理コード
						'present_id' => $ach_present_ids[$ach_id],					// プレゼントID
						'present_category' => $ach_cond_master[$ach_id]['reward_category'],	// 配布物カテゴリ
						'present_value' => $ach_cond_master[$ach_id]['reward_id'],	// 配布物ID
						'num' => $ach_cond_master[$ach_id]['reward_num'],			// 配布数
						'status' => Pp_PresentManager::STATUS_NEW,					// ステータス
						'comment_id' => Pp_PresentManager::COMMENT_ACHIEVEMENT		// 配布コメント
					);
					$res = $logdata_m->logPresent( $columns );
				}
			}

			// キャラクタ情報変動履歴
			if( $result_type !== Pp_MissionManager::RESULT_TYPE_FAIL )
			{	// 変動があった場合だけ記録
				// プレイヤーキャラ
				$columns = array(
					'pp_id' => $pp_id,										// サイコパスID
					'api_transaction_id' => $api_transaction_id,			// トランザクションID
					'processing_type' => 'A04',								// 処理コード
					'character_id' => Pp_CharacterManager::CHARACTER_ID_PLAYER,	// キャラクターID
					'crime_coef' => $user_game['crime_coef'],				// 犯罪係数
					'crime_coef_prev' => $user_game['crime_coef'],			// 犯罪係数（変動前）
					'body_coef' => $user_game_after['body_coef'],			// 身体係数
					'body_coef_prev' => $user_game['body_coef'],			// 身体係数（変動前）
					'intelli_coef' => $user_game_after['intelli_coef'],		// 知能係数
					'intelli_coef_prev' => $user_game['intelli_coef'],		// 知能係数（変動前）
					'mental_coef' => $user_game_after['mental_coef'],		// 心的係数
					'mental_coef_prev' => $user_game['mental_coef'],		// 心的係数（変動前）
					'ex_stress_care' => $user_game['ex_stress_care'],		// 臨時ストレスケア回数（変動前と同じ）
					'ex_stress_care_prev' => $user_game['ex_stress_care']	// 臨時ストレスケア回数（変動前）
				);
				$res = $logdata_m->logCharacter( $columns );

				// 同行サポートキャラ
				$columns = array(
					'pp_id' => $pp_id,											// サイコパスID
					'api_transaction_id' => $api_transaction_id,				// トランザクションID
					'processing_type' => 'A04',									// 処理コード
					'character_id' => $before_data['accompany_character_id'],	// キャラクターID
					'crime_coef' => $support_chara['crime_coef'],				// 犯罪係数
					'crime_coef_prev' => $support_chara['crime_coef'],			// 犯罪係数（変動前）
					'body_coef' => $support_chara_after['body_coef'],			// 身体係数
					'body_coef_prev' => $support_chara['body_coef'],			// 身体係数（変動前）
					'intelli_coef' => $support_chara_after['intelli_coef'],		// 知能係数
					'intelli_coef_prev' => $support_chara['intelli_coef'],		// 知能係数（変動前）
					'mental_coef' => $support_chara_after['mental_coef'],		// 心的係数
					'mental_coef_prev' => $support_chara['mental_coef'],		// 心的係数（変動前）
					'ex_stress_care' => $support_chara['ex_stress_care'],		// 臨時ストレスケア回数（変動前と同じ）
					'ex_stress_care_prev' => $support_chara['ex_stress_care']	// 臨時ストレスケア回数（変動前）
				);
				$res = $logdata_m->logCharacter( $columns );
			}

			//-------------------------------------------------------------------
			//		KPIログ
			//-------------------------------------------------------------------
			// 最初のミッションの初クリア時のみ
			if(( $before_data['mission_id'] == 10010101 )&&( $first_clear === true ))
			{
				$ua = $user_base['device_type'];
				$kpi_tag = ( $ua == 1 ) ? "Apple-ppp-start" : "Google-ppp-start";
				$kpi_m->log( $kpi_tag, 3, 1, time(), $pp_id, "", "", "" );
			}

			//-------------------------------------------------------------------
			//		クライアントへの返却データを作成
			//-------------------------------------------------------------------
			$buff = array();

			// ミッションの結果
			$mission_result = array(
				'mission_id' => ( int )$before_data['mission_id'],						// プレイしたミッションID
				'next_mission_id' => ( int )$next_mission_id, 							// 進行ミッションID
				'unlock_stage_id' => ( int )$next_info['stage_id'],						// 新規解放ステージID
				'unlock_area_id' => ( int )$next_info['area_id'],						// 新規解放エリアID
				'unlock_mission_id' => ( int )$next_info['mission_id'],					// 新規解放ミッションID
				'unlock_support_id' => $unlock_support_id,								// 解放サポートキャラID
				'clear_type' => $result_type,											// クリア種別
				'first_clear' => (( $result_first_clear === true )&&( $is_karanomori_area == false )) ? 1 : 0,	// 初クリアか？
				'get_photo_id' => $photo_id,											// 獲得したフォトID
				'karanomori_status' => $karanomori_status,								// 唐之杜調査結果
				'p_hazard_diff' => ( int )$before_data['hazard_diff_pl'],				// サイコハザード発生中と未発生での犯罪係数上昇値差分
				's_hazard_diff' => ( int )$before_data['hazard_diff_sp'],				// サイコハザード発生中と未発生での犯罪係数上昇値差分
				'p_crime_coef_before' => ( int )$before_data['crime_coef_pl'],			// 更新前のプレイヤー犯罪係数
				'p_crime_coef_after' => ( int )$before_data['crime_coef_pl_after'],		// 更新後のプレイヤー犯罪係数
				'p_body_coef_before' => ( int )$user_game['body_coef'],					// 更新前のプレイヤー身体係数
				'p_body_coef_after' => ( int )$user_game_after['body_coef'],			// 更新後のプレイヤー身体係数
				'p_intelli_coef_before' => ( int )$user_game['intelli_coef'],			// 更新前のプレイヤー知能係数
				'p_intelli_coef_after' => ( int )$user_game_after['intelli_coef'],		// 更新後のプレイヤー知能係数
				'p_mental_coef_before' => ( int )$user_game['mental_coef'],				// 更新前のプレイヤー心的係数
				'p_mental_coef_after' => ( int )$user_game_after['mental_coef'],		// 更新後のプレイヤー心的係数
				'support_charcter_id' => ( int )$before_data['accompany_character_id'],	// 同行したサポートキャラID
				's_crime_coef_before' => ( int )$before_data['crime_coef_sp'],			// 更新前のサポートキャラ犯罪係数
				's_crime_coef_after' => ( int )$before_data['crime_coef_sp_after'],		// 更新後のサポートキャラ犯罪係数
				's_body_coef_before' => ( int )$support_chara['body_coef'],				// 更新前のサポートキャラ身体係数
				's_body_coef_after' => ( int )$support_chara_after['body_coef'],		// 更新後のサポートキャラ身体係数
				's_intelli_coef_before' => ( int )$support_chara['intelli_coef'],		// 更新前のサポートキャラ知能係数
				's_intelli_coef_after' => ( int )$support_chara_after['intelli_coef'],	// 更新後のサポートキャラ知能係数
				's_mental_coef_before' => ( int )$support_chara['mental_coef'],			// 更新前のサポートキャラ心的係数
				's_mental_coef_after' => ( int )$support_chara_after['mental_coef']		// 更新後のサポートキャラ心的係数
			);
			$buff['mission_result'] = $mission_result;

			// 全サポートキャラクター情報
			$support_character = array();
			foreach( $character as $v )
			{
				$support_character[] = array(
					'character_id' => ( int )$v['character_id'],	// キャラクターID
					'crime_coef' => ( int )$v['crime_coef'],		// 犯罪係数
					'body_coef' => ( int )$v['body_coef'],			// 身体係数
					'intelli_coef' => ( int )$v['intelli_coef'],	// 知能係数
					'mental_coef' => ( int )$v['mental_coef'],		// 心的係数
					'ex_stress_care' => ( int )$v['ex_stress_care']	// 臨時ストレスケア回数
				);
			}
			$buff['support_character'] = $support_character;

			// フォト差分情報
			if( $photo_id > 0 )
			{
				$modify_photo = array(
					array(
						'photo_id' => ( int )$photo_id,
						'photo_lv' => ( int )$photo_lv
					)
				);
				$buff['modify_photo'] = $modify_photo;
			}

			// ユーザーエリア情報差分
			$modify_area = array();
			if( !empty( $area_update_buff ))
			{	// 変動があるならデータをセット
				foreach( $area_update_buff as $area_id => $v )
				{
					$modify_area[] = array(
						'area_id' => ( int )$area_id,				// エリアID
						'stage_id' => ( int )$v['stage_id'],		// 所属ステージID
						'area_stress' => ( int )$v['area_stress'],	// エリアストレス値
						'status' => ( int )$v['status']				// エリアステータス
					);
				}
			}
			$buff['modify_area'] = $modify_area;

			// ユーザーミッション情報差分
			$modify_mission = array(
				array(
					'stage_id' => ( int )$stage_master['stage_id'],				// ステージID（実行ミッションと同じステージID）
					'area_id' => ( int )$area_master['area_id'],				// エリアID
					'mission_id' => ( int )$user_mission_new['mission_id'],		// ミッションID
					'best_clear' => ( int )$user_mission_new['best_clear'],		// BESTクリア回数
					'normal_clear' => ( int )$user_mission_new['normal_clear'],	// NORMALクリア回数
					'fail' => ( int )$user_mission_new['fail']					// FAIL回数
				)
			);
			$buff['modify_mission'] = $modify_mission;

			// ユーザーゲーム情報差分
			$modify_user_base = array(
				'crime_coef' => ( int )$user_game_new['crime_coef'],		// 犯罪係数
				'body_coef' => ( int )$user_game_new['body_coef'],			// 身体係数
				'intelli_coef' => ( int )$user_game_new['intelli_coef'],	// 知能係数
				'mental_coef' => ( int )$user_game_new['mental_coef'],		// 心的係数
				'ex_stress_care' => ( int )$user_game_new['ex_stress_care']	// 臨時ストレスケア回数
			);
			$buff['modify_user_base'] = $modify_user_base;

			// InGame結果
			$avg = 0;
			foreach( $stage_list as $row )
			{
				if( $row['stage_id'] == $stage_master['stage_id'] )
				{
					$avg = $row['ave_area_stress'];
					break;
				}
			}
			$ingame = array(
				'paralyzer' => $ingame_result['paralyzer'],					// パラライザー執行数
				'eliminator' => $ingame_result['eliminator'],				// エリミネーター執行数
				'decomposer' => $ingame_result['decomposer'],				// ドミネーター執行数
				'stage_id' => $stage_master['stage_id'],					// 実行したミッションのステージID
				'ave_area_stress' => $avg									// 実行したミッションが所属するエリアのエリア平均ストレス値
			);
			$buff['ingame_result'] = $ingame;

			// ガチャリスト
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

			// プレゼントBOXリスト
			$user_box = $present_m->convertUserBox( $present_list );
			$buff['user_box'] = $user_box;

			// チュートリアル情報
			$user_tutorial = array( 'tutorial' => $user_tuto['flag'] );
			$buff['user_tutorial'] = $user_tutorial;

			// 新規解放スペシャルエリア
			$send_release_sp_area = array();
			if( !empty( $release_sp_area ))
			{
				foreach( $release_sp_area as $area_id )
				{
					$send_release_sp_area[] = array( 'area_id' => $area_id );
				}
				$buff['release_sp_area'] = $send_release_sp_area;
			}

			$buff['stage_list'] = $stage_list;
			$buff['area_list'] = $area_list;
			$buff['mission_list'] = $mission_list;
			$buff['achieve_medal'] = $achieve_medal;
			$buff['complete_medal'] = $complete_medal;
			$buff['new_medal'] = $new_medal;

			// 処理結果をトランザクション情報として記録する
			$result_json = json_encode( $buff );		// JSON文字列にする
			$res = $transaction_m->registTransaction( $pp_id, $api_transaction_id, $result_json );
			if( $res !== true )
			{	// 記録エラー
				$err_msg = 'registTransaction error.';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			$this->af->setApp( 'mission_result', $mission_result, true ); 
			$this->af->setApp( 'support_character', $support_character, true ); 
			if( $photo_id > 0 )
			{
				$this->af->setApp( 'modify_photo', $modify_photo, true ); 
			}
			$this->af->setApp( 'modify_area', $modify_area, true ); 
			$this->af->setApp( 'modify_mission', $modify_mission, true ); 
			$this->af->setApp( 'modify_user_base', $modify_user_base, true ); 
			$this->af->setApp( 'ingame_result', $ingame, true ); 
			$this->af->setApp( 'gacha_list', $gacha_list, true ); 
			$this->af->setApp( 'user_box', $user_box, true ); 
			$this->af->setApp( 'user_tutorial', $user_tutorial, true ); 
			if( !empty( $send_release_sp_area ))
			{
				$this->af->setApp( 'release_sp_area', $send_release_sp_area, true ); 
			}
			$this->af->setApp( 'stage_list', $stage_list, true );
			$this->af->setApp( 'area_list', $area_list, true );
			$this->af->setApp( 'mission_list', $mission_list, true );
			$this->af->setApp( 'achieve_medal', $achieve_medal, true );
			$this->af->setApp( 'complete_medal', $complete_medal, true );
			$this->af->setApp( 'new_medal', $new_medal, true );

			$db->commit();
		}
		catch( Exception $e )
		{	// 例外発生
			//$this->backend->logger->log( LOG_INFO, '### rollback ###' );
			$db->rollback();		// 更新をロールバックする
			return 'error_500';
		}

		return 'api_json_encrypt';
	}

	//====================================================================================================
	//====================================================================================================
	//====================================================================================================
	/**
	 * 結果処理種別（BEST,NORMAL,FAIL）の取得
	 *
	 * @param array $mission_master プレイしたミッションのマスタ情報
	 * @param array $ingame_result ミッションのプレイ結果情報
	 *
	 * @return int:処理結果種別(RESULT_TYPE_BEST/RESULT_TYPE_NORMAL/RESULT_TYPE_FAIL) | null:取得エラー
	 */
	private function _getResultType( $mission_master, $ingame_result )
	{
		$key = array(
			'paralyzer', 'eliminator', 'decomposer', 'life', 'battery',
			'paralyzer_boss', 'eliminator_boss', 'remain_time', 'decomposer_boss'
		);

		// FAILかどうかの判定
		if( $ingame_result['status'] != Pp_MissionManager::RESULT_STATUS_CLEAR )
		{	// クリア以外ならFAILと考える
			return Pp_MissionManager::RESULT_TYPE_FAIL;
		}

		// BESTかどうかの判定
		$temp = str_replace( ' ', '', $mission_master['condition_best'] );	// 半角スペースを削除
		$cond = explode( ',', $temp );			// 各パラメータ毎に配列にする
		if( count( $cond ) === 0 )
		{	// BESTの条件がない
			return null;		// これはエラーですよね？！
		}
		//$this->backend->logger->log( LOG_INFO, 'cond_count: '.count( $cond ));
		$result = Pp_MissionManager::RESULT_TYPE_BEST;
		foreach( $cond as $str )
		{
			list( $index, $value ) = explode( ':', $str );	// パラメータ種別と条件値に分ける
			if( !isset( $ingame_result[$key[$index]] ))
			{	// 知らないパラメータ
				return null;
			}
			if( $ingame_result[$key[$index]] < $value )
			{	// BEST条件を満たしていないものがあるならNORMAL
				$result = Pp_MissionManager::RESULT_TYPE_NORMAL;
				break;
			}
		}

		return $result;
	}

	/**
	 * キャラクターの変動後のパラメータを取得
	 *
	 * @param array $before_param 変動前のキャラクターパラメータ情報
	 * @param array $variation パラメータ変動値文字列
	 * @param array $support_chara_master サポートキャラクターマスタ情報（処理対象がプレイヤーの場合はnull）
	 * @param int $difficulty ミッション難易度
	 *
	 * @return array 変動後パラメータ情報 | null:取得エラー
	 */
	private function _getAfterParam( $before_param, $variation, $support_chara_master, $difficulty )
	{
		$key = array( 1 => 'body_coef', 2 => 'intelli_coef', 3 => 'mental_coef' );

		// 返却用パラメータを初期化
		$after_param = array();
		foreach( $key as $k )
		{
			$after_param[$k] = $before_param[$k];
		}

		// 変動パラメータを加算する
		$temp = str_replace( ' ', '', $variation );		// 半角スペースを削除
		$temp2 = explode( ',', $temp );					// 各パラメータ毎に配列にする
		$cnt = count( $temp2 );
		if( $cnt === 0 )
		{	// 変動パラメータなし
			return $after_param;	// そのまま返す
		}
		if(( $cnt === 1 )&&( $temp2[0] === '0' ))
		{	// '0'だけ設定されている場合は、変動パラメータなしと判断
			return $after_param;	// そのまま返す
		}
		foreach( $temp2 as $str )
		{
			//$this->backend->logger->log( LOG_INFO, 'str: '.$str );
			list( $index, $value ) = explode( ':', $str );	// パラメータ種別と上昇値に分ける
			if( empty( $value ))	// 【A】== 0
			{	// 変動なし
				continue;		// 0（プレイヤー・サポートキャラ共通）
			}

			$k = $key[$index];
			if( empty( $k ))
			{	// パラメータ種別エラー
				return null;
			}

			// 分かりにくいんで仕様書の【Ａ】～【Ｄ】に合わせて変数に入れる
			// 【Ａ】パラメータ変動値
			// 【Ｂ】ミッションマスタ基本難易度
			// 【Ｃ】プレイヤー／サポートキャラ現在能力値
			// 【Ｄ】サポートキャラパラメータリザルト補正値
			$a = $value;
			$b = $difficulty;
			$c = $before_param[$k];
			$d = ( is_null( $support_chara_master )) ? null : $support_chara_master[$k.'_corr'];

			//error_log( "!!! ============================================" );
			//error_log( "!!! a = {$a}" );
			//error_log( "!!! b = {$b}" );
			//error_log( "!!! c = {$c}" );
			//error_log( "!!! d = {$d}" );

			if( $c <= ( 2 * $b ))	//【C】<= 2 *【B】&&【A】!= 0
			{
				//error_log( '!!! if(( $c <= ( 2 * $b ))&&( $a != 0 ))' );

				if( is_null( $d ))
				{	// プレイヤーキャラの場合
					//【A】
					//error_log( "!!! PLAYER" );
					//error_log( "!!! val1: $c + $a" );
					$after_param[$k] += $a;
				}
				else
				{	// 同行サポートキャラの場合
					// CeilToInt[【A】*【D】/ 100 ]
					//error_log( "!!! SUPPORT" );
					//error_log( '!!! val1: $c + ( ceil( $a * $d / 100 ))' );
					$after_param[$k] += ceil( $a * $d / 100 );
				}
			}
			else if( $c >= ( 2.5 * $b ))	//【C】>= 2.5 *【B】&&【A】!= 0
			{
				// 1（プレイヤー・サポートキャラ共通）
				//error_log( '!!! if(( $c >= ( 2.5 * $b ))&&( $a != 0 ))' );
				//error_log( "!!! PLAYER or SUPPORT" );
				//error_log( "!!! val1: $c + 1" );
				$after_param[$k] += 1;
			}
			else	// 条件式【FALSE】
			{
				//error_log( "!!! 【FALSE】" );
				if( is_null( $d ))
				{	// プレイヤーキャラの場合
					//error_log( "!!! PLAYER" );
					//error_log( '!!! val1: $c + ( $a - ceil( 2 * ( $a - 1 ) * (( $c / $b ) - 2 )))' );
					//【A】- CeilToInt[ 2 * (【A】- 1 ) * (【C】/【B】- 2 ) ]
					$after_param[$k] += $a - ceil( 2 * ( $a - 1 ) * (( $c / $b ) - 2 ));
				}
				else
				{	// 同行サポートキャラの場合
					// Ceil ToInt[ {【A】- CeilToInt[ 2 * (【A】- 1 ) * (【C】/【B】- 2 ) ] } *【D】/ 100 ]
					//error_log( "!!! SUPPORT" );
					//error_log( '!!! val1: $c + ( ceil(( $a - ceil( 2 * ( $a - 1 ) * (( $c / $b ) - 2 ))) * $d / 100 ))' );
					$after_param[$k] += ceil(( $a - ceil( 2 * ( $a - 1 ) * (( $c / $b ) - 2 ))) * $d / 100 );
				}
			}
			//error_log( "!!! val1 = ".$after_param[$k] );

			// 最大値・最小値補正
			if( $after_param[$k] > Pp_CharacterManager::CHARACTER_PARAM_MAX )
			{	// 最大値補正
				$after_param[$k] = Pp_CharacterManager::CHARACTER_PARAM_MAX;
			}
			else if( $after_param[$k] < Pp_CharacterManager::CHARACTER_PARAM_MIN )
			{	// 最小値補正
				$after_param[$k] = Pp_CharacterManager::CHARACTER_PARAM_MIN;
			}
			//error_log( "!!! val2 = ".$after_param[$k] );
		}
		return $after_param;
	}

	/**
	 * エリアストレス上昇抽選処理（サイコハザード発生チェックも含む）
	 *
	 * @param int $play_area_id プレイしたミッションが所属するエリアのID
	 * @param array $user_area_list プレイしたミッションが所属するステージに所属するエリアの中で解放されているユーザーエリア情報
	 * @param array $area_master_list プレイしたミッションが所属するステージに所属するエリアのマスタ情報
	 *
	 * @return array ユーザーエリア情報の変動情報
	 */
	private function _checkAreaStress( $play_area_id, $user_area_list, $area_master_list )
	{
		// 各エリア毎にエリアストレス変動の抽選を行う
		$buff = array();		// エリアストレス変動値格納バッファ
		$hazard_prob = array( 4 => 1, 5 => 2, 6 => 3, 7 => 5, 8 => 7, 9 => 9, 10 => 1000 );	// サイコハザード発生確率テーブル（1=0.1%）
		foreach( $user_area_list as $id => $ua )
		{
			if( $id == $play_area_id )
			{	// プレイしたミッションが所属するエリア
				$value = $ua['area_stress'] - 1;
			}
			else
			{	// プレイした以外のエリア（解放済みのエリアが対象）
				// 【Ａ】エリアマスタのエリアストレス上昇抽選率
				// 【Ｂ】エリアストレス現在値
				// 式：RoundToInt[【A】* Log10( 11 -【B】) ]
				$per = round( $area_master_list[$id]['area_stress_prob'] * log(( 11 - $ua['area_stress'] ), 10 ));
				if( mt_rand( 1, 100 ) > $per )
				{	// 変動なし
					continue;
				}
				$value = $ua['area_stress'] + 1;
			}

			// 最大値・最小値補正
			if( $value > self::AREA_STRESS_MAX )
			{	// 最大値補正
				$value = self::AREA_STRESS_MAX;
			}
			else if( $value < self::AREA_STRESS_MIN )
			{	// 最小値補正
				$value = self::AREA_STRESS_MIN;
			}
			$buff[$id]['area_stress'] = $value;

			// 所属ステージIDも渡しておく
			$buff[$id]['stage_id'] = $area_master_list[$id]['stage_id'];

			// サイコハザード発生チェック
			if( $value >= 4 )
			{	// エリアストレス値が４以上の場合に特定確率でサイコハザードが発生
				$r = mt_rand( 1, 1000 );	// 確率が0.1%単位なので1~1000のランダム値をとる
				if( $r <= $hazard_prob[$value] )
				{	// サイコハザード発生
					$buff[$id]['status'] = Pp_MissionManager::AREA_STATUS_HAZARD;
					break;	// サイコハザードが発生したら以後のエリアの処理はキャンセル
				}
			}
			// サイコハザードが発生しなければ通常ステータス
			$buff[$id]['status'] = Pp_MissionManager::AREA_STATUS_NORMAL;
		}

		return $buff;
	}

	/**
	 * ユーザー実績情報の更新
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $accompany_character_id 同行サポートキャラID
	 * @param array $ingame_result クライアントから送信されたInGame結果
	 * @param int $result_type 結果処理種別
	 * @param int $clear_time クリアまでかかった時間（秒）
	 * @param int $is_mission_designate 指定ミッションか？
	 * @param int $user_m ユーザーマネージャのインスタンス
	 *
	 * @return 更新結果
	 */
	private function _updateUserAchievementCount( $pp_id, $accompany_character_id, $ingame_result, $result_type, $clear_time, $is_mission_designate, $user_m )
	{
		$columns = array(
			'btl_paralyzer' => $ingame_result['paralyzer'],		// パラライザー執行数
			'btl_eliminator' => $ingame_result['eliminator'],	// エリミネーター執行数
			'btl_decomposer' => $ingame_result['decomposer'],	// デコンポーザー執行数
			'btl_persuasion' => $ingame_result['persuasion'],	// [説得]での交渉成功数
			'btl_reprimand' => $ingame_result['reprimand'],		// [叱責]での交渉成功数
			'btl_warning' => $ingame_result['warning'],			// [警告]での交渉成功数
		);

		if(( $result_type === Pp_MissionManager::RESULT_TYPE_BEST )||( $result_type === Pp_MissionManager::RESULT_TYPE_NORMAL ))
		{	// クリアした
			if( $result_type === Pp_MissionManager::RESULT_TYPE_BEST )
			{
				$columns['btl_clear_best'] = 1;							// BESTクリア回数
			}
			else if( $result_type === Pp_MissionManager::RESULT_TYPE_NORMAL )
			{
				$columns['btl_clear_normal'] = 1;						// NORMALクリア回数
			}
			if( $clear_time <= 600 )
			{	// 10分以内クリア
				$columns['btl_clear_10min'] = 1;
				if( $clear_time <= 540 )
				{	// 9分以内クリア
					$columns['btl_clear_9min'] = 1;
					if( $clear_time <= 480 )
					{	// 8分以内クリア
						$columns['btl_clear_8min'] = 1;
						if( $clear_time <= 420 )
						{	// 7分以内クリア
							$columns['btl_clear_7min'] = 1;
							if( $clear_time <= 360 )
							{	// 6分以内クリア
								$columns['btl_clear_6min'] = 1;
								if( $clear_time <= 300 )
								{	// 5分以内クリア
									$columns['btl_clear_5min'] = 1;
								}
							}
						}
					}
				}
			}

			// クリアした時の同行サポートキャラは誰？
			switch( $accompany_character_id )
			{
				case Pp_CharacterManager::CHARACTER_ID_TSUNEMORI:	// 常守朱
					$key = 'mis_support_tsunemori';
					break;
				case Pp_CharacterManager::CHARACTER_ID_GINOZA:		// 宜野座伸元
					$key = 'mis_support_ginoza';
					break;
				case Pp_CharacterManager::CHARACTER_ID_MASAOKA:		// 征陸智己
					$key = 'mis_support_masaoka';
					break;
				case Pp_CharacterManager::CHARACTER_ID_KAGARI:		// 縢秀星
					$key = 'mis_support_kagari';
					break;
				case Pp_CharacterManager::CHARACTER_ID_KUNIZUKA:	// 六合塚弥生
					$key = 'mis_support_kunizuka';
					break;
				case Pp_CharacterManager::CHARACTER_ID_KOUGAMI:		// 狡噛慎也
					$key = 'mis_support_kougami';
					break;
				default:
					return null;
			}
			$columns[$key] = 1;			// 同行サポートキャラでのクリア数加算
		}

		// 指定ミッション？
		if( $is_mission_designate === true )
		{
			$columns['evt_paralyzer'] = $ingame_result['paralyzer'];	// パラライザー執行数
			$columns['evt_eliminator'] = $ingame_result['eliminator'];	// エリミネーター執行数
			$columns['evt_decomposer'] = $ingame_result['decomposer'];	// デコンポーザー執行数
			$columns['evt_persuasion'] = $ingame_result['persuasion'];	// [説得]での交渉成功数
			$columns['evt_reprimand'] = $ingame_result['reprimand'];	// [叱責]での交渉成功数
			$columns['evt_warning'] = $ingame_result['warning'];		// [警告]での交渉成功数
			if( $result_type === Pp_MissionManager::RESULT_TYPE_BEST )
			{
				$columns['evt_clear_best'] = 1;							// 指定ミッションBESTクリア回数
			}
			else if( $result_type === Pp_MissionManager::RESULT_TYPE_NORMAL )
			{
				$columns['evt_clear_normal'] = 1;						// 指定ミッションNORMALクリア回数
			}
			if( $clear_time <= 600 )
			{	// 10分以内クリア
				$columns['evt_clear_10min'] = 1;
				if( $clear_time <= 540 )
				{	// 9分以内クリア
					$columns['evt_clear_9min'] = 1;
					if( $clear_time <= 480 )
					{	// 8分以内クリア
						$columns['evt_clear_8min'] = 1;
						if( $clear_time <= 420 )
						{	// 7分以内クリア
							$columns['evt_clear_7min'] = 1;
							if( $clear_time <= 360 )
							{	// 6分以内クリア
								$columns['evt_clear_6min'] = 1;
								if( $clear_time <= 300 )
								{	// 5分以内クリア
									$columns['evt_clear_5min'] = 1;
								}
							}
						}
					}
				}
			}
		}
		return $user_m->addUserAchievementCount( $pp_id, $columns );
	}

	/**
	 * 新規に獲得した勲章の一覧を取得
	 *
	 * @param array $ach_cond_master 勲章条件マスタ
	 * @param array $ach_count_diff ユーザー実績差分情報
	 * @param array $ach_rank ユーザー勲章情報
	 * @param array $achievement_m Pp_AchievementManagerのインスタンス
	 *
	 * @return 更新結果
	 */
	private function _checkAchievement( $ach_cond_master, $ach_count_diff, $ach_rank, $achievement_m )
	{
		// 獲得済みの勲章IDを配列にまとめる
		$temp = array();
		if( !empty( $ach_rank ))
		{
			foreach( $ach_rank as $v )
			{
				$temp[] = $v['ach_id'];
			}
		}

		$get_ach_ids = array();	// 新規獲得勲章
		foreach( $ach_cond_master as $ach_id => $cond )
		{
			if( in_array( $ach_id, $temp ))
			{	// 既に取得済み
				continue;
			}

			if( !isset( $ach_count_diff[$ach_id] ))
			{	// まだ解放されていないもの
				continue;
			}

			// で、どうよ？条件満たしてる？
			if( $ach_count_diff[$ach_id] >= $cond['cond_value'] )
			{	// 条件を満たしていたら新規獲得
				$get_ach_ids[] = $ach_id;
			}
		}
		return $get_ach_ids;
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

		// エリアマスタリストの取得
		$ma = $mission_m->getMasterAreaList();
		if( empty( $ma ))
		{	// 取得エラー
			return null;
		}

		// ミッションマスタリストの取得
		$mm = $mission_m->getMasterMissionList();
		if( empty( $mm ))
		{	// 取得エラー
			return null;
		}

		// 最新の全ユーザーステージ情報を取得
		$us = $user_m->getUserStageList( $pp_id, "db" );
		if( empty( $us ))
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
		$ua = $user_m->getUserAreaList( $pp_id, $area_ids, "db" );
		if( empty( $ua ))
		{
			return null;
		}

		// 唐之杜ミッション以外のミッションマスタ情報を取得
		$mm = $mission_m->getMasterMissionListAssocByAreaIds( $area_ids );
		$mission_ids = array_keys( $mm );

		// 最新の全ユーザーミッション情報を取得（唐之杜ミッションは除外）
		$um = $user_m->getUserMissionList( $pp_id, $mission_ids, "db" );
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

			$avg = isset( $area_stress_avg[$stage_id] ) ? $area_stress_avg[$stage_id] : 0;
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
