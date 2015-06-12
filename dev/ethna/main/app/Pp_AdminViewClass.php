<?php
// vim: foldmethod=marker
/**
 *  Pp_AdminViewClass.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'classes/Util.php';

// {{{ Pp_AdminViewClass
/**
 *  管理画面ビュークラス
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_AdminViewClass extends Pp_ViewClass
{
	/**
	 *  set common default value.
	 *
	 *  @access protected
	 *  @param  object  Pp_Renderer  Renderer object.
	 */
	function _setDefault(&$renderer)
	{
	}
	
    /**
     *  遷移名に対応する画面を出力する
     *
     *  アクションフォーム変数の'format'に'csv'がセットされている場合はCSV出力する。
     *  CSV情報はアクションフォーム変数の'table', 'filename'から取得する。
     */
	function forward()
	{
		$format = $this->af->get('format');
		if (!$format) {
			$format = $this->af->getApp('format');
		}

		if ($format == 'csv') {
			$this->outputCsv($this->af->getApp('table'), $this->af->getApp('filename'));
		} else {
			// CSV以外の場合は基底クラスで処理
			parent::forward();
		}
	}
	
	/**
	 * CSV出力する
	 * 
	 * @param array $table    CSVデータ（array(array(セル内容, セル内容, ...), array(セル内容, セル内容, ...), ...)）
	 * @param array $filename 出力時のContent-Dispositionファイル名
	 */
	protected function outputCsv($table, $filename)
	{
//		// CSV組み立て
//		ob_start();
//		$fp = fopen('php://output', 'w');
//		foreach ($table as $fields) {
//			fputcsv($fp, $fields);
//		}
//		fclose($fp);
//		$csv = ob_get_contents();
//		ob_end_clean();
//			
//		// クライアントPC用に改行コードと文字コードを変換
//		$csv = str_replace(PHP_EOL, "\r\n", $csv);
//		$csv = mb_convert_encoding($csv, 'SJIS');
$csv = Util::assembleCsv($table);
			
		// CSV出力
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Length: " . strlen($csv));
		header("Content-Type: application/octet-stream");
		echo $csv;
	}
	
	/** 昨日のY-m-d表記を取得する */
	protected function getYesterdayYmd()
	{
		// 昨日の日付を求める。日付の切替えは午前9時とする。
		return date('Y-m-d', $_SERVER['REQUEST_TIME'] - (3600 * (24 + 9)));
	}
	
	/** 先月のY-m表記を取得する */
	protected function getLastMonthYm()
	{
		// 先月を求める。月の切替えは1日午前9時とする。
		return date('Y-m',
			strtotime(date('Y-m', $_SERVER['REQUEST_TIME'] - (3600 * 9)) . '-01 00:00:00') - 1
		);
	}
}
// }}}

?>