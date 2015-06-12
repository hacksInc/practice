<?php
/**
 *  Error/500.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once BASE . '/app/view/Api/Json/Encrypt.php';

/**
 *  error_500 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_Error500 extends Pp_View_ApiJsonEncrypt
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$user_id = $this->af->getRequestedBasicAuth('user');
		$json = $this->af->getInputJson();
		$status_detail_code = $this->af->getApp('status_detail_code');
		error_log("[HTTP500]$user_id status_detail_code=$status_detail_code json=$json");
		$this->af->setApp('http_status_code', 500);

		return parent::preforward();
	}
}

?>
