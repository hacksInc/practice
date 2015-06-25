<?php
/**
 *  Admin/Log/Cs/Achievement/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_achievement_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsAchievement extends Pp_Form_AdminLogCs
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
 *  admin_log_cs_achievement_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsAchievementIndex extends Pp_Form_AdminLogCsAchievement
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
 *  admin_log_cs_achievement_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsAchievementIndex extends Pp_Action_AdminLogCsIndex
{
	const MAX_PAGE_DATA_COUNT = '100';
	const MAX_DATA_COUNT = '10000';
	const MAX_TERM_DAY = 14;

	/**
	 *  preprocess of admin_log_cs_achievement_index Action.
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
			return 'admin_log_cs_achievement_index';
		}

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('name_option', $this->af->get('search_name_option'));
		if ($search_flg == '1'){
			// 検索日時のチェック
			if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
				return 'admin_log_cs_achievement_index';
			}
		}
		return null;

	}

	/**
	 *  admin_log_cs_achievement_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$limit = self::MAX_PAGE_DATA_COUNT;
		$offset = $this->af->get('start');
		$data_max_cnt = self::MAX_DATA_COUNT;

		$logdata_view_m = $this->backend->getManager('LogdataViewAchievement');
		$logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$achievement_m = $this->backend->getManager('Achievement');
		$search_params = array(
			'date_from' => $this->af->get('search_date_from'),
			'date_to' => $this->af->get('search_date_to'),
			'name' => $this->af->get('search_name'),
			'name_option' => $this->af->get('search_name_option'),
			'pp_id' => $this->af->get('search_pp_id'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		$this->af->setApp('name_option', $this->af->get('search_name_option'));

		if ($search_flg == '1'){
			$achievement_log_count = $logdata_view_m->getAchievementLogDataCount($search_params);
			if ($achievement_log_count > $data_max_cnt) {
				$this->af->setApp('achievement_log_count', -1);
				return 'admin_log_cs_achievement_index';
			}
			$achievement_log_data = $logdata_view_m->getAchievementLogData($search_params, $limit, $offset);
			$pager = $logdata_view_m->getPager($achievement_log_count, $offset, $limit);

			$achievement_master_data = $achievement_m->getMasterAchievementConditionListAssoc();

			$this->af->setApp('achievement_log_list', $achievement_log_data['data']);
			$this->af->setApp('achievement_log_count', $achievement_log_count);
			$this->af->setApp('achievement_master', $achievement_master_data);
		}
		$this->af->setApp('create_file_path', 'admin/log/cs/achievement');
		return 'admin_log_cs_achievement_index';
	}
}
