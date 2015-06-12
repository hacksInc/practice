<?php
/**
 *  Inapi/Raid/Party/Leave.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';
require_once 'Pp_RaidPartyManager.php';

/**
 *  inapi_raid_party_changestatus Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyLeave extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',
		'master_user_id',
		'user_id',
		'leave_type',
		'total_enemy_hp',
		'user_total_damage'
    );
}

/**
 *  Inapi_raid_party_leave action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyLeave extends Pp_InapiActionClass
{
	/**
	 *  preprocess of inapi_raid_party_leave Action.
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
	 *  inapi_raid_party_leave action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$party_id = $this->af->get( 'party_id' );
		$master_user_id = $this->af->get( 'master_user_id' );
		$user_id = $this->af->get( 'user_id' );
		$leave_type = $this->af->get( 'leave_type' );
		$total_enemy_hp = $this->af->get( 'total_enemy_hp' );
		$user_total_damage = $this->af->get( 'user_total_damage' );

		// マネージャのインスタンスを取得
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$raid_log_m =& $this->backend->getManager( 'RaidLog' );
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );
		$raid_user_m =& $this->backend->getManager( 'RaidUser' );

		// パーティメンバー情報の取得
		$member = $raid_party_m->getPartyMember( $party_id, $user_id, true );
		if(( !$member )||( Ethna::isError( $member )))
		{
			$error_detail = "getPartyMember( $party_id, $user_id )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		// パーティ情報の取得
		$party_info = $raid_party_m->getParty( $party_id );
		if(( !$party_info )||( Ethna::isError( $party_info )))
		{
			$error_detail = "getParty( $party_id )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		// 強制退室の場合のみのチェック
		if( $leave_type == Pp_RaidPartyManager::LEAVE_TYPE_FORCE )
		{	// 強制退室
			if( $master_user_id != $party_info['master_user_id'] )
			{	// パーティマスター以外による操作
				$error_detail = "control by no party master. [".$master_user_id."/".$party_info['master_user_id']."]";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_FORCE_LEAVE_NO_MASTER, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}

			if( $master_user_id == $user_id )
			{	// パーティマスターを強制退室？
				$error_detail = "can not force leave to party_master.";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_FORCE_LEAVE_MASTER, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}

			// クエスト中の敵HP合計に対してどの程度ダメージを与えたかを求める
			if( $total_enemy_hp === 0 )
			{	// Zero Divide（ZOOMのゲームじゃないぉ）対策
				error_log( 'WARNING!!: Almost Zero Divide!!（￣皿￣#）' );
				$total_enemy_hp = 1;
			}
			$avg = floor( $user_total_damage * 100 / $total_enemy_hp );	// 小数点以下切り捨て
			if( $avg >= 5 )
			{	// ５％以上与えている場合は強制退室させることはできない
				$error_detail = "user_id[$user_id] is active user. [damage: $user_total_damage/total_enemy_hp]";
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_FORCE_LEAVE_ACTIVE, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}
			$member_status = Pp_RaidPartyManager::MEMBER_STATUS_FORCE;
			$sally_status = Pp_RaidPartyManager::SALLY_STATUS_FORCE;
		}
		else
		{	// 自主退室
			$member_status = Pp_RaidPartyManager::MEMBER_STATUS_BREAK;
			$sally_status = Pp_RaidPartyManager::SALLY_STATUS_RETIRE;
		}

		if(( $member['status'] == Pp_RaidPartyManager::MEMBER_STATUS_BREAK )||( $member['status'] == Pp_RaidPartyManager::MEMBER_STATUS_FORCE ))
		{	// 既に離脱済み
			$status = array(
				Pp_RaidPartyManager::MEMBER_STATUS_READY,		// 準備中の人
				Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY,	// 出撃準備完了の人
				Pp_RaidPartyManager::MEMBER_STATUS_RECOVER,		// 回復中の人
				Pp_RaidPartyManager::MEMBER_STATUS_BATTLE,		// 戦闘中の人
				Pp_RaidPartyManager::MEMBER_STATUS_MAP			// 探索中の人
			);
			$members = $raid_party_m->getPartyMembers( $party_id, $status, true, true );
			if(( is_array( $members ) === false )||( Ethna::isError( $members )))
			{	// 取得エラー
				$error_detail = "getPartyMembers( $party_id, array( "
							  . Pp_RaidPartyManager::MEMBER_STATUS_READY.","
							  . Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY.","
							  . Pp_RaidPartyManager::MEMBER_STATUS_RECOVER.","
							  . Pp_RaidPartyManager::MEMBER_STATUS_BATTLE.","
							  . Pp_RaidPartyManager::MEMBER_STATUS_MAP." ), true )";
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true, true );
				$this->af->setApp( 'result', 0, true );
			}
			else
			{
				$this->af->setApp( 'party_num', count( $members ), true );
				$this->af->setApp( 'master_user_id', $party_info['master_user_id'], true );
				$this->af->setApp( 'result', 1, true );		// とりあえず成功で返す
			}
			return 'inapi_json';
		}

		// 出撃メンバー情報を取得
		$sally_member = $raid_party_m->getSallyMember( $party_id, $party_info['sally'], $user_id );
		if(( is_array( $sally_member ) === false )||( Ethna::isError( $sally_member )))
		{
			$error_detail = "getSallyMember( $party_id, ".$party_info['sally'].", $user_id )";
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	退室ユーザーが出撃中なら出撃メンバー情報のステータスを更新
			//-------------------------------------------------------------
			if(( $party_info['status'] == Pp_RaidPartyManager::PARTY_STATUS_QUEST )&&( empty( $sally_member ) === false ))
			{	// パーティステータスが出撃中で、退室ユーザーが出撃メンバーとしている場合
				$colomns = array( 'status' => $sally_status );
				$ret = $raid_party_m->updateSallyMember( $party_id, $party_info['sally'], $user_id, $colomns );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// 更新できなければエラー
					$error_detail = "updateSallyMember( $party_id, ".$party_info['sally'].", $user_id,"
								  . " array( ".Pp_RaidPartyManager::SALLY_STATUS_RETIRE. "))";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
			}

			//-------------------------------------------------------------
			//	パーティメンバー情報のステータスを更新
			//-------------------------------------------------------------
			$columns = array( 'status' => $member_status );
			$ret = $raid_party_m->updatePartyMember( $party_id, $user_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// 更新できなければエラー
				$error_detail = "updatePartyMember( $party_id, $user_id, "
							  . "array( ".Pp_RaidPartyManager::MEMBER_STATUS_BREAK."))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	パーティ情報の変更
			//-------------------------------------------------------------
			// パーティ在籍メンバーを取得
			$status = array(
				Pp_RaidPartyManager::MEMBER_STATUS_READY,		// 準備中の人
				Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY,	// 出撃準備完了の人
				Pp_RaidPartyManager::MEMBER_STATUS_RECOVER,		// 回復中の人
				Pp_RaidPartyManager::MEMBER_STATUS_BATTLE,		// 戦闘中の人
				Pp_RaidPartyManager::MEMBER_STATUS_MAP			// 探索中の人
			);
			$members = $raid_party_m->getPartyMembers( $party_id, $status, true, true );
			if(( is_array( $members ) === false )||( Ethna::isError( $members )))
			{	// 取得エラー
				$error_detail = "getPartyMembers( $party_id, array( "
							  . Pp_RaidPartyManager::MEMBER_STATUS_READY.","
							  . Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY.","
							  . Pp_RaidPartyManager::MEMBER_STATUS_RECOVER.","
							  . Pp_RaidPartyManager::MEMBER_STATUS_BATTLE.","
							  . Pp_RaidPartyManager::MEMBER_STATUS_MAP." ), true, true )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			$member_num = count( $members );	// 残ったパーティメンバーの人数
			$changed_master_status = null;		// マスターユーザーのステータスに変更があった場合の変更後のステータス
			if( $party_info['master_user_id'] == $user_id )
			{	// 退室者がパーティマスターだった場合
				if( empty( $members ) === true )
				{	// 次の候補がいないので閉店（長い間のご愛顧ありがとうございました）
					$master_user_id = '0';	// とりあえず次のパーティマスターは０でリセット
					$columns = array(
						'status' => Pp_RaidPartyManager::PARTY_STATUS_BREAKUP,	// ステータスを“解散”
						'master_user_id' => $master_user_id,	// とりあえず０にしておく
						'member_num' => 0						// メンバー数を０に
					);
				}
				else
				{	// 候補がいるならマスターを引き継ぐ
					// getPartyMembers()がパーティ加入順に出力するので、最初のメンバーが新マスター
					$master_user_id = $members[0]['user_id'];	// 新しいパーティマスター

					// 新パーティマスターのステータスが「準備完了」の場合は「準備中」に戻す
					if( $members[0]['status'] == Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY )
					{
						$columns = array( 'status' => Pp_RaidPartyManager::MEMBER_STATUS_READY );
						$ret = $raid_party_m->updatePartyMember( $party_id, $master_user_id, $columns );
						if( !$ret )
						{	// 更新エラー
							$detail_code = SDC_INAPI_DB_ERROR;
							$error_detail = "updatePartyMember( $party_id, $master_user_id, "
										  . "array( 'status' => ".Pp_RaidPartyManager::MEMBER_STATUS_READY."))";
							throw new Exception( $error_detail, $detail_code );
						}
						$changed_master_status = Pp_RaidPartyManager::MEMBER_STATUS_READY;
					}

					$columns = array(
						'master_user_id' => $master_user_id,	// マスターを引き継ぎ
						'member_num' => $member_num				// メンバー数を変更
					);
				}
			}
			else
			{	// 退室者がパーティマスターではない
				$columns = array(
					'member_num' => $member_num						// メンバー数を変更
				);
				$master_user_id = $party_info['master_user_id'];	// 今までのパーティマスター変わらず
			}
			$ret = $raid_party_m->updateParty( $party_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// 更新エラー
				$error_detail = "updateParty( $party_id, array( ... ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			// パーティマスターのダンジョンクリアの状態を取得
			$clear_info = $raid_user_m->getUserDungeonClear(
				$master_user_id, $party_info['dungeon_id'], $party_info['difficulty']
			);
			if( is_null( $clear_info ) === true )
			{	// クリア情報取得エラー
				$error_detail = "getUserDungeonClear( $master_user_id,".$party_info['dungeon_id'].",".$party_info['difficulty']." )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			if(( empty( $clear_info ) === true )||( $clear_info['dungeon_lv'] < $party_info['dungeon_lv'] ))
			{	// パーティマスターはまだこのダンジョンをクリアしていない
				$clear_flag = 0;
			}
			else
			{	// 既にクリア済
				$clear_flag = 1;
			}

			//-------------------------------------------------------------
			//	アクションをログに記録
			//-------------------------------------------------------------
			// ユーザーアクションログ
			$api_transaction_id = $logdata_m->createApiTransactionId( $user_id );
			switch( $leave_type )
			{
				case Pp_RaidPartyManager::LEAVE_TYPE_SELF:
					$action = Pp_RaidLogManager::ACTION_LEAVE_SELF;
					break;
				case Pp_RaidPartyManager::LEAVE_TYPE_FORCE:
					$action = Pp_RaidLogManager::ACTION_LEAVE_FORCE;
					break;
				case Pp_RaidPartyManager::LEAVE_TYPE_AUTO:
					$action = Pp_RaidLogManager::ACTION_LEAVE_AUTO;
					break;
				default:
					throw new Exception( "leave_type = ".$leave_type );
			}
			$columns = array(
				'api_transaction_id' => $api_transaction_id,
				'party_id' => $party_id,
				'user_id' => $user_id,
				'action' => $action
			);
			$ret = $raid_log_m->trackingPartyMemberAction( $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// エラー
				$error_detail = "trackingPartyMemberAction( array( "
							  . "'$api_transaction_id', $party_id, $user_id, $action ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	パーティ検索用テーブルを更新
			//-------------------------------------------------------------
			// 最新の情報を取得
			$party_info = $raid_party_m->getParty( $party_id, false, true );
			if( is_null( $party_info ) === true )
			{
				$error_detail = "getParty( $party_id, false, true ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			if( empty( $party_info ) === true )
			{
				$error_detail = "getParty( $party_id, false, true ))";
				$detail_code = SDC_RAID_PARTYID_INVALID;
				throw new Exception( $error_detail, $detail_code );
			}
			$ret = $raid_search_m->renewTmpDataAfterPartyUpdate( $party_info );
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
				'user_id' => $user_id,
				'status' => $member_status,
				'disconn' => 0
			);
			$ret = $raid_search_m->renewTmpDataAfterPartyMemberUpdate( $columns, $party_info['date_created'] );
			if( $ret === false )
			{	// エラー
				$error_detail = 'renewTmpDataAfterPartyMemberUpdate( array( '
							  . 'party_id => '.$party_id.','
							  . 'user_id => '.$user_id.','
							  . 'status => '.$member_status.','
							  . 'disconn => 0 ))';
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			// 新パーティマネージャのステータス変更がある場合は、そのユーザーの検索情報も更新
			if( is_null( $changed_master_status ) === false )
			{
				// 新パーティマスターの情報を取得
				$m = $raid_party_m->getPartyMember( $party_id, $master_user_id, true );
				if( empty( $m ) === true )
				{
					$error_detail = "getPartyMember( $party_id, $master_user_id, true )";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
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
			//	nodejsに返すパラメータをセット
			//-------------------------------------------------------------
			$this->af->setApp( 'changed_master_status', $changed_master_status, true );
			$this->af->setApp( 'master_user_id', $master_user_id, true );
			$this->af->setApp( 'party_num', $member_num, true );
			$this->af->setApp( 'clear_flag', $clear_flag, true );
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
