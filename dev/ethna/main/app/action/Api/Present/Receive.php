<?php
/**
 *	Api/Present/Receive.php
 *	プレゼント受け取り
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_present_receive Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiPresentReceive extends Pp_ApiActionForm
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
		'present_id' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => null,           // Maximum value
		),
	);
}

/**
 *	api_present_receive action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiPresentReceive extends Pp_ApiActionClass
{
	private $present = null;
	
	/**
	 *	preprocess of api_present_receive Action.
	 *
	 *	@access public
	 *	@return string	  forward name(null: success.
	 *								  false: in case you want to exit.)
	 */
	function prepare()
	{
		if ( $this->af->validate() > 0 )
		{
			return 'error_400';
		}
		
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		$api_transaction_id = $this->getApiTransactionId();

		$present_id = str_replace( '"', '', $this->af->get( "present_id" ) );

		$item_m =& $this->backend->getManager( "Item" );
		$present_m =& $this->backend->getManager( "Present" );
		$transaction_m =& $this->backend->getManager( 'Transaction' );

		// 多重処理防止チェック
		$json = $transaction_m->getResultJson( $api_transaction_id );
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

		// 受け取ったデータを配列に整形
		$present_id = explode( ",", $present_id );
		
		// 対象が受け取り済みかどうかチェック
		// ひとつでも受け取り済みだったり、他人のデータが含まれていたら処理しない（偽装対策）
		// 件数が異なる場合も処理しない（削除されたデータをリクエストしている可能性がある）
		// あとアイテム受け取りの場合は個数計算する。上限以上になる場合は受け取らない（受け取らないが、アイテム以外の受領処理は行う）
		$list = $present_m->getUserPresentListByPresentIds( $present_id );
		
		if ( count( $list ) != count( $present_id ) ) {
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		
		$m_item = $item_m->getMasterItemList();
		$u_item = $item_m->getUserItemList( $pp_id, "db" );
		
		foreach ( $list as $key => $row ) {
			if ( $row['status'] != Pp_PresentManager::STATUS_NEW ) {
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'error_500';
			}
			
			if ( $row['pp_id'] != $pp_id ) {
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'error_500';
			}
			
			$list[$key]['receive'] = true;
			
			if ( $row['present_category'] == Pp_PresentManager::CATEGORY_ITEM ) {
				// 受け取ったら最大数が超えないかチェック
				if ( isset( $u_item[$row['present_value']] )) {
					// ユーザーアイテムのレコードが存在する場合
					if ( $u_item[$row['present_value']]['num'] + $row['num'] > $m_item[$row['present_value']]['maximum'] ) {
						$list[$key]['receive'] = false;			// 受け取ると最大所持数を超えちゃう
					}
				} else {
					// ユーザーアイテムのレコードが存在しない場合
					if ( $row['num'] > $m_item[$row['present_value']]['maximum'] ) {
						$list[$key]['receive'] = false;			// 配布数が多すぎて受け取れない
					}
				}
			}
		}
		
		$this->present = $list;
		return null;
	}

	/**
	 *	api_present_receive action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		$api_transaction_id = $this->getApiTransactionId();

		// マネージャのインスタンスを取得
		$user_m =& $this->backend->getManager( "User" );
		$item_m =& $this->backend->getManager( "Item" );
		$photo_m =& $this->backend->getManager( "Photo" );
		$puser_m =& $this->backend->getManager( "PortalUser" );
		$present_m =& $this->backend->getManager( 'Present' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$mission_m =& $this->backend->getManager( 'Mission' );
		$transaction_m =& $this->backend->getManager( 'Transaction' );

		$db_cmn =& $this->backend->getDB( "cmn" );
		$db =& $this->backend->getDB();
		
		$user_base = $user_m->getUserBase( $pp_id );
		if ( empty( $user_base ))
		{
			$this->af->setApp( 'status_detail_code', SDC_USER_NONEXISTENCE, true );
			return 'error_500';
		}

		// トランザクション開始
		$db_cmn->begin();
		$db->begin();
		
		$modify_photo_ids = array();
		$release_sp_area = array();
		
		foreach ( $this->present as $row ) {
			if ( !$row['receive'] ) continue;
			
			$ret = $present_m->changePresentStatus( $row['present_id'], Pp_PresentManager::STATUS_RECEIVE );
			if ( !$ret || Ethna::isError( $ret ) ) {
//error_log( 1 );
				$db_cmn->rollback();
				$db->rollback();
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'error_500';
			}
			
			// カテゴリーごとに処理を変更
			switch ( $row['present_category'] ) {
				case Pp_PresentManager::CATEGORY_ITEM:
					$before = $user_m->getUserItem( $pp_id, $row['present_value'] );
					if( Ethna::isError( $before ))
					{
						$db_cmn->rollback();
						$db->rollback();
						$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
						return 'error_500';
					}
					$result = $item_m->updateUserItem( $pp_id, $row['present_value'], $row['num'] );
					$after = $user_m->getUserItem( $pp_id, $row['present_value'] );
					if( empty( $after ) || Ethna::isError( $after ))
					{
						$db_cmn->rollback();
						$db->rollback();
						$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
						return 'error_500';
					}
					break;
					
				case Pp_PresentManager::CATEGORY_PHOTO:
					$modify_photo_ids[] = $row['present_value'];
					$photo_id = $row['present_value'];

					$lv_max_photo = $photo_m->getUserPhotoMaxLvByPhotoIds( $pp_id, array( $photo_id ));
					if( empty( $lv_max_photo ))
					{	// 獲得したフォトは最大レベルではない
						$result = $photo_m->addUserPhoto( $pp_id, array( $photo_id ) );
						if( $result !== true )
						{
							$db_cmn->rollback();
							$db->rollback();
							$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
							return 'error_500';
						}
						$after = $photo_m->getUserPhoto( $pp_id, $photo_id, true );
						if( empty( $after ) || Ethna::isError( $after ))
						{
							$db_cmn->rollback();
							$db->rollback();
							$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
							return 'error_500';
						}
						$photo_lv = $after['photo_lv'];	// 獲得後のフォトLV
					}
					else
					{	// 既に最大レベル
						$columns = array(
							'comment_id'       => Pp_PresentManager::COMMENT_PRESENT,	// 運営からのプレゼンツ！？
							'present_category' => Pp_PresentManager::CATEGORY_ITEM,		// プレゼントのカテゴリ
							'present_value'    => Pp_ItemManager::ITEM_ID_PHOTO_FILM,	// フォトフィルムのアイテムID
							'num'              => 1										// 配布数
						);
						$photo_film_present_id = $present_m->setUserPresent(
							$pp_id,
							0,
							$columns
						);
						if( Ethna::isError( $photo_film_present_id ))
						{	// 更新エラー
							$db_cmn->rollback();
							$db->rollback();
							$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
							return 'error_500';
						}
						$photo_lv = Pp_PhotoManager::PHOTO_LV_MAX;
					}

					if( $photo_lv == 1 )
					{	// 獲得したフォトが新規の場合のみ
						$release = $mission_m->checkSpAreaRelease( $pp_id, $photo_id );
						if( is_null( $release ))
						{	// エラー
							$db_cmn->rollback();
							$db->rollback();
							$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
							return 'error_500';
						}
						if( !empty( $release ))
						{	// 解放エリアあり
							foreach( $release as $area_id )
							{
								$ret = $user_m->releaseNewArea( $pp_id, $area_id );
								if( $ret !== true )
								{
									$db_cmn->rollback();
									$db->rollback();
									$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
									return 'error_500';
								}
							}
						}
					}
					break;
					
				case Pp_PresentManager::CATEGORY_PP:
					$result = $puser_m->addPoint( $pp_id, $row['num'], "ゲームクライアント：プレゼントによる取得" );
					break;
			}
			
			if ( !$result || Ethna::isError( $result ) ) {
				$db_cmn->rollback();
				$db->rollback();
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'error_500';
			}

			// プレゼントBOX情報履歴を記録
			$columns = array(
				'pp_id' => $pp_id,
				'api_transaction_id' => $api_transaction_id,
				'processing_type' => 'C04',
				'present_id' => $row['present_id'],
				'present_category' => $row['present_category'],
				'present_value' => $row['present_value'],
				'num' => $row['num'],
				'status' => Pp_PresentManager::STATUS_RECEIVE,
				'comment_id' => $row['comment_id']
			);
			$res = $logdata_m->logPresent( $columns );
			if( $res !== true )
			{
				$db_cmn->rollback();
				$db->rollback();
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'error_500';
			}

			if( $row['present_category'] == Pp_PresentManager::CATEGORY_ITEM )
			{	// アイテム履歴の記録
				$columns = array(
					'pp_id' => $pp_id,											// サイコパスID
					'api_transaction_id' => $api_transaction_id,				// トランザクションID
					'item_id' => $row['present_value'],							// 配布物ID
					'processing_type' => 'B04',									// 処理コード
					'device_type' => $user_base['device_type'],					// 端末種別
					'count' => $row['num'],										// 増減数
					'num' => $after['num'],										// 増減後の所持数
					'num_prev' => (( empty( $before )) ? 0 : $before['num'] )	// 増減前の所持数
				);
				$res = $logdata_m->logItem( $columns );
			}
			else if( $row['present_category'] == Pp_PresentManager::CATEGORY_PHOTO )
			{	// フォト獲得の記録
				$columns = array(
					'pp_id' => $pp_id,
					'api_transaction_id' => $api_transaction_id,
					'processing_type' => 'D03',
					'photo_id' => $row['present_value'],
					'photo_lv' => $after['photo_lv']
				);
				$res = $logdata_m->logPhoto( $columns );

				if( isset( $photo_film_present_id ))
				{	// 獲得フォトLV最大でのフォトフィルムのプレゼント情報を記録
					$columns = array(
						'pp_id' => $pp_id,
						'api_transaction_id' => $api_transaction_id,			// トランザクションID
						'processing_type' => 'C04',								// 処理コード
						'present_id' => $photo_film_present_id,					// プレゼントID
						'present_category' => Pp_PresentManager::CATEGORY_ITEM,	// 配布物カテゴリ
						'present_value' => Pp_ItemManager::ITEM_ID_PHOTO_FILM,	// 配布物ID
						'num' => 1,												// 配布数
						'status' => Pp_PresentManager::STATUS_NEW,				// ステータス
						'comment_id' => Pp_PresentManager::COMMENT_PRESENT		// 配布コメント
					);
					$res = $logdata_m->logPresent( $columns );
				}
			}
		}
		
		
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
		$user_present = $present_m->getUserPresentList( $pp_id );
		if( is_null( $user_present ) || ( $user_present === false ))
		{
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		

		$buff = array();

		$user_item = $item_m->getUserItemList( $pp_id, "db" );
		
		$user_portal = $puser_m->getUserBase( $pp_id, "db" );
		
		if ( count( $modify_photo_ids ) > 0 ) {
			$user_photo = $photo_m->getUserPhotoByPhotoIds( $pp_id, $modify_photo_ids, "db" );
			
			$buff['modify_photo'] = $user_photo;
			$this->af->setApp( "modify_photo", $user_photo, true );
		}
		
		$item_send = array();
		foreach ( $user_item as $key => $row ) {
			$item_send[] = array(
				"item_id"	=> $row['item_id'],
				"item_num"	=> $row['num'],
			);
		}
		
		$user_box = $present_m->convertUserBox( $user_present );

		$buff['user_box'] = $user_box;
		$buff['user_item'] = $item_send;
		$buff['portal_point'] = $user_portal['point'];

		// 処理結果をトランザクション情報として記録する
		$result_json = json_encode( $buff );
		$res = $transaction_m->registTransaction( $pp_id, $api_transaction_id, $result_json );
		if( $res !== true )
		{	// 記録エラー
			$err_msg = "error: registTransaction({$pp_id}, {$api_transaction_id}, {$result_json})";
			$err_code = SDC_DB_ERROR;
			throw new Exception( $err_msg, $err_code );
		}

		// 取得したデータをクライアントに返す
		$this->af->setApp( 'user_box', $user_box, true );
		$this->af->setApp( "user_item", $item_send, true );
		$this->af->setApp( "portal_point", $user_portal['point'], true );

		// トランザクション完了
		$db->commit();
		$db_cmn->commit();

		return 'api_json_encrypt';
	}
}
