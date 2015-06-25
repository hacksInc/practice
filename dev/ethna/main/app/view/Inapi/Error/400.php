<?php
/**
 *  Inapi/Error/400.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  inapi_error_400 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_InapiError400 extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
    }
	
	/**
	 * 
	 */
	function forward()
	{
		header('HTTP/1.0 400 Bad Request');
	}
}

?>