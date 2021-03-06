<?php
/**
 *  Admin/Developer/Master/Deploy/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_master_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterDeployConfirm extends Pp_AdminActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'mode' => array(
			'type'        => VAR_TYPE_STRING, // Input type
			'required'    => true,                 // Required Option(true/false)
			'min'         => null,                 // Minimum value
			'max'         => 16,                   // Maximum value
			'regexp'      => '/^deploy|refresh|standby$/', // String by Regexp
		),


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

	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);

		$developer_m =& $this->backend->getManager('Developer');
		$tables = $developer_m->MASTER_INDEX_TABLES;

		foreach ($tables as $table) {
			$this->form[$table] = array(
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_HIDDEN,   // Form type
				//'name'        => $table, // Display name
				'required'    => false,             // Required Option(true/false)
			);
		}

	}

}

/**
 *  admin_developer_master_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterDeployConfirm extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_developer_master_confirm Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
/*
	function prepare()
	{
		// アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
	}
 */

	/**
	 *  admin_developer_master_confirm action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_developer_master_deploy_confirm';
	}
}

?>
