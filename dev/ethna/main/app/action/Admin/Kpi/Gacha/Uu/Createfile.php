<?php
/**
 *  Admin/Kpi/Gacha/Uu/Createfile.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_log_kpi_gacha_charge_createfile Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiGachaUuCreatefile extends Pp_Form_AdminKpiGachaUu
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
        'search_ua' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_flg',
        'start',
    );
}

/**
 *  admin_kpi_gacha_charge_createfile action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiGachaUuCreatefile extends Pp_AdminActionClass
{

    /**
     *  preprocess of admin_kpi_gacha_charge_index Action.
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

        return null;

    }

    /**
     *  admin_kpi_gacha_charge_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $kpiview_m = $this->backend->getManager('KpiViewGacha');

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
            $this->af->setApp('code', 100);
            return 'admin_json_encrypt';
        }

        $kpi_gacha_charge_count = $kpiview_m->getKpiGachaUuListByDateGachaTallyCount($search_params);
       if ($kpi_gacha_charge_count == 0) {
            $this->af->setApp('code', 400);
            $this->af->setApp('err_msg', "対象となるデータが存在しません。");
            return 'admin_json_encrypt';
       }

        $kpi_gacha_charge_data = $kpiview_m->getKpiGachaUuListByDateGachaTallyAll($search_params);
        $kpi_gacha_charge_sum_data = $kpiview_m->getKpiGachaUuListByDateGachaTallyAllSum($search_params);
        $view_list = $kpiview_m->editDailyViewKpiGachaUuRate($kpi_gacha_charge_data['data'], $kpi_gacha_charge_sum_data['data']);

        $res = $kpiview_m->createCsvFileKpiGachaUuRate($view_list['view_list'], $kpi_gacha_charge_sum_data, $view_list['name_list']);
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
