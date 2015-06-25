<?php
/**
 *  Admin/Log/Cs/Point/Request/List.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_log_cs_point_request_list Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsPointRequestList extends Pp_Form_AdminLogCsPointRequest
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_type'      => array('required' => true),
        'search_date_from' => array('required' => true),
        'search_date_to'   => array('required' => true, 'custom' => 'checkDatetimePast'),
        'search_user_id',
		'pageID',
    );
}

/**
 *  admin_log_cs_point_request_list action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsPointRequestList extends Pp_Action_AdminLogCsIndex
{
    const MAX_PAGE_DATA_COUNT = 100;
    const MAX_DATA_COUNT = 10000;
    const MAX_TERM_DAY = 31;
	
	protected $validation_error_forward_name = 'admin_log_cs_point_request_index';
	
    /**
     *  preprocess of admin_log_cs_point_request_list Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
		
        // 検索日時のチェック
        if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
            return $this->validation_error_forward_name;
        }
    }

    /**
     *  admin_log_cs_point_request_list action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $limit = self::MAX_PAGE_DATA_COUNT;
        $offset = $this->af->getPageFromPageID() * $limit;
        $data_max_cnt = self::MAX_DATA_COUNT;
		
		$search_params = $this->af->getSearchParams();

        $logdata_view_m = $this->backend->getManager('LogdataViewPoint');
		
		// レプリケーション遅延検出
		if ($offset == 0) {
			$is_replica_ok = $logdata_view_m->checkLogPointRequestReplica($search_params['date_to']);
			if (!$is_replica_ok) {
				$this->af->ae->add(null, '対象となるログの保存処理が完了していません。しばらく待つか、日時の条件を変えて再度絞込みを行ってください。');
				return 'admin_log_cs_point_request_index';
			}
		}
		
		// 件数取得
		$point_log_count = $logdata_view_m->cacheCountLogPointRequest($search_params);
		if ($point_log_count == 0) {
			$this->af->ae->add(null, '対象となるログは存在しません');
			return 'admin_log_cs_point_request_index';
		} else if ($point_log_count > $data_max_cnt) {
			$this->af->ae->add(null, '対象ログ件数が' . $data_max_cnt . '件を超えます。条件を変えて再度絞込みを行ってください。');
			return 'admin_log_cs_point_request_index';
		}
		
		// データ取得
		$point_log_list = $logdata_view_m->getLogPointRequestListEx($search_params, $limit, $offset);
		
		$this->af->setApp('point_log_list', $point_log_list);
		$this->af->setApp('point_log_count', $point_log_count);
		
        return 'admin_log_cs_point_request_list';
    }
}

?>