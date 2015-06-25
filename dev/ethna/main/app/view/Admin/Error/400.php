<?php
/**
 *  Admin/Error/400.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  admin_error_400 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminError400 extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
/*	
    function preforward()
    {
    }
*/
	
	function forward()
	{
		header('HTTP/1.0 400 Bad Request');
		
		if ($this->ae->count() > 0) {
			parent::forward();
		}
	}
}

?>
