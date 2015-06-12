<?php
/**
 *  Admin/Developer/Assetbundle/Monster/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_monster_update_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleMonsterUpdateConfirm extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$id = $this->af->get('id');
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		
		$row = $assetbundle_m->getMasterAssetBundleEx($id);
//		foreach (array('icon', 'image') as $type) {
//			if (is_file($row[$type])) {
//				$row[$type . '_basename'] = basename($row[$type]);
//			}
//		}
		
		$this->af->setApp('row', $row);
//		$this->af->setApp('active_flg_options', array(0, 1));
		
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