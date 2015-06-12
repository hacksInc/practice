<?php
// vim: foldmethod=marker
/**
 *  Pp_NoahActionClass.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Base/Client.php';
require_once 'classes/Util.php';
require_once 'Pp_ActionForm.php';

// {{{ Pp_NoahActionClass
/**
 *  管理画面用 action execution class
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_NoahActionClass extends Ethna_ActionClass
{
	/**
	 *  コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);
	}

	/**
	 *  authenticate before executing action.
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (null if no errors. false if we have something wrong.)
	 */
	function authenticate()
	{
	//	return parent::authenticate();
		$parent_ret = parent::authenticate();
		if ($parent_ret) {
			return $parent_ret;
		}
		// ユニットチェック
		// マネージャが未生成の段階でチェックしないと以後のDB接続に支障があるのでここで行う
		$ret = $this->authenticateUnit();
		if ($ret) {
			return $ret;
		}
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
	
	/**
	 * 
	 */
	private function authenticateUnit()
	{
		$action = $this->backend->ctl->getCurrentActionName();
		$unit_m = $this->backend->getManager('Unit');
		$user_id = $_GET['guid'];//ユニットを調べるためユーザIDを取得する
	//	$unit = $unit_m->cacheGetUnitFromUserId($user_id);
		$unit = $unit_m->getUnitFromUserId($user_id);
		if (!is_numeric($unit)) {
			header('HTTP/1.0 500 Internal Server Error');
			error_log("[Noah]$user_id HTTP:500 Unit not found");
			exit;
		}
		$unit_m->resetUnit($unit);
		
		//OK
		$this->backend->logger->log(LOG_DEBUG, "Unit allocated. unit=[" . $unit . "] action=[" . $action . "]");
		return;
	}
	
}
?>