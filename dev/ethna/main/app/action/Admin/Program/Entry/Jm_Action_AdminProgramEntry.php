<?php
/**
 *  admin_program_entry_* で共通のアクション定義
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */
class Pp_Action_AdminProgramEntry extends Pp_AdminActionClass
{
	/** main環境へのアクセスが必須か */
	protected $must_main = true;
	
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

		if ($this->must_main) {
			$appver_env = Util::getAppverEnv();
			if ($appver_env && ($appver_env != 'main')) {
				$this->af->ae->add(null, "main環境で実行して下さい。");
				return 'admin_error_403';
			}
		}
	}
}
