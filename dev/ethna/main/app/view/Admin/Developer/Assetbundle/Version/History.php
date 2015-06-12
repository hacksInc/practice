<?php
/**
 *  Admin/Developer/Assetbundle/Version/History.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_version_history view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleVersionHistory extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_assetbundle_version_create_exec' => null,
	);

	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$list = $assetbundle_m->getVersionList(0, 100, true);
		
		$this->af->setApp('list', $list);
		$this->af->setApp('clear_options', $assetbundle_m->RES_VER_CLEAR_OPTIONS);
		$this->af->setApp('res_ver_keys',  Pp_AdminAssetbundleManager::getResVerKeys());
    }
}

?>