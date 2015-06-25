<?php
/**
 *  Inapi/Raid/Party/Reentry.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_reentry Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyReentry extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',
		'sally_no',
		'user_id',
		'hash_key',
		'end_timestamp'
    );
}

/**
 *  Inapi_raid_party_reentry action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyReentry extends Pp_InapiActionClass
{

	/**
	 *  preprocess of inapi_raid_party_reentry Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{	// バリデートエラー
			$this->af->setApp( 'error_detail', 'validate error.', true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_ARGUMENT_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
	    return null;
	}

	/**
	 *  inapi_raid_party_reentry action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$party_id = $this->af->get( 'party_id' );
		$sally_no = $this->af->get( 'sally_no' );
		$user_id = $this->af->get( 'user_id' );
		$hash_key = $this->af->get( 'hash_key' );
		$end_timestamp = $this->af->get( 'end_timestamp' );

		// マネージャのインスタンスを取得
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$raid_quest_m =& $this->backend->getManager( 'RaidQuest' );
		$raid_user_m =& $this->backend->getManager( 'RaidUser' );
		$monster_m =& $this->backend->getManager( 'Monster' );
		$user_m =& $this->backend->getManager( 'User' );
		$item_m =& $this->backend->getManager('Item');
		$team_m =& $this->backend->getManager('Team');

		try
		{
			// 送られてきたデータからユーザー情報を取得する
			if( empty( $hash_key ) === false )
			{	// HASH_KEYあり
				error_log( '# find party_member by hash_key.' );
				$user_info = $raid_party_m->getPartyMemberByHash( $hash_key );
				if( is_null( $user_info ) === true )
				{	// 取得エラー
					$error_detail = "getPartyMemberByHash( '$hash_key' )";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
				else if( empty( $user_info ) === false )
				{
					$party_id = $user_info['party_id'];
					$user_id = $user_info['user_id'];
				}
			}
			if( empty( $user_info ) === true )
			{	// HASH_KEYがないor取得できない
				error_log( '# find party_member by party_id & user_id.' );
				$user_info = $raid_party_m->getPartyMember( $party_id, $user_id );
				if( empty( $user_info ) === true )
				{	// 取得エラー
					$error_detail = "getPartyMember( $party_id, $user_id )";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
			}
			if( $user_info['status'] == Pp_RaidPartyManager::MEMBER_STATUS_BREAK )
			{	// 既に自主退室している部屋
				$error_detail = "can't reentry party. party_id[$party_id], status[".Pp_RaidPartyManager::MEMBER_STATUS_BREAK."]";
				$detail_code = SDC_INAPI_LEAVE_REENTRY;
				throw new Exception( $error_detail, $detail_code );
			}
			else if( $user_info['status'] == Pp_RaidPartyManager::MEMBER_STATUS_FORCE )
			{	// 強制退室させられた部屋
				$error_detail = "can't reentry party. party_id[$party_id], status[".Pp_RaidPartyManager::MEMBER_STATUS_FORCE."]";
				$detail_code = SDC_INAPI_FORCE_LEAVE_REENTRY;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	DBから情報を取得
			//-------------------------------------------------------------
			// 最新のパーティ情報を取得
			$party = $raid_party_m->getParty( $party_id );
			if( is_null( $party ) === true )
			{	// パーティ情報取得エラー
				$error_detail = "getParty( $party_id )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			if( empty( $party ) === true )
			{	// パーティ情報取得エラー
				$error_detail = "getParty( $party_id )";
				$detail_code = SDC_RAID_PARTYID_INVALID;
				throw new Exception( $error_detail, $detail_code );
			}

			// パーティメンバーの取得
			$status = array(	// 退室した人以外のステータス全て
				Pp_RaidPartyManager::MEMBER_STATUS_READY,		// 準備中の人
				Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY,	// 出撃準備完了の人
				Pp_RaidPartyManager::MEMBER_STATUS_RECOVER,		// 回復中の人
				Pp_RaidPartyManager::MEMBER_STATUS_BATTLE,		// 戦闘中の人
				Pp_RaidPartyManager::MEMBER_STATUS_MAP			// 探索中の人
			);
			$party_members = $raid_party_m->getPartyMembers( $party_id, $status );
			if(( empty( $party_members ) === true )||( Ethna::isError( $party_members )))
			{	// 取得エラー
				$error_detail = "getPartyMembers( $party_id, $status )";
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

			// ダンジョンの開催終了日時を取得
			$guerrilla_endtime = $raid_quest_m->getDungeonGuerrillaScheduleTimeEndById( $party['dungeon_id'] );
			if( empty( $guerrilla_endtime ) === true )
			{	// ゲリラ日時の設定がない
				$guerrilla_schedules = $raid_quest_m->getMasterGuerrillaScheduleByIds( array( $party['dungeon_id'] ));
				if( empty( $guerrilla_schedules ) === false )
				{	// ゲリラ設定があるならゲリラ時間外なので、パーティを準備中に戻す
					$guerrilla_end_date = null;
				}
				else
				{
					// ゲリラ設定そのものがない場合は定常開催
					$guerrilla_end_date = $dungeon['date_end'];
				}
				$end_date = $dungeon['date_end'];
			}
			else
			{	// ゲリラ日時の設定がある
				$guerrilla_end_date = strftime( "%Y-%m-%d" ).' '.$guerrilla_endtime;
				$end_date = $guerrilla_end_date;
			}

			if( time() > $end_timestamp )
			{	// 参加していたバトルは既に終了している
				$columns = array( 'status' => Pp_RaidPartyManager::MEMBER_STATUS_READY );
				foreach( $party_members as $idx => $m )
				{	// パーティメンバーを準備中に戻す
					$ret = $raid_party_m->updatePartyMember( $party_id, $m['user_id'], $columns );
					if( !$ret )
					{
						$detail_code = SDC_INAPI_DB_ERROR;
						$error_detail = "updatePartyMember( $party_id, ".$m['user_id'].", array( "
									  . "'status' => ".Pp_RaidPartyManager::MEMBER_STATUS_READY." ))";
						throw new Exception( $error_detail, $detail_code );
					}
					$party_members[$idx]['status'] = Pp_RaidPartyManager::MEMBER_STATUS_READY;
				}

				$columns = array( 'status' => Pp_RaidPartyManager::PARTY_STATUS_READY );
				$ret = $raid_party_m->updateParty( $party_id, $columns );
				if( !$ret )
				{	// 更新エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "updateParty( $party_id, array( ... ))";
					throw new Exception( $error_detail, $detail_code );
				}
				$party['status'] = Pp_RaidPartyManager::PARTY_STATUS_READY;

				if( $sally_no == $party['sally'] )
				{	// 再出撃していなければレイド終了日時を更新
					$end_timestamp = strtotime( $end_date );
				}

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
					$error_detail = "getQuest( $party_id,".$party['sally']." )";
					throw new Exception( $error_detail );
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
					$this->af->setApp( 'hash_key', $m['hash'], true );
					$this->af->setApp( 'entry_user_status', $entry_user_status, true );
				}
				$user_info[] = $temp;
			}

			$active_team_list = $team_m->getUsersActiveTeamList( $user_ids );
			$friend_pos_list = $team_m->getUsersActiveTeamFriendPos( $user_ids );
			foreach( $user_info as $index => $value )
			{
				$uid = $value['user_id'];
				$buff = array();
				foreach( $active_team_list[$uid] as $v )
				{
					$buff[] = array(
						'position'   => ( int )$v['position'] - 1,	// ポジションはDBの1~5ではなく0~4に直す
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
			//	nodejsに返すパラメータをセット
			//-------------------------------------------------------------
			$this->af->setApp( 'end_timestamp', $end_timestamp, true );
			$this->af->setApp( 'result', 1, true );
			$this->af->setApp( 'partyInfo', $party_info, true );

			//-------------------------------------------------------------
			//	パーティメンバー情報の切断フラグをリセット
			//-------------------------------------------------------------
			$columns = array( 'disconn' => 0 );
			$ret = $raid_party_m->updatePartyMember( $party_id, $user_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// 更新エラー
				$error_detail = "updatePartyMember( $party_id, $user_id, array( 'disconn' => 0 ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
		}
		catch( Exception $e )
		{	// あくしでんと！例外発生！
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
