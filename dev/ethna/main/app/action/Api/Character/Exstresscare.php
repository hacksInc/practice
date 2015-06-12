<?php
/**
 *	Api/Character/Exstresscare.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_character_exstresscare Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiCharacterExstresscare extends Pp_ApiActionForm
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
 *	api_character_stresscare action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiCharacterExstresscare extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_character_exstresscare Action.
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
	 *	api_character_exstresscare action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		$character_id = $this->af->get( 'character_id' );				// 臨時ストレスケア実行するキャラクターのID
		$api_transaction_id = $this->getApiTransactionId();				// トランザクションID

		// マネージャのインスタンスを取得
		$character_m =& $this->backend->getManager( 'Character' );
		$user_m =& $this->backend->getManager( 'User' );
		$trans_m =& $this->backend->getManager( 'Transaction' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );

		//-------------------------------------------------------------------
		//	定時ストレスケアを実行（トランザクションチェックの前にやること）
		//-------------------------------------------------------------------
		$res = $character_m->stressCare( $pp_id, $api_transaction_id );
		if( Ethna::isError( $res ))
		{
			$this->backend->logger->log( LOG_ERR, 'fixed stress care error.' );
			$this->af->setApp( 'status_detail_code', SDC_FIXED_STRESS_CARE_ERROR, true );
			return 'error_500';
		}

		//-------------------------------------------------------------------
		//	トランザクションチェック
		//-------------------------------------------------------------------
		// 多重処理防止チェック
		$json = $trans_m->getResultJson( $api_transaction_id );
		if( !empty( $json ))
		{	// 既に一度処理している
			$this->backend->logger->log( LOG_INFO, 'Found api_transaction_id.' );
			$temp = json_decode( $json, true );

			if( $res === true )
			{	// 定時ストレスケア処理で更新があった場合、処理結果を更新
				if( $character_id == Pp_CharacterManager::CHARACTER_ID_PLAYER )
				{	// プレイヤーキャラ
					$user_game = $user_m->getUserGame( $pp_id );
					if( empty( $user_game ))
					{
						$this->backend->logger->log( LOG_ERR, 'Not found user_game. pp_id='.$pp_id );
						$this->af->setApp( 'status_detail_code', SDC_USER_NONEXISTENCE, true );
						return 'error_500';
					}

					// 定時ストレスケア後の値で上書き
					$temp['modify_user_base']['crime_coef'] = $user_game['crime_coef'];
					$temp['modify_user_base']['ex_stress_care'] = $user_game['ex_stress_care'];
				}
				else
				{	// サポートキャラ
					$user_character = $character_m->getUserCharacterAssoc( $pp_id );
					if( empty( $user_character ))
					{
						$this->backend->logger->log( LOG_ERR, 'Not found user_character. pp_id='.$pp_id.', character_id='.$character_id );
						$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
						return 'error_500';
					}

					foreach( $temp['support_character'] as $k => $row )
					{
						if( $row['character_id'] != $character_id )
						{
							continue;
						}
						$chara_id = $row['character_id'];
						$temp['support_character'][$k]['crime_coef'] = $user_character[$chara_id]['crime_coef'];
						$temp['support_character'][$k]['ex_stress_care'] = $user_character[$chara_id]['ex_stress_care'];
					}
				}

				// 上書きした値でトランザクション情報を更新する
				$result_json = json_encode( $temp );
				$res = $trans_m->registTransaction( $pp_id, $api_transaction_id, $result_json );
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

		//-------------------------------------------------------------------
		//	キャラクター情報を取得
		//-------------------------------------------------------------------
		if( $character_id == Pp_CharacterManager::CHARACTER_ID_PLAYER )
		{	// プレイヤーキャラ
			$info = $user_m->getUserGame( $pp_id );
			if( empty( $info ))
			{	// 取得エラー
				$this->backend->logger->log( LOG_ERR, 'Not found user_game. pp_id='.$pp_id );
				$this->af->setApp( 'status_detail_code', SDC_USER_NONEXISTENCE, true );
			}
		}
		else
		{	// サポートキャラ
			$info = $character_m->getUserCharacter( $pp_id, $character_id );
			if( empty( $info ))
			{	// 取得エラー
				$this->backend->logger->log( LOG_ERR, 'Not found user_character. pp_id='.$pp_id.', character_id='.$character_id );
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'return 500';
			}
		}

		//-------------------------------------------------------------------
		//	臨時ストレスケアが実行可能かをチェック
		//-------------------------------------------------------------------
		$chara_master = $character_m->getMasterCharacter( $character_id );
		if( empty( $chara_master ))
		{	// 取得エラー
			return 'return 500';
		}

		if( $info['crime_coef'] <= $chara_master['crime_coef_lower_limit'] )
		{	// 犯罪係数が下限値に達している場合は実行不可
			$this->af->setApp( 'status_detail_code', SDC_EX_STRESS_CARE_PARAM_ERROR, true );
			return 'error_500';
		}

		//-------------------------------------------------------------------
		//	犯罪係数減少量を求める
		//-------------------------------------------------------------------
		//【Ａ】犯罪係数下限値
		//【Ｂ】犯罪係数現在値
		// 式：RoundToInt[ 0.5 * (【B】-【A】) ]
		$val = array();
		$val['crime'] = round( 0.5 * ( $info['crime_coef'] - $chara_master['crime_coef_lower_limit'] ));

		//-------------------------------------------------------------------
		//	能力値減少量を求める
		//-------------------------------------------------------------------
		//【Ａ】犯罪係数減少量
		//【Ｂ】犯罪係数上限値
		//【Ｃ】犯罪係数下限値
		//【Ｄ】パラメータリザルト補正値（メインキャラの場合は100固定）
		//【Ｅ】臨時ストレスケア回数

		// 分かりにくいんで仕様書の【Ａ】～【Ｅ】に合わせて変数に入れる
		$a = $val['crime'];
		$b = $chara_master['crime_coef_upper_limit'];
		$c = $chara_master['crime_coef_lower_limit'];
		// $d はパラメータ別に変わるのでここでは入れない
		$e = $info['ex_stress_care'];

		// 条件式：【E】< 7
		// true:【F】= 2 *【E】+ 2
		// false:【F】= 15
		$f = ( $e < 7 ) ? (( 2 * $e ) + 2 ) : 15;

		$this->backend->logger->log( LOG_DEBUG, "crime:".$val['crime'] );

		$idx = array( 'body', 'intelli', 'mental' );
		foreach( $idx as $v )
		{
			// 式：CeilToInt[ 0.02 *【F】*【D】*【A】/ (【B】-【C】) ]
			$d = $chara_master[$v.'_coef_corr'];
			$val[$v] = ceil( 0.02 * $f * $d * $a / ( $b - $c ));
			$this->backend->logger->log( LOG_DEBUG, "$v:".$val[$v] );
		}

		//-------------------------------------------------------------------
		//	変動後の値を求める
		//-------------------------------------------------------------------
		// 現在値から減少量を引いた値をセット
		$update_param = array(
			'crime_coef' => ( $info['crime_coef'] - $val['crime'] ),
			'body_coef' => ( $info['body_coef'] - $val['body'] ),
			'intelli_coef' => ( $info['intelli_coef'] - $val['body'] ),
			'mental_coef' => ( $info['mental_coef'] - $val['mental'] )
		);
		// 限界値補正
		foreach( $update_param as $k => $v )
		{
			if( $k == 'crime_coef' )
			{	// 犯罪係数
				if( $update_param[$k] > $chara_master['crime_coef_upper_limit'] )
				{	// 最大値補正
					$update_param[$k] = $chara_master['crime_coef_upper_limit'];
				}
				else if( $update_param[$k] < $chara_master['crime_coef_lower_limit'] )
				{	// 最小値補正
					$update_param[$k] = $chara_master['crime_coef_lower_limit'];
				}
			}
			else
			{	// 身体係数・知能係数・心的係数
				if( $update_param[$k] > Pp_CharacterManager::CHARACTER_PARAM_MAX )
				{	// 最大値補正
					$update_param[$k] = Pp_CharacterManager::CHARACTER_PARAM_MAX;
				}
				else if( $update_param[$k] < Pp_CharacterManager::CHARACTER_PARAM_MIN )
				{	// 最小値補正
					$update_param[$k] = Pp_CharacterManager::CHARACTER_PARAM_MIN;
				}
			}
		}
		$update_param['ex_stress_care'] = $info['ex_stress_care'] + 1;

		//-------------------------------------------------------------------
		//	DBを更新
		//-------------------------------------------------------------------
		// DBトランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------------------------
			//	パラメータを更新
			//-------------------------------------------------------------------------------
			if( $character_id == Pp_CharacterManager::CHARACTER_ID_PLAYER )
			{	// プレイヤーキャラ
				$res = $user_m->updateUserGame( $pp_id, $update_param );
			}
			else
			{	// サポートキャラ
				$res = $character_m->updateUserCharacter( $pp_id, $character_id, $update_param );
			}
			if( $res !== true )
			{	// 更新エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			//-------------------------------------------------------------------------------
			//	行動ログを記録
			//-------------------------------------------------------------------------------
			$columns = array(
				'pp_id' => $pp_id,										// サイコパスID
				'api_transaction_id' => $api_transaction_id,			// トランザクションID
				'processing_type' => 'A02',								// 処理コード
				'character_id' => $character_id,						// 臨時ストレスケアを実行したキャラクターID
				'crime_coef' => $update_param['crime_coef'],			// 犯罪係数
				'crime_coef_prev' => $info['crime_coef'],				// 犯罪係数（変動前）
				'body_coef' => $update_param['body_coef'],				// 身体係数（変動前と同じ）
				'body_coef_prev' => $info['body_coef'],					// 身体係数（変動前）
				'intelli_coef' => $update_param['intelli_coef'],		// 知能係数（変動前と同じ）
				'intelli_coef_prev' => $info['intelli_coef'],			// 知能係数（変動前）
				'mental_coef' => $update_param['mental_coef'],			// 心的係数（変動前と同じ）
				'mental_coef_prev' => $info['mental_coef'],				// 心的係数（変動前）
				'ex_stress_care' => $update_param['ex_stress_care'],	// 臨時ストレスケア回数（変動前と同じ）
				'ex_stress_care_prev' => $info['ex_stress_care']		// 臨時ストレスケア回数（変動前）
			);
			$res = $logdata_m->logCharacter( $columns );

			//-------------------------------------------------------------------------------
			//	クライアントに返すパラメータをセット
			//-------------------------------------------------------------------------------
			$buff = array();

			// 最新のキャラクター情報を取得
			if( $character_id == Pp_CharacterManager::CHARACTER_ID_PLAYER )
			{	// プレイヤーキャラ
				$info_new = $user_m->getUserGame( $pp_id );
				if( empty( $info_new ))
				{	// キャラ情報がない！？
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
				$modify_user_base = array(
					'crime_coef' => $info_new['crime_coef'],
					'body_coef' => $info_new['body_coef'],
					'intelli_coef' => $info_new['intelli_coef'],
					'mental_coef' => $info_new['mental_coef'],
					'ex_stress_care' => $info_new['ex_stress_care']
				);
				$this->af->setApp( 'modify_user_base', $modify_user_base, true ); 
				$buff['modify_user_base'] = $modify_user_base;
			}
			else
			{	// サポートキャラ
				$info_new = $character_m->getUserCharacter( $pp_id );
				if( empty( $info_new ))
				{	// キャラ情報がない！？
					$err_msg = '';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
				$support_character = array();
				foreach( $info_new as $v )
				{
					$support_character[] = array(
						'character_id' => $v['character_id'],
						'crime_coef' => $v['crime_coef'],
						'body_coef' => $v['body_coef'],
						'intelli_coef' => $v['intelli_coef'],
						'mental_coef' => $v['mental_coef'],
						'ex_stress_care' => $v['ex_stress_care']
					);
				}
				$this->af->setApp( 'support_character', $support_character, true ); 
				$buff['support_character'] = $support_character;
			}

			// 処理結果をトランザクション情報として記録する
			$result_json = json_encode( $buff );
			$res = $trans_m->registTransaction( $pp_id, $api_transaction_id, $result_json );
			if( $res !== true )
			{	// 記録エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			$db->commit();			// コミット
		}
		catch( Exception $e )
		{
			$db->rollback();		// ロールバックする
			return 'error_500';
		}

		return 'api_json_encrypt';
	}
}
