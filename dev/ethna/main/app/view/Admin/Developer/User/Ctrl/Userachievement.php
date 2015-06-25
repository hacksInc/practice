<?php
/**
 *  Admin/Developer/User/Ctrl/Userachievement.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_userachievement view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminDeveloperUserCtrlUserachievement extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$user_m =& $this->backend->getManager('User');
		$achievement_m =& $this->backend->getManager('Achievement');

		$id = $this->af->get('id');

		$user_base = $user_m->getUserBase($id);
		$list = $user_m->getUserAchievementRank($id);
		$master = $achievement_m->getMasterAchievementConditionListAssoc();

		$_master = array();
		foreach ($master as $key => $val)
		{
			$_master[$key] = $val['name_ja'];
		}

		$user_item = array();
		foreach ($list as $key => $val)
		{
			$val['ach_name'] = $_master[$val['ach_id']];

			$user_item[] = $val;
		}

		$this->af->setApp('base', $user_base);
		$this->af->setApp('item', $user_item);

		parent::preforward();
	}
}
