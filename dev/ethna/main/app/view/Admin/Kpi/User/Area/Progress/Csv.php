<?php
/**
 *  Admin/Kpi/User/Area/Progress/Csv.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_user_area_progress_csv view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiUserAreaProgressCsv extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$quest_m =& $this->backend->getManager('AdminQuest');
		$logdata_viewi_m = $this->backend->getManager('LogdataViewItem');
		$logdata_viewq_m = $this->backend->getManager('LogdataViewQuest');
		
		$admin_m =& $this->backend->getManager('Admin');
		$admin_m->setSessionSqlBigSelectsOn();
		
		$date_from = $this->af->get('search_date_from');
		$date_to = $this->af->get('search_date_to');
		$ua = $this->af->get('search_ua');
		$ua_list = array('0' => 'ALL', '1' => 'iOS', '2' => 'Android');
		$quest_id = $this->af->get('search_quest_id');
		
		$search_params = array(
			'date_from' => $date_from,
			'date_to' => $date_to,
			'ua' => $ua,
			'quest_id' => $quest_id,
		);
		//エリアのリスト
		//$quest_area = $quest_m->getMasterQuestAreaAll();
		$quest_area = $quest_m->getMasterQuestAreaQuestId($quest_id);
		//ログの件数
		$quest_log_count = $logdata_viewq_m->getQuestLogDataCount($search_params);
		$this->af->setApp('quest_log_count', $quest_log_count);
		//if ($quest_log_count == 0) {
		//	return 'admin_kpi_user_area_progress_csv';
		//}
		//ログのリスト
		//$quest_log_data = $logdata_viewq_m->getQuestLogData($search_params);
		$quest_log_data = $logdata_viewq_m->getQuestLogDataForKpiArea($search_params);
		//$quest_data = array_reverse($quest_log_data['data'], true);
		$quest_data = $quest_log_data['data'];
		$quest_log_data = null;
		//$this->af->setApp('quest_log_data', $quest_data);
		//エリアデータに集計初期値をセット
		$total_cp = false;
		foreach($quest_area as $key => $val) {
			$quest_area[$key]['start_uid_rank'] = array();//スタートUID&ランク
			$quest_area[$key]['start_uniq_user'] = 0;//スタートユニーク人数
			$quest_area[$key]['clear_uid_rank'] = array();//クリアUID&ランク
			$quest_area[$key]['clear_uniq_user'] = 0;//クリアユニーク人数
			$quest_area[$key]['clear_rank_avg'] = 0.0;//クリアユーザランク平均
			$quest_area[$key]['total_continue'] = 0;//総コンティニュー数
		//	$quest_area[$key]['continue_medal'] = 0;//コンティニュー総額
		//	$quest_area[$key]['continue_srv'] = 0;//コンティニュー額（サービスメダル）
		//	$quest_area[$key]['continue_pay'] = 0;//コンティニュー額（有償メダル）
		//	$quest_area[$key]['continue_avg'] = 0.000;//コンティニュー課金比率
			$quest_area[$key]['play_cnt'] = 0;//挑戦回数
			$quest_area[$key]['clear_cnt'] = 0;//クリア回数
			$quest_area[$key]['clear_nocont_cnt'] = 0;//ノーコンクリア回数
			$quest_area[$key]['clear_cont_cnt'] = 0;//コンティニュークリア回数
			$quest_area[$key]['clear_avg'] = 0.000;//クリア率
			$quest_area[$key]['retire_cnt'] = 0;//リタイア回数
			$quest_area[$key]['retire_avg'] = 0.000;//リタイア率
			if ($total_cp == false) {
				$quest_area['total'] = $quest_area[$key];
				$quest_area['total']['area_id'] = $quest_id;
				$quest_area['total']['aname'] = mb_convert_encoding('クエスト全体', "UTF-8","SJIS");//あとでSJISに変換するため
				$total_cp = true;
			}
		}
	//	//コンティニューの課金データのみ取得する
	//	$search_params = array(
	//		'date_from' => $this->af->get('search_date_from'),
	//		'date_to' => $this->af->get('search_date_to'),
	//		'processing_type' => 'D23',
	//	);
	//	$item_log_data = $logdata_viewi_m->getItemLogData($search_params);
	//	$item_data = $item_log_data['data'];
	//	$item_log_data = null;
		//ログから集計
		foreach($quest_data as $key => $val) {
			$area_id = $val['area_id'];
			$user_id = $val['user_id'];
		//	//コンティニュー
		//	if ($val['quest_st'] == 3) {
		//		$api_transaction_id = $val['api_transaction_id'];
		//		foreach($item_data as $ikey => $ival) {
		//			if ($ival['api_transaction_id'] == $api_transaction_id && $ival['item_id']==9000) {
		//				//有償
		//				if ($ival['service_flg'] == 0) {
		//					$quest_area[$area_id]['continue_pay'] -= $ival['count'];
		//					$quest_area['total']['continue_pay'] -= $ival['count'];
		//					$quest_area[$area_id]['continue_medal'] -= $ival['count'];
		//					$quest_area['total']['continue_medal'] -= $ival['count'];
		//				}
		//				//サービス
		//				if ($ival['service_flg'] == 1) {
		//					$quest_area[$area_id]['continue_srv'] -= $ival['count'];
		//					$quest_area['total']['continue_srv'] -= $ival['count'];
		//					$quest_area[$area_id]['continue_medal'] -= $ival['count'];
		//					$quest_area['total']['continue_medal'] -= $ival['count'];
		//				}
		//			}
		//		}
		//	}
			//クリアorゲームオーバー
			if ($val['quest_st'] == 1 || $val['quest_st'] == 2) {
				//挑戦回数
				$quest_area[$area_id]['play_cnt']++;
				$quest_area['total']['play_cnt']++;
				//総コンティニュー回数
				$quest_area[$area_id]['total_continue'] += $val['continue_cnt'];
				$quest_area['total']['total_continue'] += $val['continue_cnt'];
			}
			//クリア
			if ($val['quest_st'] == 1) {
				//クリア回数
				$quest_area[$area_id]['clear_cnt']++;
				$quest_area['total']['clear_cnt']++;
				//ノーコンクリアorコンティニュークリア
				if ($val['continue_cnt'] == 0) {
					$quest_area[$area_id]['clear_nocont_cnt']++;//ノーコンクリア回数
					$quest_area['total']['clear_nocont_cnt']++;//ノーコンクリア回数
				}
				else {
					$quest_area[$area_id]['clear_cont_cnt']++;//コンティニュークリア回数
					$quest_area['total']['clear_cont_cnt']++;//コンティニュークリア回数
				}
				//クリアランク
				$quest_area[$area_id]['clear_uid_rank'][$user_id] = $val['rank'];//ランク
				$quest_area['total']['clear_uid_rank'][$user_id] = $val['rank'];//ランク
			}
			//ゲームオーバー
			if ($val['quest_st'] == 2) {
				//リタイア回数
				$quest_area[$area_id]['retire_cnt']++;
				$quest_area['total']['retire_cnt']++;
			}
			//スタート
			if ($val['quest_st'] == 0) {
				//スタートランク
				$quest_area[$area_id]['start_uid_rank'][$user_id] = $val['rank'];//ランク
				$quest_area['total']['start_uid_rank'][$user_id] = $val['rank'];//ランク
			}
		}
		$item_data = null;
		//エリア毎に再集計
		foreach($quest_area as $key => $val) {
			//スタートユニーク人数集計
			$start_user_cnt = 0;
			if (!empty($val['start_uid_rank'])) {
				foreach($val['start_uid_rank'] as $sukey => $suval) {
					$start_user_cnt++;
				}
			}
			//スタートユニーク人数
			$quest_area[$key]['start_uniq_user'] = $start_user_cnt;
			//クリアユニーク人数集計
			$clear_user_cnt = 0;
			$clear_user_rank = 0;
			if (!empty($val['clear_uid_rank'])) {
				foreach($val['clear_uid_rank'] as $cukey => $cuval) {
					$clear_user_cnt++;
					$clear_user_rank += $cuval;
				}
			}
			//クリアユニーク人数
			$quest_area[$key]['clear_uniq_user'] = $clear_user_cnt;
			//クリアユーザランク平均
			if ($clear_user_cnt > 0) $quest_area[$key]['clear_rank_avg'] = $clear_user_rank / $clear_user_cnt;
			if ($quest_area[$key]['clear_rank_avg'] > 0) $quest_area[$key]['clear_rank_avg'] = number_format($quest_area[$key]['clear_rank_avg'], 1);
		//	//コンティニュー課金比率
		//	if ($quest_area[$key]['continue_medal'] > 0) $quest_area[$key]['continue_avg'] = number_format($quest_area[$key]['continue_pay'] / $quest_area[$key]['continue_medal'], 3);
		//	if ($quest_area[$key]['continue_avg'] > 0) $quest_area[$key]['continue_avg'] = number_format($quest_area[$key]['continue_avg'], 3);
			if ($quest_area[$key]['play_cnt'] > 0) {
				//クリア率
				$quest_area[$key]['clear_avg'] = $quest_area[$key]['clear_cnt'] / $quest_area[$key]['play_cnt'];
				if ($quest_area[$key]['clear_avg'] > 0) $quest_area[$key]['clear_avg'] = number_format($quest_area[$key]['clear_avg'], 3);
				//リタイア率
				$quest_area[$key]['retire_avg'] = $quest_area[$key]['retire_cnt'] / $quest_area[$key]['play_cnt'];
				if ($quest_area[$key]['retire_avg'] > 0) $quest_area[$key]['retire_avg'] = number_format($quest_area[$key]['retire_avg'], 3);
			}
		}
		//出力データ生成
		$output_data = "ユーザー動向 エリア進捗\n";
		$output_data .= "検索日：$date_from ～ $date_to\n";
		$output_data .= "集計項目：".$ua_list[$ua]."\n\n";
	//	$output_data .= '"マップID","クエストID","クエスト名","エリアID","エリア名","スタートユニーク人数","クリアユニーク人数","クリアユーザランク平均","総コンティニュー回数","コンティニュー総額","コンティニュー額(サービスメダル)","コンティニュー額(有償メダル)","コンティニュー課金比率","挑戦回数","クリア回数","コンティニュー無クリア回数","コンティニュー有クリア回数","クリア率","リタイア回数","リタイア率"'."\n";
		$output_data .= '"マップID","クエストID","クエスト名","エリアID","エリア名","スタートユニーク人数","クリアユニーク人数","クリアユーザランク平均","総コンティニュー回数","挑戦回数","クリア回数","コンティニュー無クリア回数","コンティニュー有クリア回数","クリア率","リタイア回数","リタイア率"'."\n";
		foreach($quest_area as $key => $val) {
			$output_data .= $val['map_id'] . ',';
			$output_data .= $val['quest_id'] . ',';
			$output_data .= '"'.mb_convert_encoding($val['qname'], "SJIS") . '",';
			$output_data .= $val['area_id'] . ',';
			$output_data .= '"'.mb_convert_encoding($val['aname'], "SJIS") . '",';
			$output_data .= $val['start_uniq_user'] . ',';
			$output_data .= $val['clear_uniq_user'] . ',';
			$output_data .= $val['clear_rank_avg'] . ',';
			$output_data .= $val['total_continue'] . ',';
		//	$output_data .= $val['continue_medal'] . ',';
		//	$output_data .= $val['continue_srv'] . ',';
		//	$output_data .= $val['continue_pay'] . ',';
		//	$output_data .= $val['continue_avg'] . ',';
			$output_data .= $val['play_cnt'] . ',';
			$output_data .= $val['clear_cnt'] . ',';
			$output_data .= $val['clear_nocont_cnt'] . ',';
			$output_data .= $val['clear_cont_cnt'] . ',';
			$output_data .= $val['clear_avg'] . ',';
			$output_data .= $val['retire_cnt'] . ',';
			$output_data .= $val['retire_avg'] . "\n";
		}
		$quest_area = null;
		//出力
		$file_name = 'area_progress_'.$quest_id.'_'.substr($this->af->get('search_date_from'), 0, 10);
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=" . $file_name . ".csv");
		header("Content-Description: File Transfer");
		header("Content-Length: " . strlen($output_data) );
		echo $output_data;
		flush();
	}
}
