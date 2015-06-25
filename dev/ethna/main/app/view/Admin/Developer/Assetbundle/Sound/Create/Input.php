<?php
/**
 *  Admin/Developer/Assetbundle/Sound/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_sound_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleSoundCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_assetbundle_sound_create_confirm' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
    }
}

?>