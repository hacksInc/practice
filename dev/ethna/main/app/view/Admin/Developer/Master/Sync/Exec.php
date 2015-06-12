<?php
/**
 *  Admin/Developer/Master/Sync/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_sync_exec view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterSyncExec extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$table = $this->af->get('table');
		$developer_m =& $this->backend->getManager('Developer');

		$table_label = $developer_m->getMasterTableLabel($table);

		$this->af->setApp('table_label', $table_label);
    }
}

?>
