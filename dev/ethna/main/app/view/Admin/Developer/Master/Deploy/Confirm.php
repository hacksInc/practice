<?php
/**
 *  Admin/Developer/Master/Deploy/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterDeployConfirm extends Pp_AdminViewClass
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
			$form_table = $this->af->get($table);
			if (!empty($form_table)) {
				$list[$table] = $developer_m->getMetadataWithStatus($table);
				$sync_last[$table] = $developer_m->getLastLogMasterSync($table);
			}
		}

		$this->af->setApp('list', $list);
		$this->af->setApp('sync_last', $sync_last);
	}
}
