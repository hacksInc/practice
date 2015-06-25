<?php
/**
 *  Inapi/Raid/Party/Returnlobby.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_returnlobby Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyReturnlobby extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',
		'user_id',
		'sally_no'
    );
}

/**
 *  Inapi_raid_party_returnlobby action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyReturnlobby extends Pp_InapiActionClass
{

	/**
	 *  preprocess of api_raid_party_returnlobby Action.
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
	 *  api_raid_party_returnlobby action implementation.
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

		// マネージャのインスタンスを取得
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	対象ユーザーのステータスを更新
			//-------------------------------------------------------------
			$columns = array( 'status' => Pp_RaidPartyManager::MEMBER_STATUS_RECOVER );
			$ret = $raid_party_m->updatePartyMember( $party_id, $user_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// エラーなら例外
				$error_detail = "updatePartyMember( $party_id, $user_id, "
							  . "array( 'status' => ".Pp_RaidPartyManager::MEMBER_STATUS_RECOVER." ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			$affected_rows = $db->db->affected_rows();
			if( $affected_rows == 0 )
			{	// 更新レコードなし
				$error_detail = "updatePartyMember() is no affected_rows.";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			$ret = $raid_party_m->updateSallyMember( $party_id, $sally_no, $user_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// エラーなら例外
				$error_detail = "updateSallyMember( $party_id, $sally_no, $user_id, "
							  . "array( 'status' => ".Pp_RaidPartyManager::SALLY_STATUS_RECOVER." ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			$affected_rows = $db->db->affected_rows();
			if( $affected_rows == 0 )
			{	// 更新レコードなし
				$error_detail = "updateSallyMember() is no affected_rows.";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			$columns = array(
				'party_id' => $party_id,
				'user_id' => $user_id,
				'status' => Pp_RaidPartyManager::MEMBER_STATUS_RECOVER,
				'disconn' => 0
			);
			$ret = $raid_search_m->renewTmpDataAfterPartyMemberUpdate( $columns );
			if( $ret === false )
			{	// エラー
				$error_detail = 'renewTmpDataAfterPartyMemberUpdate( array( '
							  . 'party_id => '.$party_id.','
							  . 'user_id => '.$user_id.','
							  . 'status => '.Pp_RaidPartyManager::MEMBER_STATUS_RECOVER.','
							  . 'disconn => 0 ))';
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
			$this->af->setApp( 'error_detail', $e->getMessage(), true );
			$this->af->setApp( 'result', 0, true );
		}
		return 'inapi_json';
	}
}
?>
