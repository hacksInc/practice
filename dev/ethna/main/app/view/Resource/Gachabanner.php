<?php
/**
 *  Resource/Gachabanner.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  resource_gachabanner view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceGachabanner extends Pp_ViewClass
{
	protected $filename = null;
	
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$shop_m =& $this->backend->getManager('Shop');
		$gacha_id = $this->af->get('gacha_id');
		
		$filename = $shop_m->getGachaBannerPath($gacha_id);
		if (!file_exists($filename)) {
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		
		$this->filename = $filename;
    }

    /**
     *  遷移名に対応する画面を出力する
     *
     *  @access public
     */
    function forward()
    {
		$filename = $this->filename;
		
		header('Content-type: image/png');
		header('Content-Length: ' . filesize($filename));
		header('Last-Modified: ' . gmdate ('D, d M Y H:i:s', filemtime($filename)) . ' GMT');
		header('Cache-Control: max-age=300');
		
		@readfile($filename);
    }
}

?>