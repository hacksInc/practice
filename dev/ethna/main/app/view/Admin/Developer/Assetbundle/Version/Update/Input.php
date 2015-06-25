<?php
/**
 *  Admin/Developer/Assetbundle/Version/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_version_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleVersionUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_assetbundle_version_update_exec' => null,
	);
	
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');

		$this->af->setApp('clear_options', $assetbundle_m->RES_VER_CLEAR_OPTIONS);
		$this->af->setApp('res_ver_keys',  Pp_AdminAssetbundleManager::getResVerKeys());
    }
}

?>