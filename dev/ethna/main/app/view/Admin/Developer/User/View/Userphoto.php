<?php
/**
 *  Admin/Developer/User/View/Userphoto.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_view_userphoto view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminDeveloperUserViewUserphoto extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$user_m =& $this->backend->getManager('User');
		$photo_m =& $this->backend->getManager('Photo');

		$id = $this->af->get('id');

		$user_base = $user_m->getUserBase($id);
		$list = $photo_m->getUserPhotoByType($id);

		$user_item = array();

		foreach ($list as $key => $val)
		{
			$val['_photo_id'] = substr($val['photo_id'], 1, 4);

			$user_item[] = $val;
		}

		$this->af->setApp('base', $user_base);
		$this->af->setApp('item', $user_item);

		parent::preforward();
	}
}
