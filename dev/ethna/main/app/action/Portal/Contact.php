<?php
/**
 *	Portal/Contact.php
 *	
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_contact Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalContact extends Ethna_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  portal_contact action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalContact extends Ethna_ApiActionClass
{
    /**
     *  preprocess of portal_contact Action.
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
     *  portal_contact action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'api_json_encrypt';
    }
}
?>