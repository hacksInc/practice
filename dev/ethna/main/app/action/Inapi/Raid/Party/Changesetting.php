<?php
/**
 *  Inapi/Raid/Party/Changesetting.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_changesetting Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyChangesetting extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',
		'auto_join',
		'play_style',
		'pass'
    );
}

/**
 *  Inapi_raid_party_changesetting action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyChangesetting extends Pp_InapiActionClass
{
	/**
	 *  preprocess of api_raid_party_changesetting Action.
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
	 *  api_raid_party_changesetting action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$party_id = $this->af->get( 'party_id' );
		$auto_join = $this->af->get( 'auto_join' );
		$play_style = $this->af->get( 'play_style' );
		$pass = $this->af->get( 'pass' );

		if((( $auto_join != 0 )&&( empty( $pass ) === false ))||
		   (( $auto_join == 0 )&&( empty( $pass ) === true )))
		{	// 自動入室可＆パスワードがあり or 自動入室不可＆パスワードなし
			$this->af->setApp( 'error_detail', 'auto_join/pass error!', true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_ARGUMENT_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}

		// マネージャのインスタンスを取得
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );
		$raid_search_m =& $this->backend->getManager( 'RaidSearch' );

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			//-------------------------------------------------------------
			//	パーティ情報を更新
			//-------------------------------------------------------------
			$columns = array(
				'entry_passwd' => $pass,
				'play_style' => $play_style
			);
			$ret = $raid_party_m->updateParty( $party_id, $columns );
			if(( !$ret )||( Ethna::isError( $ret )))
			{	// 更新エラー
				$error_detail = "updateParty( $party_id, array( '$pass', $play_style ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			//-------------------------------------------------------------
			//	パーティ検索用テーブルを更新
			//-------------------------------------------------------------
			$party = $raid_party_m->getParty( $party_id, false, true );
			if( is_null( $party ) === true )
			{	// エラー
				$error_detail = "getParty( $party_id, false, true ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			if( empty( $party ) === true )
			{	// パーティ情報が取得できない
				$error_detail = "getParty( $party_id, false, true ))";
				$detail_code = SDC_RAID_PARTYID_INVALID;
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
			$this->af->setApp( 'master_user_id', $party['master_user_id'], true );
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
			$this->af->setApp( 'detail', $e->getMessage(), true );
			$this->af->setApp( 'result', 0, true );
		}
		return 'inapi_json';
	}
}
?>
