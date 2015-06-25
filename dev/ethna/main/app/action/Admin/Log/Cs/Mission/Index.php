<?php
/**
 *  Admin/Log/Cs/Mission/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_mission_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsMission extends Pp_Form_AdminLogCs
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);
	}
}

/**
 *  admin_log_cs_mission_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsMissionIndex extends Pp_Form_AdminLogCsMission
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
 *  admin_log_cs_mission_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsMissionIndex extends Pp_Action_AdminLogCsIndex
{
	const MAX_PAGE_DATA_COUNT = '100';
	const MAX_DATA_COUNT = '10000';
	const MAX_TERM_DAY = 14;

	/**
	 *  preprocess of admin_log_cs_mission_index Action.
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
			return 'admin_log_cs_mission_index';
		}

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('name_option', $this->af->get('search_name_option'));
		if ($search_flg == '1'){
			if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
				return 'admin_log_cs_mission_index';
			}
		}
		return null;

	}

	/**
	 *  admin_log_cs_mission_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$limit = self::MAX_PAGE_DATA_COUNT;
		$offset = $this->af->get('start');
		$data_max_cnt = self::MAX_DATA_COUNT;

		$logdata_view_m = $this->backend->getManager('LogdataViewMission');
		$admin_user_m = $this->backend->getManager('AdminUser');
		$mission_m = $this->backend->getManager('Mission');

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

		if ($search_flg != '1'){
			return 'admin_log_cs_mission_index';
		}

		$mission_log_count = $logdata_view_m->getMissionLogDataCount($search_params);
		if ($mission_log_count > $data_max_cnt) {
			$this->af->setApp('mission_log_count', -1);
			return 'admin_log_cs_mission_index';
		}

		if ($mission_log_count == 0) {
			$this->af->setApp('mission_log_count', 0);
			return 'admin_log_cs_mission_index';
		}

		$stage_master_data = $mission_m->getMasterStageList();
		$area_master_data = $mission_m->getMasterAreaList();
		$mission_master_data = $mission_m->getMasterMissionList();

		$mission_log_data = $logdata_view_m->getMissionLogData($search_params, $limit, $offset);
		$pager = $logdata_view_m->getPager($mission_log_count, $offset, $limit);
		foreach( $mission_log_data['data'] as $k => $v ) {
			$mission_id = $v['mission_id'];
			$area_id = $mission_master_data[$mission_id]['area_id'];
			$stage_id = $area_master_data[$area_id]['stage_id'];
			$mission_log_data['data'][$k]['mission_name'] = $mission_master_data[$mission_id]['name_ja'];
			$mission_log_data['data'][$k]['area_name'] = $area_master_data[$area_id]['name_ja'];
			$mission_log_data['data'][$k]['stage_name'] = $stage_master_data[$stage_id]['name_ja'];

			switch( $v['result_type'] ) {
			case Pp_MissionManager::RESULT_TYPE_FAIL:
				$mission_log_data['data'][$k]['result'] = "FAIL";
				break;
			case Pp_MissionManager::RESULT_TYPE_NORMAL:
				$mission_log_data['data'][$k]['result'] = "NORMAL";
				break;
			case Pp_MissionManager::RESULT_TYPE_BEST:
				$mission_log_data['data'][$k]['result'] = "BEST";
				break;
			case Pp_MissionManager::RESULT_TYPE_RETIRE:
				$mission_log_data['data'][$k]['result'] = "RETIRE";
				break;
			default:
				$mission_log_data['data'][$k]['result'] = "-";
				break;
			}
		}

		$this->af->setApp('mission_log_list', $mission_log_data['data']);
		$this->af->setApp('mission_log_count', $mission_log_count);
		$this->af->setApp('role', $role);

		return 'admin_log_cs_mission_index';
	}
}
