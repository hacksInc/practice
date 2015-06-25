<?php
/**
 *  Admin/Test/Api/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_test_api_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminTestApiInput extends Pp_ActionForm
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
 *  admin_test_api_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminTestApiInput extends Pp_AdminActionClass
{
	protected $must_login = false;

	/**
	 *  preprocess of admin_test_api_input Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		$server_env = Util::getEnv();
		if ($server_env == 'pro') {
			$this->af->ae->add(null, "API検証は開発またはステージング環境経由で行って下さい。", E_ERROR_DEFAULT);
			return 'admin_error_400';
		}
		
		return null;
	}

	/**
	 *  admin_test_api_input action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_test_api_input';
	}
}

?>