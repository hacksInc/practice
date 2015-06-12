<?php
/**
 *	Api/Serial/Input.php
 *	シリアルコード入力
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_serial_input Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiSerialInput extends Pp_ApiActionForm
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
		'serial_code' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
//			'required'    => true,            // Required Option(true/false)
			'min'         => 0,               // Minimum value
			'max'         => 12,           // Maximum value
		),
	);
}

/**
 *	api_serial_input action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiSerialInput extends Pp_ApiActionClass
{
	private $code_type = null;
	private $master = null;
	
	/**
	 *	preprocess of api_serial_input Action.
	 *
	 *	@access public
	 *	@return string	  forward name(null: success.
	 *								  false: in case you want to exit.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{
			return 'error_400';
		}
		
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$serial_code = $this->af->get( "serial_code" );
		
		$serial_m =& $this->backend->getManager( "Serial" );
		
		// シリアルコードの入力がない
		if( empty( $serial_code ))
		{
			$this->af->setApp( 'status_detail_code', SDC_SERIAL_INVALID_CODE, true );
			return 'error_500';
		}

		// 共通コードかそうでないかを判定
		$common = $serial_m->getMasterSerialByCommonCode( $serial_code );
		
		if ( Ethna::isError( $common ) ) {
			$this->af->setApp( 'status_detail_code', SDC_SERIAL_INVALID_CODE, true );
			return 'error_500';
		}
		
		if ( !isset( $common['common_code'] ) ) {
			// ユニークコード
			$code = $serial_m->getSerialUnique( $serial_code, "db_cmn" );
			
			if ( !$code || Ethna::isError( $code ) ) {
				$this->af->setApp( 'status_detail_code', SDC_SERIAL_INVALID_CODE, true );
				return 'error_500';
			}
			
			// 既に使われていたらエラー
			if ( $code['pp_id'] > 0 ) {
				$this->af->setApp( 'status_detail_code', SDC_SERIAL_USED_CODE, true );
				return 'error_500';
			}
			
			$this->code_type = 1;
			$this->master = $serial_m->getMasterSerial( $code['campaign_id'] );
			
			if ( !$this->master || Ethna::isError( $this->master ) ) {
				return 'error_500';
			}
		} else {
			// 共通コード
			// そも有効期限外は使用不可
			$date = date( "Y-m-d H:i:s" );
			if ( $date < $common['date_open'] || $common['date_close'] < $date ) {
				$this->af->setApp( 'status_detail_code', SDC_SERIAL_CAMPAIGN_ERROR, true );
				return 'error_500';
			}
			
			$code = $serial_m->getSerialCommon( $pp_id, $common['campaign_id'], "db_cmn" );
			
			if ( ( $code === false ) || Ethna::isError( $code ) ) {
				$this->af->setApp( 'status_detail_code', SDC_SERIAL_INVALID_CODE, true );
				return 'error_500';
			}
			
			if ( isset( $code['campaign_id'] ) ) {
				$this->af->setApp( 'status_detail_code', SDC_SERIAL_USED_CODE, true );
				return 'error_500';
			}
			
			$this->code_type = 2;
			$this->master = $common;
		}
		
		return null;
	}

	/**
	 *	api_serial_input action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		$api_transaction_id = $this->getApiTransactionId();

		$serial_code = $this->af->get( "serial_code" );
		
		$present_m =& $this->backend->getManager( "Present" );
		$serial_m =& $this->backend->getManager( "Serial" );
		$logdata_m =& $this->backend->getManager( "Logdata" );
		$trans_m =& $this->backend->getManager( "Transaction" );
		
		// 多重処理防止チェック
		$json = $trans_m->getResultJson( $api_transaction_id );
		if( !empty( $json ))
		{	// 既に一度処理している
			$this->backend->logger->log( LOG_INFO, 'Found api_transaction_id.' );
			$temp = json_decode( $json, true );
			foreach( $temp as $k => $v )
			{
				$this->af->setApp( $k, $v, true );
			}
			return 'api_json_encrypt';
		}

		$db =& $this->backend->getDB();
		$db_cmn =& $this->backend->getDB( "cmn" );
		$db_logex =& $this->backend->getDB( "logex" );
		
		$db->begin();
		$db_cmn->begin();
		$db_logex->begin();
		
		// プレゼント配布
		$columns = array(
			"comment_id"		=> Pp_PresentManager::COMMENT_SERIALCODE,
			"present_category"	=> $this->master['prize_category'],
			"present_value"		=> $this->master['prize_id'],
			"num"				=> $this->master['num'],
		);
		$present_id = $present_m->setUserPresent( $pp_id, Pp_PresentManager::ID_NEW_PRESENT, $columns );
		

		if ( !$present_id || Ethna::isError( $present_id ) ) {
			$db_logex->rollback();
			$db_cmn->rollback();
			$db->rollback();
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		
		switch ( $this->code_type ) {
			case 1:	// ユニークコード
				$result = $serial_m->updateSerialUnique( $pp_id, $serial_code );
				break;
				
			case 2:	// 共通コード
				$result = $serial_m->insertSerialCommon( $pp_id, $this->master['campaign_id'] );
				break;
		}
		
		if ( !$result || Ethna::isError( $result ) ) {
			$db_logex->rollback();
			$db_cmn->rollback();
			$db->rollback();
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		
		$columns = array(
			'pp_id' => $pp_id,
			'api_transaction_id' => $api_transaction_id,			// トランザクションID
			'processing_type' => 'C05',								// 処理コード
			'present_id' => $present_id,							// プレゼントID
			'present_category' => $this->master['prize_category'],	// 配布物カテゴリ
			'present_value' => $this->master['prize_id'],			// 配布物ID
			'num' => $this->master['num'],							// 配布数
			'status' => Pp_PresentManager::STATUS_NEW,				// ステータス
			'comment_id' => Pp_PresentManager::COMMENT_SERIALCODE	// 配布コメント
		);
		$res = $logdata_m->logPresent( $columns );
		if ( !$res || Ethna::isError( $res ) ) {
			$db_logex->rollback();
			$db_cmn->rollback();
			$db->rollback();
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		
		$serial_data = array(
			"campaign_text"		=> $this->master['name_ja'],
			"item_id"			=> $this->master['prize_id'],
			"item_num"			=> $this->master['num'],
			"prize_category"	=> $this->master['prize_category'],
		);
		
		// ct_user_presentの情報を取得
		$ret = $present_m->deleteMaxOverUserPresent( $pp_id );
		if(( $ret === false )||( Ethna::isError( $ret )))
		{
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		$ret = $present_m->deleteExpiredUserPresent( $pp_id );
		if( Ethna::isError( $ret ))
		{
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		$data = $present_m->getUserPresentList( $pp_id );
		if( is_null( $data ) || ( $data === false ) )
		{
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		$user_box = $present_m->convertUserBox( $data );
		
		// トランザクション情報を記録
		$transaction_json = array(
			'serial_data' => array( $serial_data ),
			'user_box' => $user_box
		);
		$result = $trans_m->registTransaction( $pp_id, $api_transaction_id, json_encode( $transaction_json ) );
		if ( !$result || Ethna::isError( $result ) ) {
			$db_logex->rollback();
			$db_cmn->rollback();
			$db->rollback();
			return 'error_500';
		}
		
		$db->commit();
		$db_logex->commit();
		$db_cmn->commit();
		
		// 取得したデータをクライアントに返す
		$this->af->setApp( "serial_data", array( $serial_data ), true );
		$this->af->setApp( 'user_box', $user_box, true );
		
		return 'api_json_encrypt';
	}
}
