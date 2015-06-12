<?php
/**
 *  Admin/Program/Deploy/Backup.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_program_deploy_backup view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminProgramDeployBackup extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
        
		$target = 'program';
        
		$this->af->setApp('directories', $admin_m->DIRECTORIES[$target]);
		$this->af->setApp('target',      $target);
    }
}

?>