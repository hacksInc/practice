<?php
/**
 *  Inapi/Raid/Battle/Defeat.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_QuestManager.php';

/**
 *  inapi_raid_battle_defeat Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidBattleDefeat extends Pp_InapiActionForm
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
		'sally_no' => array(
			'name'        => '出撃NO',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
		'enemy_idx' => array(
			'name'        => '撃破した敵のインデックス',
			'type'        => VAR_TYPE_INT,
			'required'    => true,
		),
	);
}

/**
 *  inapi_raid_battle_defeat action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidBattleDefeat extends Pp_InapiActionClass
{
	/**
	 *  preprocess of inapi_raid_battle_defeat Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{
			$this->af->setApp('error_detail', 'validate error.', true);
			$this->af->setApp('status_detail_code', SDC_INAPI_ARGUMENT_ERROR, true);
			$this->af->setApp('result', 0, true);
			return 'inapi_json';
		}
		return null;
	}

	/**
	 *  inapi_raid_battle_defeat action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		//-------------------------------------------------------------
		//	nodejsサーバーからのパラメータを取得
		//-------------------------------------------------------------
		$party_id = $this->af->get( 'party_id' );
		$sally_no = $this->af->get( 'sally_no' );
		$enemy_idx = $this->af->get( 'enemy_idx' );

		//-------------------------------------------------------------
		//	マネージャのインスタンスを取得
		//-------------------------------------------------------------
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$raid_quest_m =& $this->backend->getManager( 'RaidQuest' );

		//-------------------------------------------------------------
		//	クエストデータを取得
		//-------------------------------------------------------------
		$quest = $raid_quest_m->getQuest( $party_id, $sally_no );
		if(( empty( $quest ) === true )||( Ethna::isError( $quest )))
		{	// 取得エラー
			$error_detail = 'getQuest( '.$party_id.','.$sally_no.' )';
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
		$quest_data = json_decode( $quest['quest_data'] );

		//-------------------------------------------------------------
		//	倒した敵から報酬が獲得できるかをチェック
		//-------------------------------------------------------------
		$enemies = $quest_data->raid_common_battle;
		foreach( $enemies as $e )
		{
			if( $e->enemy_idx != $enemy_idx )
			{	// 倒した敵とは違う
				continue;
			}

			switch( $e->normal_drop_type )
			{
				// 報酬があるもの
				case Pp_QuestManager::DROP_TYPE_MONSTER:	// モンスター
					$lv = $e->normal_monster_drop_lv;		// モンスターLV
					$num = 1;								// 獲得数
					$badge_expand = $e->normal_monster_drop_badge_expand;	// 初期拡張バッジ数
					$badges = $e->normal_monster_drop_badges;				// 初期装備バッジ
					if (is_null($badges)) $badges = '';
					break;
				case Pp_QuestManager::DROP_TYPE_BOX:		// 宝箱（ゴルブロチケット）
				case Pp_QuestManager::DROP_TYPE_BADGE:		// バッジ
				case Pp_QuestManager::DROP_TYPE_MATERIAL:	// 素材
					$lv = 0;								// LV
					$num = $e->normal_monster_drop_lv;		// 獲得数
					$badge_expand = 0;						// 初期拡張バッジ数
					$badges = '';							// 初期装備バッジ
					break;

				// 報酬がなければ正常終了を返して終わる
				case Pp_QuestManager::DROP_TYPE_NONE:		// なし
				case Pp_QuestManager::DROP_TYPE_KEY:		// 鍵
					$this->af->setApp( 'result', 1, true );
					return 'inapi_json';

				// 知らない種別ならエラー
				default:
					$error_detail = 'unknown drop type. type='.$e->normal_drop_type;
					$this->af->setApp( 'status_detail_code', SDC_INAPI_REWARD_ERROR, true );
					$this->af->setApp( 'error_detail', $error_detail, true );
					$this->af->setApp( 'result', 0, true );
					return 'inapi_json';
			}

			// 報酬IDと種別はここでセット
			$reward_type = $e->normal_drop_type;		// 報酬タイプ
			$reward_id = $e->normal_drop_monster_id;	// アイテムID／モンスターID
			break;
		}

		if( empty( $reward_type ) === true )
		{	// 倒した敵の情報がない！？
			$error_detail = "not found drop reward info. (enemy_idx[$enemy_idx])";
			$this->af->setApp( 'status_detail_code', SDC_INAPI_REWARD_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		//-------------------------------------------------------------
		//	２重登録チェック
		//-------------------------------------------------------------
		// ※１度倒したボスは２度と出現しない（１回のクエストで１度しか取得できない）という
		//   仕様を前提としてチェックしている。
		$ret = $raid_quest_m->isExistTmpRaidReward( $party_id, $sally_no, $enemy_idx );
		if( Ethna::isError( $ret ))
		{	// SQL実行エラー
			$error_detail = "isRegistTmpRaidReward( $party_id, $sally_no, $enemy_idx )";
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
		else if( $ret === true )
		{	// 既に指定のボスがドロップした報酬は登録済
			$error_detail = "drop reward is already exist.";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 1, true );		// 登録処理はしないで正常終了を返す
			return 'inapi_json';
		}

		//-------------------------------------------------------------
		//	出撃中のメンバーを取得
		//-------------------------------------------------------------
		$status = array(
			Pp_RaidPartyManager::SALLY_STATUS_RECOVER,	// 回復中
			Pp_RaidPartyManager::SALLY_STATUS_BATTLE,	// 出撃中
			Pp_RaidPartyManager::SALLY_STATUS_MAP		// 探索中
		);
		$sally_members = $raid_party_m->getSallyMembers( $party_id, $sally_no, $status );
		if(( empty( $sally_members ) === true )||( Ethna::isError( $sally_members )))
		{	// 取得エラー
			$error_detail = 'getSallyMembers( '.$party_id.','.$sally_no.', array( '
						  . Pp_RaidPartyManager::SALLY_STATUS_RECOVER.','
						  . Pp_RaidPartyManager::SALLY_STATUS_BATTLE.','
						  . Pp_RaidPartyManager::SALLY_STATUS_MAP.' ))';
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}


		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	出撃中のメンバーに対して獲得報酬情報を登録
			//-------------------------------------------------------------
			foreach( $sally_members as $m )
			{
				// 一時テーブルに獲得情報を登録
				$ret = $raid_quest_m->insertTmpRaidReward(
					$party_id, $sally_no, $enemy_idx, $m['user_id'],
					$reward_type, $reward_id, $lv, $num, $badge_expand, $badges
				);
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = 'insertTmpRaidReward( '
								  . $party_id.','.$sally_no.','.$m['user_id'].','.$reward_type.','
								  . $reward_id.','.$lv.','.$num.','.$badge_expand.','.$badges.' ))';
					throw new Exception( $error_detail, $detail_code );
				}
			}

			$db->commit();
		}
		catch( Exception $e )
		{	// 例外発生！
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
