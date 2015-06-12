<?php
/**
 *  Inapi/Raid/Party/Disconnect.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_disconnect Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyDisconnect extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',
		'user_id'
    );
}

/**
 *  Inapi_raid_party_disconnect action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyDisconnect extends Pp_InapiActionClass
{
	/**
	 *  preprocess of api_raid_party_changestatusdisable Action.
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
	 *  api_raid_party_changestatusdisable action implementation.
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
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$raid_log_m =& $this->backend->getManager( 'RaidLog' );
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );

		// 指定されたパーティIDにおけるステータスを取得する
		$party_member = $raid_party_m->getPartyMember( $party_id, $user_id, true );
		if( empty( $party_member ) === true )
		{	// パーティメンバーが取得できなければエラー
			$error_detail = "getPartyMember( $party_id, $user_id, true )";
			$this->af->setApp( 'result', 0, true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			return 'inapi_json';
		}

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	パーティメンバー情報に切断フラグをセット
			//-------------------------------------------------------------
			$columns = array( 	'disconn' => 1 , 								// 切断状態にする
								'disconn_status' => $party_member['status']);	// この時のステータスを保持しておく
			$ret = $raid_party_m->updatePartyMember( $party_id, $user_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// パーティIDが取得できなければ作成エラー
				$error_detail = "updatePartyMember( $party_id, $user_id, array( 'disconn' => 1, 'disconn_status' => ".$party_member['status']." ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			// ユーザーのステータスを取得
			$member = $raid_party_m->getPartyMember( $party_id, $user_id, true );
			if( empty( $member ) === true )
			{
				$error_detail = "getPartyMember( $party_id, $user_id, true )";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			$user_status = $member['status'];

			//-------------------------------------------------------------
			//	アクションをログに記録
			//-------------------------------------------------------------
			$api_transaction_id = $logdata_m->createApiTransactionId( $user_id );

			if( empty( $action ) === false )
			{	// アクションの指定がある時だけログに記録
				$columns = array(
					'api_transaction_id' => $api_transaction_id,
					'party_id' => $party_id,
					'user_id' => $user_id,
					'action' => Pp_RaidLogManager::ACTION_DISCONNECT
				);
				$ret = $raid_log_m->trackingPartyMemberAction( $columns );
				if(( !$ret )||( Ethna::isError( $ret )))
				{	// エラー
					$error_detail = "trackingPartyMemberAction( array("
								  . "'api_transaction_id' => '".$api_transaction_id."',"
								  . "'party_id' => $party_id,"
								  . "'user_id' => $user_id,"
								  . "'action' => ".Pp_RaidLogManager::ACTION_DISCONNECT."))";
					$detail_code = SDC_INAPI_DB_ERROR;
					throw new Exception( $error_detail, $detail_code );
				}
			}

			//-------------------------------------------------------------
			//	パーティ検索用テーブルを更新
			//-------------------------------------------------------------
			$columns = array(
				'party_id' => $party_id,
				'user_id' => $user_id,
				'status' => $user_status,
				'disconn' => 1
			);
			$ret = $raid_search_m->renewTmpDataAfterPartyMemberUpdate( $columns );
			if( $ret === false )
			{	// エラー
				$buff = array();
				foreach( $columns as $k => $v )
				{
					$buff[] = "'".$k."' => '".$v."'";
				}
				$error_detail = 'renewTmpDataAfterPartyMemberUpdate( array('.implode(','.$buff).'))';
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
