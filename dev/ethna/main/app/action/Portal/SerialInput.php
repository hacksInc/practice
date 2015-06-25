<?php
/**
 *  Portal/SerialInput.php
 *	シリアルコード入力
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_serialInput Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalSerialInput extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		"serial_id"	=> array(
			"type"		=> VAR_TYPE_INT,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		)
    );
}

/**
 *  portal_serialInput action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalSerialInput extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_serialInput Action.
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

        return null;
    }

    /**
     *  portal_serialInput action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'portal_serialInput';
    }
}
?>