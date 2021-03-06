<?php
/**
 *  Admin/Announce/Message/Help/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_help_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageHelpLogView extends Pp_AdminViewClass
{
	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$list = $admin_m->getAdminOperationLogReverse('/announce/message', 'help_log', 200);
		foreach ($list as $i => $row) {
			switch ($row['action']) {
				case 'admin_announce_message_help_create_exec':
					$action_type = '追加';
					break;
				
				case 'admin_announce_message_help_update_exec':
					$action_type = '修正';
					break;
				
				case 'admin_announce_message_help_delete_exec':
					$action_type = '削除';
					break;
				
/*				case 'admin_announce_message_help_download':
					$action_type = 'jsonダウンロード';
					break;*/
				
				default:
					$action_type = null;
			}
			
			if ($action_type) {
				$list[$i]['action_type'] = $action_type;
			}
			
/*			if (isset($row['lang']) && isset($row['ua'])) {
				$list[$i]['lu'] = $row['lang'] . $row['ua'];
            }*/
		}
		
		$this->af->setApp('list', $list);
		$this->af->setApp('form_template', $this->af->form_template);
    }
}
