<?php
/**
 *  Admin/Program/Deploy/Svn.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_program_deploy_svn view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminProgramDeploySvn extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_program_deploy_diff_svn' => null,
	);
	
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$target = 'program';
		
		// ログに書き込み可能か確認＆最新の1件を取得
		$log_writable = true;
		foreach (array('svn') as $cmd) {
			$dirname = "/api/$cmd/$target";
			$varname = 'last_' . $cmd;

			if ($log_writable) {
				$log_writable = $admin_m->isAdminOperationLogWritable($dirname, 'success');
			}
			
			$list = $admin_m->getAdminOperationLogReverse($dirname, 'success', 1);
			if (is_array($list)) {
				$this->af->setApp($varname, $list[0]);
			}
		}
		
		// 環境依存の項目について判別
		$svn = $this->config->get('svn');
		if (empty($svn)) {
			$this->af->setApp('svn_disabled', true);
		}
		
		$this->af->setApp('directories', $admin_m->DIRECTORIES[$target]);
		$this->af->setApp('log_writable', $log_writable);
		$this->af->setApp('target',       $target);

        // diff対象ディレクトリ（svn関連）を取得
        $helper = $this->_getHelperActionForm('admin_program_deploy_diff_svn');
        $def = $helper->getDef('svn_directories');
        $this->af->setApp('svn_directories', array_keys($def['option']));
    }
}

?>