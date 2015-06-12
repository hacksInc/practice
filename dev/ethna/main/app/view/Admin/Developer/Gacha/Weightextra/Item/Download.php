<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Item/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_weightextra_item_download view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaWeightextraItemDownload extends Pp_AdminViewClass
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
//		$shop_m =& $this->backend->getManager('AdminShop');

		$gacha_id = $this->af->get('gacha_id');
		
		$table = 'm_gacha_extra_itemlist';
		$where = 'gacha_id = ' . $gacha_id;
		$grid = $developer_m->getMasterCsvGrid($table, $where);
		
		$filename = "jm_" . $table . "_"  . $gacha_id . "_" . date( "Ymd" ) . ".csv";
		$this->outputCsv($grid, $filename);
	}
}

?>