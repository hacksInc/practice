<?php
/**
 *  Admin/Developer/Assetbundle/Monster/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_monster_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleMonsterUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_assetbundle_monster_update_confirm' => null,
	);

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
   }
}

?>