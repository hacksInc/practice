<?php
/**
 *  Admin/Kpi/Item/Charge/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_kpi_item_charge_* で共通のアクションフォーム定義 */
class Pp_Form_AdminKpiItemCharge extends Pp_AdminActionForm
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
 *  admin_log_kpi_item_charge_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiItemChargeIndex extends Pp_Form_AdminKpiItemCharge
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from',
        'search_date_to',
        'search_ua',
        'search_flg',
        'start',
    );
}

/**
 *  admin_kpi_item_charge_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
//class Pp_Action_AdminKpiUserContinuanceIndex extends Pp_Action_AdminLogCsIndex
class Pp_Action_AdminKpiItemChargeIndex extends Pp_AdminActionClass
{

    /**
     *  preprocess of admin_kpi_item_charge_index Action.
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
            return 'admin_kpi_item_charge_index';
        }

/*        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('name_option', $this->af->get('search_name_option'));
        if ($search_flg == '1'){
            if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
                return 'admin_kpi_item_charge_index';
            }
        }*/
        return null;

    }

    /**
     *  admin_kpi_item_charge_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $kpiview_m = $this->backend->getManager('KpiViewItem');

        $date_from = $this->af->get('search_date_from');
//        $date_to = $this->af->get('search_date_to');
        $tmp = preg_split('/\//', date('Y/m/d', strtotime($date_from)));
        $date_to = date('Y-m-d H:i:s', mktime(0, 0, 0, $tmp[1] + 1, intval($tmp[2]), $tmp[0]));
        // 動作確認用に3日間のみで表示する
        //$date_to = date('Y-m-d H:i:s', mktime(0, 0, 0, $tmp[1], intval($tmp[2]) - 3, $tmp[0]));

        $search_params = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'ua' => $this->af->get('search_ua'),
        );

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('search_flg', $search_flg);
        if ($search_flg != '1'){
            return 'admin_kpi_item_charge_index';
        }

        $kpi_item_charge_count = $kpiview_m->getKpiItemChargeListByDateItemTallyCount($search_params);
       if ($kpi_item_charge_count == 0) {
            $this->af->setApp('kpi_item_charge_count', $kpi_item_charge_count);
            return 'admin_kpi_item_charge_index';
       }

        $kpi_item_charge_data = $kpiview_m->getKpiItemChargeListByDateItemTallyAll($search_params);
        $kpi_item_charge_sum_data = $kpiview_m->getKpiItemChargeListByDateItemTallyAllSum($search_params);


        $this->af->setApp('kpi_item_charge_list', $kpi_item_charge_data['data']);
        $this->af->setApp('kpi_item_charge_sum', $kpi_item_charge_sum_data);
        $this->af->setApp('kpi_item_charge_count', $kpi_item_charge_count);
        $this->af->setApp('kpi_date_from', date('Y年m月d日', strtotime($date_from)));
        $this->af->setApp('kpi_date_to', date('Y年m月d日', strtotime($date_to)));

        return 'admin_kpi_item_charge_index';
    }
}
