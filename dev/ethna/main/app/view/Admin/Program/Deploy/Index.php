<?php
/**
 *  Admin/Program/Deploy/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_program_deploy_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminProgramDeployIndex extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
header('HTTP/1.0 500 Internal Server Error');
exit;
/*		
		switch ($this->af->getApp('env')) {
			case 'dev':
				$url = 'http://web03:8080/jugmon/deploy_dev/environment.html';
				break;

			case 'stg':
				$url = 'http://web03:8080/jugmon/deploy_stg/';
				break;
			
			case 'pro':
				$url = 'http://web03:8080/jugmon/deploy_pro/';
				break;
		}
		
		$this->af->setApp('url', $url);
*/
    }
}

?>