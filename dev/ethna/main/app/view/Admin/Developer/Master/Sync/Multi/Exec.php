<?php
/**
 *  Admin/Developer/Master/Sync/Multi/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_sync_multi_exec view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterSyncMultiExec extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');
		
		$mode = $this->af->get('mode');
		
		$this->af->setApp('sync_label', $developer_m->MASTER_SYNC_MULTI_LABEL);
    }
}

?>