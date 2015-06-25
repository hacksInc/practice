<?php
/**
 *  Portal/Special.php
 *	テーマ選択
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_special Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalSpecial extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  portal_special action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalSpecial extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_special Action.
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
     *  portal_special action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'portal_special';
    }
}
?>