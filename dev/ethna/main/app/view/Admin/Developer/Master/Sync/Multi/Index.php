<?php
/**
 *  Admin/Developer/Master/Sync/Multi/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_sync_multi_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterSyncMultiIndex extends Pp_AdminViewClass
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

//		$labels = $developer_m->getMasterTableLabelAssoc();
		$tables = $developer_m->MASTER_SYNC_MULTI_TABLES;
		$list = array();
//		foreach ($labels as $table => $label) {
		foreach ($tables as $table) {
			$list[$table] = $developer_m->getMetadataWithStatus($table);
		}

		$this->af->setApp('list', $list);
		$this->af->setApp('sync_label', $developer_m->MASTER_SYNC_MULTI_LABEL);

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
