<?php
/**
 *  Admin/Log/Cs/User/Tutorial/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';
require_once dirname(__FILE__) . '/../../Index.php';

/**
 *  admin_log_cs_user_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsUserTutorialIndex extends Pp_Form_AdminLogCsUser
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from',
        'search_date_to',
        'search_user_id',
        'search_name',
        'search_name_option',
        'search_user_id',
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
 *  admin_log_cs_user_tutorial_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsUserTutorialIndex extends Pp_Action_AdminLogCsIndex
{
    const MAX_PAGE_DATA_COUNT = '100';
    const MAX_DATA_COUNT = '10000';
    const MAX_TERM_DAY = 14;

    /**
     *  preprocess of admin_log_cs_user_tutorial_index Action.
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
            return 'admin_log_cs_user_tutorial_index';
        }

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('name_option', $this->af->get('search_name_option'));
        if ($search_flg == '1'){
            // 検索日時のチェック
            if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
                return 'admin_log_cs_user_tutorial_index';
            }
        }
        return null;

    }

    /**
     *  admin_log_cs_user_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $limit = self::MAX_PAGE_DATA_COUNT;
        $offset = $this->af->get('start');
        $data_max_cnt = self::MAX_DATA_COUNT;

        $logdata_view_m = $this->backend->getManager('LogdataViewUser');
        $search_params = array(
            'date_from' => $this->af->get('search_date_from'),
            'date_to' => $this->af->get('search_date_to'),
            'user_id' => $this->af->get('search_user_id'),
            'name' => $this->af->get('search_name'),
            'name_option' => $this->af->get('search_name_option'),
            'user_id' => $this->af->get('search_user_id'),
        );

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('search_flg', $search_flg);
        $this->af->setApp('name_option', $this->af->get('search_name_option'));

        if ($search_flg == '1'){
            $user_log_count = $logdata_view_m->getUserTutorialLogDataCount($search_params);
            if ($user_log_count == 0) {
                $this->af->setApp('user_tutorial_log_count', 0);
                return 'admin_log_cs_user_tutorial_index';
            }
            if ($user_log_count > $data_max_cnt) {
                $this->af->setApp('user_tutorial_log_count', -1);
                return 'admin_log_cs_user_tutorial_index';
            }
            $user_log_data = $logdata_view_m->getUserTutorialLogData($search_params, $limit, $offset);
            $pager = $logdata_view_m->getPager($user_log_count, $offset, $limit);
            $this->af->setApp('user_tutorial_log_list', $user_log_data['data']);
            $this->af->setApp('user_tutorial_log_count', $user_log_count);
            $this->af->setApp('user_tutorial_log_count_2', $user_log_data['count']);
        }
        $this->af->setApp('create_file_path', '/admin/log/cs/user/tutorial');
        return 'admin_log_cs_user_tutorial_index';
    }
}
