<?php
/**
 *  Portal/ChangeNameresult.php
 *	テーマ変更処理
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_changeNameresult Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalChangeNameresult extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'nickname' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'name'        => '名前',      // 表示名

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
//			'min'         => 1,               // Minimum value
//			'max'         => 40,           // Maximum value
		),
		'ruby' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'name'        => 'name',      // 表示名

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
//			'min'         => 1,               // Minimum value
//			'max'         => 40,           // Maximum value
		),
    );
}

/**
 *  portal_changeNameresult action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalChangeNameresult extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_changeNameresult Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        if ($this->af->validate() > 0) {
			//$this->af->ae->add( null, '入力エラーです', E_ERROR_DEFAULT );
			return 'portal_error_default';
        }
		
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		// '\'は文字列から取り除くという仕様になった
		$nickname = urldecode( $this->af->get( "nickname" ));
		$ruby = urldecode( $this->af->get( "ruby" ));
		$nickname	= str_replace( "\\", "", $nickname );
		$ruby		= str_replace( "\\", "", $ruby );
		
		// バリデータはprepareにまとめる
		if ( mb_strlen( $nickname ) >= 10 ) {
			$this->af->setApp( 'status_detail_code', SDC_USER_NAME_LENGTH_OVER, true );
			$this->af->ae->add( null, '名前は10文字までです', E_ERROR_DEFAULT );
			return 'portal_error_default';
		}
		else if( mb_strlen( $nickname ) == 0 )
		{	// '\'を取り除いた結果、空になるということもありえる
			$this->af->setApp( 'status_detail_code', SDC_USER_INPUT_ERROR, true );
			$this->af->ae->add( null, '名前が空です（「\\」は使用不可）', E_ERROR_DEFAULT );
			return 'portal_error_default';
		}
		
		if ( mb_strlen( $ruby ) >= 10 ) {
			$this->af->setApp( 'status_detail_code', SDC_USER_NAME_LENGTH_OVER, true );
			$this->af->ae->add( null, 'nameは10文字までです', E_ERROR_DEFAULT );
			return 'portal_error_default';
		}
		else if( mb_strlen( $ruby ) == 0 )
		{	// '\'を取り除いた結果、空になるということもありえる
			$this->af->setApp( 'status_detail_code', SDC_USER_INPUT_ERROR, true );
			$this->af->ae->add( null, 'nameが空です（「\\」は使用不可）', E_ERROR_DEFAULT );
			return 'portal_error_default';
		}
		
		// rubyは英数字＋一部記号のみ許可
		if ( preg_match( "/[^[:graph:]]+/i", $ruby ) ) {
			$this->af->setApp( 'status_detail_code', SDC_USER_NAME_UNAVAILABLE, true );
			$this->af->ae->add( null, 'nameが空です（英数字と一部記号のみ使用可）', E_ERROR_DEFAULT );
			return 'portal_error_default';
		}

        return null;
    }

    /**
     *  portal_changeNameresult action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		// クライアントからの情報を取得

		// '\'は文字列から取り除くという仕様になった
		$nickname = urldecode( $this->af->get( "nickname" ));
		$ruby = urldecode( $this->af->get( "ruby" ));
		$nickname	= str_replace( "\\", "", $nickname );
		$ruby		= str_replace( "\\", "", $ruby );
		
		$user_m =& $this->backend->getManager( "User" );
		$puser_m =& $this->backend->getManager( "PortalUser" );
		
		// 対象DBに接続
		$db =& $this->backend->getDB();
		$db_cmn =& $this->backend->getDB( "cmn" );
		
		// 複数DBに対するトランザクション実行
		// ここに来る段階でユニットは選択されている（Pp_ApiActionClass::authenticateUnit参照）
		$commit = true;
		$db->begin();
		$db_cmn->begin();
		
		// ゲーム用データを上書き
		if ( $commit ) {
			if ( Ethna::isError( $user_m->updateUserBase( $pp_id, array('name' => $nickname ) ) ) ) {
error_log( "error_01" );
				$commit = false;
			}
		}
		
		// ポータル用データを上書き
		if ( $commit ) {
			if ( Ethna::isError( $puser_m->updateUserBase( $pp_id, array('user_name' => $nickname, 'user_name_en' => $ruby ) ) ) ) {
				$commit = false;
			}
		}
if ( !$commit ) error_log( "error_02" );
		
		// トランザクション終了
		if ( $commit )	{$db->commit();$db_cmn->commit();}
		else			{$db->rollback();$db_cmn->rollback();return 'portal_error_default';}
		
		//再取得
		$user_data = $user_m->getUserBase( $pp_id );
		$puser_data = $puser_m->getUserBase( $pp_id, "db" );
		
        return 'portal_changeNameresult';
    }
}
?>