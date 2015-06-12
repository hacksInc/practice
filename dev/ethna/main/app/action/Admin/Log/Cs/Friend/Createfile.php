<?php
/**
 *  Admin/Log/Cs/Friend/Createfile.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/** admin_log_cs_friend_* で共通のアクションフォーム定義 */
/*class Pp_Form_AdminLogCsFriendCreatefile extends Pp_Form_AdminLogCsFriend
{
}*/

/**
 *  admin_log_cs_friend_createfile Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsFriendCreatefile extends Pp_Form_AdminLogCsFriend
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_date_to' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_name' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_name_option',
        'search_user_id' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_friend_name' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_friend_name_option',
        'search_friend_user_id' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_processing_type_name' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
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
    function _filter_urldecode($value)
    {
        //  convert to upper case.
        return urldecode($value);
    }
}

/**
 *  admin_log_cs_friend_createfile action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsFriendCreatefile extends Pp_Action_AdminLogCsIndex
{
    const MAX_PAGE_DATA_COUNT = '100';
    const MAX_DATA_COUNT = '10000';
    const MAX_TERM_DAY = 14;

    /**
     *  preprocess of admin_log_cs_friend_createfile Action.
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
            return 'admin_log_cs_json_encrypt';
        }

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('name_option', $this->af->get('search_name_option'));
        if ($search_flg == '1'){
            if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
                $this->af->setApp('code', 400);
                $msg = "検索日入力エラー";
                $this->af->setApp('err_msg', $msg);
                return 'admin_log_cs_json_encrypt';
            }
        }
        return null;

    }

    /**
     *  admin_log_cs_friend_createfile action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $data_max_cnt = self::MAX_DATA_COUNT;

        $logdata_view_m = $this->backend->getManager('LogdataViewFriend');
        $search_params = array(
            'date_from' => $this->af->get('search_date_from'),
            'date_to' => $this->af->get('search_date_to'),
            'name' => $this->af->get('search_name'),
            'name_option' => $this->af->get('search_name_option'),
            'user_id' => $this->af->get('search_user_id'),
            'friend_name' => $this->af->get('search_friend_name'),
            'friend_name_option' => $this->af->get('search_friend_name_option'),
            'friend_user_id' => $this->af->get('search_friend_user_id'),
            'processing_type_name' => $this->af->get('search_processing_type_name'),
        );

        $search_flg = $this->af->get('search_flg');

        if ($search_flg != '1'){
            $this->af->setApp('code', 100);
            return 'admin_log_cs_json_encrypt';
        }

        $friend_log_count = $logdata_view_m->getFriendLogDataCount($search_params);
        if ($friend_log_count > $data_max_cnt) {
            $this->af->setApp('code', 400);
            $this->af->setApp('err_msg', "対象となるデータ件数が" . $data_max_cnt . "件を超えました。\r\n検索条件を変えて絞込みを行ってください。");
            return 'admin_log_cs_json_encrypt';
        }
        if ($friend_log_count == 0) {
            $this->af->setApp('code', 400);
            $this->af->setApp('err_msg', "対象となるデータが存在しません。");
            return 'admin_log_cs_json_encrypt';
        }

        $friend_log_data = $logdata_view_m->getFriendLogData($search_params);
        $res = $logdata_view_m->createCsvFileFriendLogData($friend_log_data['data']);
        if ($res === false){
            $this->af->setApp('code', 400);
            $this->af->setApp('err_msg', 'ファイルの作成に失敗しました。');
            return 'admin_log_cs_json_encrypt';
        }

        $this->af->setApp('code', 200);
        $this->af->setApp('file_name', $res);
        return 'admin_log_cs_json_encrypt';
    }
}
