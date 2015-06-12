<?php
/**
 *  Admin/Announce/Event/News/Content/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_event_news_content_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceEventNewsContentCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_event_news_content_create_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$user_m =& $this->backend->getManager('User');
		
		$row = $this->af->getApp('row');
		
		if (is_array($row)) { // 複製の場合
			$this->af->setAppNe('body', $row['body']);
		} else { // 新規の場合
			$row = array(
				'priority'   => 99,
				'ua'         => Pp_UserManager::OS_IPHONE_ANDROID,
				'date_disp'  => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
				'date_start' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
				'date_end'   => date('Y-m-d', $_SERVER['REQUEST_TIME'] + (86400 * 7)) . ' 23:59:59', // 7日後の終わり時間
			);
			
			$this->af->setApp('row', $row);
		}
    }
}

?>
