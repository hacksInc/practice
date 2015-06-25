<?php
/**
 *  Admin/Announce/Deploy/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_deploy_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceDeployIndex extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$target = 'announce';
		
		// ログに書き込み可能か確認＆最新の1件を取得
		$log_writable = true;
		foreach (array('rsync', 'makuo') as $cmd) {
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
		$rsync_dest = $this->config->get('rsync_dest');
		if (empty($rsync_dest)) {
			$this->af->setApp('rsync_disabled', true);
		}
		
		$this->af->setApp('directories', $admin_m->DIRECTORIES[$target]);
		$this->af->setApp('log_writable', $log_writable);
		$this->af->setApp('target',       $target);
    }
}

?>
