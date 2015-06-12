<?php
/**
 *  Admin/Developer/Master/Sync/Multi/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_sync_multi_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterSyncMultiConfirm extends Pp_AdminViewClass
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
		$tables = $this->af->get('tables');
		
		$list = array();
		if (is_array($tables)) {
			foreach ($tables as $table) {
				$list[$table] = $developer_m->getMetadataWithStatus($table);
			}
		}
		
		$this->af->setApp('list', $list);
		$this->af->setApp('sync_label', $developer_m->MASTER_SYNC_MULTI_LABEL);
		
		$this->af->setApp('form_template', $this->af->form_template);
    }
}

?>