<?php
/**
 *  Admin/Test/Api/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_test_api_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminTestApiInput extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$client_m =& $this->backend->getManager('Client');

		$date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$app_ver = $client_m->getLatestAppVer($date);
		$res_ver = $client_m->getLatestResVer($date);
		
		$unit = $this->config->get('unit_id');
		
		$this->af->setApp('appver', $app_ver['app_ver']);
		$this->af->setApp('rscver', $res_ver['res_ver']);
		$this->af->setApp('unit',   $unit);
    }
}

?>