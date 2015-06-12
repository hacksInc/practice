<?php
/**
 *  Admin/Program/Deploy/Ctrl/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_program_deploy_ctrl_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminProgramDeployCtrlLogView extends Pp_AdminViewClass
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
		foreach (array('rsync', 'makuo', 'svn') as $type) {
			$dir = '/api/' . $type . '/program';
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

				case 'admin_api_svn_checkout':
					$action_type = 'SVN反映';
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