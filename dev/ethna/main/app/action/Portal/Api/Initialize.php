<?php
/**
 *	Portal/Api/Initialize.php
 *	新規登録
 *
 *	@author     {$author}
 *	@package    Pp
 *	@version    $Id$
 */

/**
 *  portal_api_initialize Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalApiInitialize extends Pp_PortalActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'nickname' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
//			'min'         => 1,               // Minimum value
//			'max'         => 40,           // Maximum value
		),
		'ruby' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
//			'min'         => 1,               // Minimum value
//			'max'         => 40,           // Maximum value
		),
		'sex' => array(
			// Form definition
			'type'        => VAR_TYPE_INT, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 2,           // Maximum value
		),
		'uuid' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 255,           // Maximum value
		),
		'ua' => array(
			// Form definition
			'type'        => VAR_TYPE_INT, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 2,           // Maximum value
		),
    );
}

/**
 *  portal_api_initialize action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalApiInitialize extends Pp_PortalActionClass
{
    /**
     *  preprocess of portal_api_initialize Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
		if ($this->af->validate() > 0) {
			return 'error_400';
		}
		
		// '\'は文字列から取り除くという仕様になった
		$nickname = urldecode( $this->af->get( "nickname" ));
		$ruby = urldecode( $this->af->get( "ruby" ));
		$nickname	= str_replace( "\\", "", $nickname );
		$ruby		= str_replace( "\\", "", $ruby );
		
		// バリデータはprepareにまとめる
		if ( mb_strlen( $nickname ) >= 10 ) {
			$this->af->setApp( 'status_detail_code', SDC_USER_NAME_LENGTH_OVER, true );
			return 'error_400';
		}
		else if( mb_strlen( $nickname ) == 0 )
		{	// '\'を取り除いた結果、空になるということもありえる
			$this->af->setApp( 'status_detail_code', SDC_USER_INPUT_ERROR, true );
			return 'error_400';
		}
		
		if ( mb_strlen( $ruby ) >= 10 ) {
			$this->af->setApp( 'status_detail_code', SDC_USER_NAME_LENGTH_OVER, true );
			return 'error_400';
		}
		else if( mb_strlen( $ruby ) == 0 )
		{	// '\'を取り除いた結果、空になるということもありえる
			$this->af->setApp( 'status_detail_code', SDC_USER_INPUT_ERROR, true );
			return 'error_400';
		}
		
		// rubyは英数字＋一部記号のみ許可
		if ( preg_match( "/[^[:graph:]]+/i", $ruby ) ) {
			$this->af->setApp( 'status_detail_code', SDC_USER_NAME_UNAVAILABLE, true );
			return 'error_400';
		}

        return null;
    }

    /**
     *  portal_api_initialize action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// クライアントからの情報を取得

		// '\'は文字列から取り除くという仕様になった
		$nickname = urldecode( $this->af->get( "nickname" ));
		$ruby = urldecode( $this->af->get( "ruby" ));
		$nickname	= str_replace( "\\", "", $nickname );
		$ruby		= str_replace( "\\", "", $ruby );

		$sex		= $this->af->get( "sex" );
		$uuid		= $this->af->get( "uuid" );
		$ua			= $this->af->get( "ua" );
		

//error_log( "$nickname:$ruby:$sex:$uuid:$ua" );
		
		$kpi_m =& $this->backend->getManager( "Kpi" );
		$user_m =& $this->backend->getManager( "User" );
		$puser_m =& $this->backend->getManager( "PortalUser" );
		$ptheme_m =& $this->backend->getManager( "PortalTheme" );
		
		// 対象DBに接続
		$db =& $this->backend->getDB();
		$db_cmn =& $this->backend->getDB( "cmn" );
		
		// 複数DBに対するトランザクション実行
		// ここに来る段階でユニットは選択されている（Pp_ApiActionClass::authenticateUnit参照）
		$commit = true;
		$db->begin();
		$db_cmn->begin();
		
		// ゲーム用データを作成
		if ( $commit ) $g_data = $user_m->createUser( $nickname, $ua );
		if ( Ethna::isError( $g_data ) ) {
error_log( "error_01" );
			$commit = false;
		}
		
		// ポータル用データを作成
		if ( $commit ) {
			if ( Ethna::isError( $puser_m->insertUserBase( $g_data['pp_id'], $nickname, $ruby, $uuid, $sex ) ) ) {
				$commit = false;
			}
		}
if ( !$commit ) error_log( "error_02" );
		
		
		if ( $commit ) {
			if ( Ethna::isError( $ptheme_m->insertUserTheme( $g_data['pp_id'], $sex, 1 ) ) ) {
				$commit = false;
			}
		}
if ( !$commit ) error_log( "error_03" );
		
		// Androidユーザーのみ、1250ptを付与
/*		if ( $commit && $ua == 2 ) {
			if ( Ethna::isError( $puser_m->addPoint( $g_data['pp_id'], 1250, 'セーブデータ不具合の補填' ) ) ) {
				$commit = false;
			}
		}*/
		
		// トランザクション終了
		if ( $commit )	{$db->commit();$db_cmn->commit();}
		else			{$db->rollback();$db_cmn->rollback();return 'error_400';}
		
		// 送信データを登録
		$this->af->setApp( "pp_id", $g_data['pp_id'], true );
		$this->af->setApp( "install_pw", $g_data['install_pw'], true );
		$this->af->setApp( "migrate_id", $g_data['migrate_id'], true );
		$this->af->setApp( "migrate_pw", $g_data['migrate_pw'], true );
		
		// KPI情報
		if ( $ua == 1 )	$kpi_m->log( "Apple-ppp-install", 2, 1, time(), $g_data['pp_id'], "", "", "" );
		else			$kpi_m->log( "Google-ppp-install", 2, 1, time(), $g_data['pp_id'], "", "", "" );
		
        return 'api_json_encrypt';
    }
}
?>