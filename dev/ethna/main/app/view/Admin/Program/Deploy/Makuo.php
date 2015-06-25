<?php
/**
 *  Admin/Program/Deploy/Makuo.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_program_deploy_makuo view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminProgramDeployMakuo extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_program_deploy_diff_makuo' => null,
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
		foreach (array('makuo') as $cmd) {
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
		
		$this->af->setApp('directories',  $admin_m->DIRECTORIES[$target]);
		$this->af->setApp('log_writable', $log_writable);
		$this->af->setApp('target',       $target);

        // diff対象ディレクトリ（makuo関連）を取得
        $helper = $this->_getHelperActionForm('admin_program_deploy_diff_makuo');
        $def = $helper->getDef('makuo_directories');
        $this->af->setApp('makuo_directories', array_keys($def['option']));
    }
}

?>