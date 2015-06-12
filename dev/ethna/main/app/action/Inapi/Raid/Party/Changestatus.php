<?php
/**
 *  Inapi/Raid/Party/ChangeStatus.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_changestatus Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyChangestatus extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',
		'user_id',
		'sally_no',
		'user_status',
    );
}

/**
 *  Inapi_raid_party_changestatus action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyChangestatus extends Pp_InapiActionClass
{
	// 端末側ユーザーステータス
	const CLI_USER_STATUS_READY    = 1;		// 準備中
	const CLI_USER_STATUS_STAND_BY = 2;		// 出撃準備完了
	const CLI_USER_STATUS_RECOVER  = 3;		// 回復中
	const CLI_USER_STATUS_BATTLE   = 4;		// 戦闘中
	const CLI_USER_STATUS_MAP      = 5;		// 探索中
	const CLI_USER_STATUS_DISABLE  = 8;		// 無効(ソケット切断)

	/**
	 *  preprocess of api_raid_party_changestatus Action.
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
	 *  api_raid_party_changestatus action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$party_id = $this->af->get( 'party_id' );
		$user_id = $this->af->get( 'user_id' );
		$sally_no = $this->af->get( 'sally_no' );
		$user_status = $this->af->get( 'user_status' );

		// マネージャのインスタンスを取得
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$raid_log_m =& $this->backend->getManager( 'RaidLog' );
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	ユーザーのステータスを更新
			//-------------------------------------------------------------
			$update_user_ids = array();
			$columns = array(
				'status' => $user_status
			);
			$ret = $raid_party_m->updatePartyMember( $party_id, $user_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// 更新エラー
				$error_detail = "updatePartyMember( $party_id, $user_id, "
							  . "array( 'status' => $status ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			if( empty( $sally_no ) === false )
			{	// 出撃NOの指定がある場合は、出撃メンバーのステータスも更新
				$ret = $raid_party_m->updateSallyMember( $party_id, $sally_no, $user_id, $columns );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// パーティIDが取得できなければ作成エラー
					$error_detail = "updateSallyMember( $party_id, $sally_no, $user_id, "
								  . "array( 'status' => $status ))";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
			}

			//-------------------------------------------------------------
			//	アクションをログに記録
			//-------------------------------------------------------------
			$api_transaction_id = $logdata_m->createApiTransactionId( $user_id );
			switch( $user_status )
			{
				// 準備完了→出撃
				case self::CLI_USER_STATUS_STAND_BY:
					$action = Pp_RaidLogManager::ACTION_LOBBY_STAND_BY;
					break;

				default:
					$action = '';
					break;
			}

			if( empty( $action ) === false )
			{	// アクションの指定がある時だけログに記録
				$columns = array(
					'api_transaction_id' => $api_transaction_id,
					'party_id' => $party_id,
					'user_id' => $user_id,
					'action' => $action
				);
				$ret = $raid_log_m->trackingPartyMemberAction( $columns );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// エラー
					$error_detail = "trackingPartyMemberAction( array("
								  . "'api_transaction_id' => '$api_transaction_id',"
								  . "'party_id' => '$party_id',"
								  . "'user_id' => '$user_id',"
								  . "'action' => '$action'))";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
			}

			//-------------------------------------------------------------
			//	パーティ検索用テーブルを更新
			//-------------------------------------------------------------
			// 最新の情報を取得
			$m = $raid_party_m->getPartyMember( $party_id, $user_id, true );
			if( empty( $m ) === true )
			{
				$error_detail = "getPartyMember( $party_id, $user_id )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			// パーティ検索情報を更新
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

			//-------------------------------------------------------------
			//	nodejsに返すパラメータをセット
			//-------------------------------------------------------------
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
			$this->af->setApp( 'result', 0, true );
			$this->af->setApp( 'error_detail', $e->getMessage(), true );
		}
		return 'inapi_json';
	}
}
?>
