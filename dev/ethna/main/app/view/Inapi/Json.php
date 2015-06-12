<?php
/**
 *  Inapi/Json.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  inapi_json view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_InapiJson extends Pp_ViewClass
{
	protected $output = array();
	
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		foreach ($this->af->getOutputNamesNoConvert() as $name) {
			$this->output[$name] = $this->af->getApp($name);
		}
    }
	
	/**
	 * 
	 */
	function forward()
	{
		$json = json_encode($this->output);
		$this->logger->log(LOG_INFO, 'Output Json:' . $json);
		
		header('Content-Type: application/json');
		header('Content-Length: ' . strlen($json));
		
		echo $json;		
	}
}

?>