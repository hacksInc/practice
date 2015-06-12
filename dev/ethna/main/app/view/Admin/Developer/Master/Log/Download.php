<?php
/**
 *  Admin/Developer/Master/Log/Download.php
 *
 *  このビューは admin_developer_master_log_download アクション以外からも呼ばれる。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_log_download view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterLogDownload extends Pp_AdminViewClass
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
	
	function forward ()
	{
		$developer_m =& $this->backend->getManager('Developer');
		$file = $this->af->get('file');
		$fullpath = $this->af->getApp('fullpath');

		$fp = fopen($fullpath, 'rb');
			
		// CSV出力
		header("Content-Disposition: attachment; filename=" . $file);
		header("Content-Length: " . filesize($fullpath));
		header("Content-Type: application/octet-stream");
		fpassthru($fp);
	}
}

?>
