<?php
/**
 *  Admin/Api/Makuo.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_api_makuo Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminApiMakuo extends Pp_AdminActionForm
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
            'regexp'      => '/^program|assetbundle|announce$/', // String by Regexp
        ),
    );
}

/**
 *  admin_api_makuo action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminApiMakuo extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_api_makuo Action.
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
     *  admin_api_makuo action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$admin_m =& $this->backend->getManager('Admin');

		$target = $this->af->get('target');

		$config_makuo = $this->config->get('makuo');
		if (!is_array($config_makuo) ||
			!isset($config_makuo['command_name'])  || (strlen($config_makuo['command_name']) == 0) ||
			!isset($config_makuo['ssh_localhost']) || (strlen($config_makuo['ssh_localhost']) == 0)
		) {
			$this->logger->log(LOG_WARNING, 'Invalid config_makuo.');
			return 'admin_error_500';
		}

		$makuo = $config_makuo['command_name'];
		$ssh_localhost = $config_makuo['ssh_localhost'];
		//$base = $admin_m->BASE_DIRECTORIES[$target];
		$base = basename(BASE);
		$subdirs = $admin_m->DIRECTORIES[$target];

//		$option = '-n';
		$option = '-g';
		if ($target == 'program' || $target == 'announce') {
			$option .= ' -d';
		}

		$result_assoc = array();
		foreach ($subdirs as $subdir) {
			//$path = $base . '/' . $subdir;
			$path = 'ethna/'.$base . '/' . $subdir;
			$command = $ssh_localhost . ' "' . $makuo . ' ' . $option . ' ' . $path . '"';
			$command .= " 2>&1";

			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->logger->log(LOG_INFO, 'command:' . $command);
			$this->logger->log(LOG_INFO, 'output:' . implode("\n", $output));
			$this->logger->log(LOG_INFO, 'return_var:' . $return_var);

			$result_assoc[$subdir] = $return_var;
		}

		// 同期結果を判定
		$is_error = false;
		foreach ($result_assoc as $subdir => $result) {
			if ($result == 0) {
				// OK
				continue;
			}

			$is_error = true;
			break;
		}

		// ログ
		$columns = array(
			'user'   => $this->session->get('lid'),
			'action' => $this->backend->ctl->getCurrentActionName(),
			'time'   => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
		);
		$admin_m->addAdminOperationLog('/api/makuo/' . $target, ($is_error ? 'error' : 'success'), $columns);

		$this->af->setApp('result_assoc', $result_assoc);
		$this->af->setApp('is_error',     $is_error);
		$this->af->setApp('user',         $columns['user']);
		$this->af->setApp('time',         $columns['time']);

        return 'admin_api_makuo';
    }
}

?>
