<?php
/**
 *  Inapi/Raid/Chat/Log.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';

/**
 *  inapi_raid_chat_log Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidChatLog extends Pp_InapiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id',
		'user_id',
		'stamp_id'
    );
}

/**
 *  Inapi_raid_chat_log action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidChatLog extends Pp_InapiActionClass
{
	/**
	 *  preprocess of api_raid_chat_log Action.
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
	 *  api_raid_chat_log action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$party_id = $this->af->get( 'party_id' );
		$user_id = $this->af->get( 'user_id' );
		$stamp_id = $this->af->get( 'stamp_id' );

		// マネージャのインスタンスを取得
		$raid_chat_m =& $this->backend->getManager( 'RaidChat' );

		//-------------------------------------------------------------
		//	発言をログに追加
		//-------------------------------------------------------------
		$ret = $raid_chat_m->logChat( $party_id, $user_id, $stamp_id );
		if( $ret === true )
		{	// 正常終了
			$this->af->setApp( 'result', 1, true );
		}
		else
		{	// エラー
			$error_detail = "logChat( ".$party_id.",".$user_id.",".$stamp_id." )";
			$this->af->setApp( 'status_detail_code', SDC_INAPI_DB_ERROR, true );
			$this->af->setApp( 'error_detail', $error_detail, true );
			$this->af->setApp( 'result', 0, true );
		}
		return 'inapi_json';
	}
}
?>
