<?php
/**
 *  Admin/Developer/Assetbundle/Version/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_version_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleVersionIndex extends Pp_AdminViewClass
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
		
		$list = $assetbundle_m->getVersionList();
		
		$first_row = $assetbundle_m->getLatestVersion();
		if ($first_row && is_array($first_row)) {
			$first_row['date_start'] = null;
			$first_row['date_deletion'] = null;
		} else {
			$first_row = array(
				'app_ver'         => null,
/*				
				'res_ver'         => null,
				'mon_ver'         => null,
				'mon_image_ver'   => null,
				'skilldata_ver'   => null,
				'skilleffect_ver' => null,
				'bgmodel_ver'     => null,
				'sound_ver'       => null,
				'map_ver'         => null,
				'worldmap_ver'    => null,
				'mon_exp_ver'     => null,
				'player_rank_ver' => null,
				'ach_ver'         => null,
				'mon_act_ver'     => null,
		        'boost_ver'       => null,
		        'badge_ver'          => null,
		        'badge_material_ver' => null,
		        'badge_skill_ver'    => null,
*/				
				'clear'           => null,
				'date_start'      => null,
				'date_deletion'   => null,
			);
			
			foreach (Pp_AdminAssetbundleManager::getResVerKeys() as $key) {
				$first_row[$key] = null;
			}
		}
		
		if ($list && isset($list[0])) {
			array_unshift($list, $first_row);
		} else {
			$list = array($first_row);
		}
		
		$this->af->setApp('list', $list);
		$this->af->setApp('clear_options', $assetbundle_m->RES_VER_CLEAR_OPTIONS);
		$this->af->setApp('res_ver_keys',  Pp_AdminAssetbundleManager::getResVerKeys());
    }
}

?>