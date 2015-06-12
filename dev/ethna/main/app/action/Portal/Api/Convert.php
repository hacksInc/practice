<?php
/**
 *  Portal/Api/Convert.php
 *	ユーザー引継ぎ（ゲーム→ゲーム、ケイブ版ポータル→ゲーム共通）
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_api_convert Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalApiConvert extends Pp_PortalActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		"id"	=> array(
			"type"		=> VAR_TYPE_STRING,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		),
		"pass"	=> array(
			"type"		=> VAR_TYPE_STRING,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		),
		"ua"	=> array(
			"type"		=> VAR_TYPE_INT,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> 2,
		),
    );
}

/**
 *  portal_api_convert action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalApiConvert extends Pp_PortalActionClass
{
	protected $mode = 0;
	
    /**
     *  preprocess of portal_api_convert Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
		if ($this->af->validate() > 0) {
			$this->af->setApp( "status_detail_code", SDC_USER_MIGRATE_ID_PW_ERROR );
			return 'error_400';
		}
		
		$id = $this->af->get( "id" );
		$pass = $this->af->get( "pass" );
		$ua = $this->af->get( "ua" );
		
		$puser_m =& $this->backend->getManager( "PortalUser" );
		$unit_m =& $this->backend->getManager( "Unit" );
		$user_m =& $this->backend->getManager( "User" );
		
		// 通常引き継ぎ→旧ユーザー引継ぎの順に判別
		$unit = $unit_m->getUnitByMigrateId( $id );
		
		if ( $unit_m->current_unit != $unit ) $unit_m->resetUnit( $unit );
		
		// 引継ぎユーザーかを判別
		if ( !Ethna::isError( $user_m->isMigrateUser( $id, $pass ) ) ) {
			$this->mode = 1; // 引継ぎモード
		} else {
			// 旧ユーザーは全員ユニット1
			if ( $unit_m->current_unit != 1 ) $unit_m->resetUnit( 1 );
			
			$row = $puser_m->isOldUser( $id, $pass, $ua );
			
			if ( !$row || Ethna::isError( $row ) ) {
				// 引継ぎでも旧ユーザーでもないのでエラー
				$this->af->setApp( "status_detail_code", SDC_USER_MIGRATE_ID_PW_ERROR );
				return 'error_500';
			}
			
			$this->mode = 2; // 旧ユーザー引継ぎモード
		}
		
        return null;
    }

    /**
     *  portal_api_convert action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// ユニットの接続はprepareで終わってるのでperformではいじらない
		$id = $this->af->get( "id" );
		$pass = $this->af->get( "pass" );
		$uuid = $this->af->get( "uuid" );
		$ua = $this->af->get( "ua" );
		
		$user_m =& $this->backend->getManager( "User" );
		$puser_m =& $this->backend->getManager( "PortalUser" );
		
		switch ( $this->mode ) {
			case 1:	// 通常引継ぎ
				$user = $user_m->getUserBaseByMigrateId( $id, "db" );
				
				$user = $user_m->migrateUser( $user['pp_id'], $ua, $id, $pass );
				
				if ( Ethna::isError( $user ) ) {
					$this->af->setApp( "status_detail_code", SDC_USER_MIGRATE );
					return 'error_500';
				}
				break;
				
			case 2:	// 旧ユーザー引継ぎ
				$puser = $puser_m->getOldUser( $id, $pass, "db" );
				$user = $user_m->getUserBase( $puser['pp_id'] );
				
				$result = $puser_m->convertOldUser( $puser['pp_id'], $uuid, $ua, $id, $pass );
				
				if ( !$result ) {
					$this->af->setApp( "status_detail_code", SDC_USER_MIGRATE );
					return 'error_500';
				}
				
				$user = $result;
				break;
				
			default:	// 多分ないと思うけど一応。未定義のが来たのでエラー
				$this->af->setApp( "status_detail_code", SDC_USER_MIGRATE );
				return 'error_500';
				break;
		}
		
		// 取得したデータをクライアントに返す
		$this->af->setApp( 'pp_id', $user['pp_id'], true );
		$this->af->setApp( 'install_pw', $user['install_pw'], true );
		$this->af->setApp( 'migrate_id', $user['migrate_id'], true );
		$this->af->setApp( 'migrate_pw', $user['migrate_pw'], true );
		
        return 'api_json_encrypt';
    }
}
?>