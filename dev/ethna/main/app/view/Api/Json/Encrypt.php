<?php
/**
 *  Api/Json/Encrypt.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Blowfish.class.php';

/**
 *  api_json_encrypt view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ApiJsonEncrypt extends Pp_ViewClass
{
	protected $output = array();

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$output = array();
		foreach ($this->af->getOutputNames() as $name) {
			$output[$name] = $this->af->getApp($name);
		}
		
		// 共通戻り値
		foreach (array('appver', 'rscver', 'maintenance') as $name) {
			$output[$name] = $this->config->get($name);
		}
		
		$status_detail_code = $this->af->getApp('status_detail_code');
		if (!$status_detail_code && !is_numeric($status_detail_code)) {
			$http_status_code = $this->af->getApp('http_status_code');
			if (!$http_status_code) {
				$http_status_code = 200;
			}
			
			$status_detail_code = floor($http_status_code / 100) * 1000;
		}
		$output['status_detail_code'] = $status_detail_code;
		$output['server_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		
		$output = $this->array_intval($output);
		
		foreach ($this->af->getOutputNamesNoConvert() as $name) {
			$output[$name] = $this->af->getApp($name);
		}
		
		$this->output = $output;
	}
	
	function forward()
	{
//		$this->headerHttpStatusCode();
		$this->setVolatileTokenCookie();
		
		$json = pp_view_json_encode($this->output);
//		$json = json_encode($this->output);
//		$this->logger->log(LOG_INFO, 'Output Json:' . $json);
		
		if ( $this->af->getApp( "auth_base64_flg" ) ) {
			$base64 = base64_encode($json);
//			$this->logger->log(LOG_DEBUG, 'Output Base64:' . $base64);
		
			$blowfish = new Blowfish();
			$blowfish->SetKey($this->af->getBlowfishKeyHex());
			$code = $blowfish->Encrypt($base64);
//			$this->logger->log(LOG_DEBUG, 'Output Blowfish:' . $code);
		} else {
			$code = $json;
		}
		echo $code;
	}

/*
	function headerHttpStatusCode()
	{
		$msg = array(
			400 => 'HTTP/1.0 400 Bad Request',
			401 => 'HTTP/1.0 401 Unauthorized',
			500 => 'HTTP/1.0 500 Internal Server Error',
			503 => 'HTTP/1.0 503 Service Unavailable',
		);
		
		$code = $this->af->getApp('http_status_code');
		
		if ($code && isset($msg[$code])) {
			header($msg[$code]);
		}
	}
*/
	
	protected function setVolatileTokenCookie()
	{
		if (isset($_COOKIE['volatile_token'])) {
			$volatile_token = $_COOKIE['volatile_token'];
		} else {
			$volatile_token = base_convert(mt_rand(0, 1073741824), 10, 36);
		}

//        //TODO volatile_tokenの変更検知処理がない
//        $volatile_token_prev = memcache_get('volatile_token_' . $uid);
//        if ($volatile_token_prev && ($volatile_token_prev != $volatile_token)) {
//            // 複数端末の可能性あり
//        }

//		error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':[volatile_token=' . $volatile_token . ']');

		setcookie("volatile_token", $volatile_token);
	}
}

?>