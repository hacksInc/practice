<?php
/**
 *  Resource/Helpdetailimage.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  resource_helpdetailimage view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceHelpdetailimage extends Pp_ViewClass
{
	protected $filename = null;

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$help_m =& $this->backend->getManager('Help');
		$img_id = $this->af->get('img_id');
		$filename = $help_m->getHelpDetailPicturePath($img_id);

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

