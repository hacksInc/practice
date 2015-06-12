<?php
/**
 *  Inpi/Raid/Party/Entry.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_entry Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyEntry extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',			// パーティID
		'user_name',		// ※ユーザー名
		'user_id',			// ユーザーID
		'user_rank',		// ※ユーザーランク
		'leader_mons_id',	// ※リーダーモンスターID
		'leader_mons_lv',	// ※リーダーモンスターLV
		'entry_type',		// 入室トリガ
		'pass'				// 入室パスワード
    );
}

/**
 *  inapi_raid_party_entry action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyEntry extends Pp_InapiActionClass
{
	/**
	 *  preprocess of inapi_raid_party_entry Action.
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
	 *  api_raid_party_entry action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$party_id = $this->af->get('party_id');
		$user_id = $this->af->get('user_id');
		$entry_type = $this->af->get('entry_type');
		$pass = $this->af->get('pass');

		// マネージャのインスタンスを取得
		$raid_party_m =& $this->backend->getManager('RaidParty');
		$raid_quest_m =& $this->backend->getManager('RaidQuest');
		$raid_search_m =& $this->backend->getManager('RaidSearch');
		$raid_user_m =& $this->backend->getManager('RaidUser');
		$raid_log_m =& $this->backend->getManager('RaidLog');
		$user_m =& $this->backend->getManager('User');
		$quest_m =& $this->backend->getManager('Quest');
		$monster_m =& $this->backend->getManager('Monster');
		$logdata_m =& $this->backend->getManager('Logdata');
		$item_m =& $this->backend->getManager('Item');
		$team_m =& $this->backend->getManager('Team');

		//-------------------------------------------------------------
		//	助っ人を適当に選ぶ
		//-------------------------------------------------------------
		// まずフレンドから取得
		$helper_list = $quest_m->getHelperFriendList( $user_id );
		$friend_flag = 1;
		if( Ethna::isError( $helper_list ))
		{	// 取得エラー
			$error_detail = "getHelperFriendList( ".$user_id." )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
		else if( empty( $helper_list ) === true )
		{	// お友達がいない(´･ω･｀)のでフレンド以外から取得
			$helper_list = $quest_m->getHelperList( $user_id );
			if(( !$helper_list )||( Ethna::isError( $helper_list )))
			{
				$error_detail = "getHelperOthersList( ".$user_id." )";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}
			$friend_flag = 0;	// フレンドじゃないのでフラグリセット
		}

		// 助っ人情報を設定
		$helper = array( 'friend_flag' => $friend_flag );		// フレンドフラグ
		$n = count( $helper_list );								// 助っ人の人数を取得
		$temp = $helper_list[mt_rand( 0, ( $n - 1 ))];			// 取得したお見合いリストから１人選ぶ
		$helper['user_id'] = $temp['user_id'];					// 助っ人ユーザーID
		$base = $user_m->getUserBase( $helper['user_id'] );		// ユーザー基本情報を取得
		$helper['name'] = $base['name'];						// ユーザー名
		$helper['login_date'] = $base['login_date'];			// ログイン日時
		$helper['rank'] = ( int )$base['rank'];					// ユーザーランク

		// 助っ人のアクティブチームのリーダーモンスターを取得
		$l = $monster_m->getActiveLeaderList( array( $helper['user_id'] ));	// 助っ人のモンスターを取得
		$helper['user_monster_id'] = $l[0]['user_monster_id'];	// ユーザーモンスターID
		$helper['monster_id'] = ( int )$l[0]['monster_id'];		// マスターモンスターID
		$helper['exp'] = ( int )$l[0]['exp'];					// モンスターの経験値
		$helper['lv'] = ( int )$l[0]['lv'];						// モンスターLV
		$helper['hp_plus'] = ( int )$l[0]['hp_plus'];			// HP増加値
		$helper['heal_plus'] = ( int )$l[0]['heal_plus'];		// 回復値
		$helper['skill_lv'] = ( int )$l[0]['skill_lv'];			// スキルLV
		$helper['badge_num'] = ( int )$l[0]['badge_num'];		// バッジ数
		$helper['badges'] = $l[0]['badges'];					// 装着バッジ

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	本当に入れるパーティかをチェック
			//-------------------------------------------------------------
			// 現在のパーティ情報を取得
			$party = $raid_party_m->getParty( $party_id, true, true );
			if( is_null( $party ) === true )
			{	// 取得エラー
				$error_detail = "getParty( $party_id )";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}
			if( empty( $party ) === true )
			{	// パーティがない
				$error_detail = "getParty( $party_id )";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_RAID_PARTYID_INVALID, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}

			if(( empty( $party['entry_passwd'] ) == false )&&( $party['entry_passwd'] != $pass ))
			{	// 自動入室OFFで入室パスワードが一致していない
				$error_detail = "Password does not match.: party_passwd=".$party['entry_passwd'].", input_passwd=$pass";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_PASSWD_NOT_MATCH, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}

			if(( $party['member_num'] == 0 )||( $party['status'] == Pp_RaidPartyManager::PARTY_STATUS_BREAKUP ))
			{	// 既に解散の場合は入れない
				$error_detail = "Cannot entry party. member_num:".$party['member_num'].", member_limit:".$party['member_limit'].", status:".$party['status'];
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_PARTY_BRAKEUP, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}
			if( $party['member_num'] >= $party['member_limit'] )
			{	// メンバー数が上限値の場合は入れない
				$error_detail = "Cannot entry party. member_num:".$party['member_num'].", member_limit:".$party['member_limit'].", status:".$party['status'];
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_PARTY_MEMBER_MAX, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}

			//-------------------------------------------------------------
			//	回線切断フラグが立っていない情報があれば切断フラグを立てる
			//-------------------------------------------------------------
			$raid_party_m->setDisconnPartyMember( $user_id );	// ここは戻り値は気にしなくてよろしい

			//-------------------------------------------------------------
			//	パーティメンバーに追加
			//-------------------------------------------------------------
			$ret = $raid_party_m->addPartyMember( $party_id, $user_id );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// エラー
				$error_detail = "addPartyMember( $party_id, $user_id )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	DBから情報を取得
			//-------------------------------------------------------------
			// パーティメンバーの取得
			$status = array(	// 退室した人以外のステータス全て
				Pp_RaidPartyManager::MEMBER_STATUS_READY,		// 準備中の人
				Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY,	// 出撃準備完了の人
				Pp_RaidPartyManager::MEMBER_STATUS_RECOVER,		// 回復中の人
				Pp_RaidPartyManager::MEMBER_STATUS_BATTLE,		// 戦闘中の人
				Pp_RaidPartyManager::MEMBER_STATUS_MAP			// 探索中の人
			);
			$party_members = $raid_party_m->getPartyMembers( $party_id, $status, true, true );
			if(( empty( $party_members ) === true )||( Ethna::isError( $party_members )))
			{	// 取得エラー
				$error_detail = "getPartyMembers( $party_id, $status, true, true )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			// ダンジョン情報を取得
			$dungeon = $raid_quest_m->getMasterDungeonById( $party['dungeon_id'] );
			if(( empty( $dungeon ) === true )||( Ethna::isError( $dungeon )))
			{
				$error_detail = "getMasterDungeonById( ".$party['dungeon_id']." )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			// ダンジョン詳細情報を取得
			$dungeon_detail = $raid_quest_m->getMasterDungeonDetail(
				$party['dungeon_id'], $party['difficulty'], $party['dungeon_lv']
			);
			if(( empty( $dungeon_detail ) === true )||( Ethna::isError( $dungeon_detail )))
			{	// ダンジョン情報取得エラー
				$error_detail = "getMasterDungeonDetail( "
							  . $party['dungeon_id'].",".$party['difficulty'].",".$party['dungeon_lv'].")";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			// ダンジョンの終了日時を取得
			$gerrilla_endtime = $raid_quest_m->getDungeonGuerrillaScheduleTimeEndById( $party['dungeon_id'] );
			if( empty( $guerrilla_endtime ) === true )
			{	// ゲリラ時間外
				$guerrilla_schedules = $raid_quest_m->getMasterGuerrillaScheduleByIds( array( $recv['dungeon_id'] ));
				if( empty( $guerrilla_schedules ) === false )
				{	// ゲリラ設定があるならゲリラ時間外なので例外をポイ
					$error_detail = "outside guerrilla hours.: dungeon_id = ".$recv['dungeon_id'];
					$detail_code = SDC_INAPI_DUNGEON_OVERTIME;
					throw new Exception( $error_detail, $detail_code );
				}

				// ゲリラ設定そのものがない場合は定常開催
				$guerrilla_end_date = $dungeon['date_end'];		// ダンジョンの開催終了日時まで
			}
			else
			{	// ゲリラ日時の設定がない
				$guerrilla_end_date =  strftime( "%Y-%m-%d" ).' '.$guerrilla_endtime;
			}

			// ボス情報を取得
			$this->af->setApp( 'boss', $party['dungeon_id'], true );
			$boss = $raid_quest_m->getMasterBossEnemy(
				$party['dungeon_id'], $party['difficulty'], $party['dungeon_lv']
			);
			if(( empty( $boss ) === true )||( Ethna::isError( $boss )))
			{	// ボス敵ID取得エラー
				$error_detail = "getMasterBossEnemy( "
							  . $party['dungeon_id'].",".$party['difficulty'].",".$party['dungeon_lv'].")";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			$boss_monster = $monster_m->getMasterMonster( $boss['monster_id'] );
			if(( empty( $boss_monster ) === true )||( Ethna::isError( $boss_monster )))
			{	// ボス情報取得エラー
				$error_detail = "getMasterMonster( ".$boss['monster_id']." )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			// ダンジョンクリア報酬を取得
			$reward = $raid_quest_m->getMasterDungeonClearRewardGrouping(
				$party['dungeon_id'], $party['difficulty'], $party['dungeon_lv']
			);
			if(( empty( $reward ) === true )||( Ethna::isError( $reward )))
			{	// クリア報酬取得エラー
				$error_detail = "getMasterDungeonClearRewardGrouping( "
							  . $party['dungeon_id'].",".$party['difficulty'].",".$party['dungeon_lv'].")";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			// パーティマスターの指定ダンジョンのクリア情報を取得
			$clear_info = $raid_user_m->getUserDungeonClear(
				$party['master_user_id'], $party['dungeon_id'], $party['difficulty']
			);
			if( is_null( $clear_info ) === true )
			{	// クリア情報取得エラー
				$error_detail = "getUserDungeonClear( ".$party['master_user_id'].",".$party['dungeon_id'].",".$party['difficulty']." )";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			if(( empty( $clear_info ) === true )||( $clear_info['dungeon_lv'] < $party['dungeon_lv'] ))
			{	// パーティマスターはまだこのダンジョンをクリアしていない
				$clear_flag = 0;
			}
			else
			{	// 既にクリア済
				$clear_flag = 1;
			}

			// パーティが出撃中の場合はplay_idを取得する
			$play_id = '';
			if( $party['status'] == Pp_RaidPartyManager::PARTY_STATUS_QUEST )
			{	// 出撃中ナリ
				$quest = $raid_quest_m->getQuest( $party_id, $party['sally'] );
				if(( empty( $quest ) === true )||( Ethna::isError( $quest )))
				{
					$error_detail = "getQuest( ".$party_id.",".$party['sally']." )";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
				$play_id = $quest['play_id'];
			}

			//-------------------------------------------------------------
			//	nodejsサーバーに返す値を生成
			//-------------------------------------------------------------
			// ダンジョン情報
			$dungeon_info = array(
				'dungeon_id'    => $dungeon_detail['dungeon_id'],
				'dungeon_name'  => $dungeon['name'],
				'dungeon_rank'  => ( int )$dungeon_detail['difficulty'],
				'dungeon_lv'    => ( int )$dungeon_detail['dungeon_lv'],
				'boss_id'       => $boss['monster_id'],
				'boss_name'     => $boss_monster['name_ja'],
				'limit_time'    => ( int )$dungeon_detail['limit_time'],
				'clear_reward'  => $reward['clear'],
				'mvp_reward'    => $reward['mvp'],
				'first_reward'  => $reward['first'],
				'second_reward' => $reward['second'],
				'raid_end_date' => $dungeon['date_end'],
				'use_type'      => ( int )$dungeon_detail['use_type'],
				'use_point'     => ( int )$dungeon_detail['use_point'],
				'guerrilla_end_date' => $guerrilla_end_date,
				'clear_flag'    => $clear_flag
			);

			// ユーザー情報
			$user_info = array();
			$user_ids = array();
			$active_team_buff = array();
			$entry_user_status = null;
			$entry_user_type = null;
			foreach( $party_members as $m )
			{
				$user_ids[] = $m['user_id'];
				$active_team_buff[$m['user_id']] = array();
				$u = $user_m->getUserBase( $m['user_id'] );
				$l = $monster_m->getActiveLeaderList( array( $m['user_id'] ));
				$temp = array(
					'user_name'      => $u['name'],
					'user_id'        => $m['user_id'],
					'user_rank'      => ( int )$u['rank'],
					'leader_mons_id' => ( int )$l[0]['monster_id'],
					'leader_mons_lv' => ( int )$l[0]['lv'],
					'user_type'      => (( $m['user_id'] == $party['master_user_id'] ) ? 1 : 2 ),
					'user_status'    => ( int )$m['status']
				);
				if( $user_id == $m['user_id'] )
				{	// 今回参加したユーザー
					$entry_user_status = ( int )$m['status'];
					$entry_user_type = $temp['user_type'];
					$this->af->setApp( 'hash_key', $m['hash'], true );
					$this->af->setApp( 'entry_user_status', $entry_user_status, true );
					$this->af->setApp( 'entry_user_type', $entry_user_type, true );
				}
				$user_info[] = $temp;
			}

			// パーティメンバーのデッキ情報を取得
			$active_team_list = $team_m->getUsersActiveTeamList( $user_ids );
			$friend_pos_list = $team_m->getUsersActiveTeamFriendPos( $user_ids );
			foreach( $user_info as $index => $value )
			{
				$uid = $value['user_id'];
				$buff = array();
				foreach( $active_team_list[$uid] as $v )
				{
					$buff[] = array(
						'position'   => (( int )$v['position'] - 1 ),	// ポジションはDBの1~5ではなく0~4に直す
						'leader'     => ( int )$v['leader_flg'],
						'monster_id' => ( int )$v['monster_id'],
						'unique_id'  => $v['user_monster_id'],
						'exp'        => ( int )$v['exp'],
						'lv'         => ( int )$v['lv'],
						'skill_lv'   => ( int )$v['skill_lv'],
						'badge_num'  => ( int )$v['badge_num'],
						'badges'     => $v['badges']
					);
				}

				// フレンドのポジション
				$user_info[$index]['friend_pos'] = ( int )$friend_pos_list[$uid]['position'] - 1;	// ポジションはDBの1~5ではなく0~4に直す

				$user_info[$index]['deckInfoList'] = $buff;		// user_infoに追加

				if( $uid == $user_id )
				{	// エントリー対象のユーザーのデッキ
					$join_user_deck = $buff;
					$join_user_friend_pos = $user_info[$index]['friend_pos'];
				}
			}

			// パーティ情報
			$party_info = array(
				'party_id'      => $party_id,
				'party_num'     => count( $user_info ),
				'party_max_num' => ( int )$party['member_limit'],
				'play_style'    => ( int )$party['play_style'],
				'auto_join'     => (( empty( $party['entry_passwd'] ) === true ) ? 1 : 0 ),
				'force_reject'  => ( int )$party['force_elimination'],
				'message'       => $party['message_id'],
				'pass'          => (( empty( $party['entry_passwd'] ) === true ) ? '' : $party['entry_passwd'] ),
				'status'        => ( int )$party['status'],
				'play_id'       => $play_id,
				'dungeonInfo'   => $dungeon_info,
				'userInfoList'  => $user_info
			);

			//-------------------------------------------------------------
			//	パーティ検索用テーブルを更新
			//-------------------------------------------------------------
			$ret = $raid_search_m->renewTmpDataAfterPartyUpdate( $party );
			if( $ret === false )
			{	// エラー
				$buff = array();
				foreach( $party as $k => $v )
				{
					$buff[] = "'".$k."' => '".$v."'";
				}
				$error_detail = "renewTmpDataAfterPartyUpdate( array(".implode(',',$buff)."))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			$columns = array(
				'party_id' => $party_id,
				'user_id' => $user_id,
				'status' => $entry_user_status,
				'disconn' => 0
			);
			$ret = $raid_search_m->renewTmpDataAfterPartyMemberInsert( $columns );
			if( $ret === false )
			{	// エラー
				$error_detail = 'renewTmpDataAfterPartyMemberInsert( array( '
							  . 'party_id => '.$party_id.','
							  . 'user_id => '.$user_id.','
							  . 'status => '.$entry_user_status.','
							  . 'disconn => 0 ))';
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	アクションログに記録
			//-------------------------------------------------------------
			$api_transaction_id = $logdata_m->createApiTransactionId( $user_id );
			if( $entry_type === Pp_RaidPartyManager::ENTRY_TYPE_AUTO )
			{	// 自動入室
				$action = Pp_RaidLogManager::ACTION_LOGIN_AUTO;
			}
			else if( $entry_type === Pp_RaidPartyManager::ENTRY_TYPE_SEARCH )
			{	// 検索機能による入室
				$action = Pp_RaidLogManager::ACTION_LOGIN_SEARCH;
			}
			else
			{	// フレンド戦歴
				$action = Pp_RaidLogManager::ACTION_LOGIN_FRIEND;
			}
			$columns = array(
				'api_transaction_id' => $api_transaction_id,
				'party_id'           => $party_id,
				'user_id'            => $user_id,
				'action'             => $action
			);
			$ret = $raid_log_m->trackingPartyMemberAction( $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// エラー
				$error_detail = "trackingPartyMemberAction( array( "
							  . "'".$api_transaction_id."',".$party_id.",".$user_id.",".$action." ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	nodejsに返すパラメータをセット
			//-------------------------------------------------------------
			$this->af->setApp( 'result', 1, true );
			$this->af->setApp( 'partyInfo', $party_info, true );
			$this->af->setApp( 'helperInfo', $helper, true );
			$this->af->setApp( 'join_user_deck', $join_user_deck, true );
			$this->af->setApp( 'join_user_friend_pos', $join_user_friend_pos, true );

			$db->commit();		// 問題がなければコミットしてトランザクション終了
		}
		catch( Exception $e )
		{	// あくしでんと！例外発生！
			$db->rollback();	// エラーなのでロールバックする
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
}

?>