<?php
/**
 *  Admin/Program/Deploy/Diff/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_program_deploy_diff_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminProgramDeployDiffIndex extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_program_deploy_diff_svn' => null,
		'admin_program_deploy_diff_makuo' => null,
		'admin_program_deploy_diff_dest' => null,
	);
	
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$svn = $this->config->get('svn');
		if (empty($svn)) {
			$this->af->setApp('svn_disabled', true);
		}
        
		$rsync_dest_scp_options = $this->config->get('rsync_dest_scp_options');
		if (empty($rsync_dest_scp_options)) {
			$this->af->setApp('dest_disabled', true);
		}
     }
}

?>