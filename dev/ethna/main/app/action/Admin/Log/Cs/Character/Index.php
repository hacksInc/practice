<?php
/**
 *  Admin/Log/Cs/Character/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_character_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsCharacter extends Pp_Form_AdminLogCs
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);
	}
}

/**
 *  admin_log_cs_character_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsCharacterIndex extends Pp_Form_AdminLogCsCharacter
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
		'search_processing_type_name',
		'search_flg',
		'start',
	);

}

/**
 *  admin_log_cs_character_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsCharacterIndex extends Pp_Action_AdminLogCsIndex
{
	const MAX_PAGE_DATA_COUNT = '100';
	const MAX_DATA_COUNT = '10000';
	const MAX_TERM_DAY = 14;

	/**
	 *  preprocess of admin_log_cs_character_index Action.
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
			return 'admin_log_cs_character_index';
		}

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('name_option', $this->af->get('search_name_option'));
		if ($search_flg == '1'){
			if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
				return 'admin_log_cs_character_index';
			}
		}
		return null;

	}

	/**
	 *  admin_log_cs_character_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$limit = self::MAX_PAGE_DATA_COUNT;
		$offset = $this->af->get('start');
		$data_max_cnt = self::MAX_DATA_COUNT;

		$logdata_view_m = $this->backend->getManager('LogdataViewCharacter');
		$admin_user_m = $this->backend->getManager('AdminUser');
		$character_m = $this->backend->getManager('Character');

		$search_params = array(
			'date_from' => $this->af->get('search_date_from'),
			'date_to' => $this->af->get('search_date_to'),
			'name' => $this->af->get('search_name'),
			'name_option' => $this->af->get('search_name_option'),
			'pp_id' => $this->af->get('search_pp_id'),
			'processing_type_name' => $this->af->get('search_processing_type_name'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		$this->af->setApp('name_option', $this->af->get('search_name_option'));

		if ($search_flg != '1'){
			return 'admin_log_cs_character_index';
		}

		$character_log_count = $logdata_view_m->getCharacterLogDataCount($search_params);
		if ($character_log_count > $data_max_cnt) {
			$this->af->setApp('character_log_count', -1);
			return 'admin_log_cs_character_index';
		}

		if ($character_log_count == 0) {
			$this->af->setApp('character_log_count', 0);
			return 'admin_log_cs_character_index';
		}

		$character_log_data = $logdata_view_m->getCharacterLogData($search_params, $limit, $offset);
		$pager = $logdata_view_m->getPager($character_log_count, $offset, $limit);
		$character_log_data_detail = array();

		$i = 0;
		foreach ($character_log_data['data'] as $k => $v) {
			$parameter_keys = array(
				'crime_coef'      => '犯罪係数',
				'body_coef'       => '身体係数',
				'intelli_coef'    => '知能係数',
				'mental_coef'     => '心的係数',
			);
			foreach( $parameter_keys as $key => $parameter )  {
				$prev_key = $key."_prev";
				if ( $v[$key] != $v[$prev_key] ) {
					$character_log_data_detail[$i] = array();
					$character_log_data_detail[$i]['api_transaction_id'] = $v['api_transaction_id'];
					$character_log_data_detail[$i]['date_created'] = $v['date_created'];
					$character_log_data_detail[$i]['character_id'] = $v['character_id'];
					$character_log_data_detail[$i]['parameter'] = $parameter;
					$character_log_data_detail[$i]['change_coef'] = $v[$key] - $v[$prev_key];
					$character_log_data_detail[$i]['last_coef'] = $v[$key];
					$i++;
				}
			}
		}

		$character_master_data = $character_m->getMasterCharacterAssoc();

		$this->af->setApp('character_log_list', $character_log_data_detail);
		$this->af->setApp('character_log_count', $character_log_count);
		$this->af->setApp('character_master', $character_master_data);
		$this->af->setApp('role', $role);

		return 'admin_log_cs_character_index';
	}
}
