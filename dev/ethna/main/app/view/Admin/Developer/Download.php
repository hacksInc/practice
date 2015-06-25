<?php
/**
 *  Admin/Developer/Download.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_download view implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_View_AdminDeveloperDownload extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$file_name = $this->af->get('file_name');
		$mime_type = 'text/plain';

		header("Content-type: $mime_type");
		header("Content-Disposition: attachment; filename=" . $file_name . ".csv");

		$file =  BASE . '/tmp/asset_bundle_csv/' . $file_name;

		header('Content-Length: ' . filesize($file));
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		ob_clean();
		flush();
		readfile($file);
		unlink($file);
	}
}
