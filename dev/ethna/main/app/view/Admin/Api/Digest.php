<?php
/**
 *  Admin/Api/Digest.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_api_digest view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminApiDigest extends Pp_AdminViewClass
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
exit;
/*
		$user = $this->af->getApp('user');
		
		$body = array(
			'user' => $user['user'],
			'role' => $user['role'],
		);
		
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($body);
*/
	}
}

?>
