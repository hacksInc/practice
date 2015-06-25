<?php
/**
 *  Pp_KpiViewItemManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewItemManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_KpiViewItemManager extends Pp_KpiViewManager
{
    /**
     * 指定期間のKPI アイテム毎課金率の件数を取得する
     *
     * @params array $search_param
     * @return mixed
     */
    public function getKpiItemChargeListByDateItemTallyCount ($search_param)
    {
        $condition = $this->_getConditionKpiItemChargeListByDateItemTally($search_param);

        return $this->getKpiItemChargeCount($condition['condition'], $condition['param']);
    }

    /**
     * 指定期間のKPI アイテム毎課金率を取得する
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiItemChargeListByDateItemTally ($search_param)
    {
        $condition = $this->_getConditionKpiItemChargeListByDateItemTally($search_param);
        $sort = array(
            'date_item_tally' => 'ASC',
            'item_use_type' => 'ASC',
        );

        $res = $this->getKpiItemCharge($condition['condition'], $condition['param'], $sort);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;

    }

    /**
     * 指定期間のKPI アイテム毎課金率を取得する
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiItemChargeListByDateItemTallyAll ($search_param)
    {
        $condition = $this->_getConditionKpiItemChargeListByDateItemTally($search_param);
        $sort = array(
            'r1.date_item_tally' => 'ASC',
        );

        $res = $this->getKpiItemChargeAll($condition['condition'], $condition['param'], $sort);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;

    }

    /**
     * 指定期間のKPI アイテム毎課金率の合計を取得する
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiItemChargeListByDateItemTallyAllSum ($search_param)
    {
        $condition = $this->_getConditionKpiItemChargeListByDateItemTally($search_param);

        $res = $this->getKpiItemChargeAllSum($condition['condition'], $condition['param']);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);

        // 課金率の算出
        if ($res['gacha_count_item_pay'] != 0 && $res['gacha_count_item_total'] != 0){
            $res['gacha_charge_rate'] = round((($res['gacha_count_item_pay'] / $res['gacha_count_item_total']) * 100) , 2);
        }
        if ($res['box_count_item_pay'] != 0 && $res['box_count_item_total'] != 0){
            $res['box_charge_rate'] = round((($res['box_count_item_pay'] / $res['box_count_item_total']) * 100) , 2);
        }
        if ($res['stamina_count_item_pay'] != 0 && $res['stamina_count_item_total'] != 0){
            $res['stamina_charge_rate'] = round((($res['stamina_count_item_pay'] / $res['stamina_count_item_total']) * 100) , 2);
        }
        if ($res['continue_count_item_pay'] != 0 && $res['continue_count_item_total'] != 0){
            $res['continue_charge_rate'] = round((($res['continue_count_item_pay'] / $res['continue_count_item_total']) * 100) , 2);
        }

        $log_data['data'] = $res;

        return $res;

    }

    /**
     * 指定期間のKPI アイテム毎課金率を指定する(条件編集)
     *
     * @param array $search_param
     * @return array
     */
    private function _getConditionKpiItemChargeListByDateItemTally ($search_param)
    {
        $where = " where (date_item_tally >= ? and date_item_tally <= ?)"
               . " and ua = ?";
        $param = array(
            $search_param['date_from'],
            $search_param['date_to'],
            $search_param['ua']
        );

        return array('condition' => $where, 'param' => $param);
    }

    /**
     * KPI アイテム毎課金率を取得する
     *
     * @param string $where
     * @param array $param
     * @param array $sort
     * @param integer $limit
     * @param integer $offset
     * @return mixed
     */
    public function getKpiItemCharge ($where, $param, $sort=null, $limit=null, $offset=null)
    {
        $log_db = $this->backend->getDB('logex');
        $order = $this->_createSqlPhraseOrderBy($sort);
        $sql = "select * from kpi_item_charge_rate" . $where . $order;

        return $log_db->GetAll($sql, $param);
    }

    /**
     * KPI アイテム毎課金率を取得する
     *
     * @param string $where
     * @param array $param
     * @param array $sort
     * @param integer $limit
     * @param integer $offset
     * @return mixed
     */
    public function getKpiItemChargeAll ($where, $param, $sort=null, $limit=null, $offset=null)
    {
        $log_db = $this->backend->getDB('logex');
        $order = $this->_createSqlPhraseOrderBy($sort);
        // 自己結合を行うため
        $where2 = " where (r1.date_item_tally >= ? and r1.date_item_tally <= ?)"
            . " and r1.ua = ?";
        $param_list = array_merge(array_merge(array_merge($param, $param), $param), $param);

        $sql = "select "
             . " DATE_FORMAT(r1.date_item_tally, '%Y-%m-%d') as date_item_tally,"
             . " DATE_FORMAT(r1.date_item_tally, '%a') as date_item_tally_day,"
             . " r1.count_item_pay as gacha_count_item_pay,"
             . " r1.count_item_free as gacha_count_item_free,"
             . " r1.count_item_total as gacha_count_item_total,"
             . " r1.charge_rate as gacha_charge_rate,"
             . " r2.count_item_pay as box_count_item_pay,"
             . " r2.count_item_free as box_count_item_free,"
             . " r2.count_item_total as box_count_item_total,"
             . " r2.charge_rate as box_charge_rate,"
             . " r3.count_item_pay as stamina_count_item_pay,"
             . " r3.count_item_free as stamina_count_item_free,"
             . " r3.count_item_total as stamina_count_item_total,"
             . " r3.charge_rate as stamina_charge_rate,"
             . " r4.count_item_pay as continue_count_item_pay,"
             . " r4.count_item_free as continue_count_item_free,"
             . " r4.count_item_total as continue_count_item_total,"
             . " r4.charge_rate as continue_charge_rate"
             . " from kpi_item_charge_rate r1"
             . " join (select * from kpi_item_charge_rate ". $where . "and item_use_type = 2) r2 on r2. date_item_tally = r1.date_item_tally"
             . " join (select * from kpi_item_charge_rate ". $where . "and item_use_type = 3) r3 on r3. date_item_tally = r1.date_item_tally"
             . " join (select * from kpi_item_charge_rate ". $where . "and item_use_type = 4) r4 on r4. date_item_tally = r1.date_item_tally"
             . $where2 . "and r1.item_use_type = 1"
             . $order;

        return $log_db->GetAll($sql, $param_list);
    }

    /**
     * KPI アイテム毎課金率を取得する
     *
     * @param string $where
     * @param array $param
     * @return mixed
     */
    public function getKpiItemChargeAllSum ($where, $param)
    {
        $log_db = $this->backend->getDB('logex');
        $order = $this->_createSqlPhraseOrderBy($sort);
        // 自己結合を行うため、添え字
        $where2 = " where (r1.date_item_tally >= ? and r1.date_item_tally <= ?)"
            . " and r1.ua = ?";
        $param_list = array_merge(array_merge(array_merge($param, $param), $param), $param);

        $sql = "select "
             . " sum(r1.count_item_pay) as gacha_count_item_pay,"
             . " sum(r1.count_item_free) as gacha_count_item_free,"
             . " sum(r1.count_item_total) as gacha_count_item_total,"
             . " sum(r2.count_item_pay) as box_count_item_pay,"
             . " sum(r2.count_item_free) as box_count_item_free,"
             . " sum(r2.count_item_total) as box_count_item_total,"
             . " sum(r3.count_item_pay) as stamina_count_item_pay,"
             . " sum(r3.count_item_free) as stamina_count_item_free,"
             . " sum(r3.count_item_total) as stamina_count_item_total,"
             . " sum(r4.count_item_pay) as continue_count_item_pay,"
             . " sum(r4.count_item_free) as continue_count_item_free,"
             . " sum(r4.count_item_total) as continue_count_item_total,"
             . " '0' as gacha_charge_rate,"
             . " '0' as box_charge_rate,"
             . " '0' as stamina_charge_rate,"
             . " '0' as continue_charge_rate"
             . " from kpi_item_charge_rate r1"
             . " join (select * from kpi_item_charge_rate ". $where . "and item_use_type = 2) r2 on r2. date_item_tally = r1.date_item_tally"
             . " join (select * from kpi_item_charge_rate ". $where . "and item_use_type = 3) r3 on r3. date_item_tally = r1.date_item_tally"
             . " join (select * from kpi_item_charge_rate ". $where . "and item_use_type = 4) r4 on r4. date_item_tally = r1.date_item_tally"
             . $where2 . "and r1.item_use_type = 1";

        return $log_db->GetRow($sql, $param_list);
    }

    /**
     * KPI アイテム毎課金率の件数を取得する
     *
     * @param string $where
     * @param array $param
     * @return mixed
     */
    public function getKpiItemChargeCount ($where, $param)
    {
        $db_logex_r = $this->backend->getDB('logex_r');
        $sql = "select count(*) from kpi_item_charge_rate" . $where;

        return $db_logex_r->GetOne($sql, $param);
    }

    /**
     * 指定日のKPI アイテム毎課金率を取得する
     *
     * @params datetime $tran_date
     * @return mixed
     */
    public function getKpiItemChargeDataByDateItemTally ($tran_date)
    {
        $condition = $this->_getConditionKpiItemChargeDataByDateItemTally ($tran_date);

        $res = $this->getKpiItemCharge ($condition['condition'], $condition['param']);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;
    }

    /**
     * 指定日のKPI アイテム毎課金率の件数を取得する
     *
     * @params datetime $tran_date
     * @return mixed
     */
    public function getKpiItemChargeDataByDateItemTallyCount ($tran_date)
    {
        $condition = $this->_getConditionKpiItemChargeDataByDateItemTally ($tran_date);

        $res = $this->getKpiItemChargeCount ($condition['condition'], $condition['param']);

        return $res;
    }

    /**
     * 指定日のKPI 継続率を取得する(条件編集)
     *
     * @params datetime $tran_date
     * @return mixed
     */
    private function _getConditionKpiItemChargeDataByDateItemTally ($date_item_tally)
    {
        $where = " where date_item_tally = ?";
        $param = array($date_item_tally);

        return array('condition' => $where, 'param' => $param);
    }

    /**
     * 指定日のKPI 継続率の件数を取得する
     *
     * @params datetime $tran_date
     * @return mixed
     */
    public function getKpiGachaChargeDataByDateGachaTallyCount ($tran_date)
    {
        $condition = $this->_getConditionKpiGachaChargeDataByDateGachaTally ($tran_date);

        $res = $this->getKpiGachaChargeCount ($condition['condition'], $condition['param']);

        return $res;
    }

    /**
     * 指定日のKPI 継続率を取得する(条件編集)
     *
     * @params datetime $tran_date
     * @return mixed
     */
    private function _getConditionKpiGachaChargeDataByDateGachaTally ($date_gacha_tally)
    {
        $where = " where date_gacha_tally = ?";
        $param = array($date_gacha_tally);

        return array('condition' => $where, 'param' => $param);
    }

    /**
     * KPI アイテム毎課金率の件数を取得する
     *
     * @param string $where
     * @param array $param
     * @return mixed
     */
    public function getKpiGachaChargeCount ($where, $param)
    {
        $log_db = $this->backend->getDB('logex');
        $sql = "select count(*) from kpi_gacha_charge_rate" . $where;

        return $log_db->GetOne($sql, $param);
    }

    /**
     * ログデータより指定した日付のアイテム課金情報を抽出する
     *
     * @param array $param
     * @return mixed
     */
    public function getLogItemDataForKpiItemCharge($search_param)
    {
        $db_logex_r = $this->backend->getDB('logex_r');
        $tmp = explode('/', $search_param['tran_date']);
        $date_from = date('Y-m-d H:i:s' , mktime(0, 0, 0, $tmp[1], $tmp[2], $tmp[0]));
        $date_to = date('Y-m-d H:i:s', mktime(23, 59, 59, $tmp[1], $tmp[2], $tmp[0]));
        $param = array(
            $search_param['item_id'],
            $date_from,
            $date_to,
        );
        $sql = "select"
             . " ua,"
             . " processing_type,"
             . " processing_type_name,"
             . " service_flg,"
             . " count(*) as item_count,"
             . " sum(count) as sum_count"
             . " from log_item_data"
             . " where item_id = ?"
             . " and date_log >= ? and date_log <= ?"
             . " group by ua, processing_type, service_flg"
             . " order by processing_type, service_flg";

        $res = $db_logex_r->GetAll($sql, $param);

        if ($res === false) {
            return false;
        }

        $data['data'] = $res;
        $data['count'] = count($data);

        return $data;

    }

    /**
     * ログデータより指定した日付のガチャ別アイテム課金情報を抽出する
     *
     * @param array $param
     * @return mixed
     */
    public function getLogItemDataForKpiGachaCharge($search_param)
    {
        $log_db = $this->backend->getDB('logex');
        $tmp = explode('/', $search_param['tran_date']);
        $date_from = date('Y-m-d H:i:s' , mktime(0, 0, 0, $tmp[1], $tmp[2], $tmp[0]));
        $date_to = date('Y-m-d H:i:s', mktime(23, 59, 59, $tmp[1], $tmp[2], $tmp[0]));
        $param = array(
            $search_param['item_id'],
            $search_param['processing_type'],
            $date_from,
            $date_to,
        );
        $sql = "select"
             . " id.ua,"
             . " gd.gacha_id,"
             . " gd.gacha_name,"
             . " id.service_flg,"
             . " count(*) as item_count,"
             . " sum(id.count) as sum_count"
             . " from log_item_data id"
             . " join log_gacha_data gd on gd.api_transaction_id = id.api_transaction_id"
             . " where id.item_id = ? "
             . " and id.processing_type = ?"
             . " and id.date_log >= ? and id.date_log <= ?"
             . " group by id.ua, id.service_flg, gd.gacha_id"
             . " order by id.service_flg, gd.gacha_type";

        $res = $log_db->GetAll($sql, $param);

        if ($res === false) {
            return false;
        }

        $data['data'] = $res;
        $data['count'] = count($data);

        return $data;

    }

    /**
     * ログデータより指定した日付のアイテム使用情報を抽出する
     *
     * @param array $param
     * @return mixed
     */
    public function getLogItemDataForKpiItemUse($search_param)
    {
        $log_db = $this->backend->getDB('logex');
        $tmp = explode('/', $search_param['tran_date']);
        $date_from = date('Y-m-d H:i:s' , mktime(0, 0, 0, $tmp[1], $tmp[2], $tmp[0]));
        $date_to = date('Y-m-d H:i:s', mktime(23, 59, 59, $tmp[1], $tmp[2], $tmp[0]));
        $param = array(
            $search_param['item_id'],
            $date_from,
            $date_to,
        );
        $sql = "select"
             . " ua,"
             . " item_id,"
             . " processing_type,"
             . " processing_type_name,"
             . " service_flg,"
             . " count(*) as item_count,"
             . " sum(count) as sum_count"
             . " from log_item_data"
             . " where date_log >= ? and date_log <= ?"
             . " group by ua, item_id, processing_type, service_flg"
             . " order by processing_type, service_flg";

        $res = $log_db->GetAll($sql, $param);

        if ($res === false) {
            return false;
        }

        $data['data'] = $res;
        $data['count'] = count($data);

        return $data;

    }

    /**
     * log_item_dataを集計しDB(kpi_item_charge_rate)へ登録する
     *
     * @param array $log_item_data
     * @param string $tran_date
     * @return boolean
     */
    public function editLogItemDataforKpiChaegeRate ($log_item_data, $tran_date)
    {
        $type_list = array(
            '1' => 'G11', // ガチャ
            '2' => 'D22', // BOX拡張
            '3' => 'D21', // 体力回復剤
            '4' => 'D23'  // コンティニュー
        );

        $total_box = array(
            'count_item_pay' => 0,
            'count_item_free' => 0,
            'count_item_total' => 0,
            'charge_rate' => 0,
            'date_item_tally' => $tran_date,
        );
        foreach ($type_list as $key => $val){
            // 処理タイプからアイテム使用区分を取得
            $total_box['processing_type'] = $val;
            $total_box['item_use_type'] = $key;
            $tmp_data[$val . '1'] = $total_box; // 1 は Pp_UserManager::OS_IPHONE の意
            $tmp_data[$val . '2'] = $total_box; // 2 は Pp_UserManager::OS_ANDROID の意
            $tmp_data[$val . '1']['ua'] = 1;
            $tmp_data[$val . '2']['ua'] = 2;
            $tmp_data[$val] = $total_box;
            $tmp_data[$val]['ua'] = 0; // iOS,Android合計
        }

        foreach($log_item_data as $k => $v){
            // 課金金額を振り分ける
            switch ($v['service_flg']){
            case '0':
                $tmp_data[$v['processing_type'] . $v['ua']]['count_item_pay'] = $v['sum_count'] * -1;
                $tmp_data[$v['processing_type']]['count_item_pay'] += $v['sum_count'] * -1;
                break;
            case '1':
                $tmp_data[$v['processing_type'] . $v['ua']]['count_item_free'] = $v['sum_count'] * -1;
                $tmp_data[$v['processing_type']]['count_item_free'] += $v['sum_count'] * -1;
                break;
            }
            // 課金合計
            $tmp_data[$v['processing_type'] . $v['ua']]['count_item_total'] += ($v['sum_count'] * -1);
            $tmp_data[$v['processing_type']]['count_item_total'] += ($v['sum_count'] * -1);
        }

        // 取得したログデータを元に、課金率の計算を行いDBへ登録する
        foreach ($tmp_data as $t_k => $t_v){
            $data=$t_v;
            if(!isset($data['item_use_type'])){
                // 集計対象外のデータはスキップする
                continue;
            }
            if ( $data['count_item_total'] != 0 && $data['count_item_pay'] != 0){
                $data['charge_rate'] = ($data['count_item_pay'] / $data['count_item_total']) * 100;
            }
            $res = $this->insertKpiItemChargeRate($data);
            if (Ethna::isError($ret)) {
                return false;
            }
        }
        return true;
    }

    /**
     * log_item_dataを集計しDB(kpi_gacha_charge_rate)へ登録する
     *
     * @param array $log_item_data
     * @param string $tran_date
     * @return boolean
     */
    public function editLogItemDataforKpiGachaChargeRate ($log_item_data, $tran_date)
    {
        $sql = "select * from m_gacha_list where date_start <= ? and date_end >= ? and type in(?, ?)";
        $param = array($tran_date, $tran_date, '3', '4');
        $res = $this->db_r->GetAll($sql, $param);
        if ( $res === false) {
            return false;
        }

        $total_box = array(
            'ua' => 1,
            'count_item_pay' => 0,
            'count_item_free' => 0,
            'count_item_total' => 0,
            'gacha_charge_rate' => 0,
            'date_gacha_tally' => $tran_date,
        );
        foreach($res as $k => $v){
            $total_box['gacha_id'] = $v['gacha_id'];
            $total_box['gacha_name'] = $v['comment'];
            $tmp[$v['gacha_id'] . '_0'] = $total_box;
            $tmp[$v['gacha_id'] . '_1'] = $total_box;
            $tmp[$v['gacha_id']] = $total_box;
            $tmp[$v['gacha_id'] . '_1']['ua'] = 2;
            $tmp[$v['gacha_id']]['ua'] = 0;
        }

        foreach($log_item_data as $data_k => $data_v){
            $sum_count = $data_v['sum_count'] * -1;
            switch($data_v['service_flg']){
            case 0:
                $tmp[$data_v['gacha_id'] . '_' . $data_v['ua']]['count_item_pay'] = $sum_count;
                $tmp[$data_v['gacha_id']]['count_item_pay'] += $sum_count;
                break;
            case 1:
                $tmp[$data_v['gacha_id'] . '_' . $data_v['ua']]['count_item_free'] = $sum_count;
                $tmp[$data_v['gacha_id']]['count_item_free'] += $sum_count;
                break;
            }
            $tmp[$data_v['gacha_id'] . '_' . $data_v['ua']]['count_item_total'] += $sum_count;
            $tmp[$data_v['gacha_id']]['count_item_total'] += $sum_count;

        }

        foreach($tmp as $gacha_k => $gacha_v){
            $data = $gacha_v;
            if ( $gacha_v['count_item_pay'] != 0 && $gacha_v['count_item_total'] != 0){
                $data['gacha_charge_rate'] = round((($gacha_v['count_item_pay'] / $gacha_v['count_item_total']) * 100) , 2);
            }
            $res = $this->insertKpiGachaChargeRate($data);
            if (Ethna::isError($res)) {
                return false;
            }
        }
        return true;
    }

    /**
     * KPI アイテム課金率を登録する
     *
     * @param data
     * @return mixed
     */
    public function insertKpiItemChargeRate($data)
    {
        $now = date('Y-m-d H:i:s', time());
        $param = array(
            $data['ua'],
            $data['date_item_tally'],
            $data['item_use_type'],
            $data['count_item_pay'],
            $data['count_item_free'],
            $data['count_item_total'],
            $data['charge_rate'],
            $now,
            $now,
        );

        $sql = "INSERT INTO kpi_item_charge_rate ("
             . " ua,"
             . " date_item_tally,"
             . " item_use_type,"
             . " count_item_pay,"
             . " count_item_free,"
             . " count_item_total,"
             . " charge_rate,"
             . " date_created,"
             . " date_modified"
             . " ) value (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->_executeQuery($sql, $param);

    }

    /**
     * KPI ガチャ毎課金率を登録する
     *
     * @param data
     * @return mixed
     */
    public function insertKpiGachaChargeRate($data)
    {
        $now = date('Y-m-d H:i:s', time());
        $param = array(
            $data['ua'],
            $data['date_gacha_tally'],
            $data['gacha_id'],
            $data['gacha_name'],
            $data['count_item_pay'],
            $data['count_item_free'],
            $data['count_item_total'],
            $data['gacha_charge_rate'],
            $now,
            $now,
        );

        $sql = "INSERT INTO kpi_gacha_charge_rate ("
             . " ua,"
             . " date_gacha_tally,"
             . " gacha_id,"
             . " gacha_name,"
             . " count_item_pay,"
             . " count_item_free,"
             . " count_item_total,"
             . " gacha_charge_rate,"
             . " date_created,"
             . " date_modified"
             . " ) value (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->_executeQuery($sql, $param);

    }

    /**
     * KPI アイテム毎課金率のCSVファイル作成
     *
     * @param array $kpi_continuance_data
     * @return mixed 
     */
    public function createCsvFileKpiItemChargeRate($kpi_charge_data, $kpi_charge_sum_data)
    {
        $log_name = 'kpi_item_charge_rate';
        $file_path = KPIDATA_PATH_ITEM_DATA;

        $rand_num = mt_rand();
        $today_date = date('Ymd', time());
        $file_name = $log_name .'_' . $today_date . '_' . $rand_num;
        if (!$fp=@fopen($file_path . '/' . $file_name, 'a')){
            return false;
        }

        $title_list = array(
            '日付',
            '曜日',
            'ガチャ 有料メダル額',
            'ガチャ 無料メダル額',
            'ガチャ 総メダル額',
            'ガチャ 課金率',
            'BOX拡張 有料メダル額',
            'BOX拡張 無料メダル額',
            'BOX拡張 総メダル額',
            'BOX拡張 課金率',
            '体力回復 有料メダル額',
            '体力回復 無料メダル額',
            '体力回復 総メダル額',
            '体力回復 課金率',
            'コンティニュー 有料メダル額',
            'コンティニュー 無料メダル額',
            'コンティニュー 総メダル額',
            'コンティニュー 課金率',
        );
        $title_str = implode(',', $title_list);
        fwrite($fp, mb_convert_encoding($title_str, "Shift_JIS", "UTF-8") . "\r\n");

        $data_list = array();
        foreach ($kpi_charge_data as $data_k => $data_v){
            $data_list = array(
                $data_v['date_item_tally'],
                $data_v['date_item_tally_day'],
                $data_v['gacha_count_item_pay'],
                $data_v['gacha_count_item_free'],
                $data_v['gacha_count_item_total'],
                $data_v['gacha_charge_rate'] . '%',
                $data_v['box_count_item_pay'],
                $data_v['box_count_item_free'],
                $data_v['box_count_item_total'],
                $data_v['box_charge_rate'] . '%',
                $data_v['stamina_count_item_pay'],
                $data_v['stamina_count_item_free'],
                $data_v['stamina_count_item_total'],
                $data_v['stamina_charge_rate'] . '%',
                $data_v['continue_count_item_pay'],
                $data_v['continue_count_item_free'],
                $data_v['continue_count_item_total'],
                $data_v['continue_charge_rate'] . '%',
            );
            $str = implode(',', $data_list);
            fwrite($fp, mb_convert_encoding($str, "Shift-JIS", "UTF-8") . "\r\n");
        }
        $data_list = array(
            '合計',
            '',
            $kpi_charge_sum_data['gacha_count_item_pay'],
            $kpi_charge_sum_data['gacha_count_item_free'],
            $kpi_charge_sum_data['gacha_count_item_total'],
            $kpi_charge_sum_data['gacha_charge_rate'] . '%',
            $kpi_charge_sum_data['box_count_item_pay'],
            $kpi_charge_sum_data['box_count_item_free'],
            $kpi_charge_sum_data['box_count_item_total'],
            $kpi_charge_sum_data['box_charge_rate'] . '%',
            $kpi_charge_sum_data['stamina_count_item_pay'],
            $kpi_charge_sum_data['stamina_count_item_free'],
            $kpi_charge_sum_data['stamina_count_item_total'],
            $kpi_charge_sum_data['stamina_charge_rate'] . '%',
            $kpi_charge_sum_data['continue_count_item_pay'],
            $kpi_charge_sum_data['continue_count_item_free'],
            $kpi_charge_sum_data['continue_count_item_total'],
            $kpi_charge_sum_data['continue_charge_rate'] . '%',
        );
        $str = implode(',', $data_list);
        fwrite($fp, mb_convert_encoding($str, "Shift-JIS", "UTF-8") . "\r\n");
        fclose($fp);

        return $file_name;

    }
}
