<?php
/**
 *	Api/Gacha/Photo/Exec.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_gacha_photo_exec Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiGachaPhotoExec extends Pp_ApiActionForm
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
		'c'
	);
}

/**
 *	api_gacha_photo_exec action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiGachaPhotoExec extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_gacha_photo_exec Action.
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
		return null;
	}

	/**
	 *	api_gacha_photo_exec action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからのデータを取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );			// サイコパスID
		$api_transaction_id = $this->getApiTransactionId();				// トランザクションID
		$gacha_id = $this->af->get( 'gacha_id' );						// 実行するガチャID

		if( empty( $pp_id ))
		{
			$pp_id = 918552204;
			$api_transaction_id = time();
			$gacha_id = 100001;
		}

		// マネージャのインスタンスを取得
		$photo_gacha_m =& $this->backend->getManager( 'PhotoGacha' );
		$photo_m =& $this->backend->getManager( 'Photo' );
		$present_m =& $this->backend->getManager( 'Present' );
		$item_m =& $this->backend->getManager( 'Item' );
		$user_m =& $this->backend->getManager( 'User' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$mission_m =& $this->backend->getManager( 'Mission' );
		$transaction_m =& $this->backend->getManager( 'Transaction' );

		// 多重処理防止チェック
		$json = $transaction_m->getResultJson( $api_transaction_id );
		if( !empty( $json ))
		{	// 既に一度処理している
			error_log( 'transaction!!' );
			$this->backend->logger->log( LOG_INFO, 'Found api_transaction_id.' );
			$temp = json_decode( $json, true );
			foreach( $temp as $k => $v )
			{
				error_log( "{$k}::{$v}");
				$this->af->setApp( $k, $v, true );
			}
			return 'api_json_encrypt';
		}

		// ユーザー基本情報を取得
		$user_base = $user_m->getUserBase( $pp_id );
		if( empty( $user_base ))
		{	// 取得エラー
			$this->logger->log( LOG_INFO, "getUserBase() error!: pp_id={$pp_id}" );
			$this->af->setApp( 'status_detail_code', SDC_USER_NONEXISTENCE, true );
			return 'error_500';
		}

		// フォトガチャマスタの取得
		$master_gacha = $photo_gacha_m->getMasterPhotoGacha( $gacha_id );
		if( empty( $master_gacha ))
		{	// 取得エラー
			$this->logger->log( LOG_INFO, "getMasterPhotoGacha() error!: gacha_id={$gacha_id}" );
			$this->af->setApp( 'status_detail_code', SDC_PHOTO_GACHA_ERROR, true );
			return 'error_500';
		}

		// 販売期間チェック
		$now = time();
		if(( $now < strtotime( $master_gacha['date_start'] ))||(( strtotime( $master_gacha['date_end'] ) < $now )))
		{	// 期間外
			$this->logger->log( LOG_INFO, "photo gacha closed: gacha_id={$gacha_id}" );
			$this->af->setApp( 'status_detail_code', SDC_PHOTO_GACHA_CLOSE, true );
			return 'error_500';
		}

		// ガチャBOX情報を取得
		$gacha_box = $photo_gacha_m->getPhotoGachaBox( $gacha_id );
		if( empty( $gacha_box ))
		{	// 取得できるフォトがない
			$this->logger->log( LOG_INFO, "getPhotoGachaBox() error!: gacha_id={$gacha_id}" );
			$this->af->setApp( 'status_detail_code', SDC_PHOTO_GACHA_ERROR, true );
			return 'error_500';
		}

		// 所有フォトフィルム数チェック
		$item = $item_m->getUserItem( $pp_id, Pp_ItemManager::ITEM_ID_PHOTO_FILM );
		if( Ethna::isError( $item ))
		{	// DB処理エラー
			$this->logger->log( LOG_INFO, "getUserItem() error!: pp_id={$pp_id}, item_id=".Pp_ItemManager::ITEM_ID_PHOTO_FILM );
			$this->af->setApp( 'status_detail_code', SDC_PHOTO_GACHA_ERROR, true );
			return 'error_500';
		}
		if( empty( $item )||( $item['num'] <= 0 ))
		{	// フォトフィルム不足
			$this->logger->log( LOG_INFO, "photo_film shortage" );
			$this->af->setApp( 'status_detail_code', SDC_PHOTO_GACHA_FILM_SHORTAGE, true );
			return 'error_500';
		}

		// 指定ガチャID全体の合計ウエイト
		$total_weight = $photo_gacha_m->getPhotoGachaBoxTotalWeight( $gacha_id );

		// 今回引くのは累計何回目のガチャか？
		$gacha_cnt_sum = 1;
		foreach( $gacha_box as $row )
		{
			$gacha_cnt_sum += intval( $row['gacha_cnt'] );
		}

		// ガチャの周回数を求める
		$lap = ceil( $gacha_cnt_sum / $total_weight );

		// 引けるアイテムで抽選BOXを作成
		$buff = array();
		foreach( $gacha_box as $row )
		{
			$remain = ( $lap * $row['weight'] ) - $row['gacha_cnt'];
			for( $i = 0; $i < $remain; $i++ )
			{
				$buff[] = $row['photo_id'];
			}
		}
		shuffle( $buff );

		$index = mt_rand( 0, ( count( $buff ) - 1 ));
		$photo_id = $buff[$index];

		// 引いたフォトのレベルが最大かどうか
		$lv_max_photo = $photo_m->getUserPhotoMaxLvByPhotoIds( $pp_id, array( $photo_id ));

		try
		{
			// トランザクション開始
			$db =& $this->backend->getDB();
			$db_cmn =& $this->backend->getDB( 'cmn' );
			$db_logex =& $this->backend->getDB( 'logex' );

			$db->begin();
			$db_cmn->begin();
			$db_logex->begin();

			// フォトフィルムを消費
			$ret = $item_m->updateUserItem( $pp_id, Pp_ItemManager::ITEM_ID_PHOTO_FILM, -1 );
			if( $ret !== true )
			{	// エラー
				$error_detail = "updateUserItem() error!: pp_id={$pp_id}, item_id=".Pp_ItemManager::ITEM_ID_PHOTO_FILM.", num=-1";
				throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
			}

			// フォトガチャ管理情報のドロップカウンターを加算
			$ret = $photo_gacha_m->incPhotoGachaDropCount( $gacha_id );
			if( !$ret || Ethna::isError( $ret ))
			{	// 更新エラー
				$error_detail = "incPhotoGachaDropCount() error!: gacha_id={$gacha_id}";
				throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
			}

			$ret = $photo_gacha_m->incPhotoGachaBoxCount( $gacha_id, $photo_id );
			if( !$ret || Ethna::isError( $ret ))
			{	// 更新エラー
				$error_detail = "incPhotoGachaBoxCount() error!: gacha_id={$gacha_id}, photo_id={$photo_id}";
				throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
			}

			if( empty( $lv_max_photo ))
			{	// 引いたフォトは最大LVではない
				// フォト所有情報を更新
				$ret = $photo_m->addUserPhoto( $pp_id, array( $photo_id ));
				if( !$ret )
				{	// 更新エラー
					$error_detail = "addUserPhoto() error!: gacha_id={$gacha_id}, photo_id={$photo_id}";
					throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
				}

				// 更新後の所有情報を取得
				$photo_info = $photo_m->getUserPhoto( $pp_id, $photo_id, true );
				if( !$photo_info )
				{	// 取得エラー
					$error_detail = "getUserPhoto() error!: gacha_id={$gacha_id}, photo_id={$photo_id}";
					throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
				}
				$photo_lv = $photo_info['photo_lv'];	// 獲得後のフォトLV
			}
			else
			{	// 引いたフォトは既に最大LV
				// プレゼントBOXにフォトフィルムを１枚送る
				$columns = array(
					'comment_id'       => Pp_PresentManager::COMMENT_PRESENT,	// 運営からのプレゼンツ！？
					'present_category' => Pp_PresentManager::CATEGORY_ITEM,		// プレゼントのカテゴリ
					'present_value'    => Pp_ItemManager::ITEM_ID_PHOTO_FILM,	// フォトフィルムのアイテムID
					'num'              => 1	// 配布数（１枚でいいのか？）
				);
				$present_id = $present_m->setUserPresent(
					$pp_id,
					0,
					$columns
				);
				if( Ethna::isError( $present_id ))
				{	// 更新エラー
					$error_detail = "setUserPresent() error!: pp_id={$pp_id}, photo_id={$photo_id}";
					throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
				}
				$photo_lv = Pp_PhotoManager::PHOTO_LV_MAX;	// 獲得後のフォトLVは最大のままで
			}

			// 新規のフォト獲得の場合、スペシャルエリアが解放される可能性があるのでチェック
			//error_log( "photo_lv = {$photo_lv}" );
			if( $photo_lv == 1 )
			{
				//error_log( "check release_sp_area" );
				$release_sp_area = $mission_m->checkSpAreaRelease( $pp_id, $photo_id );
				//error_log( "release_sp_area = ".print_r( $release_sp_area, true ));
				if( is_null( $release_sp_area ))
				{	// エラー
					$error_detail = "checkSpAreaRelease() error!: pp_id={$pp_id}, photo_id={$photo_id}";
					throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
				}
				if( !empty( $release_sp_area ))
				{	// 解放エリアあり
					foreach( $release_sp_area as $area_id )
					{
						$ret = $user_m->releaseNewArea( $pp_id, $area_id );
						if( $ret !== true )
						{
							$error_detail = "releaseNewArea() error!: pp_id={$pp_id}, area_id={$area_id}";
							throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
						}
					}
				}
			}

			// 最新のユーザーアイテム所持情報を取得
			$temp = $user_m->getUserItemList( $pp_id );
			if( Ethna::isError( $temp ))
			{
				$error_detail = "getUserItemList() error!: pp_id={$pp_id}";
				throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
			}
			$user_item = array();
			$used_num = 0;		// 使用後所持数初期化（所持数が０になった場合はgetUserItemList()の取得データには入らないので）
			foreach( $temp as $item_id => $v )
			{
				$user_item[] = array( 'item_id' => $item_id, 'item_num' => ( int )$v['num'] );
				if( $item_id == Pp_ItemManager::ITEM_ID_PHOTO_FILM )
				{	// フォトフィルムが残っているなら所持数を更新
					$used_num = ( int )$v['num'];
				}
			}

			// ガチャ実行ログを記録
			$columns = array(
				'pp_id' => $pp_id,									// サイコパスID
				'api_transaction_id' => $api_transaction_id,		// トランザクションID
				'gacha_id' => $gacha_id,							// 実行ガチャID
				'type' => $master_gacha['type'],					// 実行ガチャ種別
				'photo_id' => $photo_id,							// 獲得したフォトID
				'photo_lv' => $photo_lv								// 獲得後のフォトLV
			);
			$res = $logdata_m->logPhotoGacha( $columns );
			if( $res !== true )
			{
				$error_detail = "logPhotoGacha() error!";
				throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
			}

			// フォト獲得ログを記録
			$columns = array(
				'pp_id' => $pp_id,									// サイコパスID
				'api_transaction_id' => $api_transaction_id,		// トランザクションID
				'processing_type' => 'D02',							// 処理コード
				'photo_id' => $photo_id,							// 獲得したほとID
				'photo_lv' => $photo_lv								// 獲得後のほとLV
			);
			$res = $logdata_m->logPhoto( $columns );
			if( $res !== true )
			{
				$error_detail = "logPhoto() error!";
				throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
			}

			// アイテム（ほとひるむ）使用ログを記録
			$columns = array(
				'pp_id' => $pp_id,									// サイコパスID
				'api_transaction_id' => $api_transaction_id,		// トランザクションID
				'processing_type' => 'B01',							// 処理コード
				'device_type' => $user_base['device_type'],			// 端末種別
				'item_id' => Pp_ItemManager::ITEM_ID_PHOTO_FILM,	// 使ったアイテムID
				'count' => -1,										// 消費数
				'num' => $used_num,									// アイテム所持数
				'num_prev' => $item['num']							// アイテム所持数（消費前）
			);
			$res = $logdata_m->logItem( $columns );
			if( $res !== true )
			{
				$error_detail = "logItem() error!";
				throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
			}

			if( isset( $present_id ))
			{	// プレゼント情報を記録
				$columns = array(
					'pp_id' => $pp_id,
					'api_transaction_id' => $api_transaction_id,			// トランザクションID
					'processing_type' => 'C03',								// 処理コード
					'present_id' => $present_id,							// プレゼントID
					'present_category' => Pp_PresentManager::CATEGORY_ITEM,	// 配布物カテゴリ
					'present_value' => Pp_ItemManager::ITEM_ID_PHOTO_FILM,	// 配布物ID
					'num' => 1,												// 配布数
					'status' => Pp_PresentManager::STATUS_NEW,				// ステータス
					'comment_id' => Pp_PresentManager::COMMENT_PRESENT		// 配布コメント
				);
				$res = $logdata_m->logPresent( $columns );
				if( $res !== true )
				{
					$error_detail = "logPresent() error!";
					throw new Exception( $error_detail, SDC_PHOTO_GACHA_ERROR );
				}
			}


			$buff = array();

			$modify_photo = array(
				array(
					'photo_id' => $photo_id,	// 獲得したフォトのID
					'photo_lv' => $photo_lv		// 獲得後のフォトLV
				)
			);
			$buff['modify_photo'] = $modify_photo;

			$buff['user_item'] = $user_item;

			// 新規解放スペシャルエリア
			$send_release_sp_area = array();
			if( !empty( $release_sp_area ))
			{
				foreach( $release_sp_area as $area_id )
				{
					$send_release_sp_area[] = array( 'area_id' => $area_id );
				}
				$buff['release_sp_area'] = $send_release_sp_area;
			}

			// 処理結果をトランザクション情報として記録する
			$result_json = json_encode( $buff );		// JSON文字列にする
			$res = $transaction_m->registTransaction( $pp_id, $api_transaction_id, $result_json );
			if( $res !== true )
			{	// 記録エラー
				$err_msg = 'registTransaction error.';
				$err_code = SDC_PHOTO_GACHA_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// クライアントに返す情報を設定
			$this->af->setApp( 'modify_photo', $modify_photo, true );
			$this->af->setApp( 'user_item', $user_item, true );
			if( !empty( $send_release_sp_area ))
			{
				$this->af->setApp( 'release_sp_area', $send_release_sp_area, true );
			}

			// 更新情報をコミット
			$db->commit();
			$db_cmn->commit();
			$db_logex->commit();
		}
		catch( Exception $e )
		{
			$db->rollback();
			$db_cmn->rollback();
			$db_logex->rollback();
			$this->backend->logger->log( LOG_INFO, $e->getMessage());
			$this->af->setApp( 'status_detail_code', $e->getCode(), true );
			return 'error_500';
		}

		return 'api_json_encrypt';
	}
}
