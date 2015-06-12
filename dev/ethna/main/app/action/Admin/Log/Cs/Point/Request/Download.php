<?php
/**
 *  Admin/Log/Cs/Point/Request/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_log_cs_point_request_download Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsPointRequestDownload extends Pp_Form_AdminLogCsPointRequest
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
    );
}

/**
 *  admin_log_cs_point_request_download action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsPointRequestDownload extends Pp_Action_AdminLogCsIndex
{
    const MAX_DATA_COUNT = 10000;
    const MAX_TERM_DAY = 31;
	
    /**
     *  preprocess of admin_log_cs_point_request_download Action.
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
            return 'admin_error_500';
		}
    }

    /**
     *  admin_log_cs_point_request_download action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $data_max_cnt = self::MAX_DATA_COUNT;
		
        $logdata_view_m = $this->backend->getManager('LogdataViewPoint');
		
		$search_params = $this->af->getSearchParams();

        $point_log_count = $logdata_view_m->countLogPointRequest($search_params);
        if ($point_log_count > $data_max_cnt) {
            return 'admin_error_500';
        }
		
		// CSVファイルを生成
        $file_name = $logdata_view_m->createCsvFileLogPointRequest($search_params);
		if (!$file_name || (strlen($file_name) == 0)) {
            return 'admin_error_500';
		}
		
		// ダウンロードユニーク値を生成
		$download_uniq = uniqid();
		
		$this->af->setApp('file_name', $file_name);
		$this->af->setApp('download_uniq', $download_uniq);
		
        return 'admin_log_cs_download';
    }
}

?>