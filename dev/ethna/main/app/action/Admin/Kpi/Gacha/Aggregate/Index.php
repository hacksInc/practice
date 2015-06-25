<?php
/**
 *  Admin/Kpi/Gacha/Aggregate/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_kpi_user_area_* で共通のアクションフォーム定義 */
class Pp_Form_AdminKpiGachaAggregate extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'search_gacha_ids' => array(
				'type'        => array(VAR_TYPE_INT),     // Input type
			),
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}

		parent::__construct($backend);
	}
}

/**
 *  admin_kpi_gacha_aggregate_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiGachaAggregateIndex extends Pp_Form_AdminKpiGachaAggregate
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_gacha_ids',
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
class Pp_Action_AdminKpiGachaAggregateIndex extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_kpi_gacha_aggregate_index Action.
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
			return 'admin_kpi_gacha_aggregate_index';
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

			if (Pp_Util::checkDateRange($date_from, $date_to, 31) === false) {
				$this->af->setApp('search_flg', '');
				$msg = "期間指定は31日以内で指定をしてください";
				$this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
				return 'admin_kpi_gacha_aggregate_index';
			}

			if (Pp_Util::checkDateReversal($date_from, $date_to) === false) {
				$this->af->setApp('search_flg', '');
				$msg = "開始日と終了日が逆転しています";
				$this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
				return 'admin_kpi_gacha_aggregate_index';
			}

		}
		return null;

	}

	/**
	 *  admin_kpi_gacha_aggregate_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
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
		$this->af->setApp('search_flg', $search_flg);

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
		$this->af->setApp('gacha_list', $gacha_list);

		if ($this->af->get('csv') != null) {
			return 'admin_kpi_gacha_aggregate_csv';
		}

		$dataname = array();
		$datazero = array();
		$data = array();
		if ($search_flg == '1'){
			$idx = 0;
			$dataname[$idx] = $datazero[$idx] = 'ユーザID';
			$idx++;
			foreach ($gacha_ids as $gkey => $gval) {
				$gacha_master = $shop_m->getGachaListId($gval);
				//error_log("gacha_id=$gval ".$gacha_master['comment']);
				$dataname[$idx] = "ガチャID".$gval ."\n". $gacha_master['comment']."\n有料";
				$datazero[$idx] = 0;
				$idx++;
				$dataname[$idx] = "ガチャID".$gval ."\n". $gacha_master['comment']."\n無料";
				$datazero[$idx] = 0;
				$idx++;
			}
			$idx = 1;
			foreach ($gacha_ids as $gkey => $gval) {
				$search_params = array(
					'date_from' => $this->af->get('search_date_from'),
					'date_to' => $this->af->get('search_date_to'),
					'gacha_id' => $gval,
				//	'name' => $this->af->get('search_name'),
				//	'name_option' => $this->af->get('search_name_option'),
				//	'user_id' => $this->af->get('search_user_id'),
				);
				$gacha_log_data = $logdata_viewg_m->getGachaLogData($search_params);
				// api_transaction_idでひも付く詳細ログ情報を取得する
				foreach ($gacha_log_data['data'] as $k => $v) {
					$user_id = $v['user_id'];
					$datazero[0] = $user_id;
					if (array_key_exists($user_id, $data) == false) $data[$user_id] = $datazero;
					//$search_params = array(
					//	'api_transaction_id' => $v['api_transaction_id'],
					//);
				//	error_log("api_transaction_id=".$v['api_transaction_id']);
					$item_log_data = $logdata_viewi_m->getItemDataByApiTransactionId($v['api_transaction_id']);
					foreach($item_log_data['data'] as $kk => $vv) {
						//error_log("  item_log_data ($kk) : vv=".print_r($vv,true));
						if ($vv['count'] < 0) {
							$data[$user_id][$idx+($vv['service_flg'])]++;
						//	error_log("$user_id:$gval(".$vv['service_flg'].")=".$data[$user_id][$idx+($vv['service_flg'])]);
						}
					}
				}
				$idx+=2;
			}
			$this->af->setApp('dataname', $dataname);
			$this->af->setApp('data', $data);
		}
		$this->af->setApp('create_file_path', '/admin/kpi/gacha/aggregate/index');
		return 'admin_kpi_gacha_aggregate_index';
	}
}
