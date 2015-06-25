<?php
/**
 *  Inapi/Raid/Battle/Start.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_RaidLogManager.php';

/**
 *  inapi_raid_battle_start Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidBattleStart extends Pp_InapiActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'party_id' => array(
			'name'        => 'パーティID',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		/*
		'sally_no' => array(
			'name'        => '出撃NO',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		*/
		'play_id' => array(
			'name'        => 'プレイID',
			'type'        => VAR_TYPE_STRING,
			'required'    => true,
		),
		'dungeon_id' => array(
			'name'        => 'ダンジョンID',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		'dungeon_rank' => array(
			'name'        => '難易度',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		'dungeon_lv' => array(
			'name'        => 'ダンジョンLV',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		'user_id' => array(
			'name'        => 'ユーザID',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		'helper_id' => array(
			'name'        => '助っ人ID',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		'helper_monster_id' => array(
			'name'        => '助っ人モンスターID',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
	);
}

/**
 *  inapi_raid_battle_start action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidBattleStart extends Pp_InapiActionClass
{
	/**
	 *  preprocess of inapi_raid_battle_start Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		if ($this->af->validate() > 0) {
			$this->af->setApp('error_detail', 'validate error.', true);
			$this->af->setApp('status_detail_code', SDC_INAPI_ARGUMENT_ERROR, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}

		return null;
	}

	/**
	 *  inapi_raid_battle_start action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$party_id       = $this->af->get('party_id');		//パーティ毎のID
		//$sally_no       = $this->af->get('sally_no');		//出撃NO
		$dungeon_id     = $this->af->get('dungeon_id');		//選択したダンジョンのID
		$dungeon_rank   = $this->af->get('dungeon_rank');	//選択したダンジョンのランク
		$dungeon_lv     = $this->af->get('dungeon_lv');		//選択したダンジョンのLV
		$user_id        = $this->af->get('user_id');		//パーティマスターユーザID
		$play_id        = $this->af->get('play_id');		//プレイID

		$raidquest_m =& $this->backend->getManager('RaidQuest');
		$raidparty_m =& $this->backend->getManager('RaidParty');
		$raiduser_m =& $this->backend->getManager('RaidUser');
		$raidsearch_m =& $this->backend->getManager('RaidSearch');
		$raidlog_m =& $this->backend->getManager('RaidLog');
		$quest_m =& $this->backend->getManager('Quest');
		$user_m  =& $this->backend->getManager('User');
		$monster_m  =& $this->backend->getManager('Monster');
		$slot_m =& $this->backend->getManager('Slot');
		$logdata_m = $this->backend->getManager('Logdata');
		$kpi_m = $this->backend->getManager('Kpi');

		$team_m  =& $this->backend->getManager('Team');
		$helper_id         = $this->af->get('helper_id');         // 助っ人のユーザID
		$helper_monster_id = $this->af->get('helper_monster_id'); // 助っ人のリーダーモンスターID
		$team_id           = $this->af->get('active_team_id');

		$unit_m =& $this->backend->getManager( 'Unit' );
		$unit = $unit_m->cacheGetUnitFromUserId( $user_id );
/*
		$friend_m  =& $this->backend->getManager('Friend');
		$monster_m =& $this->backend->getManager('Monster');
		//同じプレイIDが来たらリトライとみなして直前のスタート処理結果を返す
		//当然、体力も減らさない
		$start_content = $quest_m->getTmpUserQuestStart($user_id);
		//プレイIDのチェック(同じプレイIDでもクリア処理済の時は除外)
		if ($play_id == $start_content['play_id'] && $start_content['proc_end'] != 1) {
			foreach ($start_content as $key => $value) {
				$this->af->setApp($key, $value, true);
				//error_log("$key=".print_r($value,true));
			}
			error_log("Quest Start Retry...");
			return 'api_json_encrypt';
		}

		// チームコストを取得
		$cost = $team_m->getUserTeamCost($user_id, $team_id);
		if ($cost > $user_base['team_cost']) {
			$this->af->setApp('status_detail_code', SDC_QUEST_COST_OVER_ERROR, true);
			return 'error_500';
		}

		//所持モンスター一覧を取得
		$user_monster_list = $monster_m->getUserMonsterListForApiResponse($user_id);
		//モンスター枠との判定
		if ($user_base['monster_box_max'] < count($user_monster_list)) {
			$this->logger->log(LOG_INFO, 'user_monster over '.$user_id );
			$this->af->setApp('status_detail_code', SDC_QUEST_MONSTER_OVER, true);
			return 'error_500';
		}
		$helper_monster = $monster_m->getUserMonsterEx($helper_id, $helper_monster_id);
		
		// ログ情報
		$input_params = array(
			'api_transaction_id' => $logdata_m->createApiTransactionId($user_id),
			'user_id' => $user_id,
			'helper_id' => $helper_id,
			'helper_monster_id' => $helper_monster_id,
			'area_id' => $area_id,
			'quest_id' => $quest_id,
			'play_id' => $play_id,
			'team_id' => $team_id,
			'needful_stamina' => $area_result['needful_stamina'],
		);

		// チーム情報を取得
		$team_monster_list = $team_m->getUserTeam($user_id, $team_id);
		$logdata_m->trackingQuestStart($input_params, $user_base, $team_monster_list, $helper_monster, $area_result['quest_battle']);

		$this->af->setApp('quest_area', $master_area, true);
		$this->af->setApp('slot_level', $slot_level, true);

		// KPI
		$kpi_m->log($kpi_m->getPlatform($user_id)."-jgm-quest_UU",2,1,"",$user_id,"","","");
*/

		// パーティ情報を取得
		$party = $raidparty_m->getParty($party_id);
		if( is_null( $party ) === true )
		{	// DBエラー
			$error_detail = "getParty($party_id)";
			$this->af->setApp('error_detail', $error_detail);
			$this->af->setApp('status_detail_code', SDC_INAPI_DB_ERROR, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}
		if( empty( $party ) === true )
		{	// 無効なパーティID
			$error_detail = "getParty($party_id)";
			$this->af->setApp('error_detail', $error_detail);
			$this->af->setApp('status_detail_code', SDC_RAID_PARTYID_INVALID, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}

		// パーティのステータスをチェック
		if( $party['status'] != Pp_RaidPartyManager::PARTY_STATUS_QUEST )
		{	// クエストが始まってない！？終わってる！？解散しちゃった！？
			$error_detail = "party(id=$party_id) is not sally.";	// 出撃中じゃないナリよ
			$this->af->setApp('error_detail', $error_detail, true);
			$this->af->setApp('status_detail_code', SDC_INAPI_PARTY_NOT_SALLY, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}

		// 出撃NOを取得
		$sally_no = ( int )$party['sally'];

		//レイドクエストログデータを取得する
		$quest = $raidquest_m->getQuest( $party_id, $sally_no );
		$quest_arr = json_decode($quest['quest_data'],true);
		
		// パーティマスターがダンジョンをクリアしているかを取得
		$clear_info = $raiduser_m->getUserDungeonClear(
			$party['master_user_id'], $dungeon_id, $dungeon_rank
		);
		if( is_null( $clear_info ) === true )
		{	// クリア情報取得エラー
			$error_detail = "getUserDungeonClear( ".$party['master_user_id'].", $dungeon_id, $dungeon_rank )";
			$this->af->setApp('error_detail', $error_detail, true);
			$this->af->setApp('status_detail_code', SDC_INAPI_DB_ERROR, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}
		if(( empty( $clear_info ) === true )||( $clear_info['dungeon_lv'] < $party['dungeon_lv'] ))
		{	// パーティマスターはまだこのダンジョンをクリアしていない
			$clear_flag = 0;
		}
		else
		{	// 既にクリア済
			$clear_flag = 1;
		}

		$quest_arr['dungeon_info']['clear_flag'] = $clear_flag;
		$dungeon_info = $quest_arr['dungeon_info'];

		//以下はユーザ毎の処理（ユニットがユーザ固有のものに切り替わる？）
		
	/*
		//getUserBaseForApiResponseで呼んでいるからコメントアウトする
		//時間から計算してレイドポイント回復させる
		//このアクションのみ、最初にrecoverRaidpointを実行しておく必要がある。
		$ret = $user_m->recoverRaidpoint($user_id);
		if (!$ret || Ethna::isError($ret)) {
			$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}
	*/
		//レイドポイント回復
		$ret = $user_m->recoverRaidpointForRaidUnit($user_id, $unit);
		// ユーザ基本情報を取得
		$user_base = $user_m->getUserBase($user_id);//この関数の中でユニット判定を行っているっぽい
	//	$user_base = $user_m->getUserBaseForApiResponse($user_id, true, false);
		//エラー
		if ($user_base === false) {
			$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}
		//存在しない
		if (count($user_base) == 0) {
			$this->af->setApp('status_detail_code', SDC_USER_NONEXISTENCE, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}
		//レイドポイント関連処理
		if ($user_base['raid_point'] >= Pp_UserManager::RAID_POINT_MAX) {
			$rp_rest_sec = Pp_UserManager::RAID_POINT_CHARGE_SEC;
		} else {
			$rp_rest_sec = strtotime($user_base['date_raid_point_update']) + Pp_UserManager::RAID_POINT_CHARGE_SEC - $_SERVER['REQUEST_TIME'];
		}
		$user_base['rp_rest_sec'] = $rp_rest_sec;
		// レイドpt回復に必要な残り秒の基点日時
		$user_base['rp_base_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		// レイドpt回復に必要な秒
		$user_base['rp_charge_sec'] = Pp_UserManager::RAID_POINT_CHARGE_SEC;
		//team_idの値が来なかったらuser_baseの値を参照する
		if ($team_id == null) {
			$team_id = $user_base['active_team_id'];
		}

		//パーティメンバーの取得
		$status = array(	// 退室した人以外のステータス全て
			Pp_RaidPartyManager::MEMBER_STATUS_READY,		// 準備中の人
			Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY,	// 出撃準備完了の人
			Pp_RaidPartyManager::MEMBER_STATUS_RECOVER,		// 回復中の人
			Pp_RaidPartyManager::MEMBER_STATUS_BATTLE,		// 戦闘中の人
			Pp_RaidPartyManager::MEMBER_STATUS_MAP			// 探索中の人
		);
		$party_members = $raidparty_m->getPartyMembers($party_id, $status);
		$user_info = array();
		$user_ids = array();
		$active_team_buff = array();
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

		//パーティ情報を取得
		$party_info = array(
			'party_id'      => $party_id,
			'party_num'     => ( int )$party['member_num'],
			'party_max_num' => ( int )$party['member_limit'],
			'play_style'    => ( int )$party['play_style'],
			'auto_join'     => (( empty( $party['entry_passwd'] ) === true ) ? 1 : 0 ),
			'force_reject'  => ( int )$party['force_elimination'],
			'message'       => $party['message_id'],
			'play_id'       => $play_id,
			'pass'          => (( empty( $party['entry_passwd'] ) === true ) ? null : $party['entry_passwd'] ),
			'status'        => ( int )$party['status'],
			'userInfoList'  => $user_info
		);

		// クエストの終了日時を取得
		$dungeon = $raidquest_m->getMasterDungeonById( $dungeon_id );
		if( empty( $dungeon ) === true )
		{	// 取得エラー
			$error_detail = "getMasterDungeonById( $dungeon_id )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
		$dungeon_detail = $raidquest_m->getMasterDungeonDetail( $dungeon_id, $dungeon_rank, $dungeon_lv );
		if( empty( $dungeon_detail ) === true )
		{	// 取得エラー
			$error_detail = "getMasterDungeonDetail( $dungeon_id, $dungeon_rank, $dungeon_lv )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		$battle_end_timestamp = strtotime( $quest['date_created'] ) + ( int )$dungeon_detail['limit_time'];

		// ダンジョンの開催終了時間を取得
		$guerrilla_endtime = $raidquest_m->getDungeonGuerrillaScheduleTimeEndById( $dungeon_id );
		if( empty( $guerrilla_endtime ) === true )
		{	// ゲリラ時間外
			$guerrilla_schedules = $raidquest_m->getMasterGuerrillaScheduleByIds( array( $dungeon_id ));
			if( empty( $guerrilla_schedules ) === false )
			{	// ゲリラ設定があるならゲリラの時間外なのでエラー
				$error_detail = "outside guerrilla hours.: dungeon_id = $dungeon_id";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DUNGEON_OVERTIME, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}

			// ゲリラ設定そのものがない場合は定常開催
			$dungeon_end_date = $dungeon['date_end'];	// ダンジョンの開催終了日時まで
		}
		else
		{	// ゲリラ時間内
			$dungeon_end_date = strftime( "%Y-%m-%d" ).' '.$guerrilla_endtime;	// 今日の指定の時間まで
		}
		$dungeon_end_timestamp = strtotime( $dungeon_end_date );		// タイムスタンプに直す

		if( $dungeon_end_timestamp < $battle_end_timestamp )
		{	// ダンジョンのタイムリミットよりも出撃ダンジョンの開催終了が早い場合は開催終了日時に合わせる
			$battle_end_timestamp = $dungeon_end_timestamp;
		}

		//バトル基本情報
		$battle_base_info = array(
			'sally_no' => $sally_no,
			'battle_start_time' => $quest['date_created'],
			'battle_end_time' => strftime( "%Y-%m-%d %H:%M:%S", $battle_end_timestamp )
		);

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// DBを更新
		try
		{
			$is_reconnect = false;	// 再接続か？

			//ユーザーを出撃メンバーとして登録
			$m = $raidparty_m->getSallyMember( $party_id, $sally_no, $user_id );
			if( empty( $m ) === true )
			{	// メンバーとして登録されていなければ登録
				$ret = $raidparty_m->addSallyMember( $party_id, $sally_no, array( $user_id ));
				$action = Pp_RaidLogManager::ACTION_LOBBY_SALLY;	// 新規に出撃

				// 新規の出撃なのでユーザーの出撃回数を加算
				$columns = array( 'sally' => 1 );
				$ret = $raiduser_m->sumUserTotal( $user_id, $columns );
				if (!$ret || Ethna::isError($ret))
				{	// 更新エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "sumUserTotal( ".$user_id.", array( 'sally' => 1 ))";
					throw new Exception( $error_detail, $detail_code );
				}
			}
			else
			{	// 既に登録されている（再出撃、回線が落ちた場合など）
				if(( $m['status'] == Pp_RaidPartyManager::SALLY_STATUS_MAP )||
				   ( $m['status'] == Pp_RaidPartyManager::SALLY_STATUS_BATTLE ))
				{	// 探索中・戦闘中（接続が切れた？）
					$is_reconnect = true;		// 再接続だよ
				}
				else
				{	// ロビー（回復中）から再出撃
					$action = ACTION_LOBBY_SALLY_AGAIN;		// 再出撃
					$columns = array( 'status' => Pp_RaidPartyManager::SALLY_STATUS_BATTLE );
					$ret = $raidparty_m->updateSallyMember( $party_id, $sally_no, $user_id, $columns );
				}
			}
			if( $is_reconnect === false )
			{
				if( !$ret || Ethna::isError($ret))
				{	// エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = 'add/updateSarryMember( '.$party_id.','.$sally_no.','.$user_id.' )';
					throw new Exception( $error_detail, $detail_code );
				}
			}

			// パーティメンバーステータスを出撃中に変更
			$columns = array( 'status' => Pp_RaidPartyManager::MEMBER_STATUS_BATTLE );
			$ret = $raidparty_m->updatePartyMember( $party_id, $user_id, $columns );
			if (!$ret || Ethna::isError($ret)) {
				$detail_code = SDC_INAPI_DB_ERROR;
				$error_detail = 'updatePartyMember( '.$party_id.','.$user_id.", array( 'status' => ".Pp_RaidPartyManager::MEMBER_STATUS_QUEST.'))';
				throw new Exception( $error_detail, $detail_code );
			}

			// パーティ検索情報を更新
			$m = $raidparty_m->getPartyMember( $party_id, $user_id, true );
			if( empty( $m ) === true )
			{
				$error_detail = "getPartyMember( $party_id, $user_id )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			$ret = $raidsearch_m->renewTmpDataAfterPartyMemberUpdate( $m );
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

			// ポイント消費
			$user_raid_point = ( int )$user_base['raid_point'];
			if( $is_reconnect === false )
			{	// 探索中でもなく戦闘中でもないならレイドポイントを消費する
				if ($dungeon_info['use_type'] == 1) {
					if ($user_raid_point < $dungeon_info['use_point']) {
						//レイドポイントが足りない
						$detail_code = SDC_INAPI_POINT_SHORTAGE;
						$error_detail = 'shortage of raid_point.';
						throw new Exception( $error_detail, $detail_code );
					} else {
						//レイドポイント消費
						$user_raid_point -= $dungeon_info['use_point'];
						$user_base['raid_point'] = $user_raid_point;
						//user_base保存
					//	$ret = $user_m->setUserBase($user_id, array('raid_point' => $user_raid_point));
						$ret = $user_m->setUserBaseForRaidUnit($user_id, array('raid_point' => $user_raid_point), $unit);
					}
				}
			}

			// アクションログに行動を記録
			if( $is_reconnect === false )
			{	// 再接続でなければ記録
				$api_transaction_id = $logdata_m->createApiTransactionId( $user_id );
				$columns = array(
					'api_transaction_id' => $api_transaction_id,
					'party_id' => $party_id,
					'user_id' => $user_id,
					'action' => $action
				);
				$ret = $raidlog_m->trackingPartyMemberAction( $columns );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = 'trackingPartyMemberAction('.$party_id.','.$user_id.','.$action.')';
					throw new Exception( $error_detail, $detail_code );
				}
			}

			// トランザクション完了
			$db->commit();
		}
		catch( Exception $e )
		{	// 例外発生
			$db->rollback();	// エラーなのでロールバックする
			$detail_code = $e->getCode();
			if( empty( $detail_code ) === true )
			{	// コードが設定されていない時は適当な値で返す
				$detail_code = SDC_RAID_ERROR;
			}
			$this->af->setApp('status_detail_code', $detail_code, true);
			$this->af->setApp('error_detail', $e->getMessage(), true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}

		//KPIやログ関連の処理
		//$helper_monster_idがユーザモンスターIDじゃなくてモンスターIDが来てるのでログを取れないから取らない
		//$helper_monster = $monster_m->getUserMonsterEx($helper_id, $helper_monster_id);
		$helper_monster = null;
		// ログ情報
		$input_params = array(
			'api_transaction_id' => $logdata_m->createApiTransactionId($user_id),
			'user_id' => $user_id,
			'party_id' => $party_id,
			'sally_no' => $sally_no,
			'create_user_id' => $party['create_user_id'],
			'master_user_id' => $quest_arr['master_user_id'],
			'dungeon_id' => $dungeon_id,
			'difficulty' => $dungeon_rank,
			'dungeon_lv' => $dungeon_lv,
			'team_id' => $team_id,
			'play_id' => $play_id,
			'needful_rp' => $dungeon_info['use_point'],
			'helper_id' => $helper_id,
			'helper_monster_id' => $helper_monster_id,
		);

		// チーム情報を取得
	//	$team_monster_list = $team_m->getUserTeam($user_id, $team_id);
		$team_monster_list = null;
		$logdata_m->trackingRaidQuestStart($input_params, $user_base, $team_monster_list, $helper_monster, $quest_arr['raid_common_battle']);

//各自のユニットに保存するように変更する
//万一影響が出ないように下の方へ移動した
		//今回のクエストで発生するドロップアイテムのデータをDBに保持しておく
		//既に存在していれば削除する
		$quest_m->deleteTmpUserQuestStartForUnit($user_id, $unit);
		$data = array(
			'raid_data' => $quest_arr,
			'party_id'  => $party_id,
			'sally_no'  => $sally_no,
			'play_id'   => $play_id,
		);
		$ret = $quest_m->setTmpUserQuestStartForUnit($user_id, $data, $unit);
		if (!$ret || Ethna::isError($ret)) {
			error_log("start err set tmpuserqueststart ret=$ret");
			$this->af->setApp('status_detail_code', SDC_QUEST_START_ERROR, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}
//各自のユニットに保存するように変更する
		
		//ここから下は出力のみ
		
		//スタート用データ
		$this->af->setApp('raid_common_battle', $quest_arr['raid_common_battle'], true);
		$this->af->setApp('battleBaseInfo', $battle_base_info, true);
		$this->af->setApp('dungeon_info', $quest_arr['dungeon_info'], true);
		$this->af->setApp('partyInfo', $party_info, true);

/*
		//user_baseからはユーザレイドポイントのみ返す
		$this->af->setApp('user_raid_point', $user_raid_point, true);
		//2014/9/4追加
		$this->af->setApp('user_raid_point_max', (int)$user_base['raid_point_max'], true);
		$this->af->setApp('user_rp_rest_sec', (int)$user_base['rp_rest_sec'], true);
		$this->af->setApp('user_rp_charge_sec', (int)$user_base['rp_charge_sec'], true);
		$this->af->setApp('user_rp_base_date', $user_base['rp_base_date'], true);
*/
		$user_raid_point_set = $user_m->convertUserBaseToUserRaidPointSet($user_base);
		foreach ($user_raid_point_set as $user_raid_point_key => $user_raid_point_value) {
			$this->af->setApp($user_raid_point_key, $user_raid_point_value, true);
		}
		
		//スロットの設定を取得
		$slot_level = $slot_m->getSlotSetting( date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ));
		$this->af->setApp('slot_level', $slot_level, true);
		
		//効果は無いから初期値を入れておく
		$this->af->setApp('effect', 0, true);//効果タイプ
		$this->af->setApp('effectval', 100, true);//効果値

		//正常終了
		$this->af->setApp('result', 1, true);

		return 'inapi_json';
	}
}

?>
