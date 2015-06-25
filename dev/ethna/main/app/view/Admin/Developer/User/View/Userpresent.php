<?php
/**
 *  Admin/Developer/User/View/Userpresent.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_view_userpresent view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminDeveloperUserViewUserpresent extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$user_m =& $this->backend->getManager('User');
		$present_m =& $this->backend->getManager('Present');
		$item_m =& $this->backend->getManager('Item');

		$id = $this->af->get('id');

		$user_base = $user_m->getUserBase($id);
		$list = $present_m->getUserPresentList($id);

		$user_item = array();

		foreach ($list as $key => $val)
		{
			$val['_present_value'] = $val['present_value'];

			switch ((int)$val['present_category'])
			{
				case Pp_PresentManager::CATEGORY_ITEM :

					$_item = $item_m->getMasterItem($val['present_value']);
					$val['_present_value'] = $_item['name_ja'];
					break;

				case Pp_PresentManager::CATEGORY_PHOTO :

					$val['_present_value'] = substr($val['present_value'], 1, 4);
					break;

				case Pp_PresentManager::CATEGORY_PP :
					break;
			}

			$val['status_name'] = $present_m->getStatusName($val['status']);

			$user_item[] = $val;
		}

		$this->af->setApp('base', $user_base);
		$this->af->setApp('item', $user_item);

		parent::preforward();
	}
}
