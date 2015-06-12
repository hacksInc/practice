<?php
/**
 *  Admin/Developer/Master/Log/List.php
 *
 *  このビューは admin_developer_master_log_list アクション以外からも呼ばれる。
 *  （他のアクションから、Pp_AdminActionClass の performMasterLogList 経由で、このビューへ遷移する）
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_log_list view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterLogList extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$table = $this->af->get('table');

		$table_label = $developer_m->getMasterTableLabel($table);
		
		$list = $this->af->getApp('list');
		if ($list) {
			for ($i = 0; $i < count($list); $i++) {
				$date = $list[$i]['date'];
				$isodate = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2) . ' '
				         . substr($date, 8, 2) . ':' . substr($date, 10, 2) . ':' . substr($date, 12);

				$list[$i]['isodate'] = $isodate;
			}
		
			$this->af->setApp('list', $list);
		}
		
		$this->af->setApp('table_label', $table_label);
		$this->af->setApp('max', Pp_DeveloperManager::MAXTER_UPLOAD_LOG_MAX);

		parent::preforward();
	}
}

?>
