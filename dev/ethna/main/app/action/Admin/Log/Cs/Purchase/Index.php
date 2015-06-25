<?php
/**
 *  Admin/Log/Cs/Purchase/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_purchase_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsPurchase extends Pp_Form_AdminLogCs
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);
	}
}

/**
 *  admin_log_cs_purchase_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsPurchaseIndex extends Pp_Form_AdminLogCsPurchase
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_name',
		'search_name_option',
		'search_user_id',
		'search_processing_type_name',
		'search_flg',
		'start',
	);

}

/**
 *  admin_log_cs_purchase_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsPurchaseIndex extends Pp_Action_AdminLogCsIndex
{
	const MAX_PAGE_DATA_COUNT = '100';
	const MAX_DATA_COUNT = '10000';
	const MAX_TERM_DAY = 14;

	/**
	 *  preprocess of admin_log_cs_purchase_index Action.
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
			return 'admin_log_cs_purchase_index';
		}

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('name_option', $this->af->get('search_name_option'));
		if ($search_flg == '1'){
			if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
				return 'admin_log_cs_purchase_index';
			}
		}
		return null;

	}

	/**
	 *  admin_log_cs_purchase_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$limit = self::MAX_PAGE_DATA_COUNT;
		$offset = $this->af->get('start');
		$data_max_cnt = self::MAX_DATA_COUNT;

		$logdata_view_m = $this->backend->getManager('LogdataViewPurchase');
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$admin_m = $this->backend->getManager('Admin');

		$search_params = array(
			'date_from' => $this->af->get('search_date_from'),
			'date_to' => $this->af->get('search_date_to'),
			'name' => $this->af->get('search_name'),
			'name_option' => $this->af->get('search_name_option'),
			'user_id' => $this->af->get('search_user_id'),
			'processing_type_name' => $this->af->get('search_processing_type_name'),
		);

		$lid = $this->session->get('lid');
		$user = $admin_m->getAdminUser($lid);
		$role = $user['role'];

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		$this->af->setApp('name_option', $this->af->get('search_name_option'));

		if ($search_flg != '1'){
			return 'admin_log_cs_purchase_index';
		}

		$purchase_log_count = $logdata_view_m->getPurchaseLogDataCount($search_params);
		if ($purchase_log_count > $data_max_cnt) {
			$this->af->setApp('purchase_log_count', -1);
			return 'admin_log_cs_purchase_index';
		}

		if ($purchase_log_count == 0) {
			$this->af->setApp('purchase_log_count', 0);
			return 'admin_log_cs_purchase_index';
		}

		$purchase_log_data = $logdata_view_m->getPurchaseLogData($search_params, $limit, $offset);
		$pager = $logdata_view_m->getPager($purchase_log_count, $offset, $limit);

		// api_transaction_idでひも付くモンスターログ情報を取得する(モンスター単位での売却価格を取得)
		foreach ($purchase_log_data['data'] as $k => $v) {
			$transaction_id_list[] = $v['api_transaction_id'];
		}

		// 売却時喪失モンスター情報
		$item_list = $logdata_view_item_m->getItemDataByApiTransactionId($transaction_id_list);
		$item_data_list = '';
		foreach($item_list['data'] as $k => $v){
			$item_data_list[$v['api_transaction_id']][] = $v;
		}

		$this->af->setApp('purchase_log_list', $purchase_log_data['data']);
		$this->af->setApp('purchase_log_count', $purchase_log_count);
		$this->af->setApp('item_data_list', $item_data_list);
		$this->af->setApp('create_file_path', '/admin/log/cs/purchase');
		$this->af->setApp('role', $role);
		return 'admin_log_cs_purchase_index';
	}
}
