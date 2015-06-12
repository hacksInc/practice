<?php
// vim: foldmethod=marker
/**
 *  Pp_ActionForm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

// {{{ Pp_ActionForm
/**
 *  ActionForm class.
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_ActionForm extends Ethna_ActionForm
{
	/**#@+
	 *  @access private
	 */

	/** @var    array   form definition (default) */
	var $form_template = array();

	/**#@-*/

	/**
	 *  Error handling of form input validation.
	 *
	 *  @access public
	 *  @param  string      $name   form item name.
	 *  @param  int         $code   error code.
	 */
	function handleError($name, $code)
	{
		return parent::handleError($name, $code);
	}

	/**
	 *  setter method for form template.
	 *
	 *  @access protected
	 *  @param  array   $form_template  form template
	 *  @return array   form template after setting.
	 */
	function _setFormTemplate($form_template)
	{
		return parent::_setFormTemplate($form_template);
	}

	/**
	 *  setter method for form definition.
	 *
	 *  @access protected
	 */
	function _setFormDef()
	{
		return parent::_setFormDef();
	}

	/**
	 *  Form input value convert filter : hex_base64_decrypt
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	function _filter_hex_base64_decrypt($value)
	{
		$value_byte = pack('H*', $value);
		// Base64‚ð‰ð“Ç‚·‚é
		$value_str = base64_decode($value_byte);
		
		$this->logger->log(LOG_DEBUG, "base64_decode value=(".print_r($value_str, true).").");
		
		return $value_str;
	}
}
// }}}

?>
