<?php
/**
 *  Admin/Log/Cs/Photo/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_photo_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsPhoto extends Pp_Form_AdminLogCs
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}

		parent::__construct($backend);
	}
}

/**
 *  admin_log_cs_photo_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsPhotoIndex extends Pp_Form_AdminLogCsPhoto
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
 *  admin_log_cs_photo_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsPhotoIndex extends Pp_Action_AdminLogCsIndex
{
	const MAX_PAGE_DATA_COUNT = '100';
	const MAX_DATA_COUNT = '10000';
	const MAX_TERM_DAY = 14;

	/**
	 *  preprocess of admin_log_cs_photo_index Action.
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
			return 'admin_log_cs_photo_index';
		}

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('name_option', $this->af->get('search_name_option'));
		if ($search_flg == '1'){
			if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
				return 'admin_log_cs_photo_index';
			}
		}
		return null;

	}

	/**
	 *  admin_log_cs_photo_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$limit = self::MAX_PAGE_DATA_COUNT;
		$offset = $this->af->get('start');
		$data_max_cnt = self::MAX_DATA_COUNT;

		$logdata_view_m = $this->backend->getManager('LogdataViewPhoto');
		$search_params = array(
			'date_from' => $this->af->get('search_date_from'),
			'date_to' => $this->af->get('search_date_to'),
			'photo_id' => $this->af->get('search_photo_id'),
			'name' => $this->af->get('search_name'),
			'name_option' => $this->af->get('search_name_option'),
			'pp_id' => $this->af->get('search_pp_id'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		$this->af->setApp('name_option', $this->af->get('search_name_option'));

		if ($search_flg != '1'){
			return 'admin_log_cs_photo_index';
		}

		$photo_log_count = $logdata_view_m->getPhotoLogDataCount($search_params);
		if ($photo_log_count == 0) {
			$this->af->setApp('photo_log_count', 0);
			return 'admin_log_cs_photo_index';
		}
		if ($photo_log_count > $data_max_cnt) {
			$this->af->setApp('photo_log_count', -1);
			return 'admin_log_cs_photo_index';
		}

		$photo_log_data = $logdata_view_m->getPhotoLogData($search_params, $limit, $offset);
		$pager = $logdata_view_m->getPager($photo_log_count, $offset, $limit);

		// api_transaction_idでひも付く詳細ログ情報を取得する
		foreach ($photo_log_data['data'] as $k => $v) {
			$transaction_id_list[] = $v['api_transaction_id'];
		}

		$this->af->setApp('photo_log_list', $photo_log_data['data']);
		$this->af->setApp('photo_log_count', $photo_log_count);

		return 'admin_log_cs_photo_index';
	}
}
