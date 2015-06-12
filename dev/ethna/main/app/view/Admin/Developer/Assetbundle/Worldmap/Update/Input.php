<?php
/**
 *  Admin/Developer/Assetbundle/Worldmap/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_worldmap_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleWorldmapUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_assetbundle_worldmap_update_confirm' => null,
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
		
		$row = $assetbundle_m->getMasterAssetBundle($id);
		
		$this->af->setApp('row', $row);
    }
}

?>