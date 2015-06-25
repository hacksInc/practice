<?php
/**
 *  Admin/Program/Deploy/Diff/Dest.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_program_deploy_diff_dest Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminProgramDeployDiffDest extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'dest_directories' => array(
			// Form definition
			'type'        => array(VAR_TYPE_STRING),       // Input type
			'form_type'   => FORM_TYPE_CHECKBOX, // Form type
			'name'        => 'ディレクトリ',        // Display name

			//  Validator (executes Validator by written order.)
			'required'    => true,           // Required Option(true/false)
			'custom'      => 'checkFormDefinitionOptionExists',
			
			// option には、input タグの value 値をキーにして、表示するラベルを値にした 配列を指定します。
			// http://www.ethna.jp/ethna-document-dev_guide-view-form_helper.html#p19c658d
			'option'      => array(
				'app'       => 'app',
				'bin'       => 'bin',
//				'etc'       => 'etc',
				'lib'       => 'lib',
				'schema'    => 'schema',
				'template'  => 'template',
				'www'       => 'www',
			),
		),
    );
}

/**
 *  admin_program_deploy_diff_dest action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminProgramDeployDiffDest extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_program_deploy_diff_dest Action.
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
     *  admin_program_deploy_diff_dest action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$admin_m =& $this->backend->getManager('Admin');
		
		// 引数取得
		$subdirs = $this->af->get('dest_directories');
        
        // 設定取得
        $config_rsync_dest = $this->config->get('rsync_dest');
		if (!is_array($config_rsync_dest) ||
			!isset($config_rsync_dest['home']) || (strlen($config_rsync_dest['home']) == 0) ||
			!isset($config_rsync_dest['user']) || (strlen($config_rsync_dest['user']) == 0) ||
			!isset($config_rsync_dest['host']) || (strlen($config_rsync_dest['host']) == 0)
		) {
			$this->logger->log(LOG_WARNING, 'Invalid config rsync_dest.');
			return 'admin_error_500';
		}
        
        $scp_options = $this->config->get('rsync_dest_scp_options');
        if (!$scp_options) {
			$this->logger->log(LOG_WARNING, 'Invalid config rsync_dest_scp_options.');
			return 'admin_error_500';
        }
        
        // 各種パス名を準備
        $appver_env = Util::getAppverEnv();
        if (!$appver_env) {
            $appver_env = 'main';
        }
        
		$uniqid = uniqid();
		$wcroot = $developer_m->getTmpSvnWcroot($uniqid, session_id());
        
		$ruser = $config_rsync_dest['user'];
		$rhost = $config_rsync_dest['host'];
		$rpath_base = $config_rsync_dest['home'] . '/' . $appver_env;
        
		$base = $appver_env;
        
		// コマンド組み立て(scp)
		$commands = array();
		
		$commands[] = "mkdir $wcroot";
		$commands[] = "cd $wcroot";
        $commands[] = "mkdir $base";
		$commands[] = "cd $base";
        
        $scp_command = "scp {$scp_options} -r";
        foreach ($subdirs as $subdir) {
            $scp_command .= " $ruser@$rhost:{$rpath_base}/$subdir";
        }
        $scp_command .= " .";
        $commands[] = $scp_command;
		
		$command = $developer_m->getCommandViaSshLocalhost(implode(' && ', $commands));
		if (!$command) {
			return 'admin_error_500';
		}
		
		// コマンド実行(scp)
		$output = null;
		$return_var = null;
		exec($command, $output, $return_var);
		$this->logger->log(LOG_WARNING, 'command:' . $command);
		$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
		$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
		
		if ($return_var != 0) {
			return 'admin_error_500';
		}

        // コマンド実行(diff) 
        $wcside = 'right';
        $lists = $admin_m->execProgramDiff($wcroot, $base, $subdirs, $wcside);        
		
		$this->af->setApp('lists', $lists);
		$this->af->setApp('checkout_uniq', $uniqid);
		$this->af->setApp('wcside', $wcside);
        
        return 'admin_program_deploy_diff_list';
    }
}

?>