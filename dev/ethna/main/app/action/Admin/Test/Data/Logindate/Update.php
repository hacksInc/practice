<?php
/**
 *  Admin/Test/Data/Logindate/Update.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_test_data_logindate_update Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminTestDataLogindateUpdate extends Pp_ActionForm
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
 *  admin_test_data_logindate_update action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminTestDataLogindateUpdate extends Pp_AdminActionClass
{
	protected $must_login = false;

	/**
     *  preprocess of admin_test_data_logindate_update Action.
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

		return null;
    }

    /**
     *  admin_test_data_logindate_update action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$db =& $this->backend->getDB();
		
		$param = array(
			date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
			date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - 86400 * 3),
		);
		$sql = "UPDATE t_user_base SET login_date = ?"
		     . " WHERE login_date < ?";
		if (!$db->execute($sql, $param)) {
//			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
//				$db->db->ErrorNo(), $db->db->ErrorMsg(), __FILE__, __LINE__);
			return 'admin_error_500';
		}
		
        return 'admin_test_data_logindate_update';
    }
}

?>