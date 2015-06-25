<?php
/**
 *  Admin/Api/Rsync.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_api_rsync Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminApiRsync extends Pp_AdminActionForm
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
 *  admin_api_rsync action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminApiRsync extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_api_rsync Action.
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
     *  admin_api_rsync action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$admin_m =& $this->backend->getManager('Admin');

		$target = $this->af->get('target');

		$appver_env = Util::getAppverEnv();

		$config_rsync_dest = $this->config->get('rsync_dest');
		if (!is_array($config_rsync_dest) ||
			!isset($config_rsync_dest['home']) || (strlen($config_rsync_dest['home']) == 0) ||
			!isset($config_rsync_dest['user']) || (strlen($config_rsync_dest['user']) == 0) ||
			!isset($config_rsync_dest['host']) || (strlen($config_rsync_dest['host']) == 0) ||
			!isset($config_rsync_dest['rsh']) || (strlen($config_rsync_dest['rsh']) == 0)
		) {
			$this->logger->log(LOG_WARNING, 'Invalid config rsync_dest.');
			return 'admin_error_500';
		}

		$ruser = $config_rsync_dest['user'];
		$rhost = $config_rsync_dest['host'];
		$rsh = $config_rsync_dest['rsh'];
		$lpath_base = BASE;
		$rpath_base = $config_rsync_dest['home'] . '/' . $appver_env;
		$subdirs = $admin_m->DIRECTORIES[$target];

//		$option = '--dry-run -rlv --cvs-exclude --checksum';
		$option = '--verbose -rlv --cvs-exclude --checksum';
		if ($target == 'program') {
			$option .= ' --delete';
		}

		$result_assoc = array();
		foreach ($subdirs as $subdir) {
			$lpath = $lpath_base . '/' . $subdir . '/';
			$rpath = $rpath_base . '/' . $subdir;

			$command = "rsync $option --rsh=\"$rsh\" $lpath $ruser@$rhost:$rpath";
			$command .= " 2>&1";

			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->logger->log(LOG_WARNING, 'command:' . $command);
			$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);

			$result_assoc[$subdir] = $return_var;
		}

		// 同期結果を判定
		$is_error = false;
		foreach ($result_assoc as $subdir => $result) {
			if ($result == 0) { // 0: Success
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
		$admin_m->addAdminOperationLog('/api/rsync/' . $target, ($is_error ? 'error' : 'success'), $columns);

		$this->af->setApp('result_assoc', $result_assoc);
		$this->af->setApp('is_error',     $is_error);
		$this->af->setApp('user',         $columns['user']);
		$this->af->setApp('time',         $columns['time']);

		return 'admin_api_rsync';
    }
}

?>
