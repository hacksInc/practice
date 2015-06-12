<?php
// vim: foldmethod=marker
/**
 *  Pp_CliActionClass.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

// {{{ Pp_CliActionClass
/**
 *  Command Line Interface用 action execution class
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_CliActionClass extends Ethna_ActionClass
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
		$ret = parent::authenticate();
		if ($ret) {
			return $ret;
		}

		$ret = $this->authenticateUnit();
		if ($ret) {
			return $ret;
		}
	}
	
	private function authenticateUnit()
	{
		$unit_m = $this->backend->getManager('Unit');
		
		$unit = null;
		if (isset($_ENV['JUGMON_UNIT'])) {
			$unit = $_ENV['JUGMON_UNIT'];
		} else if (isset($_SERVER['JUGMON_UNIT'])) {
			$unit = $_SERVER['JUGMON_UNIT'];
		} else {
			$unit_default = $this->config->get('unit_default');
			if (is_array($unit_default) && isset($unit_default['cli'])) {
				$unit = $unit_default['cli'];
			}
		}
		
		if ($unit) {
			$unit_m->resetUnit($unit);
			$this->backend->logger->log(LOG_DEBUG, 'Unit found. unit=[' . $unit . ']');
			
			// setSessionSqlBigSelectsOnはコンストラクタで行なっていたが、
			// $admin_m->offSessionQueryCacheで、
			// PHP Fatal error:  Call to a member function query() on a non-object
			// となるエンバグがあったので、ここに移動した。
			$admin_m =& $this->backend->getManager('Admin');
			$admin_m->setSessionSqlBigSelectsOn();
			
		} else {
			$this->backend->logger->log(LOG_WARNING, 'Unit not found.');
		}
		
		return;
	}
	
	/**
	 * ディレクトリロックする
	 * 
	 * @param string $path ロックディレクトリのパス（省略可，省略するとEthnaアクション名から決定）
	 * @param int $expire アンロックされず放置されたディレクトリを無効とみなすまでの秒数（省略可，省略すると3600）
	 * @return bool 成否
	 */
	protected function dirLock($path = null, $expire = 3600)
	{
		if ($path === null) {
			$path = $this->getDefaultLockDir();
		}
	
		// ロックディレクトリが存在するか
		if (is_dir($path)) {
			// expire時間が経過しているか
			$mtime = filemtime($path);
			$threshold = time() - $expire;
			
			if ($threshold <= $mtime) {
				return false;
			}

			// 一旦ロックディレクトリを削除
			rmdir($path);
		}
		
		// ディレクトリを作成できたらロック成功
		return mkdir($path);
	}
	
	/**
	 * ディレクトリロックを解除する
	 * 
	 * @param string $path ロックディレクトリのパス（省略可，省略するとEthnaアクション名から決定）
	 * @return bool 成否
	 */
	protected function dirUnlock($path = null)
	{
		if ($path === null) {
			$path = $this->getDefaultLockDir();
		}
		
		return rmdir($path);
	}

	/**
	 * デフォルトのロック用ディレクトリのパスを取得する
	 * 
	 * 現在のEthnaアクション名から決定する
	 * @return string ロックディレクトリのパス
	 */
	protected function getDefaultLockDir()
	{
		$tmp = $this->backend->ctl->getDirectory('tmp');
		$action = $this->backend->ctl->getCurrentActionName();
		$path = "{$tmp}/cli_{$action}_lockdir";
		
		return $path;
	}
}
// }}}

?>