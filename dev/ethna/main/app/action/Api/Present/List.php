<?php
/**
 *	Api/Present/List.php
 *	プレゼント一覧取得
 *
 *	@author     {$author}
 *	@package    Pp
 *	@version    $Id$
 */

/**
 *  api_present_list Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiPresentList extends Pp_ApiActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
	);
}

/**
 *  api_present_list action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiPresentList extends Pp_ApiActionClass
{
	/**
	 *  preprocess of api_present_list Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
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
	 *  api_present_list action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// クライアントから送信されてくるサイコパスIDを取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );

		// マネージャのインスタンスを取得
		$present_m =& $this->backend->getManager( 'Present' );

		// 期間超過したプレゼントを削除する
		$res = $present_m->deleteExpiredUserPresent( $pp_id );
		if( Ethna::isError( $res ))
		{
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// プレゼント保持最大数を超過したプレゼントを削除する
		$res = $present_m->deleteMaxOverUserPresent( $pp_id );
		if(( $res === false )||( Ethna::isError( $res )))
		{
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// ct_user_presentの情報を取得
		$list = $present_m->getUserPresentList( $pp_id );
		if( is_null( $list ) || ( $list === false ))
		{
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// 取得したデータをクライアントに返す
		$this->af->setApp( 'user_box', $present_m->convertUserBox( $list ), true );

		return 'api_json_encrypt';
	}
}
