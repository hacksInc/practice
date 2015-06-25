<?php
/**
 *  Admin/Developer/Master/Deploy/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterDeployIndex extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$developer_m =& $this->backend->getManager('Developer');

		//		$labels = $developer_m->getMasterTableLabelAssoc();
		$tables = $developer_m->MASTER_INDEX_TABLES;
		$list = array();
		$sync_last = array();
		//		foreach ($labels as $table => $label) {
		foreach ($tables as $table) {
			$list[$table] = $developer_m->getMetadataWithStatus($table);
			$sync_last[$table] = $developer_m->getLastLogMasterSync($table);
		}

		$this->af->setApp('list', $list);
		$this->af->setApp('sync_last', $sync_last);
	}
}
