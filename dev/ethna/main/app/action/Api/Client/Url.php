<?php
/**
 *  Api/Client/Url.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  api_client_url Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiClientUrl extends Pp_ApiActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'c'
	);
}

/**
 *  api_client_url action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiClientUrl extends Pp_ApiActionClass
{
	/**
	 *  preprocess of api_client_url Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		if ($this->af->validate() > 0) {
			return 'error_400';
		}

		return null;
	}

	/**
	 *  api_client_url action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$url_id = $this->af->get('url_id');

		$news_m =& $this->backend->getManager('News');
		
		// URLを取得
		$url_data = $news_m->getUrlData($url_id);
		//DBエラー
		if (!$url_data || Ethna::isError($url_data)) {
			$this->af->setApp('url_data', '', true);
		}
		$url = '';
		if (isset($url_data['url'])) $url = $url_data['url'];
		
		// ビュー用にアサイン
		$this->af->setApp('url_data', $url, true);
		
		return 'api_json_encrypt';
	}
}

?>