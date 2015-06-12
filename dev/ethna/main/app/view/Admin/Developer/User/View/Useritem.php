<?php
/**
 *  Admin/Developer/User/View/Useritem.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_view_useritem view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminDeveloperUserViewUseritem extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$user_m =& $this->backend->getManager('User');
		$item_m =& $this->backend->getManager('Item');

		$id = $this->af->get('id');

		$user_base = $user_m->getUserBase($id);
		$master = $item_m->getMasterItemList();
		$list = $item_m->getUserItemList($id);

		$user_item = array();
		foreach($list as $key => $val)
		{
			$_item = $item_m->getMasterItem($val['item_id']);
			$val['name'] = $_item['name_ja'];

			$user_item[] = $val;
		}

		$this->af->setApp('base', $user_base);
		$this->af->setApp('item', $user_item);

		parent::preforward();
	}
}
