<?php
/**
 *  Admin/Error/403.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  admin_error_403 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminError403 extends Pp_ViewClass
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
		header('HTTP/1.0 403 Forbidden');
		
		if ($this->ae->count() > 0) {
			parent::forward();
		}
	}
}

?>
