<?php
/**
 *  Inapi/Raid/Battle/Makeresult.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_ItemManager.php';

/**
 *  inapi_raid_party_Makeresult Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidBattleMakeresult extends Pp_InapiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id' => array(
			'name'     => 'パーティID',
			'type'     => VAR_TYPE_INT,
			'required' => true,
		),
		'sally_no' => array(
			'name'     => '出撃NO',
			'type'     => VAR_TYPE_INT,
			'required' => true,
		),
		'dungeon_id' => array(
			'name'     => 'ダンジョンID',
			'type'     => VAR_TYPE_INT,
			'required' => true,
		),
		'dungeon_rank' => array(
			'name'     => '難易度',
			'type'     => VAR_TYPE_INT,
			'required' => true,
		),
		'dungeon_lv' => array(
			'name'     => 'ダンジョンLV',
			'type'     => VAR_TYPE_INT,
			'required' => true,
		),
		'quest_result' => array(
			'name'     => 'クエスト結果',
			'type'     => VAR_TYPE_INT,
			'required' => true,
		),
		'damage' => array(
			'name'     => '与ダメージ',
			'type'     => array( VAR_TYPE_INT ),
			'required' => true,
		),
    );
}

/**
 *  Inapi_raid_battle_makeresult action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidBattleMakeresult extends Pp_InapiActionClass
{

	/**
	 *  preprocess of inapi_raid_battle_makeresult Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{	// バリデートエラー
			$this->af->setApp('error_detail', 'validate error.', true);
			$this->af->setApp('status_detail_code', SDC_INAPI_ARGUMENT_ERROR, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}
	    return null;
	}

	/**
	 *  inapi_raid_battle_makeresult action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$party_id = $this->af->get( 'party_id' );
		$sally_no = $this->af->get( 'sally_no' );
		$dungeon_id = $this->af->get( 'dungeon_id' );
		$dungeon_rank = $this->af->get( 'dungeon_rank' );
		$dungeon_lv = $this->af->get( 'dungeon_lv' );
		$quest_result = $this->af->get( 'quest_result' );
		$damage = $this->af->get( 'damage' );

		// マネージャのインスタンスを取得
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$raid_user_m =& $this->backend->getManager( 'RaidUser' );
		$raid_quest_m =& $this->backend->getManager( 'RaidQuest' );
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );
		$badge_m =& $this->backend->getManager( 'Badge' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$unit_m =& $this->backend->getManager( 'Unit' );
		$present_m =& $this->backend->getManager( 'Present' );
		$monster_m =& $this->backend->getManager( 'Monster' );
		$user_m =& $this->backend->getManager( 'User' );

		//-------------------------------------------------------------
		//	共通情報を取得
		//-------------------------------------------------------------
		// パーティ情報
		$party_info = $raid_party_m->getParty( $party_id );
		if( is_null( $party_info ) === true )
		{	// 取得エラー
			$error_detail = "getParty( $party_id )";
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
		if( empty( $party_info ) === true )
		{	// 取得エラー
			$error_detail = "getParty( $party_id )";
			$this->af->setApp( 'status_detail_code', SDC_RAID_PARTYID_INVALID, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		// クエストデータ
		$quest_data = $raid_quest_m->getQuest( $party_id, $sally_no );
		if(( !$quest_data )||( Ethna::isError( $quest_data )))
		{	// 取得エラー
			$error_detail = "getQuest( $party_id, $sally_no )";
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		// ダンジョン情報
		$dungeon = $raid_quest_m->getMasterDungeonById( $dungeon_id );
		if( empty( $dungeon ) === true )
		{
			$error_detail = "getMasterDungeonById( $dungeon_id )";
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		// ダンジョン詳細情報
		$dungeon_detail = $raid_quest_m->getMasterDungeonDetail(
			$dungeon_id, $dungeon_rank, $dungeon_lv
		);
		if(( !$dungeon_detail )||( Ethna::isError( $dungeon_detail )))
		{	// 取得エラー
			$error_detail = "getMasterDungeonDetail( $dungeon_id, $dungeon_rank, $dungeon_lv )";
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
		//同ダンジョン・同難易度での次のダンジョンLV
		//現在の値にしておき、パーティマスタのダンジョンクリア処理内で+1する
		$next_dungeon_lv = $dungeon_lv;

		// ダンジョンの開催終了時間を取得
		$guerrilla_endtime = $raid_quest_m->getDungeonGuerrillaScheduleTimeEndById( $dungeon_id );
		if( empty( $guerrilla_endtime ) === true )
		{	// ゲリラ時間外
			$guerrilla_schedules = $raid_quest_m->getMasterGuerrillaScheduleByIds( array( $dungeon_id ));
			if( empty( $guerrilla_schedules ) === false )
			{	// ゲリラ設定があるならゲリラ時間外なのでエラー
				/*
				$error_detail = "outside guerrilla hours.: dungeon_id = ".$dungeon_id;
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DUNGEON_OVERTIME, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
				*/
				
				$breakup = 1;			// 現在のダンジョンは続けられない
				$guerrilla_end_date = null;
			}
			else
			{	// ゲリラ設定そのものがない場合は定常開催
				$guerrilla_end_date = $dungeon['date_end'];
				$breakup = 0;
			}
		}
		else
		{	// ゲリラ時間内
			$guerrilla_end_date = strftime( "%Y-%m-%d" ).' '.$guerrilla_endtime;
			$breakup = 0;
		}

		// クリア報酬情報
		if( $quest_result == Pp_RaidQuestManager::QUEST_RESULT_CLEAR )
		{	// クエストクリア時だけ
			$reward_info = $raid_quest_m->getMasterDungeonClearRewardGrouping( $dungeon_id, $dungeon_rank, $dungeon_lv );
			if(( !$reward_info )||( Ethna::isError( $reward_info )))
			{	// 取得エラー
				$error_detail = "getMasterDungeonClearRewardGrouping( $dungeon_id, $dungeon_rank, $dungeon_lv )";
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}
		}

		// 出撃メンバー情報（ステータスが“出撃中”or“回復中”or“探索中”のメンバーだけ取得）
		$status = array(
			Pp_RaidPartyManager::SALLY_STATUS_MAP,
			Pp_RaidPartyManager::SALLY_STATUS_BATTLE,
			Pp_RaidPartyManager::SALLY_STATUS_RECOVER
		);
		$sally_members = $raid_party_m->getSallyMembers( $party_id, $sally_no, $status );
		if( is_null( $sally_members ) === true )
		{	// 取得エラー
			$error_detail = "getSallyMembers( $party_id, $sally_no, array( "
						  . Pp_RaidPartyManager::SALLY_STATUS_MAP.","
						  . Pp_RaidPartyManager::SALLY_STATUS_BATTLE.","
						  . Pp_RaidPartyManager::SALLY_STATUS_RECOVER." ))";
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
		if( empty( $sally_members ) === true )
		{	// 誰もいない（既にMakeResultをやった？）
			// 他に開催中のダンジョンがあるかをチェック
			$this->af->setApp( 'breakup', $breakup, true );
			$this->af->setApp( 'result_user_ids', null, true );
			$this->af->setApp( 'result', 1, true );
			return 'inapi_json';
		}

		// リザルト到達ユーザーIDリストを作成
		$result_user_ids = array();
		foreach( $sally_members as $v )
		{
			$result_user_ids[] = $v['user_id'];
		}

		// リザルト到達ユーザーの所属ユニットリストを作成（報酬配布用）
		$result_user_unit = array();
		$unit = $unit_m->cacheGetUnitFromUserIdList( $result_user_ids );
		foreach( $unit as $unit_no => $user_ids )
		{
			foreach( $user_ids as $user_id )
			{
				$result_user_unit[$user_id] = $unit_no;
			}
		}

		//-------------------------------------------------------------
		//	クエストの結果を集計
		//-------------------------------------------------------------
		arsort( $damage, SORT_NUMERIC );		// ダメージの大きい順にソート
		$total_damage = array_sum( $damage );	// 与ダメージの総計
		if( $total_damage === 0 )
		{	// Zero Divide（ZOOMのゲームじゃないぉ）対策
			error_log( 'WARNING!!: Almost Zero Divide!!（￣皿￣#）' );
			$total_damage = 1;
		}
		$avg = array();
		$mvp_user_id = null;
		foreach( $damage as $user_id => $dmg )
		{	// 各ユーザーの与ダメージが総ダメージの？％を求める
			$avg["$user_id"] = floor( $dmg * 100 / $total_damage );	// 小数点以下切り捨て

			// MVPチェック
			if(( is_null( $mvp_user_id ) === true )&&( in_array( $user_id, $result_user_ids ) === true ))
			{	// リザルト到達ユーザーで与ダメージの一番大きい人がMVP
				// ※MVPはチームに１人しか存在しません。従って与ダメージが同一の場合は
				//   最初にMVPと判定された人'のみ'がMVPとなります。（高木さんに仕様として確認済み）
				$mvp_user_id = $user_id;  // ^^^^←ここ重要！テストに出るよ！
			}
		}

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	各ユーザー別の処理
			//-------------------------------------------------------------
			$activity_user_ids = array();	// 活躍ユーザーIDのリスト

			//ログ用の共通データ
			$log_reward_cmn = array(
				'party_id'     => $party_id, 
				'sally_no'     => $sally_no, 
				'dungeon_id'   => $dungeon_id, 
				'difficulty'   => $dungeon_rank, 
				'dungeon_lv'   => $dungeon_lv, 
				'dungeon_name' => $dungeon['name'], 
			);
			$api_transaction_id_tbl = array();

			foreach( $damage as $user_id => $value )
			{
				// リザルト到達ユーザーか？（false:途中離脱ユーザー）
				$is_result_user = in_array( $user_id, $result_user_ids );

				//-------------------------------------------------------------
				//	更新情報を作成
				//-------------------------------------------------------------
				$api_transaction_id = $logdata_m->createApiTransactionId( $user_id );
				$api_transaction_id_tbl[$user_id] = $api_transaction_id;//ログ記録に再利用するためユーザID単位で保持しておく

				if(( $quest_result != Pp_RaidQuestManager::QUEST_RESULT_CLEAR )||( $is_result_user === false ))
				{	// クエストをクリアできなかった or 途中離脱ユーザー
					$reward_flag = 0;			// 報酬なし
					$mvp_flag = 0;				// MVPフラグ
					$get_base_ranking_pt = 0;	// 獲得ベースランキングポイント
					$get_dmg_ranking_pt = 0;	// 獲得ダメージランキングポイント
				}
				else
				{	// リザルト到達ユーザー
					$reward_flag = 1;			// 報酬あり

					// 活躍した？
					if( $avg["$user_id"] >= 5 )
					{	// 与ダメージがクエスト総ダメージの５％以上なら活躍とする
						$activity_user_ids[] = $user_id;	// 活躍リストに追加
					}

					// 獲得ダメージランキングポイント（全体の総ダメージに対する自分の与ダメージの割合分だけもらえる）
					$get_dmg_ranking_pt = floor(( $dungeon_detail['ranking_dmg_pt'] * $avg["$user_id"] ) / 100 );

					// 獲得ベースランキングポイント
					$get_base_ranking_pt = ( int )$dungeon_detail['ranking_base_pt'];

					// MVP関連
					if( $user_id == $mvp_user_id )
					{	// MVPの人
						$mvp_flag = 1;				// MVPフラグ
					}
					else
					{	// MVPでない人
						$mvp_flag = 0;				// MVPフラグ
					}
				}
				$get_ranking_pt = $get_base_ranking_pt + $get_dmg_ranking_pt;	// 獲得ランキングポイント合計

				//-------------------------------------------------------------
				//	データベースを更新
				//-------------------------------------------------------------
				// 出撃メンバーテーブル（t_raid_sally_member）の更新
				$columns = array(
					'mvp'          => $mvp_flag,		// MVPフラグ
					'reward_flag'  => $reward_flag,		// 報酬獲得フラグ
					'total_damage' => $value,			// ユーザーがクエストで与えた総ダメージ
					'ranking_pt'   => $get_ranking_pt	// 今回獲得したランキングポイント（ベースとダメージ）の合計
				);
				$ret = $raid_party_m->updateSallyMember(
					$party_id, $sally_no, $user_id, $columns
				);
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// 更新エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "updateSallyMember( $party_id, $sally_no, $user_id, "
								  . "array( 'mvp' => $mvp_flag,'reward_flag' => $reward_flag, "
								  . "'total_damage' => $value, 'ranking_pt' => $get_ranking_pt ))";
					throw new Exception( $error_detail, $detail_code );
				}

				// 累計情報テーブル（t_raid_user_total）の更新
				$columns = array(
					'win' => 1,
					'ranking_base_pt' => $get_base_ranking_pt,
					'ranking_dmg_pt' => $get_dmg_ranking_pt
				);
				$ret = $raid_user_m->sumUserTotal( $user_id, $columns );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// 更新エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "sumUserTotal( $user_id, array( "
								  . "'win' => 1, 'ranking_base_pt' => $get_base_ranking_pt, "
								  . "'ranking_dmg_pt' => $get_ranking_dmg_pt ))";
					throw new Exception( $error_detail, $detail_code );
				}

				// レイドランキングポイントログ（log_raid_ranking_point）に追加
				// ベースランキングポイント
				$log_data = array(
					'api_transaction_id' => $api_transaction_id,
					'party_id' => $party_id,
					'sally_no' => $sally_no,
					'user_id' => $user_id,
					'type' => Pp_LogDataManager::LOG_RAID_RANKING_POINT_TYPE_BASE,
					'point' => $get_base_ranking_pt,
					'date_log' => date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] )
				);
				$ret = $logdata_m->insertLogRaidRankingPoint( $log_data );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "insertLogRaidRankingPoint( array( "
								  . "'api_transaction_id' => '$api_transaction_id', "
								  . "'party_id' => $party_id, "
								  . "'sally_no' => $sally_no, "
								  . "'user_id' => $user_id, "
								  . "'type' => ".Pp_LogDataManager::LOG_RAID_RANKING_POINT_TYPE_BASE.", "
								  . "'point' => $get_base_ranking_pt, "
								  . "'date_log' => ".date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] )." ))";
					throw new Exception( $error_detail, $detail_code );
				}

				// ダメージランキングポイント
				$log_data['type'] = Pp_LogDataManager::LOG_RAID_RANKING_POINT_TYPE_DAMAGE;
				$log_data['point'] = $get_dmg_ranking_pt;
				$logdata_m->insertLogRaidRankingPoint( $log_data );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "insertLogRaidRankingPoint( array( "
								  . "'api_transaction_id' => '$api_transaction_id', "
								  . "'party_id' => $party_id, "
								  . "'sally_no' => $sally_no, "
								  . "'user_id' => $user_id, "
								  . "'type' => ".Pp_LogDataManager::LOG_RAID_RANKING_POINT_TYPE_DAMAGE.", "
								  . "'point' => $get_dmg_ranking_pt, "
								  . "'date_log' => ".date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] )." ))";
					throw new Exception( $error_detail, $detail_code );
				}

				if( $is_result_user === false )
				{	// 離脱ユーザーはこれ以後は処理しない
					continue;
				}

				// パーティメンバーのステータスを変更
				$ret = $raid_party_m->updatePartyMember( $party_id, $user_id, array( 'status' => Pp_RaidPartyManager::MEMBER_STATUS_READY ));
				if (!$ret || Ethna::isError($ret)) {
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "updatePartyMember( $party_id, $user_id, array( 'status' => ".Pp_RaidPartyManager::MEMBER_STATUS_READY."))";
					throw new Exception( $error_detail, $detail_code );
				}

				if( $quest_result != Pp_RaidQuestManager::QUEST_RESULT_CLEAR )
				{	// クエストクリアでなければこれ以後は処理しない
					continue;
				}

				// レイドユーザーダンジョンクリア履歴ログ（log_raid_user_dungeon）に追加
				if( $quest_data['early_master_user_id'] == $user_id )
				{	// 出撃時からのパーティマスター
					$status = Pp_RaidUserManager::STATUS_MASTER;
				}
				else if( $party_info['master_user_id'] == $user_id )
				{	// 途中からパーティマスター
					$status = Pp_RaidUserManager::STATUS_PROXY_MASTER;
				}
				else
				{	// 通常メンバー
					$status = Pp_RaidUserManager::STATUS_MEMBER;
				}
				$ret = $raid_user_m->logUserDungeon(
					$user_id, $dungeon_id, $dungeon_rank, $dungeon_lv, $status, $mvp_flag
				);
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "logUserDungeon( $user_id, $dungeon_id, $dungeon_rank, "
								  . " $dungeon_lv, $status, $mvp_flag )";
					throw new Exception( $error_detail, $detail_code );
				}

				// MVP報酬をプレゼントとして配布
				if( $mvp_flag === 1 )
				{	// MVPの人には『豪華な粗品』をプレゼント！
					$distribute_result = $this->_distributeReward(
						$present_m, $mvp_user_id, $result_user_unit[$mvp_user_id],
						Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_MVP, $reward_info['mvp'],
							$api_transaction_id_tbl, $user_m, $monster_m, $logdata_m, $log_reward_cmn, $badge_m
					);
					if( $distribute_result !== true )
					{	// 配布エラー
						$detail_code = SDC_INAPI_REWARD_ERROR;
						throw new Exception( '[MVP_REWARD]: '.$distribute_result, $detail_code );
					}
				}

				// クリア報酬をプレゼントとして配布
				if( $status == Pp_RaidUserManager::STATUS_MASTER )
				{	// 出撃時からのパーティマスター
					// 過去のダンジョンクリア情報を取得
					$clear_info = $raid_user_m->getUserDungeonClear( $user_id, $dungeon_id, $dungeon_rank );
					if( Ethna::isError( $clear_info ))
					{	// エラー
						$detail_code = SDC_INAPI_DB_ERROR;
						$error_detail = "getUserDungeonClear( $user_id, $dungeon_id, $dungeon_rank )";
						throw new Exception( $error_detail, $detail_code );
					}

					if(( empty( $clear_info ) === true )||( $clear_info['dungeon_lv'] < $dungeon_lv ))
					{	// ダンジョンのクリア情報がないorクリア情報よりも今回のクリアの方がダンジョンLVが高い
						$reward = $reward_info['first'];	// 初回クリア報酬
						$category = Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_1ST;
					}
					else
					{	// 過去に一度クリアしたことがある
						$reward = $reward_info['second'];	// ２回目以降クリア報酬
						$category = Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_2ND;
					}
				}
				else
				{	// 途中からのパーティマスターor通常のパーティメンバー
					$reward = $reward_info['clear'];		// メンバークリア報酬
					$category = Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_MEMBER;
				}
				$distribute_result = $this->_distributeReward(
					$present_m, $user_id, $result_user_unit[$user_id], $category, $reward,
						$api_transaction_id_tbl, $user_m, $monster_m, $logdata_m, $log_reward_cmn, $badge_m
				);
				if( $distribute_result !== true )
				{	// 配布エラー
					$detail_code = SDC_INAPI_REWARD_ERROR;
					throw new Exception( $distribute_result, $detail_code );
				}

				// レイドユーザー別ダンジョンクリア情報ログ（log_raid_user_dungeon_clear）に追加
				// ※この処理はクリア報酬の配布を行った後に実行すること（初回クリア報酬の判定ができなくなる）
				if( $status == Pp_RaidUserManager::STATUS_MASTER )
				{	// 出撃時からのパーティマスターのみ
					$ret = $raid_user_m->logUserDungeonClear(
						$user_id, $dungeon_id, $dungeon_rank, $dungeon_lv
					);
					if(( !$ret )||( Ethna::isError( $ret )))
					{	// エラー
						$detail_code = SDC_INAPI_DB_ERROR;
						$error_detail = "logUserDungeonClear( $user_id, $dungeon_id, $dungeon_rank, $dungeon_lv )";
						throw new Exception( $error_detail, $detail_code );
					}
					//同ダンジョン・同難易度での次のダンジョンLV
					if ($dungeon_detail['last_lv'] != 1) $next_dungeon_lv++;	// 最後のレベルじゃなければ次のレベル
				}
			}

			//-------------------------------------------------------------
			//	次のダンジョンの情報を取得
			//-------------------------------------------------------------
			if( $dungeon_lv == $next_dungeon_lv )
			{	// 次も同じダンジョンならコピー
				$next_dungeon_detail = $dungeon_detail;
			}
			else
			{	// 違うダンジョンなら取得する
				$next_dungeon_detail = $raid_quest_m->getMasterDungeonDetail(
					$dungeon_id, $dungeon_rank, $next_dungeon_lv
				);
				if(( !$next_dungeon_detail )||( Ethna::isError( $next_dungeon_detail )))
				{	// 取得エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "getMasterDungeonDetail( $dungeon_id, $dungeon_rank, $next_dungeon_lv )";
					throw new Exception( $error_detail, $detail_code );
				}
				$reward_info = $raid_quest_m->getMasterDungeonClearRewardGrouping( $dungeon_id, $dungeon_rank, $next_dungeon_lv );
			}

			// ボス情報を取得
			$boss = $raid_quest_m->getMasterBossEnemy( $dungeon_id, $dungeon_rank, $next_dungeon_lv );
			if(( !$boss )||( Ethna::isError( $boss )))
			{	// ボス敵ID取得エラー
				$detail_code = SDC_INAPI_DB_ERROR;
				$error_detail = "getMasterBossEnemy( $dungeon_id, $dungeon_rank, $next_dungeon_lv )";
				throw new Exception( $error_detail, $detail_code );
			}
			$boss_monster = $monster_m->getMasterMonster( $boss['monster_id'] );
			if(( !$boss_monster )||( Ethna::isError( $boss_monster )))
			{	// ボス情報取得エラー
				$detail_code = SDC_INAPI_DB_ERROR;
				$error_detail = "getMasterMonster( ".$boss['monster_id']." )";
				throw new Exception( $error_detail, $detail_code );
			}

			$next_dungeon_info = array(
				'dungeon_id' => $dungeon_id,
				'dungeon_lv' => (int)$next_dungeon_lv,
				'dungeon_rank' => (int)$dungeon_rank,
				'dungeon_name' => $dungeon['name'],
				'bg_id' => (int)$next_dungeon_detail['bg_id'],
				'limit_time' => (int)$next_dungeon_detail['limit_time'],
				'use_type' => (int)$next_dungeon_detail['use_type'],
				'use_point' => (int)$next_dungeon_detail['use_point'],
				'exp' => (int)$next_dungeon_detail['exp'],
				'attack_seven' => (int)$next_dungeon_detail['attack_seven'],
				'attack_bar' => (int)$next_dungeon_detail['attack_bar'],
				'ranking_base_pt' => (int)$next_dungeon_detail['ranking_base_pt'],
				'ranking_dmg_pt' => (int)$next_dungeon_detail['ranking_dmg_pt'],
				'clear_reward'  => $reward_info['clear'],
				'mvp_reward'    => $reward_info['mvp'],
				'first_reward'  => $reward_info['first'],
				'second_reward' => $reward_info['second'],
				'raid_end_date' => $dungeon['date_end'],
				'guerrilla_end_date' => $guerrilla_end_date,
				'boss_id' => $boss['monster_id'],
				'boss_name' => $boss_monster['name_ja'],
				'clear_flag' => (( $next_dungeon_lv == $dungeon_lv ) ? 1 : 0 )	// 次のダンジョンが今回と違う場合は未クリアと判断
			);

			//-------------------------------------------------------------
			//	出撃メンバーのステータスを一括変更
			//-------------------------------------------------------------
			// リザルト到達メンバーの出撃結果を更新
			$status = ( $quest_result == Pp_RaidQuestManager::QUEST_RESULT_CLEAR ) ?
				Pp_RaidPartyManager::SALLY_STATUS_WIN : Pp_RaidPartyManager::SALLY_STATUS_LOSE;

			$columns = array( 'status' => $status );
			$ret = $raid_party_m->updateSallyMembers( $party_id, $sally_no, $result_user_ids, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// 更新エラー
				$detail_code = SDC_INAPI_DB_ERROR;
				$error_detail = "updateSallyMembers( $party_id, $sally_no, array( "
							  . implode( ",", $result_user_ids )." ), array( "
							  . "'status' => $status ))";
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	パーティの情報を更新
			//-------------------------------------------------------------
			// 退室していないメンバーを取得
			$status = array(	// 退室した人以外のステータス全て
				Pp_RaidPartyManager::MEMBER_STATUS_READY,		// 準備中の人
				Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY,	// 出撃準備完了の人
				Pp_RaidPartyManager::MEMBER_STATUS_RECOVER,		// 回復中の人
				Pp_RaidPartyManager::MEMBER_STATUS_BATTLE,		// 戦闘中の人
				Pp_RaidPartyManager::MEMBER_STATUS_MAP			// 探索中の人
			);
			$party_members = $raid_party_m->getPartyMembers( $party_id, $status );
			if( !$party_members )
			{
				$detail_code = SDC_INAPI_DB_ERROR;
				$error_detail = "getPartyMembers( $party_id, array( "
							  . Pp_RaidPartyManager::MEMBER_STATUS_READY.", "
							  . Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY.", "
							  . Pp_RaidPartyManager::MEMBER_STATUS_RECOVER.", "
							  . Pp_RaidPartyManager::MEMBER_STATUS_BATTLE.", "
							  . Pp_RaidPartyManager::MEMBER_STATUS_MAP." ))";
				throw new Exception( $error_detail, $detail_code );
			}

			if( $quest_result == Pp_RaidQuestManager::QUEST_RESULT_CLEAR )
			{	// ダンジョンクリア
				// 準備中に戻して、次のダンジョンLVに変更
				$columns = array(
					'dungeon_lv' => $next_dungeon_lv,
					'status' => Pp_RaidPartyManager::PARTY_STATUS_READY
				);
				$ret = $raid_party_m->updateParty( $party_id, $columns );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// 更新エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "updateParty( $party_id, array( "
								  . "'status' => ".Pp_RaidPartyManager::PARTY_STATUS_READY.","
								  . "'dungeon_lv' => $next_dungeon_lv ))";
					throw new Exception( $error_detail, $detail_code );
				}
				$party_info['status'] = $columns['status'];
			}
			else
			{	// 時間切れ
				// 他に開催中のダンジョンがあるかをチェック
				$dungeons = $raid_quest_m->getMasterDungeonMixed();
				$status = ( empty( $dungeons ) === true ) ? Pp_RaidPartyManager::MEMBER_STATUS_BREAK : Pp_RaidPartyManager::MEMBER_STATUS_READY;
				$columns = array( 'status' => $status );	// 他に開催中のダンジョンがあれば準備中に、なければ退室状態にする
				foreach( $party_members as $m )
				{
					$ret = $raid_party_m->updatePartyMember( $party_id, $m['user_id'], $columns );
					if( !$ret )
					{
						$detail_code = SDC_INAPI_DB_ERROR;
						$error_detail = "updatePartyMember( $party_id, ".$m['user_id'].", array( "
									  . "'status' => $status ))";
						throw new Exception( $error_detail, $detail_code );
					}
				}

				if( empty( $dungeons ) === true )
				{
					$columns = array(
						'status' => Pp_RaidPartyManager::PARTY_STATUS_BREAKUP,
						'member_num' => 0
					);
				}
				else
				{	// パーティのステータスを準備中に戻す
					$columns = array(
						'status' => Pp_RaidPartyManager::PARTY_STATUS_READY
					);
				}
				$ret = $raid_party_m->updateParty( $party_id, $columns );
				if( !$ret )
				{	// 更新エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "updateParty( $party_id, array( ... ))";
					throw new Exception( $error_detail, $detail_code );
				}
				$party_info['status'] = $columns['status'];
			}

			//-------------------------------------------------------------
			//	パーティ検索用テーブルを更新
			//-------------------------------------------------------------
			$ret = $raid_search_m->renewTmpDataAfterPartyUpdate( $party_info );
			if( $ret === false )
			{	// エラー
				$buff = array();
				foreach( $party_info as $k => $v )
				{
					$buff[] = "'".$k."' => '".$v."'";
				}
				$error_detail = "renewTmpDataAfterPartyUpdate( array(".implode(',',$buff)."))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			foreach( $party_members as $m )
			{
				$ret = $raid_search_m->renewTmpDataAfterPartyMemberUpdate( $m );
				if( $ret === false )
				{	// エラー
					$error_detail = 'renewTmpDataAfterPartyMemberUpdate( array( '
								  . 'party_id => '.$m['party_id'].','
								  . 'user_id => '.$m['user_id'].','
								  . 'status => '.$m['status'].','
								  . 'disconn => '.$m['disconn'].' ))';
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
			}

			//-------------------------------------------------------------
			//	ドロップ報酬をプレゼントで配布
			//-------------------------------------------------------------
			// 獲得報酬一時テーブルの情報を取得する（ドロップ報酬はリザルト到達ユーザーのうち活躍した人だけもらえる）
			if( $quest_result == Pp_RaidQuestManager::QUEST_RESULT_CLEAR )
			{	// もらえるのクリア時だけッス！
				$tmp_rewards = $raid_quest_m->getTmpRaidReward(
					$party_id, $sally_no, $activity_user_ids,
					array( Pp_RaidQuestManager::TMP_REWARD_STATUS_ENTRY )	// 未配布のものだけ取得
				);
				if( Ethna::isError( $sally_members ))
				{	// 取得エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "getTmpRaidReward( $party_id, $sally_no, "
								  . "array( ".implode( ',', $result_user_ids )." ), "
								  . "array( ".Pp_RaidQuestManager::TMP_REWARD_STATUS_ENTRY." ))";
					throw new Exception( $error_detail, $detail_code );
				}

				if( empty( $tmp_rewards ) === false )
				{	// ドロップ報酬あり
					$distribute_result = $this->_distributeTmpReward(
						$present_m, $result_user_unit, $tmp_rewards,
							$api_transaction_id_tbl, $user_m, $monster_m, $logdata_m, $log_reward_cmn, $badge_m
					);
					if( is_array( $distribute_result ) === false )
					{	// 配布エラー
						$detail_code = SDC_INAPI_REWARD_ERROR;
						throw new Exception( $distribute_result, $detail_code );
					}

					// 獲得報酬一時テーブルのステータスを配布済みに変更
					$ret = $raid_quest_m->updateTmpRaidRewardByIds(
						$distribute_result,
						array( 'status' => Pp_RaidQuestManager::TMP_REWARD_STATUS_DISTRIBUTED )
					);
					if(( Ethna::isError( $ret ))||( count( $distribute_result ) != $ret ))
					{	// 更新エラー
						$detail_code = SDC_INAPI_DB_ERROR;
						$error_detail = "updateTmpRaidRewardByIds( "
									  . "array( ".implode( ",", $distribute_result )." ), "
									  . "array( 'status' => ".Pp_RaidQuestManager::TMP_REWARD_STATUS_DISTRIBUTED." ))";
						throw new Exception( $error_detail, $detail_code );
					}
				}
			}

			//-------------------------------------------------------------
			//	クエスト結果ログ（log_raid_quest_result）に追加
			//-------------------------------------------------------------
			$columns = array(
				'party_id' => $party_id,
				'sally_no' => $sally_no,
				'dungeon_id' => $dungeon_id,
				'difficulty' => $dungeon_rank,
				'dungeon_lv' => $dungeon_lv,
				'map_no' => 0,
				'result' => $quest_result
			);
			$ret = $raid_quest_m->logQuestResult( $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// パーティIDが取得できなければ作成エラー
				$detail_code = SDC_INAPI_DB_ERROR;
				$error_detail = "logQuestResult( array( "
							  . "'party_id' => $party_id, "
							  . "'sally_no' => $sally_no, "
							  . "'dungeon_id' => $dungeon_id, "
							  . "'difficulty' => $dungeon_rank, "
							  . "'dungeon_lv' => $dungeon_lv, "
							  . "'map_no' => 0, "
							  . "'result' => $quest_result ))";
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	nodejsに返すパラメータをセット
			//-------------------------------------------------------------
			$this->af->setApp( 'dungeonInfo', $next_dungeon_info, true );
			$this->af->setApp( 'result_user_ids', $result_user_ids, true );
			$this->af->setApp( 'breakup', $breakup, true );
			$this->af->setApp( 'result', 1, true );
			//追加
			$this->af->setApp( 'next_dungeon_lv', $next_dungeon_lv, true );

			$db->commit();		// 正常に処理が完了したらコミットする
		}
		catch( Exception $e )
		{	// あくしでんと！例外発生！
			$db->rollback();		// エラーなのでロールバックする
			$detail_code = $e->getCode();
			if( empty( $detail_code ) === true )
			{	// コードが設定されていない時は適当な値で返す
				$detail_code = SDC_RAID_ERROR;
			}
			$this->af->setApp( 'status_detail_code', $detail_code, true );
			$this->af->setApp( 'error_detail', $e->getMessage(), true );
			$this->af->setApp( 'result', 0, true );
		}
		return 'inapi_json';
	}

	/**
	 * 報酬をプレゼントとして配布する
	 * 
	 * @param array $present_m プレゼントマネージャのインスタンス
	 * @param int $user_id 配布先ユーザーID
	 * @param int $unit 配布先ユーザーの所属ユニットID
	 * @param int $category 報酬カテゴリ
	 * @param array $rewards 一時テーブルの報酬情報の配列
	 * @param array $api_transaction_id_tbl ユーザID毎APIトランザクションIDの配列
	 * @param array $user_m ユーザマネージャのインスタンス
	 * @param array $monster_m モンスターマネージャのインスタンス
	 * @param array $logdata_m ログデータマネージャのインスタンス
	 * @param array $log_reward_cmn ログ用情報の配列
	 *
	 * @return string|bool (string:エラーメッセージ, true:正常終了）
	 */
	private function _distributeReward( $present_m, $user_id, $unit, $category, $rewards, $api_transaction_id_tbl, $user_m, $monster_m, $logdata_m, $log_reward_cmn, $badge_m )
	{
		// 報酬カテゴリからプレゼントコメントIDを取得するテーブル
		$comment_id_tbl = array(
			Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_MVP    => Pp_PresentManager::COMMENT_RAID_MVP,		// MVP
			Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_1ST    => Pp_PresentManager::COMMENT_RAID_MASTER,	// マスター初回
			Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_2ND    => Pp_PresentManager::COMMENT_RAID_MASTER,	// マスター２回目以降
			Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_MEMBER => Pp_PresentManager::COMMENT_RAID_MEMBER		// メンバー
		);

		foreach( $rewards as $r )
		{
			$lv = 0;
			$badge_expand = 0;
			$badges = '';
			// 報酬のタイプ
			if( $r['reward_type'] == 1 )
			{	// モンスター
				$item_id = $r['reward_id'];	// そのまんま報酬IDがモンスターIDになる
				$type = Pp_PresentManager::TYPE_MONSTER;
				$lv = $r['lv'];
				$badge_expand = ( int )$r['badge_expand'];
				if (isset($r['badges']) === true) $badges = $r['badges'];
				$monster_data = $monster_m->getMasterMonster($item_id);
				$reward_name = $monster_data['name_ja'];
				$present_type_name = 'モンスター';
			}
			else if( $r['reward_type'] == 3 )
			{	// 宝箱
				switch( $r['reward_id'] )
				{
					//reward_idに識別番号とアイテムIDのどちらが来ても良いように（あまり良いやり方ではないが）
					case    1:		// ブロンズチケット
					case Pp_ItemManager::ITEM_TICKET_GACHA_FREE:
						$item_id = Pp_ItemManager::ITEM_TICKET_GACHA_FREE;
						$type = Pp_PresentManager::TYPE_ITEM;
						$reward_name = 'ブロンズチケット';
						$present_type_name = '通常アイテム';
						break;
					case    2:		// ゴールドチケット
					case Pp_ItemManager::ITEM_TICKET_GACHA_RARE:
						$item_id = Pp_ItemManager::ITEM_TICKET_GACHA_RARE;
						$type = Pp_PresentManager::TYPE_ITEM;
						$reward_name = 'ゴールドチケット';
						$present_type_name = '通常アイテム';
						break;
					case    3:		// 合成メダル
					case Pp_ItemManager::ITEM_MEDAL_SYNTHESIS:
						$item_id = 0;
						$type = Pp_PresentManager::TYPE_MEDAL;
						$reward_name = '合成メダル';
						$present_type_name = '合成メダル（コイン）';
						break;
					case    4:		// マジカルメダル
					case Pp_ItemManager::ITEM_MEDAL_MAGICAL:
						$item_id = 0;
						$type = Pp_PresentManager::TYPE_MAGICAL_MEDAL;
						$reward_name = 'マジカルメダル';
						$present_type_name = 'マジカルメダル';
						break;
					case    5:		// バッジ拡張
					case Pp_ItemManager::ITEM_BADGE_EXPAND:
						$item_id = Pp_ItemManager::ITEM_BADGE_EXPAND;
						$type = Pp_PresentManager::TYPE_ITEM;
						$reward_name = 'バッジ拡張';
						$present_type_name = '通常アイテム';
						break;
					default:	// 知らないID
						$error_detail = 'reward_id['.$r['reward_id'].']: unknown reward id.';
						return $error_detail;
				}
			}
			else if( $r['reward_type'] == 4 )
			{	// バッジ（スフィア）
				$item_id = $r['reward_id'];		// バッジID
				$type = Pp_PresentManager::TYPE_BADGE;
				$badge = $badge_m->getMasterBadge($item_id);
				$reward_name = $badge['name_ja'];//マスタから取得
				$present_type_name = 'バッジ';
			}
			else if( $r['reward_type'] == 5 )
			{	// 素材
				$item_id = $r['reward_id'];		// 素材ID
				$type = Pp_PresentManager::TYPE_MATERIAL;
				$material = $badge_m->getMasterBadgeMaterial($item_id);
				$reward_name = $material['name_ja'];//マスタから取得
				$present_type_name = 'バッジ素材';
			}
			else
			{	// 知らないタイプ
				$error_detail = 'reward_type['.$r['reward_type'].']: unknown reward type.';
				return $error_detail;
			}

			$columns = array(
				'user_id_to'   => $user_id,
				'comment_id'   => $comment_id_tbl[$category],
				'comment'      => '',
				'item_id'      => $item_id,
				'type'         => $type,
				'lv'           => $lv,
				'badge_expand' => $badge_expand,
				'badges'       => $badges,
				'number'       => $r['reward_num']
			);

			// プレゼントとして配布
			$ret = $present_m->setUserPresent(
				Pp_PresentManager::USERID_FROM_ADMIN, Pp_PresentManager::ID_NEW_PRESENT, $columns, $unit
			);
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// 配布エラー
				$temp = array();
				foreach( $columns as $k => $v )
				{
					$temp[] = "'$k' => $v";
				}
				$error_detail = "setUserPresent( ".Pp_PresentManager::USERID_FROM_ADMIN.", "
							  . Pp_PresentManager::ID_NEW_PRESENT.", "
							  . "array( ".implode( ",", $temp )." ), "
							  . $unit." )";
				return $error_detail;
			}

			//ログ用の共通データに個人データを入れる
			$user_base = $user_m->getUserBase($user_id);
			$log_reward_cmn['user_id']   = $user_id;
			$log_reward_cmn['user_name'] = $user_base['name'];
			$log_reward_cmn['rank']      = $user_base['rank'];
			//プレゼント固有の情報
			$log_reward_cmn['reward_category'] = $category;
			$log_reward_cmn['reward_type']     = $type;
			$log_reward_cmn['reward_id']       = $item_id;
			$log_reward_cmn['reward_name']     = $reward_name;
			$log_reward_cmn['reward_num']      = $r['reward_num'];
			$log_reward_cmn['reward_lv']       = $lv;
			$log_reward_cmn['reward_badge_expand'] = $badge_expand;
			$log_reward_cmn['reward_badges']       = $badges;
			//配布した内容をログに残す
			$logdata_m->insertLogRaidRewardData($log_reward_cmn);
			
			//プレゼント配布ログも
			$log_present_data = array(
				'api_transaction_id'   => $api_transaction_id_tbl[($log_reward_cmn['user_id'])],
				'user_id'              => $log_reward_cmn['user_id'],
				'ua'                   => $user_base['ua'],
				'user_name'            => $user_base['name'],
				'rank'                 => $user_base['rank'],
				'present_id'           => $ret,
				'present_type'         => $log_reward_cmn['reward_type'],
				'present_type_name'    => $present_type_name,
				'item_id'              => $log_reward_cmn['reward_id'],
				'item_name'            => $log_reward_cmn['reward_name'],
				'lv'                   => $log_reward_cmn['reward_lv'],
				'badge_expand'         => $log_reward_cmn['reward_badge_expand'],
				'badges'               => $log_reward_cmn['reward_badges'],
				'number'               => $log_reward_cmn['reward_num'],
				'status'               => Pp_LogDataManager::PRESENT_INCREASE_STATUS_NEW,
				'status_name'          => '新規',//Pp_PresentManager::_present_statusがprivateだから直値
				'old_status'           => '',
				'old_status_name'      => '',
				'processing_type'      => 'R80',
				'processing_name'      => 'ドンスタークリア報酬',
				'processing_type_name' => 'ドンスタークリア報酬',
			);
			$logdata_m->insertLogPresentData(array($log_present_data));
		}
		return true;
	}

	/**
	 * 獲得報酬一時テーブルの情報をプレゼントとして配布する
	 * 
	 * @param array $present_m プレゼントマネージャのインスタンス
	 * @param array $result_user_unit ユーザーの所属ユニット情報
	 * @param array $tmp_rewards 一時テーブルの報酬情報の配列
	 * @param array $api_transaction_id_tbl ユーザID毎APIトランザクションIDの配列
	 * @param array $user_m ユーザマネージャのインスタンス
	 * @param array $monster_m モンスターマネージャのインスタンス
	 * @param array $logdata_m ログデータマネージャのインスタンス
	 * @param array $log_reward_cmn ログ用情報の配列
	 * @param array $badge_m バッジマネージャのインスタンス
	 *
	 * @return string|array (string:エラーメッセージ, array:配布完了した報酬の管理ID）
	 */
	private function _distributeTmpReward( $present_m, $result_user_unit, $tmp_rewards, $api_transaction_id_tbl, $user_m, $monster_m, $logdata_m, $log_reward_cmn, $badge_m )
	{
		$distributed_tmp_reward_ids = array();	// 配布が完了した獲得報酬一時テーブルのID
		foreach( $tmp_rewards as $r )
		{
			$lv = 0;
			$badge_expand = 0;
			$badges = '';
			// 報酬のタイプ
			if( $r['reward_type'] == 1 )
			{	// モンスター
				$item_id = $r['reward_id'];	// そのまんま報酬IDがモンスターIDになる
				$type = Pp_PresentManager::TYPE_MONSTER;
				$lv = $r['lv'];
				$badge_expand = $r['badge_expand'];
				if (isset($r['badges']) === true) $badges = $r['badges'];
				$monster_data = $monster_m->getMasterMonster($item_id);
				$reward_name = $monster_data['name_ja'];
				$present_type_name = 'モンスター';
			}
			else if( $r['reward_type'] == 3 )
			{	// 宝箱
				switch( $r['reward_id'] )
				{
					//reward_idに識別番号とアイテムIDのどちらが来ても良いように（あまり良いやり方ではないが）
					case    1:		// ブロンズチケット
					case Pp_ItemManager::ITEM_TICKET_GACHA_FREE:
						$item_id = Pp_ItemManager::ITEM_TICKET_GACHA_FREE;
						$type = Pp_PresentManager::TYPE_ITEM;
						$reward_name = 'ブロンズチケット';
						$present_type_name = '通常アイテム';
						break;
					case    2:		// ゴールドチケット
					case Pp_ItemManager::ITEM_TICKET_GACHA_RARE:
						$item_id = Pp_ItemManager::ITEM_TICKET_GACHA_RARE;
						$type = Pp_PresentManager::TYPE_ITEM;
						$reward_name = 'ゴールドチケット';
						$present_type_name = '通常アイテム';
						break;
					case    3:		// 合成メダル
					case Pp_ItemManager::ITEM_MEDAL_SYNTHESIS:
						$item_id = 0;
						$type = Pp_PresentManager::TYPE_MEDAL;
						$reward_name = '合成メダル';
						$present_type_name = '合成メダル（コイン）';
						break;
					case    4:		// マジカルメダル
					case Pp_ItemManager::ITEM_MEDAL_MAGICAL:
						$item_id = 0;
						$type = Pp_PresentManager::TYPE_MAGICAL_MEDAL;
						$reward_name = 'マジカルメダル';
						$present_type_name = 'マジカルメダル';
						break;
					case    5:		// バッジ拡張
					case Pp_ItemManager::ITEM_BADGE_EXPAND:
						$item_id = Pp_ItemManager::ITEM_BADGE_EXPAND;
						$type = Pp_PresentManager::TYPE_ITEM;
						$reward_name = 'バッジ拡張';
						$present_type_name = '通常アイテム';
						break;
					default:	// 知らないID
						$error_detail = 'reward_id['.$r['reward_id'].']: unknown reward id.';
						return $error_detail;
				}
			}
			else if( $r['reward_type'] == 4 )
			{	// バッジ（スフィア）
				$item_id = $r['reward_id'];		// バッジID
				$type = Pp_PresentManager::TYPE_BADGE;
				$badge = $badge_m->getMasterBadge($item_id);
				$reward_name = $badge['name_ja'];//マスタから取得
				$present_type_name = 'バッジ';
			}
			else if( $r['reward_type'] == 5 )
			{	// 素材
				$item_id = $r['reward_id'];		// 素材ID
				$type = Pp_PresentManager::TYPE_MATERIAL;
				$material = $badge_m->getMasterBadgeMaterial($item_id);
				$reward_name = $material['name_ja'];//マスタから取得
				$present_type_name = 'バッジ素材';
			}
			else
			{	// 知らないタイプ
				$error_detail = 'reward_type['.$r['reward_type'].']: unknown reward type.';
				return $error_detail;
			}
			$columns = array(
				'user_id_to'   => $r['user_id'],
				'comment_id'   => Pp_PresentManager::COMMENT_RAID_DROP,
				'comment'      => '',
				'item_id'      => $item_id,
				'type'         => $type,
				'lv'           => $lv,
				'badge_expand' => $badge_expand,
				'badges'       => $badges,
				'number'       => $r['reward_num']
			);

			// プレゼントとして配布
			$ret = $present_m->setUserPresent(
				Pp_PresentManager::USERID_FROM_ADMIN, Pp_PresentManager::ID_NEW_PRESENT,
				$columns, $result_user_unit[$r['user_id']]
			);
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// 配布エラー
				$temp = array();
				foreach( $columns as $k => $v )
				{
					$temp[] = "'$k' => $v";
				}
				$error_detail = "setUserPresent( ".Pp_PresentManager::USERID_FROM_ADMIN.", "
							  . Pp_PresentManager::ID_NEW_PRESENT.", "
							  . "array( ".implode( ",", $temp )." ), "
							  . $result_user_unit[$r['user_id']]." )";
				return $error_detail;
			}

			// 配布完了した報酬の管理IDを追加
			$distributed_tmp_reward_ids[] = $r['id'];

			//ログ用の共通データに個人データを入れる
			$user_base = $user_m->getUserBase($r['user_id']);
			$log_reward_cmn['user_id']   = $r['user_id'];
			$log_reward_cmn['user_name'] = $user_base['name'];
			$log_reward_cmn['rank']      = $user_base['rank'];
			//プレゼント固有の情報
			$log_reward_cmn['reward_category'] = Pp_RaidQuestManager::CLEAR_REWARD_CATEGORY_DROP;//ドロップ報酬(ログ用に定義)
			$log_reward_cmn['reward_type']     = $type;
			$log_reward_cmn['reward_id']       = $item_id;
			$log_reward_cmn['reward_name']     = $reward_name;
			$log_reward_cmn['reward_num']      = $r['reward_num'];
			$log_reward_cmn['reward_lv']       = $lv;
			$log_reward_cmn['reward_badge_expand'] = $badge_expand;
			$log_reward_cmn['reward_badges']       = $badges;
			//配布した内容をログに残す
			$logdata_m->insertLogRaidRewardData($log_reward_cmn);
			
			//プレゼント配布ログも
			$log_present_data = array(
				'api_transaction_id'   => $api_transaction_id_tbl[($log_reward_cmn['user_id'])],
				'user_id'              => $log_reward_cmn['user_id'],
				'ua'                   => $user_base['ua'],
				'user_name'            => $user_base['name'],
				'rank'                 => $user_base['rank'],
				'present_id'           => $ret,
				'present_type'         => $log_reward_cmn['reward_type'],
				'present_type_name'    => $present_type_name,
				'item_id'              => $log_reward_cmn['reward_id'],
				'item_name'            => $log_reward_cmn['reward_name'],
				'lv'                   => $log_reward_cmn['reward_lv'],
				'badge_expand'         => $log_reward_cmn['reward_badge_expand'],
				'badges'               => $log_reward_cmn['reward_badges'],
				'number'               => $log_reward_cmn['reward_num'],
				'status'               => Pp_LogDataManager::PRESENT_INCREASE_STATUS_NEW,
				'status_name'          => '新規',//Pp_PresentManager::_present_statusがprivateだから直値
				'old_status'           => '',
				'old_status_name'      => '',
				'processing_type'      => 'R81',
				'processing_name'      => 'ドンスタードロップ報酬',
				'processing_type_name' => 'ドンスタードロップ報酬',
			);
			$logdata_m->insertLogPresentData(array($log_present_data));
		}

		return $distributed_tmp_reward_ids;
	}
}
?>
