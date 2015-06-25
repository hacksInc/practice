<?php
/**
 *  Admin/Log/Cs/Monster/Createfile.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_announce_message_helpbar_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsMonster extends Pp_Form_AdminLogCs
{
    /**
     * コンストラクタ
     */
    function __construct(&$backend) {
        $form_template = array(
            'search_monster_id' => array(
                // Form definition
                'type'        => VAR_TYPE_INT,     // Input type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'モンスターID', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
        );

        foreach ($form_template as $key => $value) {
            $this->form_template[$key] = $value;
        }
        parent::__construct($backend);
    }
}

/**
 *  admin_log_cs_monster_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsMonsterCreatefile extends Pp_Form_AdminLogCsMonster
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
        'search_monster_id' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_name' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_name_option',
        'search_user_id' => array(
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
 *  admin_log_cs_monster_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsMonsterCreatefile extends Pp_AdminActionClass
{
    const MAX_PAGE_DATA_COUNT = '100';
    const MAX_DATA_COUNT = '10000';

    /**
     *  preprocess of admin_log_cs_monster_index Action.
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
            $this->af->setApp('code', 400);
            $msg = "検索条件入力エラー";
            $this->af->setApp('err_msg', $msg);
            return 'admin_log_cs_json_encrypt';
        }

        $search_flg = $this->af->get('search_flg');
        if ($search_flg == '1'){

            $date_from = $this->af->get('search_date_from');
            $date_to = $this->af->get('search_date_to');
            if (empty($date_from) && empty($date_to)){
                $this->af->setApp('code', 400);
                $msg = "検索日が入力されていません";
                $this->af->setApp('err_msg', $msg);
                return 'admin_log_cs_json_encrypt';
            }

            if (!empty($date_from) && empty($date_to)){
                // 足りていないほうに日付を入力する
                $date_to = date('Y/m/d H:i:s', strtotime($date_from)+(60*60*24*14));
                $this->af->set('search_date_to', $date_to);
                return null;
            }

            if (empty($date_from) && !empty($date_to)){
                // 足りていないほうに日付を入力する
                $date_from = date('Y/m/d H:i:s', strtotime($date_to)-(60*60*24*14));
                $this->af->set('search_date_from', $date_from);
                return null;
            }

            if (Pp_Util::checkDateRange($date_from, $date_to, 14) === false) {
                $this->af->setApp('code', 400);
                $msg = "期間指定は14日以内で指定をしてください";
                $this->af->setApp('err_msg', $msg);
                return 'admin_log_cs_json_encrypt';
            }

            if (Pp_Util::checkDateReversal($date_from, $date_to) === false) {
                $this->af->setApp('code', 400);
                $msg = "開始日と終了日が逆転しています";
                $this->af->setApp('err_msg', $msg);
                return 'admin_log_cs_json_encrypt';
            }

        }
        return null;

    }

    /**
     *  admin_log_cs_monster_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $data_max_cnt = self::MAX_DATA_COUNT;

        $logdata_view_m = $this->backend->getManager('LogdataViewMonster');
        $search_params = array(
            'date_from' => $this->af->get('search_date_from'),
            'date_to' => $this->af->get('search_date_to'),
            'monster_id' => $this->af->get('search_monster_id'),
            'name' => $this->af->get('search_name'),
            'name_option' => $this->af->get('search_name_option'),
            'user_id' => $this->af->get('search_user_id'),
        );

        $search_flg = $this->af->get('search_flg');
        if ($search_flg != '1'){
            $this->af->setApp('code', 100);
            return 'admin_log_cs_json_encrypt';
        }

        $monster_log_count = $logdata_view_m->getMonsterLogDataCount($search_params);
        if ($monster_log_count > $data_max_cnt) {
            $this->af->setApp('code', 400);
            $this->af->setApp('err_msg', "対象となるデータ件数が" . $data_max_cnt . "件を超えました。\r\n検索条件を変えて絞込みを行ってください。");
            return 'admin_log_cs_json_encrypt';
        }
        if ($monster_log_count == 0) {
            $this->af->setApp('code', 400);
            $this->af->setApp('err_msg', "対象となるデータが存在しません。");
            return 'admin_log_cs_json_encrypt';
        }

        $monster_log_data = $logdata_view_m->getMonsterLogData($search_params);
        $res = $logdata_view_m->createCsvFileMonsterLogData($monster_log_data['data']);
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
