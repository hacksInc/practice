<?php
/**
 *  Admin/Developer/Assetbundle/Bgmodel/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_bgmodel_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleBgmodelUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_assetbundle_bgmodel_update_confirm' => null,
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
		$row['bgmodel_id'] = $assetbundle_m->getBgmodelId($row['file_name']);
		
		$this->af->setApp('row', $row);
    }
}

?>