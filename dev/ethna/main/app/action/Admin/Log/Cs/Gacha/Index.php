<?php
/**
 *  Admin/Log/Cs/Gacha/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_gacha_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsGacha extends Pp_Form_AdminLogCs
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
				/*
				'search_gacha_id' => array(
					// Form definition
					'type'        => VAR_TYPE_INT,     // Input type
					'form_type'   => FORM_TYPE_SELECT,   // Form type
					'option'      => array(
						'' => '',
						'1' => 'ブロンズ',
						'2' => 'ゴールド',
						'3' => 'レア',
					),
					'name'        => 'ガチャ種類', // Display name

					//  Validator (executes Validator by written order.)
					'required'    => false,             // Required Option(true/false)
					//'min'         => 30000,            // Minimum value
					//'max'         => 40000,            // Maximum value
				),
				 */
			);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}

		parent::__construct($backend);
	}
}

/**
 *  admin_log_cs_gacha_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsGachaIndex extends Pp_Form_AdminLogCsGacha
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_gacha_id',
		'search_name',
		'search_name_option',
		'search_pp_id',
		'search_flg',
		'start',
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
 *  admin_log_cs_gacha_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsGachaIndex extends Pp_Action_AdminLogCsIndex
{
	const MAX_PAGE_DATA_COUNT = '100';
	const MAX_DATA_COUNT = '10000';
	const MAX_TERM_DAY = 14;

	/**
	 *  preprocess of admin_log_cs_gacha_index Action.
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
			return 'admin_log_cs_gacha_index';
		}

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('name_option', $this->af->get('search_name_option'));
		if ($search_flg == '1'){
			if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
				return 'admin_log_cs_gacha_index';
			}
		}
		return null;

	}

	/**
	 *  admin_log_cs_gacha_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$limit = self::MAX_PAGE_DATA_COUNT;
		$offset = $this->af->get('start');
		$data_max_cnt = self::MAX_DATA_COUNT;

		$logdata_view_m = $this->backend->getManager('LogdataViewGacha');
		$logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
		$photo_gacha_m = $this->backend->getManager('PhotoGacha');
		$search_params = array(
			'date_from' => $this->af->get('search_date_from'),
			'date_to' => $this->af->get('search_date_to'),
			'gacha_id' => $this->af->get('search_gacha_id'),
			'name' => $this->af->get('search_name'),
			'name_option' => $this->af->get('search_name_option'),
			'pp_id' => $this->af->get('search_pp_id'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		$this->af->setApp('name_option', $this->af->get('search_name_option'));

		if ($search_flg != '1'){
			return 'admin_log_cs_gacha_index';
		}

		$gacha_log_count = $logdata_view_m->getGachaLogDataCount($search_params);
		if ($gacha_log_count == 0) {
			$this->af->setApp('gacha_log_count', 0);
			return 'admin_log_cs_gacha_index';
		}
		if ($gacha_log_count > $data_max_cnt) {
			$this->af->setApp('gacha_log_count', -1);
			return 'admin_log_cs_gacha_index';
		}

		$gacha_log_data = $logdata_view_m->getGachaLogData($search_params, $limit, $offset);
		$pager = $logdata_view_m->getPager($gacha_log_count, $offset, $limit);

		// api_transaction_idでひも付く詳細ログ情報を取得する
		foreach ($gacha_log_data['data'] as $k => $v) {
			$transaction_id_list[] = $v['api_transaction_id'];
		}

		$photo_gacha_list = array();
		$list = $photo_gacha_m->getMasterPhotoGachaAll();
		foreach($list as $k => $v) {
			$photo_gacha_list[$v['gacha_id']] = $v;
		}

			/*
			// ガチャ詳細情報
			$gacha_prize_list = $logdata_view_m->getGachaPrizeLogDataByApiTransactionId($transaction_id_list);
			$gacha_prize_data = '';
			foreach($gacha_prize_list['data'] as $k => $v){
				$gacha_prize_data[$v['api_transaction_id']][] = $v;
			}

			// 取得モンスター情報
			$monster_list = $logdata_view_monster_m->getMonsterDataByApiTransactionId($transaction_id_list);
			$monster_data_list = '';
			foreach($sell_monster_list['data'] as $k => $v){
				$monster_data_list[$v['api_transaction_id']][] = $v;
			}
			 */

		$this->af->setApp('gacha_log_list', $gacha_log_data['data']);
		$this->af->setApp('gacha_log_count', $gacha_log_count);
		$this->af->setApp('gacha_log_count_2', $gacha_log_data['count']);
		$this->af->setApp('photo_gacha_list', $photo_gacha_list);

		return 'admin_log_cs_gacha_index';
	}
}
