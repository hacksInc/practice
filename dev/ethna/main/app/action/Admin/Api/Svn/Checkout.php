<?php
/**
 *  Admin/Api/Svn/Checkout.php
 *
 *  所定ディレクトリをsvn checkoutしてBASE下に設置する
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_api_svn_checkout Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminApiSvnCheckout extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'target' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,                 // Required Option(true/false)
            'min'         => null,                 // Minimum value
            'max'         => 16,                   // Maximum value
            'regexp'      => '/^program$/', // String by Regexp
        ),
		
		// SVNパス（"/trunk/web" や "/tags/web_1_2_82_20140422" 等）
 		'path' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,                 // Required Option(true/false)
            'min'         => null,                 // Minimum value
            'max'         => 256,                  // Maximum value
            'regexp'      => '/^\/[\/a-zA-Z0-9_-]+$/', // String by Regexp
        ),
		
		'revision' => array(
			// Form definition
			'type'        => VAR_TYPE_INT,    // Input type

			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => null,            // Maximum value
		),
   );
}

/**
 *  admin_api_svn_checkout action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminApiSvnCheckout extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_api_svn_checkout Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
/*
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }
*/

    /**
     *  admin_api_svn_checkout action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// リソース初期化
		$admin_m =& $this->backend->getManager('Admin');
		$developer_m =& $this->backend->getManager('Developer');
		
		// 引数取得
		$target   = $this->af->get('target');
		$path     = $this->af->get('path');
		$revision = $this->af->get('revision');

		// コマンド組み立て
		$subdirs = $admin_m->DIRECTORIES[$target];
		
		$base = basename($path);
		
		$uniqid = uniqid();
//		$wcroot = BASE . '/tmp/svn' . $uniqid;
		$wcroot = $developer_m->getTmpSvnWcroot($uniqid);
		
		$svn_command = $developer_m->getSvnCheckoutCommand($path, $revision, true);
		if (!$svn_command) {
			return 'admin_error_500';
		}
		
		$commands = array();
		$commands[] = "mkdir $wcroot";
		$commands[] = "cd $wcroot";
		$commands[] = $svn_command;
		$commands[] = "cd $base";
		
		foreach ($subdirs as $subdir) {
			$dest = BASE . "/$subdir";
			$commands[] = "mv $dest $dest.$uniqid.bak";
			$commands[] = "mv $subdir $dest";
		}
		
		$command = $developer_m->getCommandViaSshLocalhost(implode(' && ', $commands));
		if (!$command) {
			return 'admin_error_500';
		}
		
		// 実行
		$output = null;
		$return_var = null;
		exec($command, $output, $return_var);
		$this->logger->log(LOG_WARNING, 'command:' . $command);
		$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
		$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
		
		// 結果を判定
		$is_error = ($return_var != 0);

		// ログ
		$columns = array(
			'user'     => $this->session->get('lid'),
			'action'   => $this->backend->ctl->getCurrentActionName(),
			'time'     => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
			'path'     => $path,
			'revision' => $revision,
		);
		$admin_m->addAdminOperationLog('/api/svn/' . $target, ($is_error ? 'error' : 'success'), $columns);
		
		// Smartyテンプレートキャッシュを削除
		// http://www.smarty.net/docsv2/ja/api.clear.compiled.tpl.tpl
        $renderer =& $this->backend->ctl->getRenderer();
        if (strtolower(get_class($renderer)) == "ethna_renderer_smarty") {
            $renderer->engine->clear_compiled_tpl(); // この関数は戻り値がvoidなので結果判定できない
		}

		$this->af->setApp('is_error', $is_error);
		$this->af->setApp('user',     $columns['user']);
		$this->af->setApp('time',     $columns['time']);
		
//        return 'admin_api_svn_checkout';
		$this->forward_exit();
    }
	
	/**
	 * ビューのforward相当の処理を実行してからexit
	 * 
	 * mvでapp配下を入れ替えた直後はビューが見つからなくなるので、
	 * (view class is not defined for [admin_api_svn_checkout] が発生する) 
	 * ビューへ遷移せずに、このアクション内で処理を終了させる。
	 */
	protected function forward_exit()
	{
		$is_error = $this->af->getApp('is_error');
		$content = array(
			'user' => $this->af->getApp('user'),
			'time' => $this->af->getApp('time'),
		);
		
		if ($is_error) {
			header('HTTP/1.0 500 Internal Server Error');
		} else {
			header('HTTP/1.0 200 OK');
		}
		
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($content);
		
		exit;
	}
}

?>