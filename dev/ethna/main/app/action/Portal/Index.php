<?php
/**
 *	Portal/Index.php
 *	ポータルトップ
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalIndex extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  portal_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalIndex extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_index Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  portal_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// クライアントから送信されてくるサイコパスIDを取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );	// サイコパスIDはヘッダーにくっつけて送られてくるのでこの記述で取得する。

		$login_now = false;
		
		$user_m =& $this->backend->getManager( "User" );
		$puser_m =& $this->backend->getManager( "PortalUser" );
		$kpi_m =& $this->backend->getManager( "Kpi" );
		
		if ( !$puser_m->execLogin( $pp_id, $login_now ) ) {
			return 'portal_error_default';
		}
		
		$base = $user_m->getUserBase( $pp_id );
		
		$this->af->setApp( "login_now", $login_now );
		
		// KPI処理
		$uym = date( "ym", strtotime( $base['date_created'] ) );
		if ( $base['device_type'] == 1 ){
			$kpi_m->log( "Apple-ppp-dau", 3, 1, time(), $pp_id, "", "", "" );
			$kpi_m->log( "Apple-ppp-" . $uym . "_install_user_mau", 3, 1, time(), $pp_id, "", "", "" );
		} else {
			$kpi_m->log( "Google-ppp-dau", 3, 1, time(), $pp_id, "", "", "" );
			$kpi_m->log( "Google-ppp-" . $uym . "_install_user_mau", 3, 1, time(), $pp_id, "", "", "" );
		}
		
        return 'portal_index';
    }
}

?>