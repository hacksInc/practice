<?php
/**
 *  Admin/Developer/Download.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_download Form implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Form_AdminDeveloperDownload extends Pp_AdminActionForm
{
	/**
	 *  @access private
	 *  @var	array   form definition.
	 */
	var $form_template = array(
		'file_name' => array(
			// Form definition
			'type'		=> VAR_TYPE_STRING,	 	// Input type
			'form_type'	=> FORM_TYPE_HIDDEN,	// Form type
			'name'		=> 'ファイル名',		// Display name

			//  Validator (executes Validator by written order.)
			'required'	=> false, // Required Option(true/false)
		),
	);
	var $form = array(
		'file_name',
	);
}

/**
 *  admin_developer_download action implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_Action_AdminDeveloperDownload extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_kpi_download Action.
	 *
	 *  @access public
	 *  @return string	forward name(null: success.
	 *								false: in case you want to exit.)
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
			return 'admin_developer_assetbundle_list_index';
		}
	}

	/**
	 *  admin_developer_download action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_developer_download';
	}
}
