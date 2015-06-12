<?php
/**
 *  Admin/Developer/User/Ctrl/Userbase.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_userbase view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminDeveloperUserCtrlUserbase extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$user_m =& $this->backend->getManager('AdminUser');

		$data = $user_m->getUserBaseDetail($this->af->get('id'));

		$content = json_decode($data['content']);
		$data['os_type'] = $content->operatingSystem;
		$data['device_name'] = $content->deviceModel;

		$this->af->setApp('user', $data);
		$this->af->setAppNe('user_json', json_encode($data));

		parent::preforward();
	}
}
