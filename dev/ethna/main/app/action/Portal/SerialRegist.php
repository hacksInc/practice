<?php
/**
 *  Portal/SerialRegist.php
 *	シリアルコード受付
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_serialRegist Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalSerialRegist extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		"sc" => array(
			"type"		=> VAR_TYPE_STRING,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		),
    );
}

/**
 *  portal_serialRegist action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalSerialRegist extends Pp_PortalWebViewActionClass
{
	private $m_serial;
	
    /**
     *  preprocess of portal_serialRegist Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        if ($this->af->validate() > 0) {
            return 'portal_error_default';
        }
		
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$code = $this->af->get( "sc" );
		
		$pevent_m =& $this->backend->getManager( "PortalEvent" );
		
		$m_serial = $pevent_m->getMasterSerialByCode( $code );
		
		if ( !$m_serial || Ethna::isError( $m_serial ) ) {
			return 'portal_error_default';
		}
		
		if ( date( "Y-m-d H:i:s" ) < $m_serial['date_open'] ) {
			return 'portal_error_default';
		}
		
		$list = $pevent_m->getUserSerialList( $pp_id, "db" );
		
		if ( isset( $list[$m_serial['serial_id']] ) ) {
			// 既にシリアルが存在してたら実処理スキップ
			return 'portal_serialRegist';
		}
		
		$this->m_serial = $m_serial;
		
        return null;
    }

    /**
     *  portal_serialRegist action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$pevent_m =& $this->backend->getManager( "PortalEvent" );
		
		$result = $pevent_m->insertUserSerial( $pp_id, $this->m_serial['serial_id'] );
		
		if ( Ethna::isError( $result ) ) {
			return 'portal_error_default';
		}
		
        return 'portal_serialRegist';
    }
}
?>