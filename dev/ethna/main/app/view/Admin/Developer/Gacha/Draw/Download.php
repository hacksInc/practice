<?php
/**
 *  Admin/Developer/Gacha/Draw/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_draw_download view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaDrawDownload extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
    }
	
	function forward()
	{
		$shop_m =& $this->backend->getManager('AdminShop');
		
		$gacha_id        = $this->af->get('gacha_id');
		$date_draw_start = $this->af->get('date_draw_start');
		$date_draw_end   = $this->af->get('date_draw_end');
		
		$table = 'log_gacha_draw_list';
		
		$filename = "jm_" . $table . date("YmdHis", $_SERVER['REQUEST_TIME']) . ".csv";
		
		// CSV用のHTTPヘッダを出力
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Type: application/octet-stream");
		
		// ヘッダ行などを出力
		$head = array(
			array('テーブル名', $table),
			array('CSV生成日時', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])),
			array($this->af->form_template['gacha_id']['name'], $gacha_id),
			array($this->af->form_template['date_draw_start']['name'], $date_draw_start, '～', $date_draw_end),
			array(),
			array_values($shop_m->getLogGachaDrawListLabels())
		);
		echo Util::assembleCsv($head);

		// データをDBから取得＆出力
		$shop_m->queryLogGachaDrawList($gacha_id, $date_draw_start, $date_draw_end);
		while ($row = $shop_m->fetchLogGachaDrawList()) {
			$arr = $shop_m->convertLogGachaDrawListRow($row);
			
			echo Util::assembleCsv(array($arr));
		}
	}
}