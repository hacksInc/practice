<?php
/**
 *  Admin/Developer/Assetbundle/Version/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_version_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleVersionCreateInput extends Pp_AdminViewClass
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
		
		$row = $assetbundle_m->getLatestVersion();
		if (!$row || !is_array($row)) {
			$row = array();
		}
		
		// デフォルトで当日0時を設定する
		$row['date_start'] = date('Y-m-d', $_SERVER['REQUEST_TIME']) . ' 00:00:00';
		
		$this->af->setApp('row', $row);
		$this->af->setApp('clear_options', $assetbundle_m->RES_VER_CLEAR_OPTIONS);
		$this->af->setApp('res_ver_keys',  Pp_AdminAssetbundleManager::getResVerKeys());
    }
}

?>