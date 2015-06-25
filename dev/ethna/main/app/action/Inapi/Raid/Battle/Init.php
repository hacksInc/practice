<?php
/**
 *  Inapi/Raid/Battle/Init.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_RaidLogManager.php';

/**
 *  inapi_raid_battle_init Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidBattleInit extends Pp_InapiActionForm
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
		/*
		'dungeon_id' => array(
			'name'        => 'ダンジョンID',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		
		'master_user_id' => array(
			'name'        => 'マスタユーザID',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		*/
	);
}

/**
 *  inapi_raid_battle_init action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidBattleInit extends Pp_InapiActionClass
{
	/**
	 *  preprocess of inapi_raid_battle_init Action.
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
	 *  inapi_raid_battle_init action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$party_id       = $this->af->get('party_id');//パーティ毎のID
		//$sally_no       = $this->af->get('sally_no');//出撃NO
		//$dungeon_id     = $this->af->get('dungeon_id');//選択したダンジョンのID
		//$master_user_id = $this->af->get('master_user_id');//パーティ内マスタのユーザID
		$play_id        = $this->af->get('play_id');//プレイID

		$raidquest_m =& $this->backend->getManager('RaidQuest');
		$raidparty_m =& $this->backend->getManager('RaidParty');
		$raidsearch_m =& $this->backend->getManager('RaidSearch');
		$raidlog_m =& $this->backend->getManager('RaidLog');
		$raiduser_m =& $this->backend->getManager('RaidUser');
		$user_m  =& $this->backend->getManager('User');
		$monster_m  =& $this->backend->getManager('Monster');
		$logdata_m = $this->backend->getManager('Logdata');
		$kpi_m = $this->backend->getManager('Kpi');
		$item_m =& $this->backend->getManager('Item');

		$party = $raidparty_m->getParty( $party_id );
		//ToDo:エラーだったら中止する

		if( $party['status'] != Pp_RaidPartyManager::PARTY_STATUS_READY )
		{	// 準備中じゃない！？
			$this->af->setApp('error_detail', "party status is not 'ready'.", true);
			$this->af->setApp('status_detail_code', SDC_INAPI_PARTY_NOT_READY, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}

		$master_user_id = $party['master_user_id'];
		$dungeon_id = $party['dungeon_id'];
		$dungeon_rank = $party['difficulty'];
		$dungeon_lv = $party['dungeon_lv'];
		$sally_no = ( int )$party['sally'] + 1;

		//ダンジョン詳細を取得
		$ddundtl = $raidquest_m->getMasterDungeonDetail( $dungeon_id, $dungeon_rank, $dungeon_lv );

		// パーティマスターに出撃対象のダンジョンの出撃資格があるかをチェック
		$ret = $raidquest_m->checkDungeonQualified( $master_user_id, $dungeon_id, $dungeon_rank, $dungeon_lv, $ddundtl['last_lv'] );
		if( $ret === false )
		{	// 出撃資格がない
			$this->af->setApp('error_detail', "no qualify of dungeon in master.", true);
			$this->af->setApp('status_detail_code', SDC_INAPI_DUNGEON_UNQUALIFIED, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}

		// ノード専用データ
		$node_data = array(
			'sally_no'     => $sally_no,
			'dungeon_id'   => $dungeon_id,
			'dungeon_rank' => $dungeon_rank,
			'dungeon_lv'   => $dungeon_lv
		);
		//出力用配列
		//共通ダンジョンデータを生成（ドロップ抽選など）
		$boss_monster_id = 0;
		//敵データを生成
		$raid_enemies = $raidquest_m->getMasterEnemy($dungeon_id, $dungeon_rank, $dungeon_lv, null);//
		$raid_common_battle = array();//出力用配列
		$raid_common_battle_node = array();//出力用配列
		$idx = 0;

		foreach($raid_enemies as $dene) {
			$turn_base = $turn_1st = $dene['turn_base'];
			if (mt_rand(0, 99) < $dene['turn_prob']) $turn_1st += $dene['turn_1st'];
			$turn_1st = $dene['turn_base'] + ($dene['turn_1st']);
			//モンスター情報取得
			$monster_data = $monster_m->getMasterMonster($dene['monster_id']);
			$raid_enemy = array(
				'enemy_idx' => $idx,
				'enemy_id' => (int)$dene['enemy_id'],
				'monster_id' => (int)$dene['monster_id'],
				'attribute_id' => $monster_data['attribute_id'],
				'monster_name' => $monster_data['name_ja'],
				'boss_flag' => (int)$dene['boss_flag'],
				'hp' => (int)$dene['hp'],
				'attack' => (int)$dene['attack'],
				'defense' => (int)$dene['defense'],
				'normal_drop_type' => 0,//通常時ドロップ種別(0:無し 1:モンスター 2:鍵 3:宝箱)
				'normal_drop_monster_id' => 0,//通常時ドロップするモンスターのモンスターマスタID
				'normal_monster_drop_lv' => 0,//通常時ドロップするモンスターのレベル
				'normal_monster_drop_badge_expand' => 0,//通常時ドロップするモンスターの初期拡張バッジ数
				'normal_monster_drop_badges' => '',//通常時ドロップするモンスターの初期装備バッジ
				'boss_lv' => (int)$dene['lv'],//ボスレベル
				'battle_num' => 0,//出現バトル番号（バトルは１回しかないから0固定）
				'enemy_id' => (int)$dene['enemy_id'],//モンスターID
				'turn_base' => (int)$turn_base,//基礎攻撃ターン数
				'turn_1st' => (int)$turn_1st,//初撃ターン数
				'acttbl_pri' => (int)$dene['acttbl_pri'],//アクションテーブルプライマリ
				'acttbl_sec' => Pp_Util::convertCsvToIntArray($dene['acttbl_sec']),//アクションテーブルセカンダリ
				'acttbl_con' => Pp_Util::convertCsvToIntArray($dene['acttbl_con']),//アクションテーブル条件
			);
			$raid_enemy_node = array(
				'enemy_idx' => $idx,
				'enemy_id' => (int)$dene['enemy_id'],
				'boss_flag' => (int)$dene['boss_flag'],
				'hp' => (int)$dene['hp'],
			);
			//ボスのモンスターID
			if ($dene['boss_flag'] == 1) $boss_monster_id = $dene['monster_id'];
			//ドロップ抽選を行う
			$drop_rand = mt_rand(0, 999); //千分率
			//１体ずつ取得
			$drop_datas = $raidquest_m->getMasterEnemyDrop(
				$dungeon_id, $dungeon_rank, $dungeon_lv, array($dene['enemy_id'])
			);
			//１体ずつ抽選
			foreach($drop_datas as $drop) {
				if ($drop_rand < $drop['reward_drop']) {
					//ドロップ種別
					// 0:ドロップ無し
					// 1:モンスター
					// 2:鍵(未使用)
					// 3:宝箱(アイテム 1:ブロンズチケット 2:ゴールドチケット 3:合成メダル 4:マジカルメダル)※バッジ拡張アイテムはドロップしない
					// 4:バッジ（drop_monster_idにバッジID）
					// 5:バッジ素材（drop_monster_idにバッジ素材ID）
					// 6～:今後追加※ものによっては個別IDをdrop_monster_idに入れる
					switch($drop['reward_type']) {
						case 1://モンスター
							$raid_enemy['normal_drop_type'] = 1;//モンスター
							$raid_enemy['normal_drop_monster_id'] = (int)$drop['reward_id'];//モンスターID
							$raid_enemy['normal_monster_drop_lv'] = (int)$drop['lv'];//レベル
							$raid_enemy['normal_monster_drop_badge_expand'] = (int)$drop['badge_expand'];//初期拡張バッジ数
							$raid_enemy['normal_monster_drop_badges'] = $drop['badges'];//初期装備バッジ
							break;
						case 3://通常アイテム
							$raid_enemy['normal_drop_type'] = 3;//宝箱
							if ($drop['reward_id'] == 1) //ブロンズチケット
								$raid_enemy['normal_drop_monster_id'] = 1;//ブロンズチケット
							if ($drop['reward_id'] == 2) //ゴールドチケット
								$raid_enemy['normal_drop_monster_id'] = 2;//ゴールドチケット
							if ($drop['reward_id'] == 3) //合成メダル
								$raid_enemy['normal_drop_monster_id'] = 3;//合成メダル
							if ($drop['reward_id'] == 4) //マジカルメダル
								$raid_enemy['normal_drop_monster_id'] = 4;//マジカルメダル
							$raid_enemy['normal_monster_drop_lv'] = (int)$drop['reward_num'];//個数
							break;
						case 4://バッジ
							$raid_enemy['normal_drop_type'] = 4;//バッジ
							$raid_enemy['normal_drop_monster_id'] = (int)$drop['reward_id'];//バッジID
							$raid_enemy['normal_monster_drop_lv'] = (int)$drop['reward_num'];//個数
							break;
						case 5://バッジ素材
							$raid_enemy['normal_drop_type'] = 5;//バッジ素材
							$raid_enemy['normal_drop_monster_id'] = (int)$drop['reward_id'];//バッジ素材ID
							$raid_enemy['normal_monster_drop_lv'] = (int)$drop['reward_num'];//個数
							break;
						default :
							break;
					}
					break;
				}
				$drop_rand -= $drop['reward_drop'];
			}
			$raid_common_battle[] = $raid_enemy;
			$raid_common_battle_node[] = $raid_enemy_node;
			$idx++;
		}

		//ダンジョンデータ取得
		$ddun = $raidquest_m->getMasterDungeonById( $dungeon_id );
	//	上の方で取得済み
	//	$ddundtl = $raidquest_m->getMasterDungeonDetail( $dungeon_id, $dungeon_rank, $dungeon_lv );
		//クリア報酬データ取得
		$rewards = $raidquest_m->getMasterDungeonClearReward( $dungeon_id, $dungeon_rank, $dungeon_lv );
		$reward_clr = array();
		$reward_mvp = array();
		$reward_1st = array();
		$reward_2nd = array();
		foreach($rewards as $rwd) {
			//宝箱の場合はreward_idをアイテムIDに変換した値を保存しておく
			if ($rwd['reward_type'] == 3) {
				switch ($rwd['reward_id']) {
					case 1://ブロンズチケット
						$rwd['reward_id'] = Pp_ItemManager::ITEM_TICKET_GACHA_FREE;
						break;
					case 2://ゴールドチケット
						$rwd['reward_id'] = Pp_ItemManager::ITEM_TICKET_GACHA_RARE;
						break;
					case 3://合成メダル
						$rwd['reward_id'] = Pp_ItemManager::ITEM_MEDAL_SYNTHESIS;
						break;
					case 4://マジカルメダル
						$rwd['reward_id'] = Pp_ItemManager::ITEM_MEDAL_MAGICAL;
						break;
					case 5://バッジ拡張
						$rwd['reward_id'] = Pp_ItemManager::ITEM_BADGE_EXPAND;
						break;
				}
			}
			//必要なデータのみ抜き出す
			$rwdata = array(
					'reward_type' => $rwd['reward_type'],
					'reward_id'   => (int)$rwd['reward_id'],
					'lv'          => (int)$rwd['lv'],
					'reward_num'  => (int)$rwd['reward_num'],
					'badge_Expand'=> (int)$rwd['badge_expand'],
					'badges'      => $rwd['badges'],
			);
			//報酬カテゴリ（1:MVP, 2:初回クリア, 3:２回目以降クリア, 4:メンバー報酬）
			if ($rwd['category'] == 1) $reward_mvp[] = $rwdata;
			if ($rwd['category'] == 2) $reward_1st[] = $rwdata;
			if ($rwd['category'] == 3) $reward_2nd[] = $rwdata;
			if ($rwd['category'] == 4) $reward_clr[] = $rwdata;
		}

		// レイド終了日時の取得
		$battle_end_timestamp = $_SERVER['REQUEST_TIME'] + ( int )$ddundtl['limit_time'];

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
			$dungeon_end_date = $ddun['date_end'];	// ダンジョンの開催終了日時まで
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

		//モンスター情報取得
		$monster_data = $monster_m->getMasterMonster($boss_monster_id);
		//出力用配列
		$dungeon_info = array(
			'dungeon_id' => $ddundtl['dungeon_id'],//ダンジョンID
			'dungeon_name' => $ddun['name_ja'],//ダンジョン名
			'dungeon_rank' => (int)$ddundtl['difficulty'],//ダンジョン難易度
			'dungeon_lv' => (int)$ddundtl['dungeon_lv'],//ダンジョンレベル
			'bg_id' => (int)$ddundtl['bg_id'],//背景ID
			'use_type' => (int)$ddundtl['use_type'],//消費タイプ
			'use_point' => (int)$ddundtl['use_point'],//消費ポイント
			'exp' => (int)$ddundtl['exp'],//獲得経験値
			'attack_seven' => (int)$ddundtl['attack_seven'],//アタックゲージ7
			'attack_bar' => (int)$ddundtl['attack_bar'],//アタックゲージBAR
			'ranking_base_pt' => (int)$ddundtl['ranking_base_pt'],//獲得ベースランキングpt
			'ranking_dmg_pt' => (int)$ddundtl['ranking_dmg_pt'],//獲得ダメージランキングpt
			'limit_time' => (int)$ddundtl['limit_time'],//制限時間(単位：秒。制限時間なしの場合は0)
			'raid_end_date' => $dungeon_end_date,//レイド終了期間
			'guerrilla_end_date' => date('Y-m-d H:i:s', $dungeon_end_timestamp),//レイドゲリラスケジュール終了日時
			//ボスモンスターのIDと名前 ※１つのダンジョンにボス１体しか出現しない前提のデータ設計になっている（それで良いらしいが）
			'boss_id' => $boss_monster_id,
			'boss_name' => $monster_data['name_ja'],
			//報酬
			'clear_reward' => $reward_clr,//クリア報酬
			'mvp_reward' => $reward_mvp,//MVP報酬
			'first_reward' => $reward_1st,//初回クリア報酬
			'second_reward' => $reward_2nd,//２回目以降クリア報酬
		);

		//マスターがクリア済みのダンジョンLVか調べる
		$clear_info = $raiduser_m->getUserDungeonClear( $master_user_id, $dungeon_id, $dungeon_rank );
		$clear_flag = 0;
		//既に同ダンジョンLVのクリアデータが存在していたらクリア済みフラグを立てる
		if (!empty($clear_info) && $clear_info['dungeon_lv'] == $dungeon_lv) $clear_flag = 1;

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//パーティ情報を更新
			$columns = array(
				'sally'  => $sally_no,			// 出撃回数
				'status' => Pp_RaidPartyManager::PARTY_STATUS_QUEST	// パーティステータス
			);
			$ret = $raidparty_m->updateParty( $party_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// エラー
				$detail_code = SDC_INAPI_DB_ERROR;
				$error_detail = "updateParty( ".$user_id.", array( "
							  . "'sally' => ".$sally_no.", 'status' => ".Pp_RaidPartyManager::PARTY_STATUS_QUEST." ))";
				throw new Exception( $error_detail, $detail_code );
			}

			//レイドクエストログデータを保存する
			$quest_data = array();
			$quest_data['raid_common_battle'] = $raid_common_battle;
			$quest_data['dungeon_info'] = $dungeon_info;
			$quest_data['master_user_id'] = $master_user_id;
			$columns = array(
				'party_id'   => $party_id,
				'sally_no'   => $sally_no,
				'early_master_user_id' => $master_user_id,
				'play_id'    => $play_id,
				'clear_flag' => $clear_flag,
				'quest_data' => json_encode($quest_data),
				'date_created' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
			);
			$ret = $raidquest_m->setQuestData( $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// エラー
				$detail_code = SDC_INAPI_DB_ERROR;
				$error_detail = "setQuestData( array( "
							  . "'party_id' => ".$party_id.","
							  . "'sally_no' => ".$sally_no.","
							  . "'early_master_user_id' => ".$master_user_id.","
							  . "'play_id' => ".$play_id.","
							  . "'clear_flag' => ".$clear_flag.","
							  . "'quest_data' => '".json_encode($quest_data)."',"
							  . "'date_created' => '".date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])."'"
							  . " ))";
				throw new Exception( $error_detail, $detail_code );
			}

			// アクションログに行動を記録
			$api_transaction_id = $logdata_m->createApiTransactionId( $master_user_id );
			$columns = array(
				'api_transaction_id' => $api_transaction_id,
				'party_id' => $party_id,
				'user_id' => $master_user_id,
				'action' => Pp_RaidLogManager::ACTION_LOBBY_START
			);
			$ret = $raidlog_m->trackingPartyMemberAction( $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// エラー
				$detail_code = SDC_INAPI_DB_ERROR;
				$error_detail = "trackingPartyMemberAction( array( "
							  . "'api_transaction_id' => ".$api_transaction_id.","
							  . "'party_id' => ".$party_id.","
							  . "'user_id' => ".$user_id.","
							  . "'action' => ".Pp_RaidLogManager::ACTION_LOBBY_START
							  . " ))";
				throw new Exception( $error_detail, $detail_code );
			}

			//	パーティ検索用テーブルを更新
			$party = $raidparty_m->getParty( $party_id, false, true );		// 最新の情報を取得
			if(( !$party )||( Ethna::isError( $party )))
			{
				$detail_code = SDC_RAID_PARTYID_INVALID;
				$error_detail = "getParty( $party_id, false, true )";
				throw new Exception( $error_detail, $detail_code );
			}
			$ret = $raidsearch_m->renewTmpDataAfterPartyUpdate( $party );
			if( $ret === false )
			{	// エラー
				$buff = array();
				foreach( $party as $k => $v )
				{
					$buff[] = "'".$k."' => '".$v."'";
				}
				$error_detail = 'renewTmpDataAfterPartyUpdate( array('.implode(','.$buff).'))';
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
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

		// ノード用データにレイド終了日時のタイムスタンプを追加
		$node_data['end_timestamp'] = $battle_end_timestamp;

		//node用に返す
		$this->af->setApp('raid_common_battle_node', $raid_common_battle_node, true);
		$this->af->setApp('node_data', $node_data, true);
		$this->af->setApp('master_user_id', $master_user_id, true);

		//正常終了
		$this->af->setApp('result', 1, true);

		return 'inapi_json';
	}
}

?>
