<?php
/**
 *	Portal/Mypage.php
 *	マイページ
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_mypage Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalMypage extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  portal_mypage action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalMypage extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_mypage Action.
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
     *  portal_mypage action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'portal_mypage';
    }
}
?>