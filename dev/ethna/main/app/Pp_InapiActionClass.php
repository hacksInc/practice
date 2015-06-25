<?php
// vim: foldmethod=marker
/**
 *  Pp_InapiActionClass.php
 *
 *  内部ネットワークAPIアクションクラス
 *  Node.jsからのリクエスト受付用
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//require_once 'Base/Client.php';
require_once 'Pp_InapiActionForm.php';

// {{{ Pp_InapiActionClass
/**
 *  inapi action execution class
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_InapiActionClass extends Ethna_ActionClass
{
	/**
	 *  authenticate before executing action.
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (null if no errors. false if we have something wrong.)
	 */
	function authenticate()
	{
		$parent_ret = parent::authenticate();
		if ($parent_ret) {
			return $parent_ret;
		}
		
		$ret = $this->authenticateUnit();
		if ($ret) {
			return $ret;
		}

/* ネットワーク的に外部からinapiを呼べなくしてもらう事で対応することにした。PHPでのチェックは行わない。
		// アクセス元IPアドレスチェック
error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . $_SERVER['REMOTE_ADDR']);
//TODO:サーバ構成決定後、アクセス元チェックを復活させること
//X_FORWARDED_FORも見る必要がある（プロクシ経由）の場合はBase_Client::getRemoteAddr()を使用すること
//localhostかどうかのチェックだけなら直接 $_SERVER['REMOTE_ADDR'] を参照するだけでOK
//		$remote_addr = Base_Client::getRemoteAddr();
		if (!isset($_SERVER['REMOTE_ADDR']) ||
			(strcmp($_SERVER['REMOTE_ADDR'], '127.0.0.1') !== 0) // localhost
		) {
			$this->backend->logger->log(LOG_DEBUG, 'Access denied. [' . $remote_addr . ']');
//			header('HTTP/1.0 403 Forbidden');
//			exit;
		}
*/

		// OK
		return null;
	}
	
	/**
	 * authenticate時に行うユニット判別処理
	 */
	private function authenticateUnit()
	{
		$unit_m = $this->backend->getManager('Unit');

		$unit_all     = $this->config->get('unit_all');
		$unit_default = $this->config->get('unit_default');
		
		$unit = null;
		if (is_array($unit_default) && isset($unit_default['inapi'])) {
			$unit = $unit_default['inapi'];
		}
		
		if ($unit && is_array($unit_all) && isset($unit_all[$unit])) {
			$unit_m->resetUnit($unit);
			$this->backend->logger->log(LOG_DEBUG, 'Unit found. unit=[' . $unit . ']');
		} else {
			$this->backend->logger->log(LOG_WARNING, 'Unit not found.');
		}
		
		// OK
		return null;
	}
	
	/**
	 *  Preparation for executing action. (Form input check, etc.)
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (null if no errors. false if we have something wrong.)
	 */
	function prepare()
	{
		return parent::prepare();
	}

	/**
	 *  execute action.
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (we does not forward if returns null.)
	 */
	function perform()
	{
		return parent::perform();
	}
}
// }}}
?>