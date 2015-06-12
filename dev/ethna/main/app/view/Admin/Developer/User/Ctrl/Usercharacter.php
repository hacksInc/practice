<?php
/**
 *  Admin/Developer/User/Ctrl/Usercharacter.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_character ctrl implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminDeveloperUserCtrlUsercharacter extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$user_m =& $this->backend->getManager('User');
		$photo_m =& $this->backend->getManager('Character');

		$id = $this->af->get('id');

		$user_base = $user_m->getUserBase($id);
		$list = $photo_m->getUserCharacter($id);
		$master = $photo_m->getMasterCharacterList();

		$_master = array();
		foreach ($master as $key => $val)
		{
			$_master[$val['character_id']] = $val['name_ja'];
		}

		$user_item = array();
		foreach ($list as $key => $val)
		{
			$val['character_name'] = $_master[$val['character_id']];

			$user_item[] = $val;
		}

		$this->af->setApp('base', $user_base);
		$this->af->setApp('item', $user_item);
		$this->af->setAppNe('item_json', json_encode($user_item));

		parent::preforward();
	}
}
