<?php
/**
 *  Admin/Developer/Assetbundle/Map/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_map_update_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleMapUpdateConfirm extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		
		foreach (array(
			'asset_bundle_android', 'asset_bundle_iphone', 'asset_bundle_pc',
		) as $name) {
			$arr = array();
			
			$file_name = $this->af->getFileName($name);
			if ($file_name) {
				$arr['name'] = $file_name;
			}
			
			$this->af->setApp($name, $arr);
		}
		
		$this->af->setApp('map_id', $assetbundle_m->getMapId($this->af->getApp('file_name')));
    }
}

?>