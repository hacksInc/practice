<?php
/**
 *  Pp_CliBackend.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

// {{{ Pp_CliBackend
/**
 *  CLIバックエンド処理クラス
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_CliBackend extends Pp_Backend
{
    /**
     *  管理用権限のDBオブジェクトを返す
     *
	 *  DROP権限が必要な場合に使用すること
     *  @access public
     *  @param  string  $db_key DBキー
     *  @return mixed   Ethna_DB:DBオブジェクト null:DSN設定なし Ethna_Error:エラー
     */
    function &getAdminDB($db_key = "")
    {
		static $db_list = array();
		
        $null = null;
        $db_varname =& $this->_getDBVarname($db_key);

        if (Ethna::isError($db_varname)) {
            return $db_varname;
        }

		// 既に接続済みだったら使い回す
        if (isset($db_list[$db_varname]) && $db_list[$db_varname] != null) {
            return $db_list[$db_varname];
        }

        $dsn = $this->controller->getDSN($db_key);
		
        if ($dsn == "") {
            // DB接続不要
            return $null;
        }

		// 通常のdsnのユーザーID、パスワード部分が所定の書式になっていることを確認する
		$pattern = '/:\/\/[^@]+@/';
		if (!preg_match($pattern, $dsn)) {
            return $null;
		}

		// adminユーザーのDSNを求める
		$dbauth_key = 'admin';
		$dbauth = $this->config->get('dbauth');
		if (!is_array($dbauth) || !isset($dbauth[$dbauth_key])) {
            return $null;
		}
		
		$username = $dbauth[$dbauth_key]['username'];
		$password = $dbauth[$dbauth_key]['password'];
		
		$dsn_admin = preg_replace($pattern, "://$username:$password@", $dsn);

		// 接続する
        $dsn_persistent = false;

        $class_factory =& $this->controller->getClassFactory();
        $db_class_name = $class_factory->getObjectName('db');
        
        // BC: Ethna_DB -> Ethna_DB_PEAR
        if ($db_class_name == 'Ethna_DB') {
            $db_class_name = 'Ethna_DB_PEAR';
        }
        if (class_exists($db_class_name) === false) {
            $class_factory->_include($db_class_name);
        }

        $db_list[$db_varname] =& new $db_class_name($this->controller, $dsn_admin, $dsn_persistent);
        $r = $db_list[$db_varname]->connect();
        if (Ethna::isError($r)) {
            $db_list[$db_varname] = null;
            return $r;
        }

        return $db_list[$db_varname];
    }
}
// }}}
?>