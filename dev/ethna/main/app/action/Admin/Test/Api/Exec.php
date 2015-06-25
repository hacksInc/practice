<?php
/**
 *  Admin/Test/Api/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Blowfish.class.php';
require_once 'Pp_AdminActionClass.php';

/**
 *  admin_test_api_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminTestApiExec extends Pp_ActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
	   /*
		*  TODO: Write form definition which this action uses.
		*  @see http://ethna.jp/ethna-document-dev_guide-form.html
		*
		*  Example(You can omit all elements except for "type" one) :
		*
		*  'sample' => array(
		*      // Form definition
		*      'type'        => VAR_TYPE_INT,    // Input type
		*      'form_type'   => FORM_TYPE_TEXT,  // Form type
		*      'name'        => 'Sample',        // Display name
		*  
		*      //  Validator (executes Validator by written order.)
		*      'required'    => true,            // Required Option(true/false)
		*      'min'         => null,            // Minimum value
		*      'max'         => null,            // Maximum value
		*      'regexp'      => null,            // String by Regexp
		*      'mbregexp'    => null,            // Multibype string by Regexp
		*      'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		*
		*      //  Filter
		*      'filter'      => 'sample',        // Optional Input filter to convert input
		*      'custom'      => null,            // Optional method name which
		*                                        // is defined in this(parent) class.
		*  ),
		*/

		'env' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'env',           // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'path' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'path',          // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'appver' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'appver',        // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'rscver' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'rscver',        // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'unit' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'unit',          // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'uid' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'uid',           // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'uipw' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'uipw',          // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'pw' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_RADIO, // Form type
			'name'        => 'pw',            // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'json' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,    // Input type
			'form_type'   => FORM_TYPE_TEXTAREA, // Form type
			'name'        => 'c',                // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
	);

	/**
	 *  Form input value convert filter : sample
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	/*
	function _filter_sample($value)
	{
		//  convert to upper case.
		return strtoupper($value);
	}
	*/
}

