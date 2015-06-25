<?php
/**
 *  Admin/Developer/Assetbundle/Monster/Image.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_monster_image view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleMonsterImage extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
/*
    function preforward()
    {
    }
*/
	
    function forward()
    {
		$path = $this->af->getApp('path');
		
		header('Cache-Control: max-age=999999');
		header('Content-type: image/png');
		@readfile($path);
    }
}

?>