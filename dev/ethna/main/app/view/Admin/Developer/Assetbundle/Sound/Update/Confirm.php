<?php
/**
 *  Admin/Developer/Assetbundle/Sound/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_sound_update_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleSoundUpdateConfirm extends Pp_AdminViewClass
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
    }
}

?>