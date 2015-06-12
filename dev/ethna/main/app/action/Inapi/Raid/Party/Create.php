<?php
/**
 *  Inapi/Raid/Party/Create.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_create Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyCreate extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'play_style',
		'auto_join',
		'force_reject',
		'message',
		'pass',
		'dungeon_id',
		'dungeon_rank',
		'dungeon_lv',
		'user_name',
		'user_id',
		'leader_mons_id',
		'leader_mons_lv'
    );
}

/**
 *  Inapi_raid_party_create action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyCreate extends Pp_InapiActionClass
{
	/**
	 *  preprocess of api_raid_party_create Action.
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
	 *  api_raid_party_create action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$recv = array(
			'play_style' => $this->af->get('play_style'),
			'auto_join' => $this->af->get('auto_join'),
			'force_reject' => $this->af->get('force_reject'),
			'message' => $this->af->get('message'),
			'pass' => $this->af->get('pass'),
			'dungeon_id' => $this->af->get('dungeon_id'),
			'dungeon_rank' => $this->af->get('dungeon_rank'),
			'dungeon_lv' => $this->af->get('dungeon_lv'),
			'user_name' => $this->af->get('user_name'),
			'user_id' => $this->af->get('user_id'),
			'reader_mons_id' => $this->af->get('reader_mons_id'),
			'reader_mons_lv' => $this->af->get('reader_mons_lv')
		);

		// マネージャのインスタンスを取得
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$raid_log_m =& $this->backend->getManager( 'RaidLog' );
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );
		$raid_quest_m =& $this->backend->getManager( 'RaidQuest' );
		$quest_m =& $this->backend->getManager( "Quest" );
		$user_m =& $this->backend->getManager( "User" );
		$monster_m =& $this->backend->getManager( "Monster" );

		//-------------------------------------------------------------
		//	選択中のダンジョンの開催日時をチェック
		//-------------------------------------------------------------
		// ダンジョンマスター情報を取得（時間チェック付）
		$dungeon_master = $raid_quest_m->getMasterDungeonById( $recv['dungeon_id'], true );
		if( empty( $dungeon_master ) === true )
		{	// 開催していないor取得エラー
			$error_detail = "getMasterDungeonById( ".$recv['dungeon_id']." )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		// ダンジョンの開催終了時間を取得
		$guerrilla_endtime = $raid_quest_m->getDungeonGuerrillaScheduleTimeEndById( $recv['dungeon_id'] );
		if( empty( $guerrilla_endtime ) === true )
		{	// ゲリラ時間外
			$guerrilla_schedules = $raid_quest_m->getMasterGuerrillaScheduleByIds( array( $recv['dungeon_id'] ));
			if( empty( $guerrilla_schedules ) === false )
			{	// ゲリラ設定があるならゲリラの時間外なのでエラー
				$error_detail = "outside guerrilla hours.: dungeon_id = ".$recv['dungeon_id'];
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DUNGEON_OVERTIME, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}

			// ゲリラ設定そのものがない場合は定常開催
			$end_date = $dungeon_master['date_end'];	// ダンジョンの開催終了日時まで
		}
		else
		{	// ゲリラ時間内
			$end_date = strftime( "%Y-%m-%d" ).' '.$guerrilla_endtime;	// 今日の指定の時間まで
		}
		$this->af->setApp( 'end_timestamp', strtotime( $end_date ), true );	// タイムスタンプ値に変換

		//-------------------------------------------------------------
		//	助っ人を適当に選ぶ
		//-------------------------------------------------------------
		// まずフレンドから取得
		$helper_list = $quest_m->getHelperFriendList( $recv['user_id'] );
		$friend_flag = 1;		// フレンドフラグセット
		if( Ethna::isError( $helper_list ))
		{	// 取得エラー
			$error_detail = "getHelperFriendList( ".$recv['user_id']." )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
		else if( empty( $helper_list ) === true )
		{	// お友達がいない(´･ω･｀)のでフレンド以外から取得
			$helper_list = $quest_m->getHelperList( $recv['user_id'] );
			if(( !$helper_list )||( Ethna::isError( $helper_list )))
			{
				$error_detail = "getHelperOthersList( ".$recv['user_id']." )";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}
			$friend_flag = 0;	// フレンドではない知らない人なのでフラグリセット
		}

		$helper = array( 'friend_flag' => $friend_flag );		// フレンドフラグ
		$n = count( $helper_list );								// 助っ人の人数を取得
		$temp = $helper_list[mt_rand( 0, ( $n - 1 ))];			// 一人選ぶ
		$helper['user_id'] = $temp['user_id'];					// 助っ人ユーザーID
		$base = $user_m->getUserBase( $helper['user_id'] );		// ユーザー基本情報を取得
		$helper['name'] = $base['name'];						// ユーザー名
		$helper['login_date'] = $base['login_date'];			// ログイン日時
		$helper['rank'] = ( int )$base['rank'];					// ユーザーランク

		// 助っ人のアクティブチームのリーダーモンスターを取得
		$l = $monster_m->getActiveLeaderList( array( $helper['user_id'] ));
		$helper['user_monster_id'] = $l[0]['user_monster_id'];	// ユーザーモンスターID
		$helper['monster_id'] = ( int )$l[0]['monster_id'];		// マスターモンスターID
		$helper['exp'] = ( int )$l[0]['exp'];					// モンスター経験値
		$helper['lv'] = ( int )$l[0]['lv'];						// モンスターLV
		$helper['hp_plus'] = ( int )$l[0]['hp_plus'];			// HP補正値
		$helper['heal_plus'] = ( int )$l[0]['heal_plus'];		// 回復値
		$helper['skill_lv'] = ( int )$l[0]['skill_lv'];			// スキルレベル
		$helper['badge_num'] = ( int )$l[0]['badge_num'];		// バッジ数
		$helper['badges'] = $l[0]['badges'];					// 装着バッジ

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	回線切断フラグが立っていない情報があれば切断フラグを立てる
			//-------------------------------------------------------------
			$raid_party_m->setDisconnPartyMember( $recv['user_id'] );	// ここは戻り値は気にしなくてよろしい

			//-------------------------------------------------------------
			//	パーティの作成
			//-------------------------------------------------------------
			$party_id = $raid_party_m->createParty(
				$recv['user_id'], $recv['dungeon_id'], $recv['dungeon_rank'], $recv['dungeon_lv'],
				$recv['force_reject'], $recv['play_style'], $recv['pass'], $recv['message']
			);
			if(( !$party_id )||( Ethna::isError( $party_id )))
			{	// パーティIDが取得できなければ作成エラー
				$error_detail = 'createParty('.$recv['user_id'].','.$recv['dungeon_id'].','
							  . $recv['dungeon_rank'].','.$recv['dungeon_lv'].','
							  . $recv['force_reject'].','.$recv['play_style'].','
							  . $recv['pass'].','.$recv['message'].')';
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			// 正常にパーティIDが取得できた
			$party_info = $raid_party_m->getParty( $party_id, false, true );	// 作成直後なのでマスターDBから取得
			if( is_null( $party_info ) === true )
			{	// エラー
				$error_detail = "getParty( $party_id, false, true )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			if( empty( $party_info ) === true )
			{	// 取得エラー
				$error_detail = "getParty( $party_id, false, true )";
				$detail_code = SDC_RAID_PARTYID_INVALID;
				throw new Exception( $error_detail, $detail_code );
			}
			$user_info = $raid_party_m->getPartyMember( $party_id, $recv['user_id'], true );
			if( empty( $user_info ) === true )
			{	// 取得エラー
				$error_detail = 'getPartyMember('.$party_id.','.$recv['user_id'].',true)';
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	パーティ検索用テーブルにレコードを作成
			//-------------------------------------------------------------
			$ret = $raid_search_m->renewTmpDataAfterPartyInsert( $party_info );
			if( $ret === false )
			{	// エラー
				$buff = array();
				foreach( $party_info as $k => $v )
				{
					$buff[] = "'".$k."' => '".$v."'";
				}
				$error_detail = 'renewTmpDataAfterPartyInsert( array('.implode(','.$buff).'))';
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			$columns = array(
				'party_id' => $party_id,
				'user_id' => $recv['user_id'],
				'status' => $user_info['status'],
				'disconn' => 0
			);
			$ret = $raid_search_m->renewTmpDataAfterPartyMemberInsert( $columns );
			if( $ret === false )
			{	// エラー
				$error_detail = 'renewTmpDataAfterPartyMemberInsert( array( '
							  . 'party_id => '.$party_id.','
							  . 'user_id => '.$recv['user_id'].','
							  . 'status => '.$user_info['status'].','
							  . 'disconn => 0 ))';
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	アクションをログに記録
			//-------------------------------------------------------------
			$api_transaction_id = $logdata_m->createApiTransactionId( $recv['user_id'] );
			$columns = array(
				'api_transaction_id' => $api_transaction_id,
				'party_id' => $party_id,
				'user_id' => $recv['user_id'],
				'action' => Pp_RaidLogManager::ACTION_LOGIN_CREATE
			);
			$ret = $raid_log_m->trackingPartyMemberAction( $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// エラー
				$error_detail = "trackingPartyMemberAction( array("
							  . "'api_transaction_id' => '".$api_transaction_id."',"
							  . "'party_id' => '".$recv['party_id']."',"
							  . "'user_id' => '".$recv['user_id']."',"
							  . "'action' => '".Pp_RaidLogManager::ACTION_LOGIN_CREATE."'))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	nodejsに返すパラメータをセット
			//-------------------------------------------------------------
			$this->af->setApp( 'result', 1, true );
			$this->af->setApp( 'hash_key', $user_info['hash'], true );
			$this->af->setApp( 'party_id', $party_id, true );
			$this->af->setApp( 'party_max_num', $party_info['member_limit'], true );
			$this->af->setApp( 'helperInfo', $helper, true );
			$this->af->setApp( 'created_timestamp', strtotime( $party_info['date_created'] ), true );

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

	function VarDumpToLog( $param )
	{
		ob_start();
		var_dump( $param );
		$dmp = ob_get_contents();
		ob_end_clean();	
		error_log( $dmp );
	}

}
?>
