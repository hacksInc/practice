<?php
/**
 *	Api/Gacha/Photo/List.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_gacha_photo_list Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiGachaPhotoList extends Pp_ApiActionForm
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
 *	api_gacha_photo_list action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiGachaPhotoList extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_gacha_photo_list Action.
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
	 *	api_gacha_photo_list action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );

		// マネージャのインスタンスを取得
		$photo_gacha_m =& $this->backend->getManager( 'PhotoGacha' );
		$user_m =& $this->backend->getManager( 'User' );

		// クリア済みミッションIDの取得
		$mission_ids = $user_m->getClearedMissionIdList( $pp_id );
		if( is_null( $mission_ids ))
		{	// 取得エラー
			return 'error_500';
		}

		if( !empty( $mission_ids ))
		{
			$gacha_list = $photo_gacha_m->getMasterPhotoGachaAvailable( $mission_ids );
		}

		// 返却用データの生成
		$buff = array();
		if( !empty( $gacha_list ))
		{
			foreach( $gacha_list as $v )
			{
				$buff[] = array(
					'gacha_id' => $v['gacha_id'],	// ガチャID
					'stage_id' => $v['stage_id'],	// 対象ステージID
					'type' => $v['type'],			// ガチャ種別
					'price' => $v['price']			// 価格（使用するフォトフィルムの数）
				);
			}
		}

		$this->af->setApp( 'gacha_list', $buff, true );

		return 'api_json_encrypt';
	}
}
