<?php
/**
 *  Admin/Log/Cs/Item/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_item_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsItem extends Pp_Form_AdminLogCs
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
/*
				'search_item_id' => array(
					// Form definition
					'type'        => VAR_TYPE_INT,     // Input type
					'form_type'   => FORM_TYPE_SELECT,   // Form type
					'option'      => array(
						'' => '',
						'9000' => 'マジカルメダル',
						'9001' => '合成メダル',
						'1100' => 'ブロンズチケット',
						'1101' => 'ゴールドチケット',
					),
					'name'        => 'アイテムID', // Display name

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
 *  admin_log_cs_item_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsItemIndex extends Pp_Form_AdminLogCsItem
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_item_id',
		'search_name',
		'search_name_option',
		'search_pp_id',
		'search_processing_type_name',
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
 *  admin_log_cs_item_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsItemIndex extends Pp_AdminActionClass
{
	const MAX_PAGE_DATA_COUNT = '100';
	const MAX_DATA_COUNT = '10000';
	const MAX_TERM_DAY = 14;

	/**
	 *  preprocess of admin_log_cs_item_index Action.
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
			return 'admin_log_cs_item_index';
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
				return 'admin_log_cs_item_index';
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
				return 'admin_log_cs_item_index';
			}

			if (Pp_Util::checkDateReversal($date_from, $date_to) === false) {
				$this->af->setApp('search_flg', '');
				$msg = "開始日と終了日が逆転しています";
				$this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
				return 'admin_log_cs_item_index';
			}

		}
		return null;

	}

	/**
	 *  admin_log_cs_item_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$limit = self::MAX_PAGE_DATA_COUNT;
		$offset = $this->af->get('start');
		$data_max_cnt = self::MAX_DATA_COUNT;

		$logdata_view_m = $this->backend->getManager('LogdataViewItem');
		$admin_user_m = $this->backend->getManager('AdminUser');
		$item_m = $this->backend->getManager('item');
		$search_params = array(
			'date_from' => $this->af->get('search_date_from'),
			'date_to' => $this->af->get('search_date_to'),
			'item_id' => $this->af->get('search_item_id'),
			'name' => $this->af->get('search_name'),
			'name_option' => $this->af->get('search_name_option'),
			'pp_id' => $this->af->get('search_pp_id'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		$this->af->setApp('name_option', $this->af->get('search_name_option'));

		if ($search_flg == '1'){
			$item_log_count = $logdata_view_m->getItemLogDataCount($search_params);
			if ($item_log_count > $data_max_cnt) {
				$this->af->setApp('item_log_count', -1);
				return 'admin_log_cs_item_index';
			}
			$item_log_data = $logdata_view_m->getItemLogData($search_params, $limit, $offset);
			$pager = $logdata_view_m->getPager($item_log_count, $offset, $limit);
			foreach($item_log_data['data'] as $k => $v) {
				$item_log_data['data'][$k]['user_base'] = $admin_user_m->getUserBaseDirect($v['pp_id']);
			}

			$item_m_list = $item_m->getMasterItemList();

			$this->af->setApp('item_log_list', $item_log_data['data']);
			$this->af->setApp('item_log_count', $item_log_count);
			$this->af->setApp('item_master_list', $item_m_list);
		}
		$this->af->setApp('create_file_path', 'admin/log/cs/item');
		return 'admin_log_cs_item_index';
	}
}
