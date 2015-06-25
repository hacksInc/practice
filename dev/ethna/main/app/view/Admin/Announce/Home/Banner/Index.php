<?php
/**
 *  Admin/Announce/Home/Banner/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_home_banner_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHomeBannerIndex extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_home_banner_create_exec' => null,
	);

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$news_m =& $this->backend->getManager('AdminNews');

		$list = $news_m->getHomeBannerList(0, 100000, false);

		if ($list) foreach ($list as $i => $row) {
			if ($row['date_start'] > date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])) {
				$status = 'waiting';
			} else if ($row['date_end'] > date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])) {
				$status = 'active';
			} else {
				$status = null;
			}

			$list[$i]['status'] = $status;

//						$list[$i]['date_disp_short'] = $news_m->getDateDispShort($row['date_disp']);
		}

//				$resource_host = str_replace(array('mgr.', ':10443'), '', $_SERVER['HTTP_HOST']);

		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);
		$this->af->setApp('form_template', $this->af->form_template);
//				$this->af->setApp('resource_host', $resource_host);
		$this->af->setApp('mtime', $news_m->getHomeBannerDirMtime());
	}
}
