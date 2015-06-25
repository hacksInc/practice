<?php
/**
 *  Admin/Developer/Assetbundle/Bgmodel/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_bgmodel_create_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleBgmodelCreateConfirm extends Pp_AdminViewClass
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
			$this->af->setApp($name, array(
				'name' => $this->af->getFileName($name)
			));
		}
		
		$this->af->setApp('bgmodel_id', $assetbundle_m->getBgmodelId($this->af->getApp('file_name')));
		$this->af->setApp('dir',        $assetbundle_m->getBgmodelDir($this->af->getApp('file_name')));
    }
}

?>