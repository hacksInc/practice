<?php
/**
 *  Resource/Eventinfo.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  resource_eventinfo view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceEventinfo extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$news_m =& $this->backend->getManager('News');
		$lang = $this->af->get('lang');
		$ua   = $this->af->get('ua');
		
		$list = $news_m->getCurrentEventNewsContentList($lang, $ua);
//		if (is_array($list)) foreach ($list as $i => $row) {
//			$list[$i]['date_disp_short'] = $news_m->getDateDispShort($row['date_disp']);
//		}
		
		$this->af->setApp('lang', $lang);
		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);
		
		$this->af->setApp('appver', $this->getAppver());
    }

	// imgタグのクエリストリングにappverを付ける為の暫定対応(2014/01/07)
	// TODO: htdocs/resource.php にも同様の処理があるので、共通化すること
	protected function getAppver()
	{
		// HTTPリクエストからクライアントバージョンを取得
		$headers = getallheaders();
		if (isset($headers['X-Jugmon-Appver']) && 
			preg_match("/^[0-9]{1,10}$/", $headers['X-Jugmon-Appver']) // 整数値かチェック
		) {
			// リクエストヘッダから取得
			$client_ver = $headers['X-Jugmon-Appver'];

		} else if (isset($_GET['appver']) && 
			preg_match("/^[0-9]{1,10}$/", $_GET['appver']) // 整数値かチェック
		) {
			// クエリストリングから取得
			$client_ver = $_GET['appver'];

		} else {
			// クライアントバージョンが通知されなかった場合は現行バージョンとして扱う
			$client_ver = PP_CURRENT_VER;
		}
		
		return $client_ver;
	}
}

?>