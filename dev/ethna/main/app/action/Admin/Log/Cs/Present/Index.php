<?php
/**
 *  Admin/Log/Cs/Present/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_present_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsPresent extends Pp_Form_AdminLogCs
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'search_stetus' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_SELECT,   // Form type
				'option'      => array(
					'' => '',
					'0' => '新規',
					'2' => '受取済み',
					'-1' => '削除',
				),
				'name'        => 'ステータス', // Display name

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
 *  admin_log_cs_present_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsPresentIndex extends Pp_Form_AdminLogCsPresent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_stetus',
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
 *  admin_log_cs_present_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsPresentIndex extends Pp_AdminActionClass
{
	const MAX_PAGE_DATA_COUNT = '100';
	const MAX_DATA_COUNT = '10000';

	/**
	 *  preprocess of admin_log_cs_present_index Action.
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
			return 'admin_log_cs_present_index';
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
				return 'admin_log_cs_present_index';
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
				return 'admin_log_cs_present_index';
			}

			if (Pp_Util::checkDateReversal($date_from, $date_to) === false) {
				$this->af->setApp('search_flg', '');
				$msg = "開始日と終了日が逆転しています";
				$this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
				return 'admin_log_cs_present_index';
			}

		}
		return null;

	}

	/**
	 *  admin_log_cs_present_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$limit = self::MAX_PAGE_DATA_COUNT;
		$offset = $this->af->get('start');
		$data_max_cnt = self::MAX_DATA_COUNT;

		$logdata_view_m = $this->backend->getManager('LogdataViewPresent');
		$present_m = $this->backend->getManager('Present');
		$item_m = $this->backend->getManager('Item');
		$search_params = array(
			'date_from' => $this->af->get('search_date_from'),
			'date_to' => $this->af->get('search_date_to'),
			'status' => $this->af->get('search_status'),
			'name' => $this->af->get('search_name'),
			'name_option' => $this->af->get('search_name_option'),
			'pp_id' => $this->af->get('search_pp_id'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		$this->af->setApp('name_option', $this->af->get('search_name_option'));

		if ($search_flg == '1'){
			$present_log_count = $logdata_view_m->getPresentLogDataCount($search_params);
			if ($present_log_count == 0) {
				$this->af->setApp('present_log_count', 0);
				return 'admin_log_cs_present_index';
			}
			if ($present_log_count > $data_max_cnt) {
				$this->af->setApp('present_log_count', -1);
				return 'admin_log_cs_present_index';
			}
			$present_log_data = $logdata_view_m->getPresentLogData($search_params, $limit, $offset);
			$pager = $logdata_view_m->getPager($present_log_count, $offset, $limit);
			foreach($present_log_data['data'] as $k => $v) {
				switch($v['present_category']) {
				case Pp_PresentManager::CATEGORY_ITEM:
					$item = $item_m->getMasterItem($v['present_value']);
					$present_log_data['data'][$k]['name'] = $item['name_ja'];
					break;
				case Pp_PresentManager::CATEGORY_PHOTO:
					$present_log_data['data'][$k]['name'] = "フォトID{$v['present_value']}";
					break;
				case Pp_PresentManager::CATEGORY_PP:
					$present_log_data['data'][$k]['name'] = "ポータルポイント";
					break;
				}
			}

			$this->af->setApp('present_log_list', $present_log_data['data']);
			$this->af->setApp('present_log_count', $present_log_count);
			$this->af->setApp('present_log_count_2', $present_log_data['count']);
			$this->af->setApp('present_comment', $present_m->COMMENT_ID_OPTIONS);
			$this->af->setApp('present_status', $logdata_view_m->PRESENT_STATUS);
		}
		$this->af->setApp('create_file_path', 'admin/log/cs/present');
		return 'admin_log_cs_present_index';
	}
}
