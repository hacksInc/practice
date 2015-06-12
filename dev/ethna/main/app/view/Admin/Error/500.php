<?php
/**
 *  Admin/Error/500.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  admin_error_500 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminError500 extends Pp_ViewClass
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
		header('HTTP/1.0 500 Internal Server Error');
		
		if ($this->ae->count() > 0) {
			parent::forward();
		} else {
			echo 'Internal Server Error';
		}
	}
}

?>
