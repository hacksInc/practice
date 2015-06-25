<?php
// vim: foldmethod=marker
/**
 *  Pp_Logger.php
 */

// {{{ Pp_Logger
/**
 *  ログ管理クラス
 */
class Pp_Logger extends Ethna_Logger
{
    // {{{ log
    /**
     *  ログを出力する
     *
     *  @access public
     *  @param  int     $level      ログレベル(LOG_DEBUG, LOG_NOTICE...)
     *  @param  string  $message    ログメッセージ(+引数)
     */
    function log($level, $message)
    {
		// 可変長の引数リストを処理
        $args = func_get_args();
        if (count($args) > 2) {
            array_splice($args, 0, 2);
            $message = vsprintf($message, $args);
        }
		
		// 1行が長い場合は複数行分割（syslog対策）
		$messages = str_split($message, 1500);

		// ログメッセージにユーザIDを付加する準備
		$user_expr = '';
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$user_expr = 'user ' . $_SERVER['PHP_AUTH_USER'] . ': ';
		}

		foreach ($messages as $msg) {
			// 基底クラスへ
			$ret = parent::log($level, $user_expr . $msg);
		}
		
		return $ret;
    }
    // }}}
}
// }}}
?>