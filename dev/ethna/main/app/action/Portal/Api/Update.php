<?php
/**
 *  Portal/Api/Update.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  portal_api_update Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalApiUpdate extends Pp_ApiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  portal_api_update action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalApiUpdate extends Pp_ApiActionClass
{
    /**
     *  preprocess of portal_api_update Action.
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

        return null;
    }

    /**
     *  portal_api_update action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$pp_id = $this->af->getRequestedBasicAuth( 'user' );
		
		$user_m =& $this->backend->getManager( "User" );
		
error_log( "[ppp_update]pp_id" . $pp_id );
		
		// 他のデータはすべて存在するので、install_pw、migrate_id、migrate_pwのみを生成
		$user = $user_m->updateCavePortalUser( $pp_id );
		
		if ( Ethna::isError( $user ) ) {
			return 'error_400';
		}
		
error_log( "[ppp_update]" . print_r( $user, 1 ) );
		
		// 送信データを登録
		$this->af->setApp( "pp_id", $user['pp_id'], true );
		$this->af->setApp( "install_pw", $user['install_pw'], true );
		$this->af->setApp( "migrate_id", $user['migrate_id'], true );
		$this->af->setApp( "migrate_pw", $user['migrate_pw'], true );
		
        return 'api_json_encrypt';
    }
}
?>