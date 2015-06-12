<?php
/**
 *  Portal/ChangeTheme.php
 *	テーマ変更処理
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_changeTheme Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalChangeTheme extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		"theme_id" => array(
			"type" 		=> VAR_TYPE_INT,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		)
    );
}

/**
 *  portal_changeTheme action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalChangeTheme extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_changeTheme Action.
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
		
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$theme_id = $this->af->get( "theme_id" );
		
		$ptheme_m =& $this->backend->getManager( "PortalTheme" );
		
		$list = $ptheme_m->getUserThemeList( $pp_id, "db" );
		
		if ( !isset( $list[$theme_id] ) ) {
			return 'portal_error_default';
		}

        return null;
    }

    /**
     *  portal_changeTheme action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$theme_id = $this->af->get( "theme_id" );
		
		$ptheme_m =& $this->backend->getManager( "PortalTheme" );
		
		if ( !$ptheme_m->updateCurrentTheme( $pp_id, $theme_id ) ) {
			return 'portal_error_default';
		}
		
        return 'portal_changeTheme';
    }
}
?>