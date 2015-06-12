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

/**
 *  kpi_count Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_KpiItemGacha extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  kpi_item_charge action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_KpiItemGacha extends Pp_CliActionClass
{

    /**
     *  preprocess of kpi_item_charge Action.
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
        $tran_date = $_REQUEST['argv1'];

        $kpiview_m = $this->backend->getManager('KpiViewGacha');

        $res = $kpiview_m->getKpiGachaChargeDataByDateGachaTallyCount($tran_date);
        $this->backend->logger->log(LOG_INFO, '******************************** argv' . print_r($res, true));
        if($res != 0){
            exit(2);
        }

        $param = array(
            'item_id' => '9000',
            'processing_type' => 'G11',
            'tran_date' => $tran_date,
        );
        // log_item_dataより集計対象のログ情報を取得
        $res = $kpiview_m->getLogItemDataForKpiGachaCharge($param);
        if($res === false){
            exit(1);
        }

        // データの集計を行いDBに登録
        $res = $kpiview_m->editLogItemDataforKpiGachaChargeRate ($res['data'], $tran_date);
        if ($res === false){
            exit(1);
        }

        $this->backend->logger->log(LOG_INFO, '******************************** end');
        exit(0);
    }
}
