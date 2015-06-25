<?php
/**
 *	Api/Portal/Point/Get.php
 *	ポータルのポイントを取得
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  api_portal_point_get Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiPortalPointGet extends Pp_ApiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  api_portal_point_get action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiPortalPointGet extends Pp_ApiActionClass
{
    /**
     *  preprocess of api_portal_point_get Action.
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
     *  api_portal_point_get action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$puser_m =& $this->backend->getManager( "PortalUser" );
		
		$user = $puser_m->getUserBase( $pp_id, "db" );
		
		$this->af->setApp( "portal_point", $user['point'], true );
		
        return 'api_json_encrypt';
    }
}
?>