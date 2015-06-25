<?php
/**
 *  Admin/Program/Deploy/Diff/List.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_program_deploy_diff_list view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminProgramDeployDiffList extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$action = $this->backend->ctl->getCurrentActionName();
        switch ($action) {
            case 'admin_program_deploy_diff_svn':
                $title = 'SVN比較';
                break;
            
            case 'admin_program_deploy_diff_dest':
                $title = '商用比較';
                break;
        }

        $this->af->setApp('title', $title);
    }
}

?>