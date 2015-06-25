<?php
/**
 *  Admin/Log/Cs/Stress/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_stress_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsStress extends Pp_Form_AdminLogCs
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);
	}
}

/**
 *  admin_log_cs_stress_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsStressIndex extends Pp_Form_AdminLogCsStress
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
 *  admin_log_cs_stress_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsStressIndex extends Pp_Action_AdminLogCsIndex
{
	const MAX_PAGE_DATA_COUNT = '100';
	const MAX_DATA_COUNT = '10000';
	const MAX_TERM_DAY = 14;

	/**
	 *  preprocess of admin_log_cs_stress_index Action.
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
			return 'admin_log_cs_stress_index';
		}

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('name_option', $this->af->get('search_name_option'));
		if ($search_flg == '1'){
			if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
				return 'admin_log_cs_stress_index';
			}
		}
		return null;

	}

	/**
	 *  admin_log_cs_stress_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$limit = self::MAX_PAGE_DATA_COUNT;
		$offset = $this->af->get('start');
		$data_max_cnt = self::MAX_DATA_COUNT;

		$logdata_view_m = $this->backend->getManager('LogdataViewStress');
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
			return 'admin_log_cs_stress_index';
		}

		$stress_log_count = $logdata_view_m->getStressLogDataCount($search_params);
		if ($stress_log_count > $data_max_cnt) {
			$this->af->setApp('stress_log_count', -1);
			return 'admin_log_cs_stress_index';
		}

		if ($stress_log_count == 0) {
			$this->af->setApp('stress_log_count', 0);
			return 'admin_log_cs_stress_index';
		}

		$stress_log_data = $logdata_view_m->getStressLogData($search_params, $limit, $offset);
		$pager = $logdata_view_m->getPager($stress_log_count, $offset, $limit);

		foreach ($stress_log_data['data'] as $k => $v) {
			$stress_log_data['data'][$k]['special_stress_care'] = 0;
			$stress_log_data['data'][$k]['therapy_stress_care'] = 0;
			$stress_log_data['data'][$k]['regular_stress_care'] = 0;

			switch ($v['processing_type']) {
			case 'A01': // 定時ストレスケア
				$stress_log_data['data'][$k]['regular_stress_care'] = $v['ex_stress_care'];
				break;
			case 'A02': // 臨時ストレスケア
				$stress_log_data['data'][$k]['special_stress_care'] = $v['ex_stress_care'];
				break;
			case 'A05': // セラピー診断
				$stress_log_data['data'][$k]['therapy_stress_care'] = $v['ex_stress_care'];
				break;
			}
		}

		$character_master_data = $character_m->getMasterCharacterAssoc();

		$this->af->setApp('stress_log_list', $stress_log_data['data']);
		$this->af->setApp('stress_log_count', $stress_log_count);
		$this->af->setApp('character_master', $character_master_data);
		$this->af->setApp('role', $role);

		return 'admin_log_cs_stress_index';
	}
}
