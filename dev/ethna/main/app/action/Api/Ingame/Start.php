<?php
/**
 *	Api/Ingame/Start.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_ingame_start Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiIngameStart extends Pp_ApiActionForm
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
 *	api_ingame_start action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiIngameStart extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_ingame_start Action.
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
	 *	api_ingame_start action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// 唐之杜通信デバッグ用サイコパスID
		$karanomori_debug_pp_id = array(
			918844311		// stg-main 'シビュラぁ後ろぉ！'
		);

		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		$api_transaction_id = $this->getApiTransactionId();
		$play_id = $this->af->get( 'play_id' );				// プレイID
		$mission_id = $this->af->get( 'mission_id' );		// 開始するミッションID

		/*
		if(empty($pp_id))
		{
			//$pp_id = 915694803;
			$pp_id = 916150486;
			$play_id = 'hogehogehoge';
			$api_transaction_id = time();
			$mission_id = 10013101;
		}
		*/

		// マネージャのインスタンスを取得
		$user_m =& $this->backend->getManager( 'User' );
		$character_m =& $this->backend->getManager( 'Character' );
		$mission_m =& $this->backend->getManager( 'Mission' );
		$transaction_m =& $this->backend->getManager( 'Transaction' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$kpi_m =& $this->backend->getManager( 'Kpi' );

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
			foreach( $temp as $k => $v )
			{
				$this->af->setApp( $k, $v, true );
			}
			return 'api_json_encrypt';
		}

		// ミッションマスタ情報を取得
		$mission_master = $mission_m->getMasterMission( $mission_id );
		if( empty( $mission_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Not found mission_master. mission_id='.$mission_id );
			return 'error_500';
		}

		// ミッションが所属するエリアマスタ情報を取得
		$area_master = $mission_m->getMasterArea( $mission_master['area_id'] );
		if( empty( $area_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Not found area_master. area_id='.$mission_master['area_id'] );
			return 'error_500';
		}

		// ミッションが所属するエリアが所属するステージマスタ情報を取得（ややこしい）
		$stage_master = $mission_m->getMasterStage( $area_master['stage_id'] );
		if( empty( $stage_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Not found stage_master. stage_id='.$area_master['stage_id'] );
			return 'error_500';
		}

		// ミッションが所属するエリアのユーザーエリア情報を取得
		$user_area = $user_m->getUserArea( $pp_id, $mission_master['area_id'] );
		if( empty( $user_area ))
		{
			if( $area_master['type'] == Pp_MissionManager::AREA_TYPE_KARANOMORI )
			{	// 唐之杜ミッションの場合は新規に作成する
				$res = $user_m->releaseNewArea( $pp_id, $mission_master['area_id'], $area_master['area_stress_def'] );
				if( $res !== true )
				{
					$this->backend->logger->log( LOG_ERR, 'Cannot release new area. pp_id='.$pp_id.', area_id='.$mission_master['area_id'] );
					return 'error_500';
				}
				$user_area = $user_m->getUserArea( $pp_id, $mission_master['area_id'], "db" );
				if( empty( $user_area ))
				{	// 取得エラー
					$this->backend->logger->log( LOG_ERR, 'Not found user_area. pp_id='.$pp_id.', area_id='.$mission_master['area_id'] );
					return 'error_500';
				}
			}
			else
			{	// 取得エラー
				$this->backend->logger->log( LOG_ERR, 'Not found user_area. pp_id='.$pp_id.', area_id='.$mission_master['area_id'] );
				return 'error_500';
			}
		}

		// ミッションが所属するエリアが所属するユーザーステージ情報を取得（これまたややこしい）
		if( $area_master['type'] == Pp_MissionManager::AREA_TYPE_NORMAL )
		{
			$user_stage = $user_m->getUserStage( $pp_id, $area_master['stage_id'] );
			if( empty( $user_stage ))
			{	// 取得エラー
				$this->backend->logger->log( LOG_ERR, 'Not found user_stage. pp_id='.$pp_id.', stage_id='.$area_master['stage_id'] );
				return 'error_500';
			}
		}

		// ユーザー基本情報を取得
		$ub = $user_m->getUserBase( $pp_id );
		if( empty( $ub ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Not found user_base. pp_id='.$pp_id );
			return 'error_500';
		}

		// ユーザーとサポートキャラの犯罪係数を取得
		$ug = $user_m->getUserGame( $pp_id );
		if( empty( $ug ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Not found user_game. pp_id='.$pp_id );
			return 'error_500';
		}
		$sp_character_id = $mission_master['accompany_character_id'];
 		$support_chara = $character_m->getUserCharacter( $pp_id, $sp_character_id );
		if( empty( $support_chara ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Not found user_character. pp_id='.$pp_id.', character_id='.$sp_character_id );
			return 'error_500';
		}

		// キャラクターマスタの情報を取得
		$player_chara_master = $character_m->getMasterCharacter( Pp_CharacterManager::CHARACTER_ID_PLAYER );
		if( empty( $player_chara_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Not found player_character_master. character_id='.Pp_CharacterManager::CHARACTER_ID_PLAYER );
			return 'error_500';
		}

		$support_chara_master = $character_m->getMasterCharacter( $sp_character_id );
		if( empty( $support_chara_master ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'Not found support_character_master. character_id='.$sp_character_id );
			return 'error_500';
		}

		if( $area_master['type'] == Pp_MissionManager::AREA_TYPE_KARANOMORI )
		{	// 唐之杜ミッションの場合
			//error_log( "@@@@@@@@@@@@@@@@@@ KARANOMORI MISSION!!!" );
			$karanomori_report = 0;
			$psycho_hazard_flag = 0;
			$crime_coef_pl = $ug['crime_coef'];
			$crime_coef_sp = $support_chara['crime_coef'];
			$psycho_hazard_correction_pl = 0;
			$psycho_hazard_correction_sp = 0;
		}
		else
		{	// 唐之杜ミッション以外の場合
			//-------------------------------------------------------------------------------
			//	ミッション受注条件チェック
			//-------------------------------------------------------------------------------
			// 開催時間チェック
			$now = $_SERVER['REQUEST_TIME'];
			if(( $now < strtotime( $area_master['date_start'] ))||( strtotime( $area_master['date_end'] ) < $now ))
			{	// 開催期間外
				$this->af->setApp( 'status_detail_code', SDC_MISSION_ERROR, true );
				return 'error_500';
			}

			// プレイヤーの犯罪係数チェック
			if( $ug['crime_coef'] >= $player_chara_master['crime_coef_upper_limit'] )
			{	// 犯罪係数が上限値に達している
				$this->af->setApp( 'status_detail_code', SDC_MISSION_CRIME_COEF_MAX, true );
				return 'error_500';
			}
			// サポートキャラの犯罪係数チェック
			if( $support_chara['crime_coef'] >= $support_chara_master['crime_coef_upper_limit'] )
			{	// 犯罪係数が上限値に達している
				$this->af->setApp( 'status_detail_code', SDC_MISSION_CRIME_COEF_MAX, true );
				return 'error_500';
			}

			// アンロックサポートキャラチェック
			if( !empty( $mission_master['unlock_character_ids'] ) && !empty( $mission_master['unlock_parameters'] ))
			{	// ミッション受注条件あり
				$unlock_chara = explode( ',', $mission_master['unlock_character_ids'] );	// アンロックサポートキャラIDを配列に
				$unlock_param = explode( ',', $mission_master['unlock_parameters'] );		// アンロックパラメータ条件を配列に
				if( count( $unlock_chara ) !== count( $unlock_param ))
				{	// キャラ数と条件パラメータの数が合っていない
					$this->backend->logger->log( LOG_ERR, 'Unlock param count error. count_chara='.count( $unlock_chara ).', count_param='.count( $unlock_param ));
					return 'error_500';
				}
				foreach( $unlock_chara as $k => $c )
				{
					// ユーザーキャラクター情報を取得
					$uc = $character_m->getUserCharacter( $pp_id, $c );
					if( empty( $uc ))
					{	// 取得エラー
						$this->backend->logger->log( LOG_ERR, 'Not found user_character. pp_id='.$pp_id.', character_id='.$c );
						return 'error_500';
					}

					// 条件チェック
					$res = $this->_checkUnlockCondition( $uc, $unlock_param[$k] );
					if( is_null( $res ))
					{	// アンロック条件文字列のフォーマットエラー
						$this->backend->logger->log( LOG_ERR, 'Unlock condition format error.' );
						return 'error_500';
					}
					if( $res !== true )
					{	// アンロック条件を満たしていない
						$this->backend->logger->log( LOG_ERR, 'Unlock condition error.' );
						$this->af->setApp( 'status_detail_code', SDC_MISSION_SUPPORT_PARAM_ERROR, true );
						return 'error_500';
					}
				}
			}

			//-------------------------------------------------------------------------------
			//	犯罪係数上昇値計算に使用するパラメータを取得
			//-------------------------------------------------------------------------------
			// サポートキャラの犯罪係数上昇基本値を取得
			$rise_crime_coef_sp = $mission_master['rise_crime_coef_sp'];

			// エリアストレス平均値補正を取得
			$avg_list = $mission_m->getAverageAreaStressAssoc( $pp_id );
			$avg = ( isset( $avg_list[$area_master['stage_id']] )) ? $avg_list[$area_master['stage_id']] : 0;
			$area_stress_value = $mission_m->getAreaStressAvgCorrection( $avg );

			// サイコハザード発生状態
			$psycho_hazard_flag = ( $user_area['status'] == Pp_MissionManager::AREA_STATUS_HAZARD ) ? 1 : 0;

			// サイコハザード補正を取得
			$psycho_hazard_value = 30 * $psycho_hazard_flag;

			//-------------------------------------------------------------------------------
			//	プレイヤーの犯罪係数を求める
			//
			// ①ミッションマスタ【プレイヤーキャラ犯罪係数上昇基本値】
			// ②プレミアム登録補正（※現時点では存在しない仕様のため無視）
			// ③エリアストレス平均値補正
			// ④サイコハザード補正
			// 式：FloorToInt( ① * ( 1 + ( ② + ③ + ④ ) / 100 ))
			//-------------------------------------------------------------------------------
			// 上昇値を求める
			$temp = $mission_master['rise_crime_coef_pl'] * ( 1 + (( $area_stress_value + $psycho_hazard_value ) / 100 ));
			if( $psycho_hazard_flag == 0 )
			{	// サイコハザード未発生なら、発生と未発生の差分はなし
				$psycho_hazard_correction_pl = 0;
			}
			else
			{	// サイコハザード発生中なら、発生していなかった場合との差分を出す
				$temp2 = $mission_master['rise_crime_coef_pl'] * ( 1 + ( $area_stress_value  / 100 ));
				$psycho_hazard_correction_pl = $temp - $temp2;
			}

			// 現在の犯罪係数に上昇値を加算
			$crime_coef_pl = $ug['crime_coef'] + $temp;

			// 最大値・最小値の補正をする
			if( $crime_coef_pl > $player_chara_master['crime_coef_upper_limit'] )
			{	// 最大値補正
				$crime_coef_pl = $player_chara_master['crime_coef_upper_limit'];
			}
			else if( $crime_coef_pl < $player_chara_master['crime_coef_lower_limit'] )
			{	// 最小値補正
				$crime_coef_pl = $player_chara_master['crime_coef_lower_limit'];
			}

			//-------------------------------------------------------------------------------
			//	サポートキャラの犯罪係数を求める
			//
			// ①ミッションマスタ【サポートキャラ犯罪係数上昇基本値】
			// ②サポートキャラマスタ【犯罪係数リザルト補正値】
			// ③プレミアム登録補正（※現時点では存在しない仕様のため無視）
			// ④エリアストレス平均値補正
			// ⑤サイコハザード補正
			// 式：FloorToInt( ① * ② / 100 * ( 1 + ( ③ + ④ + ⑤ ) / 100 ))
			//-------------------------------------------------------------------------------
			// 上昇値を求める
			$temp1 = $mission_master['rise_crime_coef_sp'] * $support_chara_master['crime_coef_corr'] / 100;
			$temp = $temp1 * ( 1 + (( $area_stress_value + $psycho_hazard_value ) / 100 ));
			if( $psycho_hazard_flag == 0 )
			{	// サイコハザード未発生なら、発生と未発生の差分はなし
				$psycho_hazard_correction_sp = 0;
			}
			else
			{	// サイコハザード発生中なら、発生していなかった場合との差分を出す
				$temp2 = $temp1 * ( 1 + ( $area_stress_value / 100 ));
				$psycho_hazard_correction_sp = $temp - $temp2;
			}

			// 現在の犯罪係数に上昇値を加算
			$crime_coef_sp = $support_chara['crime_coef'] + $temp;

			// 最大値・最小値の補正をする
			if( $crime_coef_sp > $support_chara_master['crime_coef_upper_limit'] )
			{	// 上限補正
				$crime_coef_sp = $support_chara_master['crime_coef_upper_limit'];
			}
			else if( $crime_coef_sp < $support_chara_master['crime_coef_lower_limit'] )
			{	// 下限補正
				$crime_coef_sp = $support_chara_master['crime_coef_lower_limit'];
			}

			//-------------------------------------------------------------------------------
			//	唐之杜通信発生判定
			//-------------------------------------------------------------------------------
			$karanomori_report = 0;
			$release = $user_m->isReleasedKaranomori( $pp_id );
			//error_log('@@@@@ karanomori release: '.(( $release === true ) ? 1 : 0 ));
			if(( $area_master['type'] == Pp_MissionManager::AREA_TYPE_NORMAL )&&( $release === true ))
			{
			//error_log('@@@@@ karanomori check');

				if( !empty( $stage_master['karanomori_report_prob'] ))
				{	// 確率設定あり
					if( in_array( $pp_id, $karanomori_debug_pp_id ))
					{	// 唐之杜通信デバッグユーザーなら唐之杜先生に出番を促す
						$karanomori_report = 1;
					}
					else
					{	// 通常ユーザー
						$prob = explode( ',', $stage_master['karanomori_report_prob'] );
						$r = mt_rand( 1, 100 );
						if( $r <= $prob[$user_stage['karanomori_report']] )
						{	// 唐之杜先生出番です！
							$karanomori_report = 1;
						}
					}
				}
			}
		}

		// DBトランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			if( $area_master['type'] != Pp_MissionManager::AREA_TYPE_KARANOMORI )
			{	// 唐之杜通信ミッション以外の場合
				//-------------------------------------------------------------------------------
				//	プレイヤーの犯罪係数を上昇させる
				//-------------------------------------------------------------------------------
				$columns = array( 'crime_coef' => $crime_coef_pl );
				$ret = $user_m->updateUserGame( $pp_id, $columns );
				if( $ret !== true )
				{	// 更新エラー
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}

				//-------------------------------------------------------------------------------
				//	サポートキャラの犯罪係数を上昇させる
				//-------------------------------------------------------------------------------
				$columns = array( 'crime_coef' => $crime_coef_sp );
				$ret = $character_m->updateUserCharacter( $pp_id, $sp_character_id, $columns );
				if( $ret !== true )
				{	// 更新エラー
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
			}

			//-------------------------------------------------------------------------------
			//	InGameに関する情報を保持しておく
			//-------------------------------------------------------------------------------
			$columns = array(
				'play_id' => $play_id,								// プレイID
				'mission_id' => $mission_id,						// 最後に遊んだミッションID
				'accompany_character_id' => $mission_master['accompany_character_id'],	// 最後に同行したサポートキャラID
				'hazard_flag' => $psycho_hazard_flag,				// サイコハザード発生状態
				'karanomori_report_flag' => $karanomori_report,		// 唐之杜先生出番フラグ
				'crime_coef_pl' => $ug['crime_coef'],				// InGame開始前（上昇前）のユーザー犯罪係数
				'crime_coef_pl_after' => $crime_coef_pl,			// InGame開始後のユーザー犯罪係数
				'hazard_diff_pl' => $psycho_hazard_correction_pl,	// サイコハザード発生中と未発生での上昇値差分
				'crime_coef_sp' => $support_chara['crime_coef'],	// InGame開始前（上昇前）のサポートキャラ犯罪係数
				'crime_coef_sp_after' => $crime_coef_sp,			// InGame開始後のサポートキャラ犯罪係数
				'hazard_diff_sp' => $psycho_hazard_correction_sp	// サイコハザード発生中と未発生での上昇値差分
			);
			$ret = $user_m->updateUserIngame( $pp_id, $columns );
			if( $ret !== true )
			{	// 更新エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			//-------------------------------------------------------------------------------
			//	エリアのプレイ日時を更新
			//-------------------------------------------------------------------------------
			$columns = array( 'date_last_play' => strftime( "%Y-%m-%d %H:%M:%S" ));
			$res = $user_m->updateUserArea( $pp_id, $area_master['area_id'], $columns );
			if( $res !== true )
			{
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			//-------------------------------------------------------------------------------
			//	KPI/ログの処理
			//-------------------------------------------------------------------------------
			// InGame開始履歴の記録
			$columns = array(
				'pp_id' => $pp_id,											// サイコパスID
				'api_transaction_id' => $api_transaction_id,				// トランザクションID
				'play_id' => $play_id,										// プレイID
				'mission_id' => $mission_id,								// 実行ミッションID
				'accompany_character_id' => $sp_character_id				// 同行サポートキャラクターID
			);
			$res = $logdata_m->logIngameStart( $columns );

			if( $area_master['type'] != Pp_MissionManager::AREA_TYPE_KARANOMORI )
			{	// 唐之杜通信ミッション以外の場合
				// キャラクター情報変動履歴（プレイヤー）
				$columns = array(
					'pp_id' => $pp_id,											// サイコパスID
					'api_transaction_id' => $api_transaction_id,				// トランザクションID
					'processing_type' => 'A03',									// 処理コード
					'character_id' => Pp_CharacterManager::CHARACTER_ID_PLAYER,	// キャラクターID（プレイヤー）
					'crime_coef' => $crime_coef_pl,								// 犯罪係数
					'crime_coef_prev' => $ug['crime_coef'],						// 犯罪係数（変動前）
					'body_coef' => $ug['body_coef'],							// 身体係数（変動前と同じ）
					'body_coef_prev' => $ug['body_coef'],						// 身体係数（変動前）
					'intelli_coef' => $ug['intelli_coef'],						// 知能係数（変動前と同じ）
					'intelli_coef_prev' => $ug['intelli_coef'],					// 知能係数（変動前）
					'mental_coef' => $ug['mental_coef'],						// 心的係数（変動前と同じ）
					'mental_coef_prev' => $ug['mental_coef'],					// 心的係数（変動前）
					'ex_stress_care' => $ug['ex_stress_care'],					// 臨時ストレスケア回数（変動前と同じ）
					'ex_stress_care_prev' => $ug['ex_stress_care']				// 臨時ストレスケア回数（変動前）
				);
				$res = $logdata_m->logCharacter( $columns );

				// キャラクター情報変動履歴（同行サポートキャラクター）
				$columns = array(
					'pp_id' => $pp_id,											// サイコパスID
					'api_transaction_id' => $api_transaction_id,				// トランザクションID
					'processing_type' => 'A03',									// 処理コード
					'character_id' => $sp_character_id,							// キャラクターID（同行サポートキャラクターID）
					'crime_coef' => $crime_coef_sp,								// 犯罪係数
					'crime_coef_prev' => $support_chara['crime_coef'],			// 犯罪係数（変動前）
					'body_coef' => $support_chara['body_coef'],					// 身体係数（変動前と同じ）
					'body_coef_prev' => $support_chara['body_coef'],			// 身体係数（変動前）
					'intelli_coef' => $support_chara['intelli_coef'],			// 知能係数（変動前と同じ）
					'intelli_coef_prev' => $support_chara['intelli_coef'],		// 知能係数（変動前）
					'mental_coef' => $support_chara['mental_coef'],				// 心的係数（変動前と同じ）
					'mental_coef_prev' => $support_chara['mental_coef'],		// 心的係数（変動前）
					'ex_stress_care' => $support_chara['ex_stress_care'],		// 臨時ストレスケア回数（変動前と同じ）
					'ex_stress_care_prev' => $support_chara['ex_stress_care']	// 臨時ストレスケア回数（変動前）
				);
				$res = $logdata_m->logCharacter( $columns );
			}

			// KPI処理
			$tag = ( $ub['device_type'] == 1 ) ? 'Apple' : 'Google';
			$uym = date( "ym", strtotime( $ub['date_created'] ) );
			$kpi_m->log( $tag."-ppp-dau", 3, 1, time(), $pp_id, "", "", "" );
			$kpi_m->log( $tag."-ppp-".$uym."_install_user_mau", 3, 1, time(), $pp_id, "", "", "" );

			//-------------------------------------------------------------------------------
			//	クライアントに返すパラメータをセット
			//-------------------------------------------------------------------------------
			$buff = array();

			// ミッション開始情報
			$mission_start = array(
				'karanomori_report' => $karanomori_report	// 唐之杜先生出演フラグ
			);
			$buff['mission_start'] = $mission_start;

			// 処理結果をトランザクション情報として記録する
			$result_json = json_encode( $buff );
			$res = $transaction_m->registTransaction( $pp_id, $api_transaction_id, $result_json );
			if( $res !== true )
			{	// 記録エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			$this->af->setApp( 'mission_start', $mission_start, true );

			$db->commit();
		}
		catch( Exception $e )
		{	// 例外発生
			$db->rollback();		// 更新をロールバックする
			return 'error_500';
		}

		return 'api_json_encrypt';
	}

	//====================================================================================================
	//====================================================================================================
	//====================================================================================================
	/*
		サポートキャラのアンロック条件チェック
	*/
	private function _checkUnlockCondition( $user_character, $condition )
	{
		$param_key = array( 1 => 'crime_coef', 2 => 'body_coef', 3 => 'intelli_coef', 4 => 'mental_coef' );

		// 条件文字列を分解
		$temp = str_replace( array( ' ', '{', '}' ), '', $condition );	// 半角スペースと{}を削除
		$buff = explode( ':', $temp );	// 各パラメータ毎に配列にする
		foreach( $buff as $str )
		{
			// 条件文字列の分解は、文字数による分解（１文字目・２文字目・３文字目以降）で問題ないと思う
			$type = substr( $str, 0, 1 );		// １文字目（パラメータ種別）
			$operator = substr( $str, 1, 1 );	// ２文字目（演算子）
			$value = substr( $str, 2 );			// ３文字目以降（数値）

			// 数値あるよね？
			if( !ctype_digit( $value ))
			{	// 比較する数値がおかしい（整数だけだよ）
				return null;
			}

			// 対象サポートキャラのパラメータ値を取得
			$param = ( int )$user_character[$param_key[$type]];
			if( is_null( $param ))
			{	// パラメータ種別がおかしい？
				return null;
			}

			// パラメータを比較する（条件文字列の演算子は'<','>'ではあるが、判定には等価('=')含むので注意！）
			if( $operator === '>' )
			{	// サポートキャラの数値が？以下である
				$result = (( int )$value >= $param ) ? true : false;
			}
			else if( $operator === '<' )
			{	// サポートキャラの数値が？以上である
				$result = (( int )$value <= $param ) ? true : false;
			}
			else
			{	// 謎の演算子
				return null;
			}

			if( $result === false )
			{	// 条件を満たしていなければエラーコードを返す
				return false;
			}
		}
		return true;
	}
}
