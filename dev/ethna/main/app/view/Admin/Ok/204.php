<?php
/**
 *  Admin/Ok/204.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  admin_ok_204 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminOk204 extends Pp_ViewClass
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
		header('HTTP/1.0 204 No Content');
	}
}

?>
