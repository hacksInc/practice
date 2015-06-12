<?php
/**
 *  Admin/Program/Deploy/Diff/Makuo.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_program_deploy_diff_makuo Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminProgramDeployDiffMakuo extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'makuo_directories' => array(
			// Form definition
			'type'        => array(VAR_TYPE_STRING),       // Input type
			'form_type'   => FORM_TYPE_CHECKBOX, // Form type
			'name'        => 'ディレクトリ',        // Display name

			//  Validator (executes Validator by written order.)
			'required'    => true,           // Required Option(true/false)
			'custom'      => 'checkFormDefinitionOptionExists',
			
			'option'      => array(
				'app'       => 'app',
				'etc'       => 'etc',
//				'htdocs'    => 'htdocs',
//				'htdocs_dl' => 'htdocs_dl',
				'lib'       => 'lib',
				'template'  => 'template',
				'www'       => 'www'
			),
		),
    );
}

/**
 *  admin_program_deploy_diff_makuo action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminProgramDeployDiffMakuo extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_program_deploy_diff_makuo Action.
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
     *  admin_program_deploy_diff_makuo action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// 各種リソース取得
		$developer_m =& $this->backend->getManager('Developer');
		
		$config_makuo = $this->config->get('makuo');
		if (!is_array($config_makuo) ||
			!isset($config_makuo['command_name'])  || (strlen($config_makuo['command_name']) == 0) ||
			!isset($config_makuo['ssh_localhost']) || (strlen($config_makuo['ssh_localhost']) == 0)
		) {
			$this->logger->log(LOG_WARNING, 'Invalid config_makuo.');
			return 'admin_error_500';
		}

		$makuo = $config_makuo['command_name'];
		$base = basename(BASE);
		
		// 引数取得
		$subdirs = $this->af->get('makuo_directories');

		// コマンド実行
		$lists = array(); // makuo結果リスト（キーがサブディレクトリ名、値の1行目がmakuoコマンド、値の2行目以降がmakuoコマンドの出力）
		foreach ($subdirs as $subdir) {
			$path = $base . '/' . $subdir;

			$command = "$makuo -n -d $path";
			$list = array($command);
			
			$command = $developer_m->getCommandViaSshLocalhost($command);
			
			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->logger->log(LOG_WARNING, 'command:' . $command);
			$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);

			if (!empty($output)) {
				$list = array_merge($list, $output);
			}
			
			$lists[$subdir] = $list;
		}
		
		$this->af->setApp('lists', $lists);
		
        return 'admin_program_deploy_diff_makuo';
    }
}

?>