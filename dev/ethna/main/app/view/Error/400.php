<?php
/**
 *  Error/400.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once BASE . '/app/view/Api/Json/Encrypt.php';

/**
 *  error_400 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_Error400 extends Pp_View_ApiJsonEncrypt
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$this->af->setApp('http_status_code', 400);

		return parent::preforward();
	}
}

?>
