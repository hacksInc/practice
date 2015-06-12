<?php
/**
 *  Portal/Api/Check.php
 *	認証処理
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_api_check Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalApiCheck extends Pp_PortalActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  portal_api_check action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalApiCheck extends Pp_PortalActionClass
{
    /**
     *  preprocess of portal_api_check Action.
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
     *  portal_api_check action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// クライアントから送信されてくるサイコパスIDを取得
		$pp_id = $this->af->get( "id" );

		if ( $this->config->get('maintenance') == 2 && !$this->isBreakthrough( $pp_id ) ) {
			$this->af->setApp( 'status_detail_code', SDC_HTTP_503_SERVICE_UNAVAILABLE, true );
			return 'error_503';
		}

		// テーマ情報を取得
		if ( !empty( $pp_id ) ) {
			$ptheme_m =& $this->backend->getManager( "PortalTheme" );
			
			$theme = $ptheme_m->getCurrentUserTheme( $pp_id );
			
			if ( Ethna::isError( $theme ) ) {
				$this->af->setApp( "status_detail_code", SDC_DB_ERROR );
				return 'error_500';
			}
			
			if ( !$theme ) {
				$this->af->setApp( "theme_id", -1, true );
			} else {
				$this->af->setApp( "theme_id", $theme['theme_id'], true );
			}
		} else {
			$this->af->setApp( "theme_id", -1, true );
		}
		
        return 'api_json_encrypt';
    }
}
?>