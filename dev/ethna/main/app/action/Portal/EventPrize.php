<?php
/**
 *  Portal/EventPrize.php
 *	イベント報酬
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_eventPrize Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalEventPrize extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  portal_eventPrize action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalEventPrize extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_eventPrize Action.
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
		
		// とりあえずサイコパス捜査線のみ対応
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$pevent_m =& $this->backend->getManager( "PortalEvent" );
		
		$list = $pevent_m->getUserSerialList( $pp_id, "db" );
		
		if ( count( $list ) < 5 ) {
			return 'portal_error_default';
		}

        return null;
    }

    /**
     *  portal_eventPrize action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'portal_eventPrize';
    }
}
?>