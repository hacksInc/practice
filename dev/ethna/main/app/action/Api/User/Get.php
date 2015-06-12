<?php
/**
 *  Api/User/Get.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  api_user_get Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiUserGet extends Pp_ApiActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
	);

	
	// ↓クライアントからサイコパスID以外の引数が送られてくる場合はこの記述にする
	//var $form = array(
	//  'c'
	//);
	// ※'c'が引数を暗号化した文字列になっており、Pp_ApiActionFormのクラス内で自動的に
	//   展開されます。展開された引数の取得方法はperform()内に記述。
}

/**
 *  api_user_get action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiUserGet extends Pp_ApiActionClass
{
	/**
	 *  preprocess of api_user_get Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		error_log( "BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBb" );

		if( $this->af->validate() > 0 )
		{
			return 'error_400';
		}
		return null;
	}

	/**
	 *  api_user_get action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// クライアントから送信されてくるサイコパスIDを取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );	// サイコパスIDはヘッダーにくっつけて送られてくるのでこの記述で取得する。

		$pp_id = 910000001;		// 値を取得できたことにする
		error_log( "pp_id = $pp_id" );

		// クライアントから送られてきた引数（'c'の展開後のデータ）を取得する方法
		// ※引数としてが map_id と area_id が送られてきている場合
		// $map_id = $this->af->get( 'map_id' );
		// $area_id = $this->af->get( 'area_id' );

		// マネージャのインスタンスを取得
		$user_m =& $this->backend->getManager( 'User' );	// Pp_UserManagerクラスのインスタンス
		$photo_m =& $this->backend->getManager( 'Photo' );	// Pp_PhotoManagerクラスのインスタンス

		print_r( $photo_m->getMasterPhotoCount());

		// ut_user_baseの情報を取得
		$user_base = $user_m->getUserBase( $pp_id );		// メソッド呼び出し
		if( is_null( $user_base ) === true )
		{	// 取得エラー
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// ut_user_gameの情報を取得
		$user_game = $user_m->getUserGame( $pp_id );		// メソッド呼び出し
		if( is_null( $user_game ) === true )
		{	// 取得エラー(500エラーで返す)
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		print_r( $user_base );
		print_r( $user_game );

		// 取得したデータをクライアントに返す
		$this->af->setApp( 'user_base', $user_base, true );
		$this->af->setApp( 'user_game', $user_game, true );

		// ※今のところ仕様はまだ決まっておりませんが、APIの方でクライアントが望む形式に
		//   データを加工し、戻り値として返すようになります。

		return 'api_json_encrypt';
	}

	/* ↑の処理だと以下のようなJSONになってクライアントに渡されます 

		{
			"user_base": {
				"pp_id": "*****",
				"pu_id": "*****",
				"name": "*****",
				"device_type": "*****",
				"attr": "*****",
				"migrate_id": "*****",
				"migrate_pw_hash": "*****",
				"install_pw_hash": "*****",
				"ban_limit": "*****",
				"date_created": "*****",
				"date_modified": "*****"
			},
			"user_game": {
				"pp_id": "*****",
				"crime_coef": "*****",
				"body_coef": "*****",
				"intelli_coef": "*****",
				"mental_coef": "*****",
				"date_created": "*****",
				"date_modified": "*****"
			}
		}

	*/
}
?>