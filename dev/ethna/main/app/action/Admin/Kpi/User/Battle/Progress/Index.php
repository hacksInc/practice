<?php
/**
 *  Admin/Kpi/User/Battle/Progress/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_kpi_user_continuance_* で共通のアクションフォーム定義 */
class Pp_Form_AdminKpiUserBattleProgress extends Pp_AdminActionForm
{
    /**
     * コンストラクタ
     */
    function __construct(&$backend) {
        $form_template = array(
            'search_date_from' => array(
                // Form definition
                'type'        => VAR_TYPE_DATETIME,     // Input type
                'form_type'   => FORM_TYPE_TEXT, // Form type
                'name'        => '検索日(開始日)', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
                //'min'         => 10000,               // Minimum value
                //'max'         => 20000,               // Maximum value
            ),
            'search_date_to' => array(
                // Form definition
                'type'        => VAR_TYPE_DATETIME,     // Input type
                'form_type'   => FORM_TYPE_TEXT, // Form type
                'name'        => '検索日(終了日)', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
                //'min'         => 10000,               // Minimum value
                //'max'         => 20000,               // Maximum value
            ),
            'search_map_id' => array(
                // Form definition
                'type'        => VAR_TYPE_STRING,     // Input type
                'form_type'   => FORM_TYPE_SELECT, // Form type
                'option'      => array(
                    '1' => '旅立ちの島',
                    '2' => '野獣の島',
                    '3' => '妖精の浮島',
                    '4' => '戦場島',
                    '5' => '人造島',
                    '6' => '原始の島',
                    '7' => '大精霊の島',
                    '8' => '監獄島',
                    '9' => '巨人の島',
                    '10' => '竜の島',
                    '0' => 'イベント',
                ),
                'name'        => 'マップ', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
            ),
            'search_quest_id' => array(
                // Form definition
                'type'        => VAR_TYPE_INTEGER, // Input type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'クエストID',     // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
                //'min'         => 10000,               // Minimum value
                //'max'         => 20000,               // Maximum value
            ),
            'search_ua' => array(
                // Form definition
                'type'        => VAR_TYPE_STRING,     // Input type
                'form_type'   => FORM_TYPE_SELECT,   // Form type
                'option'      => array(
                    '0' => '全て',
                    '1' => 'iOS のみ',
                    '2' => 'Android のみ',
                ),
                'name'        => '集計項目', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
                //'min'         => 10000,               // Minimum value
                //'max'         => 20000,               // Maximum value
            ),
        );

        foreach ($form_template as $key => $value) {
            $this->form_template[$key] = $value;
        }
        parent::__construct($backend);
    }
}

/**
 *  admin_log_kpi_user_battle_progress_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiUserBattleProgressIndex extends Pp_Form_AdminKpiUserBattleProgress
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from',
        'search_map_id',
        'search_quest_id',
        'search_ua',
        'search_flg',
        'start',
    );

    function setFormDef_PreHelper()
    {
        $session = $this->backend->getSession();
        $db = $this->backend->getDB();
        $kpiview_m = $this->backend->getManager('kpiView');
        //$default_option = mb_convert_encoding('全て', 'Shift-JIS', 'UTF-8');
        $sql = "select map_id, name_ja as map_name from m_map";
        $param = array();
//        $res = $db->GetAll($sql, $param);
/*        if($res === false){
            $map_data = array();
        }

        foreach($res as $v){
            $map_data[$v['map_id']] = $v['name_ja'];
        }

     

        // フォーム定義の設定(アプリID)
//        $map_data = $kpiview_m->getMaterMapForSelectList();
        $name = 'search_map_id';
        $def = $this->form_template[$name];
        $def['option'] = $map_data;
        $this->setDef($name, $def);*/

    }
}

/**
 *  admin_kpi_user_battle_progress_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
//class Pp_Action_AdminKpiUserContinuanceIndex extends Pp_Action_AdminLogCsIndex
class Pp_Action_AdminKpiUserBattleProgressIndex extends Pp_AdminActionClass
{

    /**
     *  preprocess of admin_kpi_user_battle_progress_index Action.
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
            return 'admin_kpi_user_battle_progress_index';
        }

/*        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('name_option', $this->af->get('search_name_option'));
        if ($search_flg == '1'){
            if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
                return 'admin_kpi_user_battle_progress_index';
            }
        }*/
        return null;

    }

    /**
     *  admin_kpi_user_battle_progress_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $kpiview_m = $this->backend->getManager('KpiViewUserBattleProgress');

//        $date_from = $this->af->get('search_date_from');
        $tmp = preg_split('/-/', date('Y-m-d', strtotime($this->af->get('search_date_from'))));
        $date_from = date('Y-m-d H:i:s', mktime(0, 0, 0, $tmp[1] , intval($tmp[2]), $tmp[0]));
        $date_to = date('Y-m-d H:i:s', mktime(23, 59, 59, $tmp[1] , intval($tmp[2]), $tmp[0]));

        $search_params = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'map_id' => $this->af->get('search_map_id'),
            'quest_id' => $this->af->get('search_quest_id'),
            'ua' => $this->af->get('search_ua'),
        );

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('search_flg', $search_flg);
        if ($search_flg != '1'){
            return 'admin_kpi_user_battle_progress_index';
        }

        $kpi_battle_progress_count = $kpiview_m->getKpiListByDateBattleTallyCount($search_params);
        if ($kpi_battle_progress_count == 0) {
            return 'admin_kpi_user_battle_progress_index';
        }
        $kpi_battle_progress_data = $kpiview_m->getKpiListByDateBattleTally($search_params);

        $this->af->setApp('kpi_battle_progress_list', $kpi_battle_progress_data['data']);
        $this->af->setApp('kpi_battle_progress_count', $kpi_battle_progress_count);
        $this->af->setApp('kpi_date_from', date('Y年m月d日', strtotime($date_to)));
        $this->af->setApp('kpi_date_to', date('Y年m月d日', strtotime($date_from)));

$this->backend->logger->log(LOG_INFO, '************************ user_log_data result==[' . print_r($kpi_battle_progress_data, true) . ']');
        return 'admin_kpi_user_battle_progress_index';
    }
}
