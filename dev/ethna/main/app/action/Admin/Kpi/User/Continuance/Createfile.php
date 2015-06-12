<?php
/**
 *  Admin/Kpi/User/Continuance/Createfile.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_kpi_user_continuance_createfile Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
// class Pp_Form_AdminKpiUserContinuanceCreatefile extends Pp_Form_AdminLogCsItem
class Pp_Form_AdminKpiUserContinuanceCreatefile extends Pp_Form_AdminKpiUserContinuance
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
/*        'search_date_to' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_item_id' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_name' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_name_option',
        'search_user_id' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),*/
        'search_ua' => array(
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
 *  admin_kpi_user_continuance_createfile action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiUserContinuanceCreatefile extends Pp_AdminActionClass
{

    /**
     *  preprocess of admin_log_cs_item_createfile Action.
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
            return 'admin_json_encrypt';
        }

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('name_option', $this->af->get('search_name_option'));
        /*if ($search_flg == '1'){

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

        }*/
        return null;

    }

    /**
     *  admin_kpi_user_continuance_createfile action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $kpiview_m = $this->backend->getManager('KpiViewUser');

        $date_from = $this->af->get('search_date_from');
//        $tmp = preg_split('/\//', date('Y/m/d', strtotime($date_from)));
//        $date_to = date('Y-m-d H:i:s', mktime(0, 0, 0, $tmp[1] - 1, intval($tmp[2]), $tmp[0]));
        $date_to = date('Y-m-d H:i:s', strtotime($date_from) - 86400 * (Pp_KpiViewUserManager::CONTINUANCE_LIST_DAYS + 1));
//        $count_from_to_day = (strtotime($date_from) - mktime(0, 0, 0, $tmp[1] - 1, intval($tmp[2]), $tmp[0])) / 86400;
        $count_from_to_day = (strtotime($date_from) - strtotime($date_to)) / 86400;
        $search_params = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'ua' => $this->af->get('search_ua'),
        );

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('search_flg', $search_flg);
        if ($search_flg != '1'){
            $this->af->setApp('code', 100);
            return 'admin_json_encrypt';
        }

        $kpi_continuance_count = $kpiview_m->getKpiUserContinuanceListByDateInstallCount($search_params);
        if ($kpi_continuance_count == 0) {
            $this->af->setApp('code', 400);
            $this->af->setApp('err_msg', "対象となるデータが存在しません。");
            return 'admin_json_encrypt';
        }

        $kpi_continuance_data = $kpiview_m->getKpiUserContinuanceListByDateInstall($search_params);
        $view_list = $kpiview_m->editKpiUserContinuanceList($kpi_continuance_data['data']);

        $res = $kpiview_m->createCsvFileKpiUserContinuanceRate($view_list, $count_from_to_day);
        if ($res === false){
            $this->af->setApp('code', 400);
            $this->af->setApp('err_msg', 'ファイルの作成に失敗しました。');
            return 'admin_json_encrypt';
        }

        $this->af->setApp('code', 200);
        $this->af->setApp('file_name', $res);
        return 'admin_json_encrypt';
    }
}
