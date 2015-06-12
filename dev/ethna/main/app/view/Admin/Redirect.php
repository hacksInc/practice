<?php
/**
 *  Admin/Redirect.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  admin_redirect view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminRedirect extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
    }
	
	function forward()
	{
		$path = $this->af->getApp('path');

		// URL組み立て
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// 開発・ステージングのロードバランサー(nginx)経由の場合、
			// ブラウザ上のURLがPHPで判別できる物と異なるので、$schemeと$portは直書き
			$scheme = 'https';
			$port = 10443;
		} else {
			if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
				$scheme = 'https';
			} else {
				$scheme = 'http';
			}

			$port = null;
			if ((($scheme == 'https') && ($_SERVER['SERVER_PORT'] != 443)) ||
				(($scheme == 'http') && ($_SERVER['SERVER_PORT'] != 80))
			) {
				$port = $_SERVER['SERVER_PORT'];
			}
		}
		
		$host = $_SERVER['SERVER_NAME'];
		
		$url = $scheme . '://' . $host . ($port ? ":$port" : '') . $path;
		
		// リダイレクト
		header('Location: ' . $url);
	}
}

?>