/**
 *  admin_test_api_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminTestApiExec extends Pp_AdminActionClass
{
	protected $must_login = false;

	/**
	 *  preprocess of admin_test_api_exec Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		if ($this->af->validate() > 0) {
			return 'admin_error_400';
		}
		
		$server_env = Util::getEnv();
		if ($server_env == 'pro') {
			$this->af->ae->add(null, "API検証は開発またはステージング環境経由で行って下さい。", E_ERROR_DEFAULT);
			return 'admin_error_400';
		}
		
		return null;
	}

	/**
	 *  admin_test_api_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$path = $this->af->get('path');
		$uid  = $this->af->get('uid');
		$uipw = $this->af->get('uipw');
		$pw   = $this->af->get('pw');
		$json = $this->af->get('json');

		$c = $this->encrypt($json, $pw, $uipw);
//        error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . $c);

		$result = $this->accessApi($path, $uid, $uipw, $c);
		foreach ($result as $name => $value) {
			if (is_array($value)) {
				$value = var_export($value, true);
			}

			$this->af->setAppNe($name, $value);
		}

		if (isset($result['response'])) {
			$tmp = explode("\r\n\r\n", $result['response'], 3);
			$body_json = $this->decrypt(isset($tmp[2]) ? $tmp[2] : $tmp[1], $pw, $uipw);
			$body_var = var_export(json_decode($body_json, true), true);
		}
		
		$this->af->setAppNe('response_body_json', $body_json);
		$this->af->setAppNe('response_body_var',  $body_var);
		
		return 'admin_test_api_exec';
	}
	
	function encrypt($str, $pw, $uipw)
	{
		switch ($pw) {
			case 'appw':
				$hex = $this->config->get('appw');
				break;
			case 'uipw':
				$hex = $uipw;
				break;
		}
		
		$b = new Blowfish();
		$b->SetKey($hex);
		
		return $b->Encrypt(base64_encode($str));
	}
	
	function decrypt($str, $pw, $uipw)
	{
		switch ($pw) {
			case 'appw':
				$hex = $this->config->get('appw');
				break;
			case 'uipw':
				$hex = $uipw;
				break;
		}
		
		$b = new Blowfish();
		$b->SetKey($hex);
		
		return base64_decode($b->Decrypt($str));
	}
	
	function accessApi($path, $uid, $uipw, $c)
	{
		$env = $this->af->get('env');
		$appver = $this->af->get('appver');
		$rscver = $this->af->get('rscver');
		$unit   = $this->af->get('unit');
		
		if ($env == 'dev') {
			if ($this->isSharedDevStgHost()) {
				$server_addr = '127.0.0.1';
				$server_host = 'dev.jmja.jugmon.net';
				$port = 8080;
				$scheme = 'http';
			} else {
				$server_name = 'dev.jmja.jugmon.net';
				$port = 443;
				$scheme = 'https';
			}
		} else if ($env == 'stg') {
			if ($this->isSharedDevStgHost()) {
				$server_addr = '127.0.0.1';
				$server_host = 'stg.jmja.jugmon.net';
				$port = 8080;
				$scheme = 'http';
			} else {
				$server_name = 'stg.jmja.jugmon.net';
				$port = 443;
				$scheme = 'https';
			}
		} else if ($env == 'pro') {
			$server_name = 'jmja.jugmon.net';
			$port = 443;
			$scheme = 'https';
		} else if ($env == 'st') {
			$server_name = 'st.jmja.jugmon.net';
			$port = 443;
			$scheme = 'https';
		} else if ($env == 'ost') {
			$server_name = 'ost.jmja.jugmon.net';
			$port = 80;
			$scheme = 'http';
		} else {
			$server_name = $_SERVER['SERVER_NAME'];

			if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']) {
				$port = $_SERVER['SERVER_PORT'];
			} else {
				$port = 80;
			}
		
			list($scheme, $trash) = explode(':', $_SERVER['SCRIPT_URI'], 2);
		}
		
		// パラメータ組み立て
		$headers = array();
		
		if (isset($server_name) && $server_name) {
			$url = $scheme . '://' . $server_name;
		} else {
			$url = $scheme . '://' . $server_addr;
			$headers[] = 'Host: ' . $server_host;
		}
//		if ($port != 80) $url .= ':' . $port;
		if (!(($scheme == 'http') && ($port == 80)) &&
			!(($scheme == 'https') && ($port == 443))
		) {
			$url .= ':' . $port;
		}

		$url .= '/api' . $path;

		if ($uid) {
			$headers[] = 'Authorization: Basic ' . base64_encode("$uid:$uipw");
		}
//        error_log(__FILE__ . ':' . __LINE__ . ':' . var_export($headers, true));

		$headers[] = 'X-Jugmon-Appver: ' . $appver;
		$headers[] = 'X-Jugmon-Rscver: ' . $rscver;
//		$headers[] = 'X-Jugmon-Unit: ' . $unit;
		
		$postfields = array();
		if (strlen($c) > 0) {
			$postfields['c'] = $c;
		}
//        error_log(__FILE__ . ':' . __LINE__ . ':' . var_export($postfields, true));
		
		// 通信実行
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);

		if (count($headers) > 0) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		if (count($postfields) > 0) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		}
		
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );

		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		
//        error_log(__FILE__ . ':' . __LINE__ . ':' . var_export($result, true));
//        error_log(__FILE__ . ':' . __LINE__ . ':' . var_export($info, true));
		
		return array(
			'url' => $url, 'headers' => $headers, 'postfields' => $postfields,
			'response' => $response, 'info' => $info,
		);
	}
	
	function isSharedDevStgHost($http_host = null)
	{
		if ($http_host === null) {
			$http_host = $_SERVER['HTTP_HOST'];
		}

		list($host, $port) = explode(':', $http_host, 2);

		if ((strcmp($host, 'dev.mgr.jmja.jugmon.net')        === 0) ||
			(strcmp($host, 'main.dev.mgr.jmja.jugmon.net')   === 0) ||
			(strcmp($host, 'review.dev.mgr.jmja.jugmon.net') === 0) ||
			(strcmp($host, 'main.stg.mgr.jmja.jugmon.net')   === 0) ||
			(strcmp($host, 'review.stg.mgr.jmja.jugmon.net') === 0)
		) {
			return true;
		}

		return false;
	}
}
?>
