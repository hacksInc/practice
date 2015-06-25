<?php
/**
 *  Pp_CliLogger.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  CLIログ管理クラス
 *
 *  @access     public
 *  @package    Pp
 */
class Pp_CliLogger extends Ethna_Logger
{
    /**
     *  アラートメールを送信する
     *
	 *  前回送信日時がテンポラリファイルに記録されている場合、
	 *  設定ファイルの"log_alert_throttle"の期間（秒数）が経過済みの場合のみ送信する。
	 *  (大量メール発生を抑止)
     *  @access protected
     *  @param  string  $message    ログメッセージ
     *  @return int     0:正常終了
     *  @deprecated
     */
    function _alert($message)
    {
        $config =& $this->controller->getConfig();
		
		$touch_file = $this->controller->getDirectory('tmp') . '/' 
					. $this->controller->getAppId() 
					. '_cli_logger_log_alert_throttle_touch';
		
		$throttle = $config->get('log_alert_throttle');
		if ($throttle > 0) {
			$throttle_mtime = file_exists($touch_file) ? filemtime($touch_file)
			                                           : false; 
			if ($throttle_mtime && ($throttle_mtime > time() - $throttle)) {
				return 0;
			}
		}
		
		touch($touch_file);

		return parent::_alert($message);
    }
}