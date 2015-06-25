<?php
/**
 *  Admin/Developer/Assetbundle/Version/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_version_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleVersionLogView extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_assetbundle_version_create_exec' => null,
	);
	
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$admin_m =& $this->backend->getManager('Admin');
		
		$list = $admin_m->getAdminOperationLogReverse('/developer/assetbundle', 'version_log', 200);
		foreach ($list as $i => $row) {
			switch ($row['action']) {
				case 'admin_developer_assetbundle_version_create_exec':
					$action_type = '追加';
					break;
				
				case 'admin_developer_assetbundle_version_update_exec':
					$action_type = '修正';
					break;
				
				case 'admin_developer_assetbundle_version_delete_exec':
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
		$this->af->setApp('clear_options', $assetbundle_m->RES_VER_CLEAR_OPTIONS);
		$this->af->setApp('res_ver_keys',  Pp_AdminAssetbundleManager::getResVerKeys());
    }
}

?>
