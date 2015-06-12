<?php
/**
 *  Kpi/Device/Info/Count.php
 *
 *  端末情報KPI集計
 *  ユニットの関係で、Ethnaアクション"kpi_count"に処理を統合できない為、
 *  別途このEthnaアクションで集計する。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  kpi_device_info_count Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_KpiDeviceInfoCount extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  kpi_device_info_count action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_KpiDeviceInfoCount extends Pp_CliActionClass
{
    /**
     *  preprocess of kpi_device_info_count Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  kpi_device_info_count action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$admin_m  = $this->backend->getManager('Admin');
		$client_m = $this->backend->getManager('AdminClient');
		$user_m   = $this->backend->getManager('AdminUser');
		
		// 引数取得
		if ( $GLOBALS['argc'] > 2 ) {
			$period = $GLOBALS['argv'][2];
		} else {
			$date = $admin_m->getPreviousDurationDate(Pp_AdminManager::DURATION_TYPE_MONTHLY, $_SERVER['REQUEST_TIME']);
			$period = $client_m->getUserDeviceInfoPeriod(strtotime($date));
		}
		
		echo "●kpi_device_info_count開始 [" . date('Y-m-d H:i:s') . "]\n";
		echo "period:" . $period . "\n";
		echo "unit:" . $this->config->get('unit_id') . "\n";
		
		// 集計する
		foreach (array(
			Pp_UserManager::OS_IPHONE,
			Pp_UserManager::OS_ANDROID,
		) as $ua) {
			$ret = $client_m->makeKpiDeviceInfo($period, $ua);
			if ($ret !== true) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':period=[' . $period . '] ua=[' . $ua . ']');
			}
		}
		
		if ($ret === true) {
			echo "正常終了";
		} else {
			echo "異常終了";
		}
		
		echo " [" . date('Y-m-d H:i:s') . "]\n\n";
		
        return null;
    }
}

?>