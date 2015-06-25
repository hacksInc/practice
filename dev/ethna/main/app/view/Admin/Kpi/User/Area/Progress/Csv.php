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
		//�G���A�̃��X�g
		//$quest_area = $quest_m->getMasterQuestAreaAll();
		$quest_area = $quest_m->getMasterQuestAreaQuestId($quest_id);
		//���O�̌���
		$quest_log_count = $logdata_viewq_m->getQuestLogDataCount($search_params);
		$this->af->setApp('quest_log_count', $quest_log_count);
		//if ($quest_log_count == 0) {
		//	return 'admin_kpi_user_area_progress_csv';
		//}
		//���O�̃��X�g
		//$quest_log_data = $logdata_viewq_m->getQuestLogData($search_params);
		$quest_log_data = $logdata_viewq_m->getQuestLogDataForKpiArea($search_params);
		//$quest_data = array_reverse($quest_log_data['data'], true);
		$quest_data = $quest_log_data['data'];
		$quest_log_data = null;
		//$this->af->setApp('quest_log_data', $quest_data);
		//�G���A�f�[�^�ɏW�v�����l���Z�b�g
		$total_cp = false;
		foreach($quest_area as $key => $val) {
			$quest_area[$key]['start_uid_rank'] = array();//�X�^�[�gUID&�����N
			$quest_area[$key]['start_uniq_user'] = 0;//�X�^�[�g���j�[�N�l��
			$quest_area[$key]['clear_uid_rank'] = array();//�N���AUID&�����N
			$quest_area[$key]['clear_uniq_user'] = 0;//�N���A���j�[�N�l��
			$quest_area[$key]['clear_rank_avg'] = 0.0;//�N���A���[�U�����N����
			$quest_area[$key]['total_continue'] = 0;//���R���e�B�j���[��
		//	$quest_area[$key]['continue_medal'] = 0;//�R���e�B�j���[���z
		//	$quest_area[$key]['continue_srv'] = 0;//�R���e�B�j���[�z�i�T�[�r�X���_���j
		//	$quest_area[$key]['continue_pay'] = 0;//�R���e�B�j���[�z�i�L�����_���j
		//	$quest_area[$key]['continue_avg'] = 0.000;//�R���e�B�j���[�ۋ��䗦
			$quest_area[$key]['play_cnt'] = 0;//�����
			$quest_area[$key]['clear_cnt'] = 0;//�N���A��
			$quest_area[$key]['clear_nocont_cnt'] = 0;//�m�[�R���N���A��
			$quest_area[$key]['clear_cont_cnt'] = 0;//�R���e�B�j���[�N���A��
			$quest_area[$key]['clear_avg'] = 0.000;//�N���A��
			$quest_area[$key]['retire_cnt'] = 0;//���^�C�A��
			$quest_area[$key]['retire_avg'] = 0.000;//���^�C�A��
			if ($total_cp == false) {
				$quest_area['total'] = $quest_area[$key];
				$quest_area['total']['area_id'] = $quest_id;
				$quest_area['total']['aname'] = mb_convert_encoding('�N�G�X�g�S��', "UTF-8","SJIS");//���Ƃ�SJIS�ɕϊ����邽��
				$total_cp = true;
			}
		}
	//	//�R���e�B�j���[�̉ۋ��f�[�^�̂ݎ擾����
	//	$search_params = array(
	//		'date_from' => $this->af->get('search_date_from'),
	//		'date_to' => $this->af->get('search_date_to'),
	//		'processing_type' => 'D23',
	//	);
	//	$item_log_data = $logdata_viewi_m->getItemLogData($search_params);
	//	$item_data = $item_log_data['data'];
	//	$item_log_data = null;
		//���O����W�v
		foreach($quest_data as $key => $val) {
			$area_id = $val['area_id'];
			$user_id = $val['user_id'];
		//	//�R���e�B�j���[
		//	if ($val['quest_st'] == 3) {
		//		$api_transaction_id = $val['api_transaction_id'];
		//		foreach($item_data as $ikey => $ival) {
		//			if ($ival['api_transaction_id'] == $api_transaction_id && $ival['item_id']==9000) {
		//				//�L��
		//				if ($ival['service_flg'] == 0) {
		//					$quest_area[$area_id]['continue_pay'] -= $ival['count'];
		//					$quest_area['total']['continue_pay'] -= $ival['count'];
		//					$quest_area[$area_id]['continue_medal'] -= $ival['count'];
		//					$quest_area['total']['continue_medal'] -= $ival['count'];
		//				}
		//				//�T�[�r�X
		//				if ($ival['service_flg'] == 1) {
		//					$quest_area[$area_id]['continue_srv'] -= $ival['count'];
		//					$quest_area['total']['continue_srv'] -= $ival['count'];
		//					$quest_area[$area_id]['continue_medal'] -= $ival['count'];
		//					$quest_area['total']['continue_medal'] -= $ival['count'];
		//				}
		//			}
		//		}
		//	}
			//�N���Aor�Q�[���I�[�o�[
			if ($val['quest_st'] == 1 || $val['quest_st'] == 2) {
				//�����
				$quest_area[$area_id]['play_cnt']++;
				$quest_area['total']['play_cnt']++;
				//���R���e�B�j���[��
				$quest_area[$area_id]['total_continue'] += $val['continue_cnt'];
				$quest_area['total']['total_continue'] += $val['continue_cnt'];
			}
			//�N���A
			if ($val['quest_st'] == 1) {
				//�N���A��
				$quest_area[$area_id]['clear_cnt']++;
				$quest_area['total']['clear_cnt']++;
				//�m�[�R���N���Aor�R���e�B�j���[�N���A
				if ($val['continue_cnt'] == 0) {
					$quest_area[$area_id]['clear_nocont_cnt']++;//�m�[�R���N���A��
					$quest_area['total']['clear_nocont_cnt']++;//�m�[�R���N���A��
				}
				else {
					$quest_area[$area_id]['clear_cont_cnt']++;//�R���e�B�j���[�N���A��
					$quest_area['total']['clear_cont_cnt']++;//�R���e�B�j���[�N���A��
				}
				//�N���A�����N
				$quest_area[$area_id]['clear_uid_rank'][$user_id] = $val['rank'];//�����N
				$quest_area['total']['clear_uid_rank'][$user_id] = $val['rank'];//�����N
			}
			//�Q�[���I�[�o�[
			if ($val['quest_st'] == 2) {
				//���^�C�A��
				$quest_area[$area_id]['retire_cnt']++;
				$quest_area['total']['retire_cnt']++;
			}
			//�X�^�[�g
			if ($val['quest_st'] == 0) {
				//�X�^�[�g�����N
				$quest_area[$area_id]['start_uid_rank'][$user_id] = $val['rank'];//�����N
				$quest_area['total']['start_uid_rank'][$user_id] = $val['rank'];//�����N
			}
		}
		$item_data = null;
		//�G���A���ɍďW�v
		foreach($quest_area as $key => $val) {
			//�X�^�[�g���j�[�N�l���W�v
			$start_user_cnt = 0;
			if (!empty($val['start_uid_rank'])) {
				foreach($val['start_uid_rank'] as $sukey => $suval) {
					$start_user_cnt++;
				}
			}
			//�X�^�[�g���j�[�N�l��
			$quest_area[$key]['start_uniq_user'] = $start_user_cnt;
			//�N���A���j�[�N�l���W�v
			$clear_user_cnt = 0;
			$clear_user_rank = 0;
			if (!empty($val['clear_uid_rank'])) {
				foreach($val['clear_uid_rank'] as $cukey => $cuval) {
					$clear_user_cnt++;
					$clear_user_rank += $cuval;
				}
			}
			//�N���A���j�[�N�l��
			$quest_area[$key]['clear_uniq_user'] = $clear_user_cnt;
			//�N���A���[�U�����N����
			if ($clear_user_cnt > 0) $quest_area[$key]['clear_rank_avg'] = $clear_user_rank / $clear_user_cnt;
			if ($quest_area[$key]['clear_rank_avg'] > 0) $quest_area[$key]['clear_rank_avg'] = number_format($quest_area[$key]['clear_rank_avg'], 1);
		//	//�R���e�B�j���[�ۋ��䗦
		//	if ($quest_area[$key]['continue_medal'] > 0) $quest_area[$key]['continue_avg'] = number_format($quest_area[$key]['continue_pay'] / $quest_area[$key]['continue_medal'], 3);
		//	if ($quest_area[$key]['continue_avg'] > 0) $quest_area[$key]['continue_avg'] = number_format($quest_area[$key]['continue_avg'], 3);
			if ($quest_area[$key]['play_cnt'] > 0) {
				//�N���A��
				$quest_area[$key]['clear_avg'] = $quest_area[$key]['clear_cnt'] / $quest_area[$key]['play_cnt'];
				if ($quest_area[$key]['clear_avg'] > 0) $quest_area[$key]['clear_avg'] = number_format($quest_area[$key]['clear_avg'], 3);
				//���^�C�A��
				$quest_area[$key]['retire_avg'] = $quest_area[$key]['retire_cnt'] / $quest_area[$key]['play_cnt'];
				if ($quest_area[$key]['retire_avg'] > 0) $quest_area[$key]['retire_avg'] = number_format($quest_area[$key]['retire_avg'], 3);
			}
		}
		//�o�̓f�[�^����
		$output_data = "���[�U�[���� �G���A�i��\n";
		$output_data .= "�������F$date_from �` $date_to\n";
		$output_data .= "�W�v���ځF".$ua_list[$ua]."\n\n";
	//	$output_data .= '"�}�b�vID","�N�G�X�gID","�N�G�X�g��","�G���AID","�G���A��","�X�^�[�g���j�[�N�l��","�N���A���j�[�N�l��","�N���A���[�U�����N����","���R���e�B�j���[��","�R���e�B�j���[���z","�R���e�B�j���[�z(�T�[�r�X���_��)","�R���e�B�j���[�z(�L�����_��)","�R���e�B�j���[�ۋ��䗦","�����","�N���A��","�R���e�B�j���[���N���A��","�R���e�B�j���[�L�N���A��","�N���A��","���^�C�A��","���^�C�A��"'."\n";
		$output_data .= '"�}�b�vID","�N�G�X�gID","�N�G�X�g��","�G���AID","�G���A��","�X�^�[�g���j�[�N�l��","�N���A���j�[�N�l��","�N���A���[�U�����N����","���R���e�B�j���[��","�����","�N���A��","�R���e�B�j���[���N���A��","�R���e�B�j���[�L�N���A��","�N���A��","���^�C�A��","���^�C�A��"'."\n";
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
		//�o��
		$file_name = 'area_progress_'.$quest_id.'_'.substr($this->af->get('search_date_from'), 0, 10);
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=" . $file_name . ".csv");
		header("Content-Description: File Transfer");
		header("Content-Length: " . strlen($output_data) );
		echo $output_data;
		flush();
	}
}
