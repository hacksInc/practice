<?php
/**
 *  Admin/Api/Tar/Exit.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_api_tar_exit view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminApiTarExit extends Pp_AdminViewClass
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
		$exit_code = $this->af->getApp('exit_code');
		
		header("Content-Type: text/plain");
		header("Content-Length: " . strlen($exit_code));
		
		echo $exit_code;
	}
}

?>