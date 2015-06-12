<?php
/**
 *  Admin/Test/Data/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_test_data_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminTestDataInput extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$env_name = null;
		if (strncmp($_SERVER['HTTP_HOST'], 'dev.', 4) === 0) {
			$env_name = '開発環境';
		} else if (strncmp($_SERVER['HTTP_HOST'], 'stg.', 4) === 0) {
			$env_name = 'ステージング環境';
		}
		
		if ($env_name) $this->af->setApp('env_name', $env_name);
    }
}

?>
