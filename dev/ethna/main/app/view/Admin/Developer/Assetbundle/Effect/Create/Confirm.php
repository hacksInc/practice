<?php
/**
 *  Admin/Developer/Assetbundle/Effect/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_effect_create_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleEffectCreateConfirm extends Pp_AdminViewClass
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
    }
}

?>