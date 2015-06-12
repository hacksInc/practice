<?php
/**
 *  Admin/Announce/Deploy/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_deploy_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceDeployLogView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$limit = 200;
		
		$all = array();
		foreach (array('rsync', 'makuo') as $type) {
			$dir = '/api/' . $type . '/announce';
			$list_tmp = $admin_m->getAdminOperationLogReverse($dir, 'success', $limit);
			if (!is_array($list_tmp)) {
				continue;
			}
			
			$all = array_merge($all, $list_tmp);
		}
			
		usort($all, 'time_compare_func');
		$list = array_slice($all, 0, $limit);
		
		foreach ($list as $i => $row) {
			switch ($row['action']) {
				case 'admin_api_rsync':
					$action_type = '商用反映';
					break;

				case 'admin_api_makuo':
					$action_type = 'デプロイ';
					break;

				default:
					$action_type = null;
			}

			if ($action_type) {
				$list[$i]['action_type'] = $action_type;
			}
		}
		
		$this->af->setApp('list', $list);
    }
}

function time_compare_func($a, $b)
{
	return strcmp($b['time'], $a['time']);
}

?>