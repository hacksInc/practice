<?php
/**
 *  Portal/GetTheme.php
 *	テーマ獲得処理
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_getTheme Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalGetTheme extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		"theme_id" => array(
			"type"		=> VAR_TYPE_INT,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		)
    );
}

/**
 *  portal_getTheme action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalGetTheme extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_getTheme Action.
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
		
		if ( isset( $list[$theme_id] ) ) {
			return 'portal_error_default';
		}

        return null;
    }

    /**
     *  portal_getTheme action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$theme_id = $this->af->get( "theme_id" );
		
		$ptheme_m =& $this->backend->getManager( "PortalTheme" );
		$puser_m =& $this->backend->getManager( "PortalUser" );
		
		$m_theme = $ptheme_m->getMasterTheme( $theme_id );
		
		$db =& $this->backend->getDB();
		
		$db->begin();
		
		if ( Ethna::isError( $ptheme_m->insertUserTheme( $pp_id, $theme_id ) ) ) {
			$db->rollback();
			return 'portal_error_default';
		}
		
		if ( Ethna::isError( $puser_m->addPoint( $pp_id, $m_theme['use_point'] * -1, "テーマ「" . $m_theme['chara_name'] . "」獲得" ) ) ) {
			$db->rollback();
			return 'portal_error_default';
		}
		
		$db->commit();
		
        return 'portal_getTheme';
    }
}
?>