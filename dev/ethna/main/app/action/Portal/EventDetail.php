<?php
/**
 *  Portal/EventDetail.php
 *	イベント詳細（実際にはシリアルコードの詳細を表示する）
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_eventDetail Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalEventDetail extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		"serial_id" => array(
			"type"		=> VAR_TYPE_INT,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		)
    );
}

/**
 *  portal_eventDetail action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalEventDetail extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_eventDetail Action.
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
     *  portal_eventDetail action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'portal_eventDetail';
    }
}
?>