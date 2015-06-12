<?php
/**
 *  KPI集計を行う
 *
 *  1時間ごとの集計、および所定の時刻・日付の場合は1日ごとや1ヶ月ごとの集計を行う
 *  （並列で実行されてしまうのを防ぐ為に、日次・月次の専用cronは設けない）
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';
define('CSV_FILE_APPLE', BASE . '/tmp/kpi_csv/tmp_Apple-jgm_retention.csv');
define('CSV_FILE_ANDROID', BASE . '/tmp/kpi_csv/tmp_Google-jgm_retention.csv');

/**
 *  kpi_count Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_KpiRetention extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  kpi_count action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_KpiRetention extends Pp_CliActionClass
{
    //const CSV_FILE_APPLE = '/var/devjugmon/review/tmp/kpi_csv/tmp_Apple-jgm_retention.csv';
    //const CSV_FILE_ANDROID = '/var/devjugmon/review/tmp/kpi_csv/tmp_Google-jgm_retention.csv';

    /**
     *  preprocess of kpi_count Action.
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
     *  kpi_count action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $this->backend->logger->log(LOG_INFO, '******************************** argv' . print_r($argv, true));
        $this->backend->logger->log(LOG_INFO, '******************************** argv' . print_r($_REQUEST, true));
        $tran_date = $_REQUEST['argv1'];
        $os_type = $_REQUEST['argv2'];

        $kpiview_m = $this->backend->getManager('KpiViewUser');

        // iOS
        $res = $kpiview_m->getKpiUserContinuanceListByDateLoginTallyCount($tran_date);
        $this->backend->logger->log(LOG_INFO, '******************************** argv' . print_r($res, true));
        if($res != 0){
            exit(2);
        }

        //if(!$fp=@fopen(self::CSV_FILE_APPLE, 'r')){
        if(!$fp=@fopen(CSV_FILE_APPLE, 'r')){
            exit(1);
        }
        while(($data = fgetcsv($fp, 256, "\t"))!==false){
            $this->backend->logger->log(LOG_INFO, '******************************** data' . print_r($data, true));
            $res = $kpiview_m->addRetentionData($data, $tran_date, 1);
            $apple_retention_data[$data[0]] = $data;
        }
        fclose($fp);

        // Android
        //if(!$fp_and=@fopen(self::CSV_FILE_ANDROID, 'r')){
        if(!$fp_and=@fopen(CSV_FILE_ANDROID, 'r')){
            exit(1);
        }
        while(($data_and = fgetcsv($fp_and, 256, "\t"))!==false){
            $this->backend->logger->log(LOG_INFO, '******************************** data' . print_r($data_and, true));
            $res = $kpiview_m->addRetentionData($data_and, $tran_date, 2);
            $android_retention_data[$data_and[0]] = $data_and;
        }
        fclose($fp_and);

        if(isset($apple_retention_data) && isset($android_retention_data)){
            // all
            foreach($apple_retention_data as $k => $v){
                $android_data = $android_retention_data[$k];
                $apple_data = $apple_retention_data[$k];
                $cnt_install = $android_data[1] + $apple_data[1];
                $cnt_login = $android_data[2] + $apple_data[2];
                $continuance_rate = 0;
                if ($cnt_install != 0 && $cnt_login != 0) {
                    $continuance_rate = round((($cnt_login / $cnt_install) * 100), 2);
                }
                $data_total = array(
                    $k,
                    $cnt_install,
                    $cnt_login,
                    $continuance_rate,
                );
                $res = $kpiview_m->addRetentionData($data_total, $tran_date, 0);

            }
        } else if(isset($apple_retention_data) && !isset($android_retention_data)) {
            // appleの中間ファイルのみが存在する場合
            foreach($apple_retention_data as $k => $v){
                $res = $kpiview_m->addRetentionData($v, $tran_date, 0);
            }
        } else if(!isset($apple_retention_data) && isset($android_retention_data)) {
            // androidの中間ファイルのみが存在する場合 
            foreach($android_retention_data as $k => $v){
                $res = $kpiview_m->addRetentionData($v, $tran_date, 0);
            }
        }


        $this->backend->logger->log(LOG_INFO, '******************************** end');
        exit(0);
    }
}
