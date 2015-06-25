<?php
/**
 *  Portal/History.php
 *	イベント参加履歴
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_history Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalHistory extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  portal_history action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalHistory extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_history Action.
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
     *  portal_history action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'portal_history';
    }
}
?>