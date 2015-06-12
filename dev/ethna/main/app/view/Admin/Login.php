<?php
/**
 *  Admin/Login.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_login view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogin extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$unit_all = $this->config->get('unit_all');

		if (!is_array($unit_all)) {
			$unit_all = array();
		}

		$units = array_keys($unit_all);

		$loginpath = $this->getLoginpath();

		$this->af->setApp('units', $units);
		$this->af->setApp('loginpath', $loginpath);
    }

	/**
	 * loginpathクエリを取得する
	 *
	 * ログインフォーム中のhiddenタグで使用するためのloginpathクエリを取得する
	 */
	protected function getLoginpath()
	{
		$form_path = $this->af->get('loginpath');
		if ($form_path) {
			return $form_path;
		}

		$script_name = $_SERVER['SCRIPT_URL'];
		if (($script_name == '/psychopass_game/admin/logout') || ($script_name == '/psychopass_game/admin/login')) {
			return null;
		}

		return $_SERVER['REQUEST_URI'];
	}
}

?>