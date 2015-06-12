<?php
/**
 *  Admin/Kpi/Gacha/Aggregate/Csv.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_gacha_aggregate_csv view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiGachaAggregateCsv extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$gacha_m =& $this->backend->getManager('AdminGacha');
		$item_m =& $this->backend->getManager('Item');
		$shop_m =& $this->backend->getManager('Shop');
		$logdata_viewg_m = $this->backend->getManager('LogdataViewGacha');
		$logdata_viewi_m = $this->backend->getManager('LogdataViewItem');
		$search_params = array(
			'date_from' => $this->af->get('search_date_from'),
			'date_to' => $this->af->get('search_date_to'),
		);

		$search_flg = $this->af->get('search_flg');
		if ($search_flg == '1'){
			$gacha_ids = array_reverse( $this->af->get('search_gacha_ids') );
		}
		//レアガチャのリスト
		$gacha_list = $gacha_m->getRareGachaAllListInfo();
		foreach($gacha_list as $key => $val) {
			$gacha_list[$key]['selected'] = 0;
			if ($search_flg == '1'){
				if (in_array($gacha_list[$key]['gacha_id'], $gacha_ids)) $gacha_list[$key]['selected'] = 1;
			}
		}

		$dataname = array();
		$datazero = array();
		$data = array();
		if ($search_flg == '1'){
			$idx = 0;
			$dataname[$idx] = $datazero[$idx] = mb_convert_encoding('ユーザID', "UTF-8","SJIS");//あとでSJISに変換するため
			$idx++;
			foreach ($gacha_ids as $gkey => $gval) {
				$gacha_master = $shop_m->getGachaListId($gval);
				//error_log("gacha_id=$gval ".$gacha_master['comment']);
				$dataname[$idx] = mb_convert_encoding("ガチャID".$gval ."\n", "UTF-8","SJIS"). $gacha_master['comment'].mb_convert_encoding("\n有料", "UTF-8","SJIS");//あとでSJISに変換するため
				$datazero[$idx] = 0;
				$idx++;
				$dataname[$idx] = mb_convert_encoding("ガチャID".$gval ."\n", "UTF-8","SJIS"). $gacha_master['comment'].mb_convert_encoding("\n無料", "UTF-8","SJIS");//あとでSJISに変換するため
				$datazero[$idx] = 0;
				$idx++;
			}
			$idx = 1;
			foreach ($gacha_ids as $gkey => $gval) {
				$search_params = array(
					'date_from' => $this->af->get('search_date_from'),
					'date_to' => $this->af->get('search_date_to'),
					'gacha_id' => $gval,
				);
				$gacha_log_data = $logdata_viewg_m->getGachaLogData($search_params);
				// api_transaction_idでひも付く詳細ログ情報を取得する
				foreach ($gacha_log_data['data'] as $k => $v) {
					$user_id = $v['user_id'];
					$datazero[0] = $user_id;
					if (array_key_exists($user_id, $data) == false) $data[$user_id] = $datazero;
					$item_log_data = $logdata_viewi_m->getItemDataByApiTransactionId($v['api_transaction_id']);
					foreach($item_log_data['data'] as $kk => $vv) {
						//error_log("  item_log_data ($kk) : vv=".print_r($vv,true));
						if ($vv['count'] < 0) {
							$data[$user_id][$idx+($vv['service_flg'])]++;
						}
					}
				}
				$idx+=2;
			}
		}
		
		//出力データ生成
		$output_data = "ユーザー毎 ガチャ回数\n";
		$output_data .= "検索日：".$this->af->get('search_date_from')." ～ ".$this->af->get('search_date_to')."\n";
		$ff = true;
		foreach($dataname as $val) {
			if (!$ff) $output_data .= ',';
			$output_data .= '"' . mb_convert_encoding($val, "SJIS") . '"';
			$ff = false;
		}
		$output_data .= "\n";
		foreach($data as $dkey => $dval) {
			$ff = true;
			foreach($dval as $ddkey => $ddval) {
				if (!$ff) $output_data .= ',';
				$output_data .= '"' . $ddval . '"';
				$ff = false;
			}
			$output_data .= "\n";
		}
		//出力
		$file_name = 'gacha_aggregate_'.(implode("_", $gacha_ids)).'_'.substr($this->af->get('search_date_from'), 0, 10);
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=" . $file_name . ".csv");
		header("Content-Description: File Transfer");
		header("Content-Length: " . strlen($output_data) );
		echo $output_data;
		flush();
	}
}
