<?php
/**
 *  Program/Entry/Ini/Update/Exec.php
 *
 *  このCLIアクションは管理画面のadmin_program_entry_ini_update_execからローカルホストへのSSH経由で呼ばれる
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  program_entry_ini_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_ProgramEntryIniUpdateExec extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  program_entry_ini_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_ProgramEntryIniUpdateExec extends Pp_CliActionClass
{
    /**
     *  preprocess of program_entry_ini_update_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  program_entry_ini_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// 引数取得
		if ( $GLOBALS['argc'] < 4 ) {
			// パラメータ不足
			Ethna::raiseError( 'Too few parameter.', E_GENERAL );
			error_log('Too few parameter.');
			exit(1);
		} else {
			// 第2引数以降を格納する
			$document_root = $GLOBALS['argv'][2];
			
			$current_ver = $GLOBALS['argv'][3];
			
			if (isset($GLOBALS['argv'][4])) {
				$review_ver = $GLOBALS['argv'][4];
			} else {
				$review_ver = null;
			}
		}

		if (!preg_match("/^[0-9]{1,10}$/", $current_ver)) { // 整数か
			$this->backend->logger->log(LOG_ERR, 'Invalid current_ver.');
			exit(1);
		}
		
		if (($review_ver !== null) && !preg_match("/^[0-9]{1,10}$/", $review_ver)) {
			$this->backend->logger->log(LOG_ERR, 'Invalid review_ver.');
			exit(1);
		}

		// 各種リソース取得
		$admin_m = $this->backend->getManager('Admin');
		
		// main
		$admin_m->setAdminDocumentRoot($document_root);
		
		if (!$admin_m->backupEntryIni()) {
			$this->backend->logger->log(LOG_ERR, 'backupEntryIni failed.');
			exit(1);
		}
		
		if (!$admin_m->writeEntryIni($current_ver, $review_ver)) {
			$this->backend->logger->log(LOG_ERR, 'writeEntryIni failed.');
			exit(1);
		}
		
		if (!$admin_m->deployEntryIni()) {
			$this->backend->logger->log(LOG_ERR, 'deployEntryIni failed.');
			exit(1);
		}
		
        exit(0);
    }
}

?>