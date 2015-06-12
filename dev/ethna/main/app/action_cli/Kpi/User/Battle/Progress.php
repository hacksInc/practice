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
 *  kpi_user_battle_progress Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_KpiUserBattleProgress extends Pp_ActionForm
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
class Pp_Cli_Action_KpiUserBattleProgress extends Pp_CliActionClass
{

    /**
     *  preprocess of kpi_user_battle_progress Action.
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
     *  kpi_user_battle_progress action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $tran_date = $_REQUEST['argv1'];

        $kpiview_m = $this->backend->getManager('KpiViewUserBattleProgress');

        $tmp = preg_split('/-/', date('Y-m-d', strtotime($tran_date)));
        $date_from = date('Y-m-d H:i:s', mktime(0, 0, 0, $tmp[1] , intval($tmp[2]), $tmp[0]));
        $date_to = date('Y-m-d H:i:s', mktime(23, 59, 59, $tmp[1] , intval($tmp[2]), $tmp[0]));
        $search_params = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'ua' => 2,
        );

        $kpi_count = $kpiview_m->getKpiListByDateBattleTallyCount($search_params);
        if($kpi_count != 0){
            exit(2);
        }

        $res = $kpiview_m->getMasterMap();
        if($res === false){
            exit(1);
        }
        // イベント分はマスタにないので手動で追加
        $res[] = array(
            'map_id' => 0,
            'map_name' => 'イベント',
        );

        // マップ単位で集計処理を行う(負荷を考慮しての対応)
        foreach($res as $v){

            // データの集計を行う
            $res_log = $kpiview_m->getLogQuestDataForBattleProgress ($tran_date, $v);
            if ($res_log === false){
                exit(1);
            }
/*            if ($res_log['count'] == 0){
                continue;
}*/

            // 集計した結果をDBに登録
            //if ($v['map_id']==1){
            //$res_add = $kpiview_m->addKpiUserBattleProgress($res_log['data'], $v, $tran_date);
            $res_add = $kpiview_m->addKpiUserBattleProgress($res_log, $v, $tran_date);
            if ($res_add === false){
                exit(1);
            }
            //}

        }

        exit(0);
    }
}
