<?php
/**
 *  Admin/Kpi/User/Area/Progress2/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_kpi_user_area_* で共通のアクションフォーム定義 */
class Pp_Form_AdminKpiUserAreaProgress2 extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'search_ua' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_SELECT,   // Form type
				'option'      => array(
					'' => '',
					'0' => 'ALL',
					'1' => 'iOS',
					'2' => 'Android',
				),
				'name'        => '集計項目', // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,             // Required Option(true/false)
				//'min'         => 30000,            // Minimum value
				//'max'         => 40000,            // Maximum value
			),
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}

		parent::__construct($backend);
	}
}

/**
 *  admin_kpi_user_area_progress2_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiUserAreaProgress2Index extends Pp_Form_AdminKpiUserAreaProgress2
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_ua',
		'search_quest_id',
		'search_flg',
		'csv',
	);

	/**
	 *  Form input value convert filter : sample
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	/*
	function _filter_sample($value)
	{
		//  convert to upper case.
		return strtoupper($value);
	}
	*/
}

/**
 *  admin_kpi_user_area_progress2_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiUserAreaProgress2Index extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_log_cs_area_index Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{

		// アクセス制御
		if ($this->must_login && $this->must_permission) {
			$ret = $this->permit();
			if ($ret) {
				return $ret;
			}
		}

		if ($this->af->validate() > 0) {
			return 'admin_kpi_user_area_progress2_index';
		}

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('name_option', $this->af->get('search_name_option'));
		if ($search_flg == '1'){

			$date_from = $this->af->get('search_date_from');
			$date_to = $this->af->get('search_date_to');
			if (empty($date_from) && empty($date_to)){
				$this->af->setApp('search_flg', '');
				$msg = "検索日が入力されていません";
				$this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
				return 'admin_kpi_user_area_progress_index';
			}

			if (!empty($date_from) && empty($date_to)){
				// 足りていないほうに日付を入力する
				$date_to = date('Y/m/d H:i:s', strtotime($date_from)+(60*60*24*14));
				$this->af->set('search_date_to', $date_to);
				return null;
			}

			if (empty($date_from) && !empty($date_to)){
				// 足りていないほうに日付を入力する
				$date_from = date('Y/m/d H:i:s', strtotime($date_to)-(60*60*24*14));
				$this->af->set('search_date_from', $date_from);
				return null;
			}

			if (Pp_Util::checkDateRange($date_from, $date_to, 14) === false) {
				$this->af->setApp('search_flg', '');
				$msg = "期間指定は14日以内で指定をしてください";
				$this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
				return 'admin_kpi_user_area_progress_index';
			}

			if (Pp_Util::checkDateReversal($date_from, $date_to) === false) {
				$this->af->setApp('search_flg', '');
				$msg = "開始日と終了日が逆転しています";
				$this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
				return 'admin_kpi_user_area_progress_index';
			}

		}
		return null;

	}

	/**
	 *  admin_kpi_user_area_progress2_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$quest_m =& $this->backend->getManager('AdminQuest');
		//$logdata_viewi_m = $this->backend->getManager('LogdataViewItem');
		$logdata_viewq_m = $this->backend->getManager('LogdataViewQuest');
		$quest_id = $this->af->get('search_quest_id');
		$search_params = array(
			'date_from' => $this->af->get('search_date_from'),
			'date_to' => $this->af->get('search_date_to'),
			'ua' => $this->af->get('search_ua'),
			'quest_id' => $quest_id,
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);

		//クエストのリスト
		$quest_list = $quest_m->getMasterQuestAll();
		$this->af->setApp('quest_list', $quest_list);

		if ($this->af->get('csv') != null) {
			return 'admin_kpi_user_area_progress2_csv';
		}

		if ($search_flg == '1'){
			//エリアのリスト
			$quest_area = $quest_m->getMasterQuestAreaQuestId($quest_id);
		//	//ログの件数
		//	$quest_log_count = $logdata_viewq_m->getQuestLogDataCount($search_params);
		//	$this->af->setApp('quest_log_count', $quest_log_count);
			//if ($quest_log_count == 0) {
			//	return 'admin_kpi_user_area_progress_index';
			//}
		//	//ログのリスト
		//	$quest_log_data = $logdata_viewq_m->getQuestLogDataForKpiArea($search_params);
		//	//$quest_data = array_reverse($quest_log_data['data'], true);
		//	$quest_data = $quest_log_data['data'];
		//	$quest_log_data = null;
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
					$quest_area['total']['aname'] = 'クエスト全体';
					$total_cp = true;
				}
			}
		//	//コンティニューの課金データのみ取得する
		//	$search_params = array(
		//		'date_from' => $this->af->get('search_date_from'),
		//		'date_to' => $this->af->get('search_date_to'),
		//		'processing_type' => 'D23',
		//	);
		//	$item_log_data = $logdata_viewi_m->getItemLogDataForKpiArea($search_params);
		//	$item_data = $item_log_data['data'];
		//	$item_log_data = null;
			//ログから集計
		/*
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
		*/
			//エリア毎に再集計
			foreach($quest_area as $key => $val) {
				if ($key != 'total') {
					//スタートユニーク人数集計
					$start_user_cnt = 0;
					/*
					if (!empty($val['start_uid_rank'])) {
						foreach($val['start_uid_rank'] as $sukey => $suval) {
							$start_user_cnt++;
						}
					}
					*/
					$search_params = array(
						'date_from' => $this->af->get('search_date_from'),
						'date_to' => $this->af->get('search_date_to'),
						'ua' => $this->af->get('search_ua'),
						'status' => 0,
						'area_id' => $key,
					);
					$start_user_cnt = $logdata_viewq_m->distinctQuestLogForKpiArea($search_params);
					//スタートユニーク人数
					$quest_area[$key]['start_uniq_user'] = $start_user_cnt;
				//	$quest_area['total']['start_uniq_user'] += $start_user_cnt;
					//クリアユニーク人数集計
					$clear_user_cnt = 0;
					/*
					$clear_user_rank = 0;
					if (!empty($val['clear_uid_rank'])) {
						foreach($val['clear_uid_rank'] as $cukey => $cuval) {
							$clear_user_cnt++;
							$clear_user_rank += $cuval;
						}
					}
					*/
					$search_params = array(
						'date_from' => $this->af->get('search_date_from'),
						'date_to' => $this->af->get('search_date_to'),
						'ua' => $this->af->get('search_ua'),
						'status' => 1,
						'area_id' => $key,
					);
					$clear_user_cnt = $logdata_viewq_m->distinctQuestLogForKpiArea($search_params);
					//クリアユニーク人数
					$quest_area[$key]['clear_uniq_user'] = $clear_user_cnt;
				//	$quest_area['total']['clear_uniq_user'] += $clear_user_cnt;
					//総コンティニュー回数
					$search_params = array(
						'date_from' => $this->af->get('search_date_from'),
						'date_to' => $this->af->get('search_date_to'),
						'ua' => $this->af->get('search_ua'),
						'area_id' => $key,
					);
					$total_continue = $logdata_viewq_m->sumQuestLogForKpiArea('continue_cnt', $search_params);
					$quest_area[$key]['total_continue'] = $total_continue;
					$quest_area['total']['total_continue'] += $total_continue;
					//クリア回数
					$search_params = array(
						'date_from' => $this->af->get('search_date_from'),
						'date_to' => $this->af->get('search_date_to'),
						'ua' => $this->af->get('search_ua'),
						'area_id' => $key,
						'status' => 1,
					);
					$clear_cnt = $logdata_viewq_m->countQuestLogForKpiArea($search_params);
					$quest_area[$key]['clear_cnt'] = $clear_cnt;
					$quest_area['total']['clear_cnt'] += $clear_cnt;
					//クリア回数（コンティニューなし）
					$search_params = array(
						'date_from' => $this->af->get('search_date_from'),
						'date_to' => $this->af->get('search_date_to'),
						'ua' => $this->af->get('search_ua'),
						'area_id' => $key,
						'status' => 1,
						'continue_cnt' => 0,
					);
					$clear_nocont_cnt = $logdata_viewq_m->countQuestLogForKpiArea($search_params);
					$quest_area[$key]['clear_nocont_cnt'] = $clear_nocont_cnt;
					$quest_area['total']['clear_nocont_cnt'] += $clear_nocont_cnt;
					//クリア回数（コンティニューあり）
					$search_params = array(
						'date_from' => $this->af->get('search_date_from'),
						'date_to' => $this->af->get('search_date_to'),
						'ua' => $this->af->get('search_ua'),
						'area_id' => $key,
						'status' => 1,
						'continue_gt' => 0,
					);
					$clear_cont_cnt = $logdata_viewq_m->countQuestLogForKpiArea($search_params);
					$quest_area[$key]['clear_cont_cnt'] = $clear_cont_cnt;
					$quest_area['total']['clear_cont_cnt'] += $clear_cont_cnt;
				//	//挑戦回数(総コンティニュー回数+クリア回数)
				//	$quest_area[$key]['play_cnt'] = $clear_cnt + $total_continue;
				//	$quest_area['total']['play_cnt'] += $clear_cnt + $total_continue;
					//挑戦回数
					//クリア回数
					$search_params = array(
						'date_from' => $this->af->get('search_date_from'),
						'date_to' => $this->af->get('search_date_to'),
						'ua' => $this->af->get('search_ua'),
						'area_id' => $key,
						'status' => 2,
					);
					$play_cnt = $logdata_viewq_m->countQuestLogForKpiArea($search_params);
					$quest_area[$key]['play_cnt'] = $play_cnt+$clear_cnt;
					$quest_area['total']['play_cnt'] += $play_cnt+$clear_cnt;
					//リタイア回数
					$search_params = array(
						'date_from' => $this->af->get('search_date_from'),
						'date_to' => $this->af->get('search_date_to'),
						'ua' => $this->af->get('search_ua'),
						'area_id' => $key,
						'status' => 2,
					);
					$retire_cnt = $logdata_viewq_m->countQuestLogForKpiArea($search_params);
					$quest_area[$key]['retire_cnt'] = $retire_cnt;
					$quest_area['total']['retire_cnt'] += $retire_cnt;
					
				//	//クリアユーザランク平均
				//	if ($clear_user_cnt > 0) $quest_area[$key]['clear_rank_avg'] = number_format($clear_user_rank / $clear_user_cnt, 1);
				//	//コンティニュー課金比率
				//	if ($quest_area[$key]['continue_medal'] > 0) $quest_area[$key]['continue_avg'] = $quest_area[$key]['continue_pay'] / $quest_area[$key]['continue_medal'];
				//	if ($quest_area[$key]['continue_avg'] > 0) $quest_area[$key]['continue_avg'] = number_format($quest_area[$key]['continue_avg'], 3);
					
					//クリアユーザランク平均
					$search_params = array(
						'date_from' => $this->af->get('search_date_from'),
						'date_to' => $this->af->get('search_date_to'),
						'ua' => $this->af->get('search_ua'),
						'status' => 1,
						'area_id' => $key,
					);
					$clear_rank_avg = $logdata_viewq_m->avgQuestLogForKpiArea('rank', $search_params);
					$quest_area[$key]['clear_rank_avg'] = number_format($clear_rank_avg, 1);
					
					if ($quest_area[$key]['play_cnt'] > 0) {
						//クリア率
						$quest_area[$key]['clear_avg'] = $quest_area[$key]['clear_cnt'] / $quest_area[$key]['play_cnt'];
						if ($quest_area[$key]['clear_avg'] > 0) $quest_area[$key]['clear_avg'] = number_format($quest_area[$key]['clear_avg'], 3);
						//リタイア率
						$quest_area[$key]['retire_avg'] = $quest_area[$key]['retire_cnt'] / $quest_area[$key]['play_cnt'];
						if ($quest_area[$key]['retire_avg'] > 0) $quest_area[$key]['retire_avg'] = number_format($quest_area[$key]['retire_avg'], 3);
					}
				}
			}
			//total分だけ別集計
			//スタートユニーク人数集計
			$search_params = array(
				'date_from' => $this->af->get('search_date_from'),
				'date_to' => $this->af->get('search_date_to'),
				'ua' => $this->af->get('search_ua'),
				'status' => 0,
				'quest_id' => $quest_id,
			);
			$start_user_cnt = $logdata_viewq_m->distinctQuestLogForKpiArea($search_params);
			//スタートユニーク人数
			$quest_area['total']['start_uniq_user'] += $start_user_cnt;
			//クリアユニーク人数集計
			$search_params = array(
				'date_from' => $this->af->get('search_date_from'),
				'date_to' => $this->af->get('search_date_to'),
				'ua' => $this->af->get('search_ua'),
				'status' => 1,
				'quest_id' => $quest_id,
			);
			$clear_user_cnt = $logdata_viewq_m->distinctQuestLogForKpiArea($search_params);
			//クリアユニーク人数
			$quest_area['total']['clear_uniq_user'] += $clear_user_cnt;
			
			//クリアユーザランク平均
			$search_params = array(
				'date_from' => $this->af->get('search_date_from'),
				'date_to' => $this->af->get('search_date_to'),
				'ua' => $this->af->get('search_ua'),
				'status' => 1,
				'quest_id' => $quest_id,
			);
			$clear_rank_avg = $logdata_viewq_m->avgQuestLogForKpiArea('rank', $search_params);
			$quest_area['total']['clear_rank_avg'] = number_format($clear_rank_avg, 1);
			
			if ($quest_area['total']['play_cnt'] > 0) {
				//クリア率
				$quest_area['total']['clear_avg'] = $quest_area['total']['clear_cnt'] / $quest_area['total']['play_cnt'];
				if ($quest_area['total']['clear_avg'] > 0) $quest_area['total']['clear_avg'] = number_format($quest_area['total']['clear_avg'], 3);
				//リタイア率
				$quest_area['total']['retire_avg'] = $quest_area['total']['retire_cnt'] / $quest_area['total']['play_cnt'];
				if ($quest_area['total']['retire_avg'] > 0) $quest_area['total']['retire_avg'] = number_format($quest_area['total']['retire_avg'], 3);
			}
			
			//出力
			$this->af->setApp('quest_area_cnt', count($quest_area));
			$this->af->setApp('quest_area', $quest_area);
			//$this->af->setApp('item_data', $item_data);
		}
		$this->af->setApp('create_file_path', '/admin/kpi/user/area/progress2');
		return 'admin_kpi_user_area_progress2_index';
	}
}
