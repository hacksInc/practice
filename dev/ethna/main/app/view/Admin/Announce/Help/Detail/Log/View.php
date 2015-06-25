<?php
/**
 *  Admin/Announce/Help/Detail/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_help_detail_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHelpDetailLogView extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$admin_m =& $this->backend->getManager('Admin');

		$list = $admin_m->getAdminOperationLogReverse('/announce/help', 'detail_log', 200);
		foreach ($list as $i => $row) {
			switch ($row['action']) {
			case 'admin_announce_help_detail_create_exec':
				$action_type = '追加';
				break;

			case 'admin_announce_help_detail_update_exec':
				$action_type = '修正';
				break;

			case 'admin_announce_help_detail_delete_exec':
				$action_type = '削除';
				break;

			default:
				$action_type = null;
			}

			if ($action_type) {
				$list[$i]['action_type'] = $action_type;
			}
		}

		$this->af->setApp('list', $list);
		$this->af->setApp('form_template', $this->af->form_template);
		$this->af->setApp('category_list', $this->af->getCategoryList());
	}
}
