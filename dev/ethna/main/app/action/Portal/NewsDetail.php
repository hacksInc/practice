<?php
/**
 *	Portal/NewsDetail.php
 *	ニュース詳細
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_newsDetail Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalNewsDetail extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		"news_id" => array(
			"type"		=> VAR_TYPE_INT,
			"required"	=> true,
			"min"		=> 0,
			"max"		=> null,
		),
    );
}

/**
 *  portal_newsDetail action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalNewsDetail extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_newsDetail Action.
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
     *  portal_newsDetail action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// クライアントから送信されてくるサイコパスIDを取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );	// サイコパスIDはヘッダーにくっつけて送られてくるのでこの記述で取得する。
		
		$news_id = $this->af->get( "news_id" );
		
		$read_now = false;
		
		$pnews_m =& $this->backend->getManager( "PortalNews" );
		
		if ( !$pnews_m->readNews( $pp_id, $news_id, $read_now ) ) {
			return 'portal_error_default';
		}
		
		$this->af->setApp( "read_now", $read_now );
		
        return 'portal_newsDetail';
    }
}
?>