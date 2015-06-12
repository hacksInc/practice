<?php
/**
 *  Pp_KpiViewUserManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_KpiViewUserBattleProgressManager extends Pp_KpiViewManager
{
    /**
     * 指定期間のKPI バトル進捗の件数を取得する
     *
     * @params datetime $date_from
     * @params datetime $date_to
     * @return mixed
     */
    public function getKpiListByDateBattleTallyCount ($search_param)
    {
        $condition = $this->_getConditionKpiListByDateBattleTally($search_param);

        return $this->getKpiUserBattleProgressCount($condition['condition'], $condition['param']);
    }

    /**
     * 指定期間のKPI バトル進捗を取得する
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiListByDateBattleTally ($search_param)
    {
        $condition = $this->_getConditionKpiListByDateBattleTally($search_param);
        $sort = array(
            'date_battle_tally' => 'ASC',
        );

        $res = $this->getKpiUserBattleProgress($condition['condition'], $condition['param'], $sort);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;

    }

    /**
     * 指定期間のKPI バトル進捗を指定する(条件編集)
     *
     * @param array $search_param
     * @return array
     */
    private function _getConditionKpiListByDateBattleTally ($search_param)
    {
        // 必須項目
        $where = " where (date_battle_tally >= ? and date_battle_tally <= ?)"
               . " and ua = ?";
        $param = array(
            $search_param['date_from'],
            $search_param['date_to'],
            $search_param['ua']
        );

        // map_id指定
        if ($search_param['map_id']) {
            $where = $where . " and map_id = ?";
            $param[] = $search_param['map_id'];
        }

        // quest_id指定
        if ($search_param['quest_id']) {
            $where = $where . " and quest_id = ?";
            $param[] = $search_param['quest_id'];
        }

        return array('condition' => $where, 'param' => $param);
    }

    /**
     * KPI バトル進捗を取得する
     *
     * @param string $where
     * @param array $param
     * @param array $sort
     * @param integer $limit
     * @param integer $offset
     * @return mixed
     */
    public function getKpiUserBattleProgress ($where, $param, $sort=null, $limit=null, $offset=null)
    {
        $log_db = $this->backend->getDB('logex_r');
        $order = $this->_createSqlPhraseOrderBy($sort);
        $sql = "select * from kpi_user_battle_progress" . $where . $order;

        return $log_db->GetAll($sql, $param);
    }

    /**
     * KPI バトル進捗の件数を取得する
     *
     * @param string $where
     * @param array $param
     * @return mixed
     */
    public function getKpiUserBattleProgressCount ($where, $param)
    {
        $log_db = $this->backend->getDB('logex_r');
        $sql = "select count(*) from kpi_user_battle_progress" . $where;

        return $log_db->GetOne($sql, $param);
    }

    /**
     * 指定日のKPI バトル進捗を取得する
     *
     * @params datetime $tran_date
     * @return mixed
     */
    public function getKpiDataByDateBattleTally ($tran_date)
    {
        $condition = $this->_getConditionKpiDataByDateBattleTally ($tran_date);

        $res = $this->getKpiUserBattleProgress ($condition['condition'], $condition['param']);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;
    }

    /**
     * 指定日のKPI バトル進捗の件数を取得する
     *
     * @params datetime $tran_date
     * @return mixed
     */
    public function getKpiDataByDateBattleTallyCount ($tran_date)
    {
        $condition = $this->_getConditionKpiDataByDateBattleTally ($tran_date);

        $res = $this->getKpiUserContinuanceCount ($condition['condition'], $condition['param']);

        return $res;
    }

    /**
     * 指定日のKPI 継続率を取得する(条件編集)
     *
     * @params datetime $tran_date
     * @return mixed
     */
    private function _getConditionKpiDataByDateBattleTally ($date_battle_tally)
    {
        $where = " where date_battle_tally = ?";
        $param = array($date_battle_tally);

        return array('condition' => $where, 'param' => $param);
    }

    /**
     * 指定日付のバトル進捗を集計する
     *
     * @param datetime $tran_date
     * @param array $map_data
     * @return boolean
     */
    public function getLogQuestDataForBattleProgressGameover ($tran_date, $map_data)
    {
        $log_db = $this->backend->getDB('logex');

        $tmp = explode('-', date('Y-m-d', strtotime($tran_date)));

        $date_start = date('Y-m-d H:i:s', mktime(0, 0, 0, $tmp[1], $tmp[2], $tmp[0]));
        $date_end = date('Y-m-d H:i:s', mktime(23, 59, 59, $tmp[1], $tmp[2], $tmp[0]));

        $where_st = " where"
                  . " qd.date_log >= ?"
                  . " and qd.date_log <= ?"
                  . " and qd.quest_st = ?"
                  . " and qd.map_id = ?";
        $group_st = " group by qd.ua, qd.map_id, qd.quest_id, qd.area_id, qd.lose_battle_no, qd.quest_st";

        $sql = "select"
                 . " qd.ua as ua,"
                 . " qd.map_id as map_id,"
                 . " qd.quest_id as quest_id,"
                 . " qd.area_id as area_id,"
                 . " qd.quest_st as quest_st,"
                 . " qd.lose_battle_no,"
                 . " count(*) as count"
                 . " from log_quest_data qd"
                 . $where_st
                 . $group_st;

        $param = array(
            $date_start, //
            $date_end, //
            2, // ゲームオーバー
            $map_data['map_id'],
        );


        $res = $log_db->GetAll($sql, $param);
        if($res === false){
            return false;
        }

        $data = array(
            'count' => count($res),
            'data' => $res,
        );

        return $data;
    }

    /**
     * 指定日付のバトル進捗を集計する
     *
     * @param datetime $tran_date
     * @param array $map_data
     * @return boolean
     */
    public function getLogQuestDataForBattleProgressContinue ($tran_date, $map_data, $service_flg)
    {
        $log_db = $this->backend->getDB('logex');

        $tmp = explode('-', date('Y-m-d', strtotime($tran_date)));

        $date_start = date('Y-m-d H:i:s', mktime(0, 0, 0, $tmp[1], $tmp[2], $tmp[0]));
        $date_end = date('Y-m-d H:i:s', mktime(23, 59, 59, $tmp[1], $tmp[2], $tmp[0]));

        $where_st = " where"
                  . " qd.date_log >= ?"
                  . " and qd.date_log <= ?"
                  . " and qd.quest_st = ?"
                  . " and qd.map_id = ?";
        $group_st = " group by qd.ua, qd.map_id, qd.quest_id, qd.area_id, qd.lose_battle_no, qd.quest_st";

        $sql_item = " select *"
                 . " from log_item_data"
                 . " where"
                 . " item_id = ?"
                 . " and service_flg = ?"
                 . " and date_log >= ?"
                 . " and date_log <= ?"
                 . " and processing_type = ?";

        $sql = "select"
                 . " qd.ua as ua,"
                 . " qd.map_id as map_id,"
                 . " qd.quest_id as quest_id,"
                 . " qd.area_id as area_id,"
                 . " qd.quest_st as quest_st,"
                 . " qd.lose_battle_no,"
                 . " count(*) as count,"
                 . " sum(id.count) as count_item"
                 . " from log_quest_data qd"
                 . " left outer join"
                 . " (" . $sql_item.  ") id"
                 . " On id.api_transaction_id = qd.api_transaction_id"
                 . $where_st
                 . $group_st;
        $param = array(
            9000,
            $service_flg,
            $date_start, //
            $date_end, //
            'D23',
            $date_start, //
            $date_end, //
            3, // コンティニュー
            $map_data['map_id'],
        );

        $res = $log_db->GetAll($sql, $param);
        if($res === false){
            return false;
        }

        $data = array(
            'count' => count($res),
            'data' => $res,
        );

        return $data;

    }

    /**
     * 指定日付のバトル進捗を集計する
     *
     * @param datetime $tran_date
     * @param array $map_data
     * @return boolean
     */
    public function getLogQuestDataForBattleProgress ($tran_date, $map_data)
    {
        $res_gameover_data = $this->getLogQuestDataForBattleProgressGameover($tran_date, $map_data);

        if ($res_gameover_data['count'] > 0) {
            foreach($res_gameover_data['data'] as $v){
                $gameover_data[$v['quest_id']][$v['area_id']][$v['lose_battle_no']][$v['ua']] = $v;
            }
        }

        $res_continue_data_pay = $this->getLogQuestDataForBattleProgressContinue($tran_date, $map_data, 0);
        if ($res_continue_data_pay['count'] > 0) {
            foreach($res_continue_data_pay['data']as $v){
                $continue_data_pay[$v['quest_id']][$v['area_id']][$v['lose_battle_no']][$v['ua']] = $v;
            }
        }

        $res_continue_data_free = $this->getLogQuestDataForBattleProgressContinue($tran_date, $map_data, 1);
        if ($res_continue_data_free['count'] > 0) {
            foreach($res_continue_data_free['data'] as $v){
                $continue_data_free[$v['quest_id']][$v['area_id']][$v['lose_battle_no']][$v['ua']] = $v;
            }
        }

        $master_battle = $this->getMasterBattleData($map_data['map_id']);

        $def_count = array(
            'count' => 0,
            'item_count' => 0,
        );

        // 集計結果を統合する
        $battle_data = array();
        foreach($master_battle as $v){
            $battle_num = intval($v['lose_battle_num']);
            for($i=1;$i<=$battle_num;$i++){
                for($j=1;$j<=2;$j++){

                    $go = $def_count;
                    if (isset($gameover_data[$v['quest_id']][$v['area_id']][$i][$j])){
                        $go = $gameover_data[$v['quest_id']][$v['area_id']][$i][$j];
                    }
                    $con_pay = $def_count;
                    if (isset($continue_data_pay[$v['quest_id']][$v['area_id']][$i][$j])){
                        $con_pay = $continue_data_pay[$v['quest_id']][$v['area_id']][$i][$j];
                    }
                    $con_free = $def_count;
                    if (isset($continue_data_free[$v['quest_id']][$v['area_id']][$i][$j])){
                        $con_free = $continue_data_free[$v['quest_id']][$v['area_id']][$i][$j];
        }

                    $battle_data[$v['quest_id']][$v['area_id']][$i]['mst'] = $v;
                    $battle_data[$v['quest_id']][$v['area_id']][$i][$j]['go'] = $go;
                    $battle_data[$v['quest_id']][$v['area_id']][$i][$j]['con_pay'] = $con_pay;
                    $battle_data[$v['quest_id']][$v['area_id']][$i][$j]['con_free'] = $con_free;

                }
            }
        }

        return $battle_data;

    }

    /**
     * 集計結果をバトル進捗に登録する
     * 
     * @param array $data 集計結果
     * @param array $map_data マップマスタ情報
     * @param array $tran_data 処理対象日
     * @return bolean
     */
    public function addKpiUserBattleProgress($data, $map_data, $tran_date)
    {
// $this->backend->logger->log(LOG_INFO, '************************ data result==[' . print_r($map_data, true) . ']');

        $res = $this->getMasterBattleData($map_data['map_id']);
        if ($res === false){
            return false;
        }
        foreach($res as $k => $v){
            for($i=1;$i<=$v['lose_battle_num'];$i++){
                $mst_data = $data[$v['quest_id']][$v['area_id']][$i]['mst'];
                $mst_data['map_name'] = $map_data['map_name'];
                $mst_data['lose_battle_num'] = $i;
                $apple_data = $data[$v['quest_id']][$v['area_id']][$i]['1'];
                $google_data = $data[$v['quest_id']][$v['area_id']][$i]['2'];

                $tmp = array_merge($mst_data, array('date_battle_tally' => $tran_date));

                // apple
                $a_pay = $apple_data['con_pay']['item_count'] * -1;
                $a_free = $apple_data['con_free']['item_count'] * -1;
                $a_total = $a_pay + $a_free;
                $a_continue_charge_rate = 0;
                if($a_pay != 0){
                    $a_continue_charge_rate = round(($a_pay / $a_total), 2) * 100;
                }
                $tmp_apple = array(
                    'ua' => 1,
                    'count_retire' => $apple_data['go']['count'],
                    'continue_pay' => $a_pay,
                    'continue_free' => $a_free,
                    'continue_total' => $a_total,
                    'continue_charge_rate' => $a_continue_charge_rate,
                );
                $res = $this->_insertKpiUserBattleProgress(array_merge($tmp, $tmp_apple));
                if (Ethna::isError($res)) {
                    return false;
                }

                // google
                $g_pay = $google_data['con_pay']['item_count'] * -1;
                $g_free = $google_data['con_free']['item_count'] * -1;
                $g_total = $g_pay + $g_free;
                $g_continue_charge_rate = 0;
                if($g_pay != 0){
                    $g_continue_charge_rate = round(($g_pay / $g_total), 2) * 100;
                }
                $tmp_google = array(
                    'ua' => 2,
                    'count_retire' => $google_data['go']['count'],
                    'continue_pay' => $g_pay,
                    'continue_free' => $g_free,
                    'continue_total' => $g_total,
                    'continue_charge_rate' => $g_continue_charge_rate,
                );
                $res = $this->_insertKpiUserBattleProgress(array_merge($tmp, $tmp_google));
                if (Ethna::isError($res)) {
                    return false;
                }

                // all
                $all_retire = $apple_data['go']['count'] + $google_data['go']['count'];
                $all_pay = $a_pay + $g_pay;
                $all_free = $a_free + $g_free;
                $all_total = $all_pay + $all_free;
                $all_continue_charge_rate = 0;
                if($all_pay != 0){
                    $all_continue_charge_rate = round(($all_pay / $all_total), 2) * 100;
                }
                $tmp_all = array(
                    'ua' => 0,
                    'count_retire' => $all_retire,
                    'continue_pay' => $all_pay,
                    'continue_free' => $all_free,
                    'continue_total' => $all_total,
                    'continue_charge_rate' => $all_continue_charge_rate,
                );
                $res = $this->_insertKpiUserBattleProgress(array_merge($tmp, $tmp_all));
                if (Ethna::isError($res)) {
                    return false;
                }
            }

		
        }

        return true;

    }

    /**
     * insert to kpi_user_battle_progress
     *
     * @param array $data
     * @return boolean
     */
    private function _insertKpiUserBattleProgress($data)
    {

        $now = date('Y-m-d H:i:s', time());
        $param = array(
            $data['ua'],
            $data['date_battle_tally'],
            $data['map_id'],
            $data['map_no'],
            $data['map_name'],
            $data['quest_id'],
            $data['quest_no'],
            $data['quest_name'],
            $data['area_id'],
            $data['area_name'],
            $data['lose_battle_num'],
            $data['battle_monster_num'],
            $data['count_retire'],
            $data['continue_pay'],
            $data['continue_free'],
            $data['continue_total'],
            $data['continue_charge_rate'],
            $now,
            $now,
        );

        $sql = "INSERT INTO kpi_user_battle_progress ("
             . " ua,"
             . " date_battle_tally,"
             . " map_id,"
             . " map_no,"
             . " map_name,"
             . " quest_id,"
             . " quest_no,"
             . " quest_name,"
             . " area_id,"
             . " area_name,"
             . " lose_battle_num,"
             . " count_battle_monster,"
             . " count_retire,"
             . " count_continue_pay,"
             . " count_continue_free,"
             . " count_continue_total,"
             . " continue_charge_rate,"
             . " date_created,"
             . " date_modified"
             . " ) value (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


        return $this->_executeQuery($sql, $param);
    }

    /**
     * KPI バトル進捗のCSVファイル作成
     *
     * @param array $kpi_continuance_data
     * @return mixed 
     */
    public function createCsvFileKpiUserBattleProgress($kpi_battle_data)
    {
        $log_name = 'kpi_user_battle_progress';
        $file_path = KPIDATA_PATH_USER_DATA;

        $rand_num = mt_rand();
        $today_date = date('Ymd', time());
        $file_name = $log_name .'_' . $today_date . '_' . $rand_num;
        if (!$fp=@fopen($file_path . '/' . $file_name, 'a')){
            return false;
        }

        $title_list = array(
            'マップID',
            'マップ名',
            'マップ番号',
            'クエストID',
            'クエスト名',
            'クエスト番号',
            'エリアID',
            'エリア名',
            'エリア内番号',
            'モンスター出現数',
            'リタイヤ数',
            'コンティニュー数(有料)',
            'コンティニュー数(無料)',
            'コンティニュー数(総計)',
            '課金率',
        );
        $title_str = implode(',', $title_list);
        fwrite($fp, mb_convert_encoding($title_str, "Shift_JIS", "UTF-8") . "\r\n");

        $data_list = array();
        foreach ($kpi_battle_data as $data_k => $data_v){
            $data_list = array();
            $data_list[] = $data_v['map_id'];
            $data_list[] = $data_v['map_name'];
            $data_list[] = $data_v['map_no'];
            $data_list[] = $data_v['quest_id'];
            $data_list[] = $data_v['quest_name'];
            $data_list[] = $data_v['quest_no'];
            $data_list[] = $data_v['area_id'];
            $data_list[] = $data_v['area_name'];
            $data_list[] = $data_v['lose_battle_num'];
            $data_list[] = $data_v['count_battle_monster'];
            $data_list[] = $data_v['count_retire'];
            $data_list[] = $data_v['count_continue_pay'];
            $data_list[] = $data_v['count_continue_free'];
            $data_list[] = $data_v['count_continue_total'];
            $data_list[] = $data_v['continue_charge_rate'];
            $str = implode(',', $data_list);
            fwrite($fp, mb_convert_encoding($str, "Shift-JIS", "UTF-8") . "\r\n");
        }
        fclose($fp);

        return $file_name;

    }

    /**
     * 集計結果に付随するマスタ情報を取得する
     *
     * @param int $map_id
     * @return boolean
     */
    public function getMasterBattleData($map_id = null)
    {

        $where = "";
        $param = array();
        if(!is_null($map_id)){
            $where = " where mq.map_id = ?";
            $param = array($map_id);
        }

        $sql = "select"
             . " mq.map_id, "
             . " mb.quest_id, "
             . " mb.area_id, "
             . " mq.map_no as map_no, "
             . " mq.name_ja as quest_name,"
             . " ma.no as quest_no,  "
             . " ma.name_ja as area_name,"
             . " mb.no as battle_no,"
             . " mb.no as lose_battle_num,"
             . " mb.num as battle_monster_num"
             . " from m_battle mb"
             . " join m_quest mq ON mq.quest_id = mb.quest_id"
             . " join m_area ma ON ma.area_id = mb.area_id";

        return $this->db->GetAll($sql . $where, $param);
    }
}
