<?php
/**
 *	Api/Character/List.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_character_list Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiCharacterList extends Pp_ApiActionForm
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
 *	api_character_list action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiCharacterList extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_character_list Action.
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
	 *	api_character_list action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		//--------------------------------------------------------------------------
		//	クライアントからの引数を取得
		//--------------------------------------------------------------------------
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		$api_transaction_id = $this->getApiTransactionId();

		//--------------------------------------------------------------------------
		//	マネージャのインスタンスを取得
		//--------------------------------------------------------------------------
		$character_m =& $this->backend->getManager( 'Character' );

		//--------------------------------------------------------------------------
		//	定時ストレスケア処理を実行
		//--------------------------------------------------------------------------
		$res = $character_m->stressCare( $pp_id, $api_transaction_id );
		if( Ethna::isError( $res ))
		{
			$this->backend->logger->log( LOG_ERR, 'fixed stress care error.' );
			$this->af->setApp( 'status_detail_code', SDC_FIXED_STRESS_CARE_ERROR, true );
			return 'error_500';
		}

		//--------------------------------------------------------------------------
		//	サポートキャラ情報を取得
		//--------------------------------------------------------------------------
		$character = $character_m->getUserCharacter( $pp_id );
		if( is_null( $character ) || ( $character === false ))
		{	// 取得エラー
			return 'error_500';
		}

		//--------------------------------------------------------------------------
		//	返却用データの生成
		//--------------------------------------------------------------------------
		$buff = array();
		if( !empty( $character ))
		{
			foreach( $character as $v )
			{
				$buff[] = array(
					'character_id' => ( int )$v['character_id'],	// キャラクターID
					'crime_coef' => ( int )$v['crime_coef'],		// 犯罪係数
					'body_coef' => ( int )$v['body_coef'],			// 身体係数
					'intelli_coef' => ( int )$v['intelli_coef'],	// 知能係数
					'mental_coef' => ( int )$v['mental_coef'],		// 心的係数
					'ex_stress_care' => ( int )$v['ex_stress_care']	// 臨時ストレスケア回数
				);
			}
		}

		$this->af->setApp( 'support_character', $buff, true );

		return 'api_json_encrypt';
	}
}
