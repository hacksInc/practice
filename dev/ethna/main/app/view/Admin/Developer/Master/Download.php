<?php
/**
 *  Admin/Developer/Master/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_download view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterDownload extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
/*
	function preforward()
	{
	}
*/

	function forward ()
	{
		$developer_m =& $this->backend->getManager('Developer');

		$table = $this->af->get('table');

//		$list  = $developer_m->getMasterList($table);
//		$label = $developer_m->getMasterColumnsLabel($table);
//		$keys = array_keys($label);
//
//		$grid = array();
//		$grid[] = array_values($label);
//		foreach ($list as $row) {
//			$tmp = array();
//			foreach ($keys as $key) {
//				$tmp[] = $row[$key];
//			}
//
//			$grid[] = $tmp;
//		}
		$grid = $developer_m->getMasterCsvGrid($table);
		$filename = "pp_" . $table . date( "Ymd" ) . ".csv";
		$this->outputCsv($grid, $filename);
	}
}
