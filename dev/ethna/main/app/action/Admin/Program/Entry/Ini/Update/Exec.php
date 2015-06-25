<?php
/**
 *  Admin/Program/Entry/Ini/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../Pp_Form_AdminProgramEntry.php';
require_once dirname(__FILE__) . '/../../Pp_Action_AdminProgramEntry.php';

/**
 *  admin_program_entry_ini_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminProgramEntryIniUpdateExec extends Pp_Form_AdminProgramEntry
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'current_ver',
		'review_ver',
    );
}

/**
 *  admin_program_entry_ini_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminProgramEntryIniUpdateExec extends Pp_Action_AdminProgramEntry
{
    /**
     *  preprocess of admin_program_entry_ini_update_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
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

    /**
     *  admin_program_entry_ini_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$current_ver = $this->af->get('current_ver');
		$review_ver  = $this->af->get('review_ver');
		
		$developer_m = $this->backend->getManager('Developer');
		
		$command = BASE . '/bin/cli.sh program_entry_ini_update_exec '
		         . $_SERVER['DOCUMENT_ROOT'] . ' ' . $current_ver . ' ' . $review_ver;
		
		$command = $developer_m->getCommandViaSshLocalhost($command);
		
		$output = null;
		$return_var = null;
		exec($command, $output, $return_var);
		$this->logger->log(LOG_WARNING, 'command:' . $command);
		$this->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
		$this->logger->log(LOG_WARNING, 'return_var:' . $return_var);
		
		if ($return_var) {
			return 'admin_error_500';
		}
	
        return 'admin_program_entry_ini_update_exec';
    }
}

?>