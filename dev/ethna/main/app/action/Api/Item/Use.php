<?php
/**
 *	Api/Item/Use.php
 *	アイテム使用
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_item_use Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiItemUse extends Pp_ApiActionForm
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
		'api_transaction_id',
		'item_id',
		'num' => array(	// 現状1以上が指定されても対応する処理がないので1固定
			// Form definition
			'type'        => VAR_TYPE_INT,    // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 1,            // Maximum value
		),
		'item_therapy' => array(
			// Form definition
			'type'        => array( VAR_TYPE_INT ),    // Input type

			//  Validator (executes Validator by written order.)
			'required'    => false,            // Required Option(true/false)
			'itemtherapy' => true,
		),
		'item_drone' => array(
			// Form definition
			'type'        => array( VAR_TYPE_INT ),    // Input type

			//  Validator (executes Validator by written order.)
			'required'    => false,            // Required Option(true/false)
		),
	);
}

/**
 *	api_item_use action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiItemUse extends Pp_ApiActionClass
{
	protected $item_data_array = null;
	
	/**
	 *	preprocess of api_item_use Action.
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
		
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		//$transaction_id = $this->af->get( "api_transaction_id" );
		$transaction_id = $this->getApiTransactionId();
		$item_id = $this->af->get( "item_id" );
		$num = $this->af->get( "num" );
		

		if(empty($pp_id))
		{
			//$pp_id = 915694803;
			$pp_id = 916150486;
			$item_id = Pp_ItemManager::ITEM_ID_THERAPY_TICKET;
			$transaction_id = time();
			$num = 1;
		}

		$item_m =& $this->backend->getManager( "Item" );
		$character_m =& $this->backend->getManager( "Character" );
		$transaction_m =& $this->backend->getManager( "Transaction" );
		
		//	定時ストレスケア処理を実行
		$res = $character_m->stressCare( $pp_id, $transaction_id );
		if( Ethna::isError( $res ))
		{
			$this->backend->logger->log( LOG_ERR, 'fixed stress care error.' );
			$this->af->setApp( 'status_detail_code', SDC_FIXED_STRESS_CARE_ERROR, true );
			return 'error_500';
		}

		switch ( $item_id ) {
			case Pp_ItemManager::ITEM_ID_THERAPY_TICKET:	// セラピー受診命令書
				$temp = $this->af->get( "item_therapy" );
				$this->item_data_array = $temp[0];
				break;
				
			case Pp_ItemManager::ITEM_ID_DRONE:	// 巡査ドローン
				$temp = $this->af->get( "item_drone" );
				$this->item_data_array = $temp[0];
				break;
				
			default:
				$this->item_data_array = array();
				break;
		}
		
		if ( !is_array( $this->item_data_array ) ) {
			return 'error_500';
		}
		
		// トランザクションチェック。完了してたら値を取得してそのまま返す
		$json = $transaction_m->getResultJson( $transaction_id );
		
		if ( !empty( $json )) {
			$json = json_decode( $json, 1 );
			
			$this->af->setApp( "user_item", $json['user_item'], true );
			
			switch ( $item_id ) {
				case Pp_ItemManager::ITEM_ID_THERAPY_TICKET:	// セラピー受診命令書
					$this->af->setApp( "item_therapy", $json['item_therapy'], true );
					break;
					
				case Pp_ItemManager::ITEM_ID_DRONE:	// 巡査ドローン
					$this->af->setApp( "item_drone", $json['item_drone'], true );
					break;
					
				default:
					break;
			}
			
			return 'api_json_encrypt';
		}
		
		// 個数チェック
		$item = $item_m->getUserItem( $pp_id, $item_id, "db" );
		
		if ( !$item || Ethna::isError( $item ) || $item['num'] < $num ) {
			$err_code = array(
				Pp_ItemManager::ITEM_ID_THERAPY_TICKET => SDC_THERAPY_ORDER_SHORTAGE,
				Pp_ItemManager::ITEM_ID_DRONE => SDC_MISSION_DRONE_SHORTAGE,
				Pp_ItemManager::ITEM_ID_RESERVE_DOMINATOR => SDC_MISSION_NO_SPARE_DOMINATOR,
				Pp_ItemManager::ITEM_ID_PHOTO_FILM => SDC_PHOTO_GACHA_FILM_SHORTAGE
			);
			$this->af->setApp( 'status_detail_code', $err_code[$item_id], true );
			return 'error_500';
		}
		
		return null;
	}

	/**
	 *	api_item_use action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$item_id = $this->af->get( "item_id" );
		$num = $this->af->get( "num" );
		//$transaction_id = $this->af->get( "api_transaction_id" );
		$transaction_id = $this->getApiTransactionId();
		
		$item_m =& $this->backend->getManager( "Item" );
		$chara_m =& $this->backend->getManager( "Character" );
		$user_m =& $this->backend->getManager( "User" );
		$transaction_m =& $this->backend->getManager( "Transaction" );
		$logdata_m =& $this->backend->getManager( "Logdata" );
		
		$db =& $this->backend->getDB();
		
		// ユーザー基本情報を取得
		$user_base = $user_m->getUserBase( $pp_id );
		if( empty( $user_base ))
		{	// 取得エラー
			$this->logger->log( LOG_INFO, "getUserBase() error!: pp_id={$pp_id}" );
			$this->af->setApp( 'status_detail_code', SDC_USER_NONEXISTENCE, true );
			return 'error_500';
		}

		// 使用前情報を取得しておく
		$item_prev = $item_m->getUserItem( $pp_id, $item_id );
		if( empty( $item_prev ) || Ethna::isError( $item_prev ))
		{
			return 'error_500';
		}
		if( $item_id == Pp_ItemManager::ITEM_ID_DRONE )
		{	// 巡査ドローン使用ならエリア情報を取得しておく
			$area_prev = $user_m->getUserArea( $pp_id, $this->item_data_array['area_id'], "db" );
			if( empty( $area_prev ) || Ethna::isError( $area_prev ))
			{
				return 'error_500';
			}
		}
		else if( $item_id == Pp_ItemManager::ITEM_ID_THERAPY_TICKET )
		{	// セラピー受診命令書ならキャラクター情報を取得
			if( $this->item_data_array['chara_id'] == Pp_CharacterManager::CHARACTER_ID_PLAYER )
			{	// プレイヤーキャラ
				$chara_prev = $user_m->getUserGame( $pp_id );
			}
			else
			{	// サポートキャラ
				$chara_prev = $chara_m->getUserCharacter( $pp_id, $this->item_data_array['chara_id'] );
			}
			if( empty( $chara_prev ) || Ethna::isError( $chara_prev ))
			{
				return 'error_500';
			}
		}

		// トランザクション開始
		$transaction_json = array();
		$db->begin();
		
		$result = $item_m->useItem( $pp_id, $item_id, $this->item_data_array, $num, $result_data, $result_code );
		if ( !$result || Ethna::isError( $result ) ) {
			$db->rollback();
			if ( !is_null( $result_code ) ) $this->af->setApp( 'status_detail_code', $result_code, true );
			return 'error_500';
		}
		
		$item_list = $item_m->getUserItemList( $pp_id, "db" );
		
		switch ( $item_id ) {
			case Pp_ItemManager::ITEM_ID_THERAPY_TICKET:	// セラピー受診命令書
				$this->af->setApp( "item_therapy", array( $result_data ), true );
				$transaction_json['item_therapy'][] = $result_data;

				$columns = array(
					'pp_id' => $pp_id,
					'api_transaction_id' => $transaction_id,
					'processing_type' => 'A05',
					'character_id' => $result_data['chara_id'],
					'crime_coef' => $result_data['crime_coef'],
					'crime_coef_prev' => $chara_prev['crime_coef'],
					'body_coef' => $chara_prev['body_coef'],
					'body_coef_prev' => $chara_prev['body_coef'],
					'intelli_coef' => $chara_prev['intelli_coef'],
					'intelli_coef_prev' => $chara_prev['intelli_coef'],
					'mental_coef' => $chara_prev['mental_coef'],
					'mental_coef_prev' => $chara_prev['mental_coef'],
					'ex_stress_care' => $chara_prev['ex_stress_care'],
					'ex_stress_care_prev' => $chara_prev['ex_stress_care']
				);
				$res = $logdata_m->logCharacter( $columns );
				break;
				
			case Pp_ItemManager::ITEM_ID_DRONE:	// 巡査ドローン
				$this->af->setApp( "item_drone", array( $result_data ), true );
				$transaction_json['item_drone'][] = $result_data;

				$columns = array(
					'pp_id' => $pp_id,
					'api_transaction_id' => $transaction_id,
					'processing_type' => 'E02',
					'area_id' => $result_data['area_id'],
					'area_stress' => $result_data['area_stress'],
					'area_stress_prev' => $area_prev['area_stress'],
					'status' => $area_prev['status'],
					'status_prev' => $result_data['status']
				);
				$res = $logdata_m->logArea( $columns );
				break;
				
			default:
				break;
		}
		
		// 整形
		$used_num = 0;
		$user_item = array();
		foreach ( $item_list as $row ) {
			$user_item[] = array(
				"item_id"	=> $row['item_id'],
				"item_num"	=> $row['num'],
			);
			if( $row['item_id'] == $item_id )
			{
				$used_num = $row['num'];
			}
		}
		
		$transaction_json['user_item'] = $user_item;
		
		// トランザクション情報を記録
		$result = $transaction_m->registTransaction( $pp_id, $transaction_id, json_encode( $transaction_json ) );
		if ( !$result || Ethna::isError( $result ) ) {
			$db->rollback();
			return 'error_500';
		}
		
		// アイテム使用履歴を記録
		$columns = array(
			'pp_id' => $pp_id,								// サイコパスID
			'api_transaction_id' => $transaction_id,		// トランザクションID
			'device_type' => $user_base['device_type'],		// 端末種別
			'processing_type' => 'B02',						// 処理コード
			'item_id' => $item_id,							// 使ったアイテムID
			'count' => -( $num ),							// 消費数
			'num' => $used_num,								// アイテム所持数
			'num_prev' => $item_prev['num']					// アイテム所持数（消費前）
		);
		$res = $logdata_m->logItem( $columns );

		// トランザクション終了
		$db->commit();
		
		$this->af->setApp( "user_item", $user_item, true );
		
		return 'api_json_encrypt';
	}
}
