<?php
/**
 *  Admin/Test/Data/Paymentserver/Create.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_test_data_paymentserver_create Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminTestDataPaymentserverCreate extends Pp_ActionForm
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
 *  admin_test_data_paymentserver_create action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminTestDataPaymentserverCreate extends Pp_AdminActionClass
{
	protected $must_login = false;

	/**
     *  preprocess of admin_test_data_paymentserver_create Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  admin_test_data_paymentserver_create action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
return 'admin_error_500';
/*		
		$user_m =& $this->backend->getManager('User');
		
		$account = 'TEST' . substr(uniqid(), -8);
		$dmpw    = 'pass5678';
		$ua      = Pp_UserManager::OS_IPHONE;
		
		$app_id = $user_m->getPaymentServerAppIdFromUserAgent($ua);
		
		$payment_server_result = $user_m->callPaymentServer('/user/regist', array(
			'app_id'  => $app_id,
			'account' => $account, 
			'password' => $dmpw
		));
		if ($payment_server_result['sts'] != 'OK') {
//			return Ethna::raiseError("Account regist error. account[%s]", E_USER_ERROR, $account);
			return 'admin_error_500';
		}
		//'{"sts":"OK","param":{"user_id":"840","cave_id":"9c5cccb8d02d"}}
		
		$this->af->setApp('account', $account);
		$this->af->setApp('dmpw',    $dmpw);
		$this->af->setApp('ua',      $ua);

		return 'admin_test_data_paymentserver_create';
*/
    }
}

?>