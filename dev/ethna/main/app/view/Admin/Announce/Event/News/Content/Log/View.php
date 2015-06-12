<?php
/**
 *  Admin/Announce/Event/News/Content/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_event_news_content_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceEventNewsContentLogView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$list = $admin_m->getAdminOperationLogReverse('/announce/event_news', 'content_log', 200);
		foreach ($list as $i => $row) {
			switch ($row['action']) {
				case 'admin_announce_event_news_content_create_exec':
					$action_type = '追加';
					break;
				
				case 'admin_announce_event_news_content_update_exec':
					$action_type = '修正';
					break;
				
				case 'admin_announce_event_news_content_end_exec':
					$action_type = '表示終了';
					break;
				
				case 'admin_announce_event_news_content_sts_exec':
					$action_type = '表示ステータス';
					break;
				
				default:
					$action_type = null;
			}
			
			if ($action_type) {
				$list[$i]['action_type'] = $action_type;
			}
			
			if (isset($row['lang']) && isset($row['ua'])) {
				$list[$i]['lu'] = $row['lang'] . $row['ua'];
			}
		}
		
		$this->af->setApp('list', $list);
		$this->af->setApp('form_template', $this->af->form_template);
    }
}

?>