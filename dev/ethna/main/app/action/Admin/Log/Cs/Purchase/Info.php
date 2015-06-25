<?php
/**
 *  Admin/Log/Cs/Purchase/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_log_cs_purchase_info Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsPurchaseInfo extends Pp_Form_AdminLogCs
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'api_transaction_id' => array('require' => true),
	);

}

/**
 *  admin_log_cs_purchase_info action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsPurchaseInfo extends Pp_AdminActionClass
{

	/**
	 *  preprocess of admin_log_cs_purchase_info Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{

		// アクセス制御
		if ($this->must_login && $this->must_permission) {
			$ret = $this->permit();
			if ($ret) {
				return $ret;
			}
		}

		if ($this->af->validate() > 0) {
			return 'admin_log_cs_purchase_info';
		}

		return null;

	}

	/**
	 *  admin_log_cs_purchase_info action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{

		return 'admin_log_cs_purchase_info';
	}
}
