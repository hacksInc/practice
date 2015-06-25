<?php
/**
 *  Admin/Developer/User/View/Userbase.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_view_userbase view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminDeveloperUserViewUserbase extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$user_m =& $this->backend->getManager('AdminUser');

		$id = $this->af->get('id');

		$data = $user_m->getUserBaseDetail($id);

		$content = json_decode($data['content']);
		$data['os_type'] = $content->operatingSystem;
		$data['device_name'] = $content->deviceModel;

		$this->af->setApp('user', $data);

		parent::preforward();
	}
}
