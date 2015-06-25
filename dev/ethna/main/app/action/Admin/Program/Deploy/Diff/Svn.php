<?php
/**
 *  Admin/Program/Deploy/Diff/Svn.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_program_deploy_diff_svn Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminProgramDeployDiffSvn extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'svn_directories' => array(
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
				'etc'       => 'etc',
//				'htdocs'    => 'htdocs',
//				'htdocs_dl' => 'htdocs_dl',
				'lib'       => 'lib',
				'schema'    => 'schema',
				'template'  => 'template',
				'www'       => 'www',
			),
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
 *  admin_program_deploy_diff_svn action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminProgramDeployDiffSvn extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_program_deploy_diff_svn Action.
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
     *  admin_program_deploy_diff_svn action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$admin_m =& $this->backend->getManager('Admin');
		
		// 引数取得
		$path     = $this->af->get('path');
		$revision = $this->af->get('revision');
		$subdirs  = $this->af->get('svn_directories');
		
		// コマンド組み立て(svn)
		$base = basename($path);
		
		$uniqid = uniqid();
		$wcroot = $developer_m->getTmpSvnWcroot($uniqid, session_id());
		
		$svn_command = $developer_m->getSvnCheckoutCommand($path, $revision, true);
		if (!$svn_command) {
			return 'admin_error_500';
		}
		
		$commands = array();
		$commands[] = "mkdir $wcroot";
		$commands[] = "cd $wcroot";
		$commands[] = $svn_command;

		$command = $developer_m->getCommandViaSshLocalhost(implode(' && ', $commands));
		if (!$command) {
			return 'admin_error_500';
		}
		
		// コマンド実行(svn)
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
        $wcside = 'left';
        $lists = $admin_m->execProgramDiff($wcroot, $base, $subdirs, $wcside);        
		
		$this->af->setApp('lists', $lists);
		$this->af->setApp('checkout_uniq', $uniqid);
		$this->af->setApp('wcside', $wcside);
		
        return 'admin_program_deploy_diff_list';
    }
}

?>