<?php
/**
 *  Admin/Developer/User/Ctrl/Useritem.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_useritem view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminDeveloperUserCtrlUseritem extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$developer_m =& $this->backend->getManager('Developer');
		$user_m =& $this->backend->getManager('User');
		$item_m =& $this->backend->getManager('Item');

		$table = $this->af->get('table');
		$id = $this->af->get('id');

		$user_base = $user_m->getUserBase($id);
		$item_master = $item_m->getMasterItemList();
		$item_list = $item_m->getUserItemList($id);

		$user_item = array();
		foreach($item_list as $key => $val)
		{
			$item_id = $val['item_id'];
			foreach($item_master as $mkey => $mval)
			{
				if ($mval['item_id'] == $item_id)
				{
					$val['name'] = $mval['name_ja'];
					break;
				}
			}
			$user_item[] = $val;
		}

		$this->af->setApp('base', $user_base);
		$this->af->setApp('item', $user_item);
		$this->af->setAppNe('item_json', json_encode($user_item));

		parent::preforward();
	}
}
