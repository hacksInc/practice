<?php
/**
 *  Admin/Developer/Assetbundle/Monster/Upload/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_monster_upload_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleMonsterUploadConfirm extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		
		$monster_id = $this->af->getApp('monster_id');
		$version    = $this->af->getApp('version');
		$file_name  = $this->af->getApp('file_name');		
		
		if ($assetbundle_m->getMasterAssetBundleByUniqueKey(
			1, 'monster/' . $monster_id, $file_name, $version
		)) {
			$this->af->setApp('row_exists', true);
		}
		
		foreach (array(
			'monster_icon', 'monster_image',
			'asset_bundle_android', 'asset_bundle_iphone', 'asset_bundle_pc',
		) as $name) {
			$this->af->setApp($name, array(
				'name' => $this->af->getFileName($name)
			));
		}
    }
}

?>