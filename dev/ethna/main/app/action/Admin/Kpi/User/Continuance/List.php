<?php
/**
 *  Admin/Kpi/User/Continuance/List.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_log_kpi_user_continuance_list Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiUserContinuanceList extends Pp_Form_AdminKipUserContinuance
//class Pp_Form_AdminKpiUserContinuanceList extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from',
        'search_date_to',
        'search_os_type',
        'search_flg',
        'start',
    );
}

/**
 *  admin_kpi_user_continuance_list action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
//class Pp_Action_AdminKpiUserContinuanceList extends Pp_Action_AdminLogCsIndex
class Pp_Action_AdminKpiUserContinuanceList extends Pp_AdminActionClass
{
    const MAX_PAGE_DATA_COUNT = '100';
    const MAX_DATA_COUNT = '10000';
    const MAX_TERM_DAY = 14;

    /**
     *  preprocess of admin_kpi_user_continuance_list Action.
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
            return 'admin_kpi_user_continuance_list';
        }

/*        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('name_option', $this->af->get('search_name_option'));
        if ($search_flg == '1'){
            if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
                return 'admin_kpi_user_continuance_index';
            }
        }*/
        return null;

    }

    /**
     *  admin_kpi_user_continuance_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $kpiview_m = $this->backend->getManager('KpiViewUser');
        $search_params = array(
            'date_from' => $this->af->get('search_date_from'),
            'date_to' => $this->af->get('search_date_to'),
            'os_type' => $this->af->get('search_os_type'),
        );
        if ($search_flg != '1'){
            return 'admin_kpi_user_continuance_list';
        }
        $kpi_continuance_count = $kpiview_m->getKpiUserContinuanceListByDateInstallCount($search_params);
        if ($kpi_countinuance_count == 0) {
            return 'admin_kpi_user_continuance_list';
        }
        $kpi_continuance_data = $kpiview_m->getKpiUserContinuanceListByDateInstall($search_params);
        $this->af->setApp('kpi_continuance_list', $kpi_continuance_data['data']);
        $this->af->setApp('kpi_continuance_count', $kpi_continuance_count);

        return 'admin_kpi_user_continuance_index';
    }
}
