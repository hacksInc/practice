<?php
/**
 *  Admin/Api/Rsync.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_api_rsync view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminApiRsync extends Pp_AdminViewClass
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
	
	/**
	 * 
	 */
	function forward()
	{
		$is_error = $this->af->getApp('is_error');
		$content = array(
			'user' => $this->af->getApp('user'),
			'time' => $this->af->getApp('time'),
		);
		
		if ($is_error) {
			header('HTTP/1.0 500 Internal Server Error');
//DEBUG
//header('HTTP/1.0 200 OK');
		} else {
			header('HTTP/1.0 200 OK');
		}
		
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($content);
	}
}

?>