<?php
/**
 *  Resource/Homebanner.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  resource_homebanner view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceNewscontentimage extends Pp_ViewClass
{
	protected $filename = null;

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$news_m =& $this->backend->getManager('News');
		$img_id = $this->af->get('img_id');
		$type = $this->af->get('type');

		if ($type == "banner")
		{
			$filename = $news_m->getNewsContentBannerPath($img_id);
		} else {
			$filename = $news_m->getNewsContentPicturePath($img_id);
		}
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
