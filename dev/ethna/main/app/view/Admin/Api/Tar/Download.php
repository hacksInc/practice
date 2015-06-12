<?php
/**
 *  Admin/Api/Tar/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_api_tar_download view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminApiTarDownload extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
    }
	
	/**
	 * 
	 */
	function forward()
	{
		$command  = $this->af->getApp('command');
		$filename = $this->af->getApp('filename');
		$download_uniq = $this->af->getApp('download_uniq');
		
		// Cookieにダウンロードユニーク値をセット
		setcookie('download_uniq', $download_uniq, $_SERVER['REQUEST_TIME'] + 86400, '/psychopass_game/admin/');

		// HTTPヘッダ出力
		header('HTTP/1.1 200 OK');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Content-Type: application/octet-stream');
		header('Transfer-Encoding: chunked');
		flush();
		
		// tarを実行しながらHTTPボディ出力
		$fp = popen($command, 'rb');
		while (!feof($fp)) {
			$buf = fread($fp, 8192);
			$len = strlen($buf);
			
			if ($len > 0) {
				echo sprintf("%x", $len) . "\r\n";
				echo $buf . "\r\n";
			}
		}

		$exit_code = pclose($fp);
		
		// exit codeをセッションに保持
		$cache_key = 'exit_code_' . $download_uniq;
		$this->session->set($cache_key, $exit_code);
		
		// HTTPボディ出力終了
		echo "0\r\n";
	}
}

?>