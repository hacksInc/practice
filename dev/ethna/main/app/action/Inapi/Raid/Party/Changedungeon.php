<?php
/**
 *  Inapi/Raid/Party/Changedungeon.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_changedungeon Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyChangedungeon extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',
		'dungeon_id',
		'dungeon_name',
		'dungeon_rank',
		'dungeon_lv',
    );
}

/**
 *  Inapi_raid_party_changedungeon action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyChangedungeon extends Pp_InapiActionClass
{

	/**
	 *  preprocess of api_raid_party_changedungeon Action.
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
	 *  api_raid_party_changedungeon action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$party_id = $this->af->get( 'party_id' );
		$dungeon_id = $this->af->get( 'dungeon_id' );
		$dungeon_rank = $this->af->get( 'dungeon_rank' );
		$dungeon_lv = $this->af->get( 'dungeon_lv' );
		$monster_m =& $this->backend->getManager( 'Monster' );
		$item_m =& $this->backend->getManager('Item');

		// マネージャのインスタンスを取得
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$raid_quest_m =& $this->backend->getManager( 'RaidQuest' );
		$raid_user_m =& $this->backend->getManager( 'RaidUser' );
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );

		//-------------------------------------------------------------
		//	パーティ情報を取得
		//-------------------------------------------------------------
		$party_info = $raid_party_m->getParty( $party_id );
		if( is_null( $party_info ) === true )
		{	// エラー
			$error_detail = "getParty( $party_id )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return;
		}
		if( empty( $party_info ) === true )
		{	// 指定のパーティ情報がない
			$error_detail = "getParty( $party_id )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_RAID_PARTYID_INVALID, true );
			$this->af->setApp( 'result', 0, true );
			return;
		}

		//-------------------------------------------------------------
		//	新しいダンジョン情報を取得
		//-------------------------------------------------------------
		// ダンジョン情報を取得
		$dungeon = $raid_quest_m->getMasterDungeonById( $dungeon_id );
		if(( !$dungeon )||( Ethna::isError( $dungeon )))
		{	// ダンジョン情報取得エラー
			$error_detail = "getMasterDungeonById( $dungeon_id )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return;
		}

		// ダンジョン詳細情報を取得
		$detail = $raid_quest_m->getMasterDungeonDetail(
			$dungeon_id, $dungeon_rank, $dungeon_lv
		);
		if(( !$detail )||( Ethna::isError( $detail )))
		{	// ダンジョン情報取得エラー
			$error_detail = "getMasterDungeonDetail( $dungeon_id, $dungeon_rank, $dungeon_lv )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return;
		}

		// ダンジョンクリア報酬を取得
		$reward = $raid_quest_m->getMasterDungeonClearRewardGrouping(
			$dungeon_id, $dungeon_rank, $dungeon_lv
		);
		if(( !$reward )||( Ethna::isError( $reward )))
		{	// クリア報酬取得エラー
			$error_detail = "getMasterDungeonClearRewardGrouping( $dungeon_id, $dungeon_rank, $dungeon_lv )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return;
		}

		// ダンジョンの開催終了日時を取得
		$guerrilla_endtime = $raid_quest_m->getDungeonGuerrillaScheduleTimeEndById( $dungeon_id );
		if( empty( $guerrilla_endtime ) === true )
		{	// ゲリラ日時の設定がない
			$guerrilla_schedules = $raid_quest_m->getMasterGuerrillaScheduleByIds( array( $dungeon_id ));
			if( empty( $guerrilla_schedules ) === false )
			{	// ゲリラ設定があるならゲリラの時間外なのでエラー
				$error_detail = "outside guerrilla hours.: dungeon_id = ".$dungeon_id;
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DUNGEON_OVERTIME, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}

			// ゲリラ設定そのものがない場合は定常開催
			$guerrilla_end_date = $dungeon['date_end'];
		}
		else
		{	// ゲリラ日時の設定がある
			$guerrilla_end_date = strftime( "%Y-%m-%d" ).' '.$guerrilla_endtime;
		}
		$end_timestamp = strtotime( $guerrilla_end_date );		// 新たに選択したダンジョンの終了時刻をタイムスタンプにする

		// ボス情報を取得
		$boss = $raid_quest_m->getMasterBossEnemy( $dungeon_id, $dungeon_rank, $dungeon_lv );
		if(( !$boss )||( Ethna::isError( $boss )))
		{	// ボス敵ID取得エラー
			$error_detail = "getMasterBossEnemy( $dungeon_id, $dungeon_rank, $dungeon_lv )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return;
		}
		$boss_monster = $monster_m->getMasterMonster( $boss['monster_id'] );
		if(( !$boss_monster )||( Ethna::isError( $boss_monster )))
		{	// ボス情報取得エラー
			$error_detail = "getMasterMonster( ".$boss['monster_id']." )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return;
		}

		// パーティマスターの指定ダンジョンのクリア情報を取得
		$clear_info = $raid_user_m->getUserDungeonClear(
			$party_info['master_user_id'], $dungeon_id, $dungeon_rank
		);
		if( is_null( $clear_info ) === true )
		{	// クリア情報取得エラー
			$error_detail = "getUserDungeonClear( ".$party_info['master_user_id'].", $dungeon_id, $dungeon_rank )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return;
		}
		if(( empty( $clear_info ) === true )||( $clear_info['dungeon_lv'] < $dungeon_lv ))
		{	// パーティマスターはまだこのダンジョンをクリアしていない
			$clear_flag = 0;
		}
		else
		{	// 既にクリア済
			$clear_flag = 1;
		}

		//-------------------------------------------------------------
		//	nodejsサーバーに返すダンジョン情報を生成
		//-------------------------------------------------------------
		// ダンジョン情報
		$dungeon_info = array(
			'dungeon_id'    => $dungeon_id,
			'dungeon_name'  => $dungeon['name'],
			'dungeon_rank'  => $dungeon_rank,
			'dungeon_lv'    => $dungeon_lv,
			'boss_id'       => $boss['monster_id'],
			'boss_name'     => $boss_monster['name_ja'],
			'limit_time'    => ( int )$detail['limit_time'],
			'clear_reward'  => $reward['clear'],
			'mvp_reward'    => $reward['mvp'],
			'first_reward'  => $reward['first'],
			'second_reward' => $reward['second'],
			'raid_end_date' => $dungeon['date_end'],
			'use_type'      => ( int )$detail['use_type'],
			'use_point'     => ( int )$detail['use_point'],
			'guerrilla_end_date' => $guerrilla_end_date,
			'clear_flag'    => $clear_flag
		);

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	パーティのダンジョン選択情報を更新
			//-------------------------------------------------------------
			$columns = array(
				'dungeon_id' => $dungeon_id,
				'difficulty' => $dungeon_rank,
				'dungeon_lv' => $dungeon_lv
			);
			$ret = $raid_party_m->updateParty( $party_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// 更新エラー
				$error_detail = "updateParty(".$party_id.", array( "
							  . "'dungeon_id' => ".$dungeon_id.","
							  . "'difficulty' => ".$dungeon_rank.","
							  . "'dungeon_lv' => ".$dungeon_lv." ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	パーティ検索用テーブルを更新
			//-------------------------------------------------------------
			$party = $raid_party_m->getParty( $party_id, false, true );
			if(( !$party )||( Ethna::isError( $party )))
			{
				$error_detail = "getParty( $party_id, false, true )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
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

			//-------------------------------------------------------------
			//	nodejsに返すパラメータをセット
			//-------------------------------------------------------------
			$this->af->setApp( 'dungeonInfo', $dungeon_info, true );
			$this->af->setApp( 'end_timestamp', $end_timestamp, true );
			$this->af->setApp( 'master_user_id', $party_info['master_user_id'], true );
			$this->af->setApp( 'result', 1, true );

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
}
?>
