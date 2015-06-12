<?php
/**
 *  Admin/Developer/User/Api.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_index Form implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Form_AdminDeveloperUserApi extends Pp_AdminActionForm
{
	var $form = array(
		'table',
		'id',
	);
}

/**
 *  admin_developer_user_index action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminDeveloperUserApi extends Pp_AdminActionClass
{
	/**
	 *  admin_developer_user_api action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	public function perform()
	{
		$table = $this->af->get('table');
		$id = $this->af->get('id');

		$restserver_m =& $this->backend->getManager('Restserveruser');
		$data = $restserver_m->getDateModified($table, $id);

		$this->af->setApp('code', 200);
		$this->af->setApp('date_modified', $data['date_modified']);

		return 'admin_json_encrypt';
	}
}
