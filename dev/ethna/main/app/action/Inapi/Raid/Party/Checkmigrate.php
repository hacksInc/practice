<?php
/**
 *  Inapi/Raid/Party/Checkmigrate.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_changedeck Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyCheckmigrate extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',
		'user_id',
    );
}

/**
 *  Inapi_raid_party_checkmigrate action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyCheckmigrate extends Pp_InapiActionClass
{
	/**
	 *  preprocess of api_raid_party_checkmigrate Action.
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
	 *  api_raid_party_checkmigrate action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$party_id = $this->af->get( 'party_id' );
		$user_id = $this->af->get( 'user_id' );

		// マネージャのインスタンスを取得
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$raid_user_m =& $this->backend->getManager( 'RaidUser' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$raid_log_m =& $this->backend->getManager( 'RaidLog' );
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );

		// 引き継ぎ状態のパーティメンバー情報を取得
		$members = $raid_party_m->getMigratePartyMember( $user_id );
		if( empty( $members ) === true )
		{	// 引き継ぎなし
			$this->af->setApp( 'disconnect', 0, true );
			$this->af->setApp( 'result', 1, true );
			return 'inapi_json';
		}

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			// パーティマスターになっているパーティの引き継ぎを行う
			$changed_master_status = null;
			$master_user_id = null;
			$member_num = null;
			$clear_flag = null;

			// 引き継ぎ状態のユーザーを退室扱いにする
			foreach( $members as $m )
			{
				// パーティから退室させる
				$param = array(
					Pp_RaidPartyManager::MEMBER_STATUS_BREAK,
					$m['party_id'],
					$user_id,
					Pp_RaidPartyManager::MEMBER_STATUS_MIGRATE
				);
				$sql = "UPDATE t_raid_party_member SET status = ?, disconn = 1 "
				     . "WHERE party_id = ? AND user_id = ? AND status = ?";
				$ret = $db->execute( $sql, $param );
				if( !$ret )
				{	// 更新エラー
					$detail_code = SDC_INAPI_DB_ERROR;
					$error_detail = "UPDATE t_raid_party_member SET status = ".Pp_RaidPartyManager::MEMBER_STATUS_BREAK.", disconn = 1 "
					     . "WHERE party_id = ".$m['party_id']." AND user_id = $user_id AND status = ".Pp_RaidPartyManager::MEMBER_STATUS_MIGRATE;
					throw new Exception( $error_detail, $detail_code );
				}

				// 最新のパーティ情報を取得
				$party = $raid_party_m->getParty( $m['party_id'], false, true );
				if( is_null( $party ) === true )
				{
					$error_detail = "getParty( ".$m['party_id'].", false, true ))";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
				if( empty( $party ) === true )
				{
					$error_detail = "getParty( ".$m['party_id'].", false, true ))";
					$detail_code = SDC_RAID_PARTYID_INVALID;
					throw new Exception( $error_detail, $detail_code );
				}

				// パーティの在籍メンバーを取得
				$status = array(
					Pp_RaidPartyManager::MEMBER_STATUS_READY,		// 準備中の人
					Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY,	// 出撃準備完了の人
					Pp_RaidPartyManager::MEMBER_STATUS_RECOVER,		// 回復中の人
					Pp_RaidPartyManager::MEMBER_STATUS_BATTLE,		// 戦闘中の人
					Pp_RaidPartyManager::MEMBER_STATUS_MAP			// 探索中の人
				);
				$mem = $raid_party_m->getPartyMembers( $party['party_id'], $status );
				if( is_null( $mem ) === true )
				{	// 取得エラー
					$error_detail = "getPartyMembers( ".$party['party_id'].", array( "
								  . Pp_RaidPartyManager::MEMBER_STATUS_READY.","
								  . Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY.","
								  . Pp_RaidPartyManager::MEMBER_STATUS_RECOVER.","
								  . Pp_RaidPartyManager::MEMBER_STATUS_BATTLE.","
								  . Pp_RaidPartyManager::MEMBER_STATUS_MAP." ), true )";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}

				$mem_num = count( $mem );	// 残ったパーティメンバーの人数
				$temp_changed_master_status = null;
				if( $party['master_user_id'] == $user_id )
				{	// 退室者がパーティマスターだった場合、パーティマスターを引き継ぐ
					if( empty( $mem ) === true )
					{	// 候補者なし
						$temp_master_user_id = '0';
						$columns = array(
							'status' => Pp_RaidPartyManager::PARTY_STATUS_BREAKUP,	// ステータスを“解散”
							'master_user_id' => '0',								// とりあえず０にしておく
							'member_num' => 0										// メンバー数を０に
						);
					}
					else
					{	// 候補者あり
						error_log( '$mem[0][user_id] = '.$mem[0]['user_id'] );
						$temp_master_user_id = $mem[0]['user_id'];
						if( $mem[0]['status'] == Pp_RaidPartyManager::MEMBER_STATUS_STAND_BY )
						{	// 次のパーティマスターのステータスが「準備完了」の場合は「準備中」に戻す
							$columns = array( 'status' => Pp_RaidPartyManager::MEMBER_STATUS_READY );
							$ret = $raid_party_m->updatePartyMember( $m['party_id'], $temp_master_user_id, $columns );
							if( !$ret )
							{	// 更新エラー
								$detail_code = SDC_INAPI_DB_ERROR;
								$error_detail = "updatePartyMember( ".$m['party_id'].", ".$temp_master_user_id.", "
											  . "array( 'status' => ".Pp_RaidPartyManager::MEMBER_STATUS_READY."))";
								throw new Exception( $error_detail, $detail_code );
							}

							$temp_changed_master_status = Pp_RaidPartyManager::MEMBER_STATUS_READY;
						}

						$columns = array(
							'master_user_id' => $temp_master_user_id,
							'member_num' => $mem_num
						);
					}
				}
				else
				{	// 退室者がパーティマスターではない
					$columns = array(
						'member_num' => $mem_num
					);
					$temp_master_user_id = $party['master_user_id'];	// 今までのパーティマスター変わらず
				}

				// パーティ情報を更新
				$ret = $raid_party_m->updateParty( $m['party_id'], $columns );
				if( !$ret )
				{	// 更新エラー
					$error_detail = "updateParty( $party_id, array( ... ))";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}

				if( $party_id == $m['party_id'] )
				{	// 現在接続中のパーティなら戻り値用に値を退避する
					$changed_master_status = $temp_changed_master_status;
					$master_user_id = $temp_master_user_id;
					$member_num = $mem_num;

					// パーティマスターのダンジョンクリアの状態を取得
					$clear_info = $raid_user_m->getUserDungeonClear(
						$master_user_id, $party['dungeon_id'], $party['difficulty']
					);
					if( is_null( $clear_info ) === true )
					{	// クリア情報取得エラー
						$error_detail = "getUserDungeonClear( $master_user_id,".$party['dungeon_id'].",".$party['difficulty']." )";
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
				}

				//-------------------------------------------------------------
				//	アクションをログに記録
				//-------------------------------------------------------------
				// ユーザーアクションログ
				$api_transaction_id = $logdata_m->createApiTransactionId( $user_id );
				$columns = array(
					'api_transaction_id' => $api_transaction_id,
					'party_id' => $m['party_id'],
					'user_id' => $user_id,
					'action' => Pp_RaidLogManager::ACTION_LEAVE_MIGRATE
				);
				$ret = $raid_log_m->trackingPartyMemberAction( $columns );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// エラー
					$error_detail = "trackingPartyMemberAction( array( "
								  . "'$api_transaction_id', ".$m['party_id'].", $user_id, "
								  . Pp_RaidLogManager::ACTION_LEAVE_MIGRATE." ))";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}

				//-------------------------------------------------------------
				//	パーティ検索用テーブルを更新
				//-------------------------------------------------------------
				// 最新の情報を取得
				$party = $raid_party_m->getParty( $m['party_id'], false, true );
				if( is_null( $party ) === true )
				{
					$error_detail = "getParty( ".$m['party_id'].", false, true ))";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
				if( empty( $party ) === true )
				{
					$error_detail = "getParty( ".$m['party_id'].", false, true ))";
					$detail_code = SDC_RAID_PARTYID_INVALID;
					throw new Exception( $error_detail, $detail_code );
				}
				$ret = $raid_search_m->renewTmpDataAfterPartyUpdate( $party );
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
					'party_id' => $m['party_id'],
					'user_id' => $user_id,
					'status' => Pp_RaidPartyManager::MEMBER_STATUS_BREAK,
					'disconn' => 1
				);
				$ret = $raid_search_m->renewTmpDataAfterPartyMemberUpdate( $columns, $party['date_created'] );
				if( $ret === false )
				{	// エラー
					$error_detail = 'renewTmpDataAfterPartyMemberUpdate( array( '
								  . 'party_id => '.$m['party_id'].','
								  . 'user_id => '.$user_id.','
								  . 'status => '.Pp_RaidPartyManager::MEMBER_STATUS_BREAK.','
								  . 'disconn => 0 ),'
								  . "'".$party_info['date_created']."' )";
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
			$this->af->setApp( 'disconnect', 1, true );
			$this->af->setApp( 'result', 1, true );

			$db->commit();		// 問題がなければコミットしてトランザクション終了
		}
		catch( Exception $e )
		{
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
