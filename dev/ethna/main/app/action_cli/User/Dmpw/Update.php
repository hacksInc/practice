<?php
/**
 *  User/Dmpw/Update.php
 *
 *  データ移行パスワードを更新する。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  user_dmpw_update Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_UserDmpwUpdate extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  user_dmpw_update action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_UserDmpwUpdate extends Pp_CliActionClass
{
    /**
     *  preprocess of user_dmpw_update Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
		// 引数取得
		if ($GLOBALS['argc'] < 3) {
			// パラメータ不足
			error_log('Too few parameter.');
			exit(1);
		} else {
			// 第2引数以降を格納する
			$user_id = $GLOBALS['argv'][2];
		}
        
        $this->af->setApp('user_id', $user_id);
        
        return null;
    }

    /**
     *  user_dmpw_update action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $user_id = $this->af->getApp('user_id');
        $unit = $this->config->get('unit_id');
        
		$user_m = $this->backend->getManager('AdminUser');
		$admin_m = $this->backend->getManager('Admin');

        // ユーザー情報を取得する
        $user_base = $user_m->getUserBaseDirect($user_id);
        if (!is_array($user_base) || empty($user_base)) {
            error_log('ERROR:ユーザーが存在しません。');
            exit(1);
        }
        
        // 新しいデータ移行パスワードを生成する
		$dmpw = $user_m->getRandomDmpw();
		$dmpw_hash = $user_m->hashDmpw($user_id, $dmpw);
        
        // 確認プロンプト
		echo <<<EOD
------------------------------------------------------------------------------
unit    : $unit
user_id : $user_id
account : {$user_base['account']}
name    : {$user_base['name']}

以下の変更を行います。
現在の dmpw_hash : {$user_base['dmpw_hash']}
新しい dmpw      : $dmpw
新しい dmpw_hash : $dmpw_hash
------------------------------------------------------------------------------
よろしいですか？ [y/N] 
EOD;
        
        $line = trim(fgets(STDIN));
        if (strcasecmp($line, 'y') !== 0) {
            echo "中止しました。\n";
            exit(0);
        }
        
        // 変更前のt_user_baseテーブルをバックアップする
        $ret = $this->mysqldumpTUserBase($unit);
        if ($ret !== true) {
            error_log('ERROR:mysqldumpに失敗しました。');
            exit(1);
        }
        
        // DBを更新する
        $ret = $user_m->setUserBaseDirect($user_id, array('dmpw_hash' => $dmpw_hash));
        if ($ret !== true) {
            error_log('ERROR:' . __FILE__ . ':' . __LINE__);
            $exit_code = 1;
        } else {
            $exit_code = 0;
        }
        
        // ログ
        $log_columns = array(
            'user'          => `whoami`,
			'action'        => $this->backend->ctl->getCurrentActionName(),
            'unit'          => $unit,
            'user_id'       => $user_id,
            'account'       => $user_base['account'],
            'old_dmpw_hash' => $user_base['dmpw_hash'],
            'new_dmpw_hash' => $dmpw_hash,
            'exit_code'     => $exit_code,
        );
        
        $admin_m->addAdminOperationLog('/batch', 'user_dmpw_update', $log_columns);
        
        if ($exit_code) {
            echo "失敗しました。\n";
        } else {
            echo <<<EOD
\n成功しました。
            
以下はRedmineでの社内連絡用の文面です。
------------------------------------------------------------------------------
引き継ぎパスワード再発行しました。

新しい引き継ぎパスワード：$dmpw

引き継ぎIDはDBを参照下さい。
https://main.mgr.jmja.jugmon.net:10443/admin/developer/user/view/index

お手数ですがよろしくお願いいたします。
------------------------------------------------------------------------------\n
EOD;
        }
        
        exit($exit_code);
    }
    
    /**
     * t_user_baseテーブルをmysqldumpする
     * 
     * @param int $unit ユニットID
     * @return boolean 成否
     */
    protected function mysqldumpTUserBase($unit)
    {
		$db =& $this->backend->getDB();		
        
        // DB接続情報を取得
        $unit_all = $this->config->get('unit_all');
        if (!isset($unit_all[$unit]) || 
            !isset($unit_all[$unit]['dsn_r'])
        ) {
            error_log('Invalid config.');
            return false;
        }
        
        $dsn = $unit_all[$unit]['dsn_r'];
		$parsed = $db->parseDSN($dsn);
        if (!is_array($parsed) || 
            !isset($parsed['hostspec']) || !($parsed['hostspec']) ||
            !isset($parsed['database']) || !($parsed['database'])
        ) {
            error_log('Invalid config dsn.');
            return false;
        }

        // 出力先パスを決定
        $suffix = date('YmdHis', $_SERVER['REQUEST_TIME']) . uniqid();
        $dirname = BASE . "/backup";
        $filename = "{$parsed['hostspec']}_{$parsed['database']}_t_user_base_{$suffix}.sql.bz2";
        
        // コマンド組み立て
		$command = "mysqldump" 
                 . " --lock-tables=0 --add-locks=0 --add-drop-table=0" 
                 . " --extended-insert=0 --order-by-primary"
		         . " -u {$parsed['username']}" 
                 . " --password={$parsed['password']}"
		         . " -h {$parsed['hostspec']}"
                 . " {$parsed['database']}" 
                 . " t_user_base"
                 . " | bzip2 -c > $dirname/$filename";
        
        // 実行
        $output = null;
        $return_var = null;
		exec($command, $output, $return_var);
		if ($return_var) {
            error_log('command:' . $command);
            error_log('output:' . var_export($output, true));
            error_log('return_var:' . $return_var);
			return false;
		}
                 
        return true;
    }
}

?>