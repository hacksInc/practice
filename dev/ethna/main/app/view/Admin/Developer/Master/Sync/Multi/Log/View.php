<?php
/**
 *  Admin/Developer/Master/Sync/Multi/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_sync_multi_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterSyncMultiLogView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$mode = $this->af->get('mode');
		$developer_m =& $this->backend->getManager('Developer');
		
		$limit = 200;
		$list = $developer_m->getMasterSyncLogList($mode, 0, $limit);
		if (is_array($list)) foreach ($list as $i => $row) {
			$metadata = $developer_m->getMetadata($row['table_name'], false);
			if (is_array($metadata) && isset($metadata['table_label'])) {
				$list[$i]['table_label'] = $metadata['table_label'];
			}
		}
		
		$this->af->setApp('list', $list);
		$this->af->setApp('limit', $limit);
		$this->af->setApp('sync_label', $developer_m->MASTER_SYNC_MULTI_LABEL);
    }
}

?>