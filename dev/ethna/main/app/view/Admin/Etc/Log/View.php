<?php
/**
 *  Admin/Etc/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_etc_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminEtcLogView extends Pp_AdminViewClass
{
	function forward()
	{
		$start   = $this->af->get('start');
		$end     = $this->af->get('end');
		$user_id = $this->af->get('user_id');
		
		$admin_m =& $this->backend->getManager('Admin');
		
		if (!$start) {
			$start = '2001-01-01';
		}

		if (!$end) {
			$end = '2030-01-01';
		}
		
		$start_date = $start . ' 00:00:00';
		$end_date   = $end . ' 23:59:59';
		
		$filename = 'tracking' . date('YmdHis') . '.csv';
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Type: application/octet-stream");
		
		$admin_m->exportTrackingLogList($start_date, $end_date, $user_id);
	}
}

?>