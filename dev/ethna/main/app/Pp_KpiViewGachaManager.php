<?php
/**
 *  Pp_KpiViewGachaManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewGachaManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_KpiViewGachaManager extends Pp_KpiViewManager
{
    /**
     * 指定期間のKPI ガチャ毎課金率の件数を取得する
     *
     * @params array $search_param
     * @return mixed
     */
    public function getKpiGachaChargeListByDateGachaTallyCount ($search_param)
    {
        $condition = $this->_getConditionKpiGachaChargeListByDateGachaTally($search_param);

        return $this->getKpiGachaChargeCount($condition['condition'], $condition['param']);
    }

    /**
     * 指定期間のKPI ガチャ毎課金率を取得する
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiGachaChargeListByDateGachaTally ($search_param)
    {
        $condition = $this->_getConditionKpiGachaChargeListByDateGachaTally($search_param);
        $sort = array(
            'date_gacha_tally' => 'ASC',
            'gacha_id' => 'ASC',
        );

        $res = $this->getKpiGachaCharge($condition['condition'], $condition['param'], $sort);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;

    }

    /**
     * 指定期間のKPI ガチャ毎課金率を取得する()
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiGachaChargeListByDateGachaTallyAll ($search_param)
    {
        $condition = $this->_getConditionKpiGachaChargeListByDateGachaTally($search_param);
        $sort = array(
            'date_gacha_tally' => 'ASC',
            'gacha_id' => 'ASC',
        );

        $res = $this->getKpiGachaChargeAll($condition['condition'], $condition['param'], $sort);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;

    }

    /**
     * 指定日のKPI ガチャ毎課金率を取得する
     *
     * @params datetime $tran_date
     * @return mixed
     */
    public function getKpiGachaChargeDataByDateGachaTally ($tran_date)
    {
        $condition = $this->_getConditionKpiGachaChargeDataByDateGachaTally ($tran_date);

        $res = $this->getKpiGachaCharge ($condition['condition'], $condition['param']);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;
    }

    /**
     * 指定日のKPI ガチャ毎課金率の件数を取得する
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
     * 指定期間のKPI アイテム毎課金率の合計を取得する
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiGachaChargeListByDateGachaTallyAllSum ($search_param)
    {
        $condition = $this->_getConditionKpiGachaChargeListByDateGachaTally($search_param);

        $res = $this->getKpiGachaChargeAllSum($condition['condition'], $condition['param']);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
/*
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
 */
        $log_data['data'] = $res;

        return $log_data;

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
    public function getKpiGachaChargeAll ($where, $param, $sort=null, $limit=null, $offset=null)
    {
        $log_db = $this->backend->getDB('logex_r');
        $order = $this->_createSqlPhraseOrderBy($sort);

        $sql = "select"
             . " DATE_FORMAT(date_gacha_tally, '%Y-%m-%d') as date_gacha_tally,"
             . " DATE_FORMAT(date_gacha_tally, '%a') as date_gacha_tally_day,"
             . " gacha_id,"
             . " gacha_name,"
             . " count_item_pay,"
             . " count_item_free,"
             . " count_item_total,"
             . " gacha_charge_rate"
             . " from kpi_gacha_charge_rate"
             . $where
             . $order;

        return $log_db->GetAll($sql, $param);
    }

    /**
     * KPI アイテム毎課金率を取得する
     *
     * @param string $where
     * @param array $param
     * @param array $sort
     * @return mixed
     */
    public function getKpiGachaChargeAllSum ($where, $param, $sort=null)
    {
        $log_db = $this->backend->getDB('logex_r');
        $order = $this->_createSqlPhraseOrderBy($sort);

        $sql = "select "
             . " gacha_id,"
             . " sum(count_item_pay) as count_item_pay,"
             . " sum(count_item_free) as count_item_free,"
             . " sum(count_item_total) as count_item_total,"
             . " '0' as gacha_charge_rate"
             . " from kpi_gacha_charge_rate"
             . $where
             . " group by gacha_id"
             . $order;

        return $log_db->GetAll($sql, $param);
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
        $log_db = $this->backend->getDB('logex_r');
        $sql = "select count(*) from kpi_gacha_charge_rate" . $where;

        return $log_db->GetOne($sql, $param);
    }

    /**
     * 指定期間のKPI ガチャ毎課金率を指定する(条件編集)
     *
     * @param array $search_param
     * @return array
     */
    private function _getConditionKpiGachaChargeListByDateGachaTally ($search_param)
    {
        $where = " where (date_gacha_tally >= ? and date_gacha_tally <= ?)"
               . " and ua = ?";
        $param = array(
            $search_param['date_from'],
            $search_param['date_to'],
            $search_param['ua']
        );

        return array('condition' => $where, 'param' => $param);
    }

    /**
     * 指定日のKPI ガチャ毎課金率を取得する(条件編集)
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
        /*$sql = "select"
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
         */
        $sql = "select"
             . " id.ua,"
             . " gd.gacha_id,"
             . " gd.gacha_name,"
             . " id.service_flg,"
             . " count(*) as item_count,"
             . " sum(id.count) as sum_count"
             . " from (select * from log_item_data"
             . " where item_id = ? "
             . " and processing_type = ?"
             . " and date_log >= ? and date_log <= ? ) id"
             . " join log_gacha_data gd on gd.api_transaction_id = id.api_transaction_id"
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
     * log_item_dataを集計しDB(kpi_gacha_charge_rate)へ登録する
     *
     * @param array $log_item_data
     * @param string $tran_date
     * @return boolean
     */
    public function editLogItemDataforKpiGachaChargeRate ($log_item_data, $tran_date)
    {
		$shop_m =& $this->backend->getManager('Shop');

        $sql = "select * from m_gacha_list where date_start <= ? and date_end >= ? and type in(?, ?, ?, ?)";
//        $param = array($tran_date . ' 23:59:59', $tran_date . ' 00:00:00', '3', '4');
        $param = array($tran_date . ' 23:59:59', $tran_date . ' 00:00:00',
			Pp_ShopManager::GACHA_TYPE_MEDAL,
			Pp_ShopManager::GACHA_TYPE_EVENT,
			Pp_ShopManager::GACHA_TYPE_MEDAL11,
			Pp_ShopManager::GACHA_TYPE_EVENT11
		);
        $res = $this->db_r->GetAll($sql, $param);
        if ( $res === false) {
            return false;
        }

        $total_box = array(
            'count_item_pay' => 0,
            'count_item_free' => 0,
            'count_item_total' => 0,
            'gacha_charge_rate' => 0,
            'date_gacha_tally' => $tran_date,
        );
        foreach($res as $k => $v){
            $total_box['gacha_id'] = $v['gacha_id'];
            $total_box['gacha_name'] = $v['comment'];
            $tmp[$v['gacha_id'] . '_1'] = $total_box; // 1 は Pp_UserManager::OS_IPHONE の意
            $tmp[$v['gacha_id'] . '_2'] = $total_box; // 2 は Pp_UserManager::OS_ANDROID の意
            $tmp[$v['gacha_id']] = $total_box;
            $tmp[$v['gacha_id'] . '_1']['ua'] = 1;
            $tmp[$v['gacha_id'] . '_2']['ua'] = 2;
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
     *
     *
     *
     */
    public function editDailyViewKpiGachaChargeRate($data, $total_data)
    {
        $tmp_date = '';
        $tmp_list = array();
        $view_list = array();
        foreach($data as $k => $v) {
            if($tmp_date != $v['date_gacha_tally']){
                if($tmp_date != ''){
                    $view_list[] = $tmp_list;
                }
                $tmp_date = $v['date_gacha_tally'];
                $tmp_list = array(
                    'date_gacha_tally' =>  $v['date_gacha_tally'],
                    'date_gacha_tally_day' =>  $v['date_gacha_tally_day'],
                );
            }
            $tmp_list['list'][$v['gacha_id']] = $v;

        }
        $view_list[] = $tmp_list;

        // タイトル行のガチャ名を取得
        $name_list = array();
//        foreach($tmp_list['list'] as $k => $v){
        foreach($data as $k => $v){
            //$name_list[$v['gacha_id']] = mb_convert_encoding($v['gacha_name'], 'UTF-8', 'Shift-JIS');
            $name_list[$v['gacha_id']] = $v['gacha_name'];

        }

        // 各ガチャの合計値行を編集
        $tmp_list = array(
            'date_gacha_tally' =>  '合計',
            'date_gacha_tally_day' =>  '',
        );
        foreach($total_data as $k => $v) {
            $tmp_list['list'][$v['gacha_id']] = $v;
        }
        $view_list[] = $tmp_list;

        return array('view_list' => $view_list, 'name_list' => $name_list);

    }

    /**
     * KPI アイテム毎課金率のCSVファイル作成
     *
     * @param array $kpi_continuance_data
     * @return mixed
     */
    public function createCsvFileKpiGachaChargeRate($kpi_charge_data, $kpi_charge_sum_data, $kpi_name_list)
    {
        $log_name = 'kpi_gacha_charge_rate';
        $file_path = KPIDATA_PATH_ITEM_DATA;

		if (!is_dir(KPIDATA_PATH_ITEM_DATA)) {
			mkdir(KPIDATA_PATH_ITEM_DATA, 0777, true); // 後でターミナル上のjugmonユーザで削除できるように、0777にする。
		}

        $rand_num = mt_rand();
        $today_date = date('Ymd', time());
        $file_name = $log_name .'_' . $today_date . '_' . $rand_num;
        if (!$fp=@fopen($file_path . '/' . $file_name, 'a')){
            return false;
        }

        $title_list = array(
            '日付',
            '曜日',
        );
        foreach($kpi_name_list as $name_v){
            $tmp_title = array(
                $name_v . ' 有料メダル額',
                $name_v . ' 無料メダル額',
                $name_v . ' 総メダル額',
                $name_v . ' 課金率',
            );
            $title_list = array_merge($title_list , $tmp_title);
        }

        $title_str = implode(',', $title_list);
        fwrite($fp, mb_convert_encoding($title_str, "Shift_JIS", "UTF-8") . "\r\n");

        $data_list = array();
        foreach ($kpi_charge_data as $data_k => $data_v){
            $data_list = array(
                $data_v['date_gacha_tally'],
                $data_v['date_gacha_tally_day'],
            );
//            foreach($data_v['list'] as $list_k => $list_v){
			foreach ($kpi_name_list as $name_k => $name_v) {
				if (isset($data_v['list'][$name_k])) {
					$list_v = $data_v['list'][$name_k];
					$tmp_data_list = array(
						$list_v['count_item_pay'],
						$list_v['count_item_free'],
						$list_v['count_item_total'],
						$list_v['gacha_charge_rate'] . '%',
					);
				} else {
					$tmp_data_list = array('', '', '', '');
				}

                $data_list = array_merge($data_list, $tmp_data_list);
            }
            $str = implode(',', $data_list);
            fwrite($fp, mb_convert_encoding($str, "Shift-JIS", "UTF-8") . "\r\n");
        }
        fclose($fp);

        return $file_name;

    }

	//=============================================================================================
	//ガチャUU
	//=============================================================================================

    /**
     * 指定期間のKPI ガチャ毎課金UUの件数を取得する
     *
     * @params array $search_param
     * @return mixed
     */
    public function getKpiGachaUuListByDateGachaTallyCount ($search_param)
    {
        $condition = $this->_getConditionKpiGachaUuListByDateGachaTally($search_param);

        return $this->getKpiGachaUuCount($condition['condition'], $condition['param']);
    }

    /**
     * 指定期間のKPI ガチャ毎課金UUを取得する
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiGachaUuListByDateGachaTally ($search_param)
    {
        $condition = $this->_getConditionKpiGachaUuListByDateGachaTally($search_param);
        $sort = array(
            'date_gacha_tally' => 'ASC',
            'gacha_id' => 'ASC',
        );

        $res = $this->getKpiGachaUu($condition['condition'], $condition['param'], $sort);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;

    }

    /**
     * 指定期間のKPI ガチャ毎課金UUを取得する()
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiGachaUuListByDateGachaTallyAll ($search_param)
    {
        $condition = $this->_getConditionKpiGachaUuListByDateGachaTally($search_param);
        $sort = array(
            'date_gacha_tally' => 'ASC',
            'gacha_id' => 'ASC',
        );

        $res = $this->getKpiGachaUuAll($condition['condition'], $condition['param'], $sort);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;

    }

    /**
     * 指定日のKPI ガチャ毎課金UUを取得する
     *
     * @params datetime $tran_date
     * @return mixed
     */
    public function getKpiGachaUuDataByDateGachaTally ($tran_date)
    {
        $condition = $this->_getConditionKpiGachaUuDataByDateGachaTally ($tran_date);

        $res = $this->getKpiGachaUu ($condition['condition'], $condition['param']);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;
    }

    /**
     * 指定日のKPI ガチャ毎課金UUの件数を取得する
     *
     * @params datetime $tran_date
     * @return mixed
     */
    public function getKpiGachaUuDataByDateGachaTallyCount ($tran_date)
    {
        $condition = $this->_getConditionKpiGachaUuDataByDateGachaTally ($tran_date);

        $res = $this->getKpiGachaUuCount ($condition['condition'], $condition['param']);

        return $res;
    }

    /**
     * 指定期間のKPI アイテム毎課金UUの合計を取得する
     *
     * @param array $search_param
     * @return mixed
     */
    public function getKpiGachaUuListByDateGachaTallyAllSum ($search_param)
    {
        $condition = $this->_getConditionKpiGachaUuListByDateGachaTally($search_param);

        $res = $this->getKpiGachaUuAllSum($condition['condition'], $condition['param']);
        if ($res === false){
            return false;
        }

        $log_data['count'] = count($res);
        $log_data['data'] = $res;

        return $log_data;

    }

    /**
     * KPI アイテム毎課金UUを取得する
     *
     * @param string $where
     * @param array $param
     * @param array $sort
     * @param integer $limit
     * @param integer $offset
     * @return mixed
     */
    public function getKpiGachaUuAll ($where, $param, $sort=null, $limit=null, $offset=null)
    {
        $log_db = $this->backend->getDB('logex_r');
        $order = $this->_createSqlPhraseOrderBy($sort);

        $sql = "select"
             . " DATE_FORMAT(date_gacha_tally, '%Y-%m-%d') as date_gacha_tally,"
             . " DATE_FORMAT(date_gacha_tally, '%a') as date_gacha_tally_day,"
             . " gacha_id,"
             . " gacha_name,"
             . " uu_item_pay,"
             . " uu_item_free,"
             . " uu_item_total,"
             . " gacha_uu_rate"
             . " from kpi_gacha_uu_rate"
             . $where
             . $order;

        return $log_db->GetAll($sql, $param);
    }

    /**
     * KPI アイテム毎課金UUを取得する
     *
     * @param string $where
     * @param array $param
     * @param array $sort
     * @return mixed
     */
    public function getKpiGachaUuAllSum ($where, $param, $sort=null)
    {
        $log_db = $this->backend->getDB('logex_r');
        $order = $this->_createSqlPhraseOrderBy($sort);

        $sql = "select "
             . " gacha_id,"
             . " sum(uu_item_pay) as uu_item_pay,"
             . " sum(uu_item_free) as uu_item_free,"
             . " sum(uu_item_total) as uu_item_total,"
             . " '0' as gacha_uu_rate"
             . " from kpi_gacha_uu_rate"
             . $where
             . " group by gacha_id"
             . $order;

        return $log_db->GetAll($sql, $param);
    }

    /**
     * KPI アイテム毎課金UUの件数を取得する
     *
     * @param string $where
     * @param array $param
     * @return mixed
     */
    public function getKpiGachaUuCount ($where, $param)
    {
        $log_db = $this->backend->getDB('logex_r');
        $sql = "select count(*) from kpi_gacha_uu_rate" . $where;

        return $log_db->GetOne($sql, $param);
    }

    /**
     * 指定期間のKPI ガチャ毎課金UUを指定する(条件編集)
     *
     * @param array $search_param
     * @return array
     */
    private function _getConditionKpiGachaUuListByDateGachaTally ($search_param)
    {
        $where = " where (date_gacha_tally >= ? and date_gacha_tally <= ?)"
               . " and ua = ?";
        $param = array(
            $search_param['date_from'],
            $search_param['date_to'],
            $search_param['ua']
        );

        return array('condition' => $where, 'param' => $param);
    }

    /**
     * 指定日のKPI ガチャ毎課金UUを取得する(条件編集)
     *
     * @params datetime $tran_date
     * @return mixed
     */
    private function _getConditionKpiGachaUuDataByDateGachaTally ($date_gacha_tally)
    {
        $where = " where date_gacha_tally = ?";
        $param = array($date_gacha_tally);

        return array('condition' => $where, 'param' => $param);
    }

    /**
     * ログデータより指定した日付のガチャ別アイテム課金情報を抽出する
     *
     * @param array $param
     * @return mixed
     */
    public function getLogItemDataForKpiGachaUu($search_param)
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
             . " count(distinct gd.user_id) as sum_count"
             . " from (select * from log_item_data"
             . " where item_id = ? "
             . " and processing_type = ?"
             . " and date_log >= ? and date_log <= ? and count != 0) id"
             . " join log_gacha_data gd on gd.api_transaction_id = id.api_transaction_id"
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
     * log_item_dataを集計しDB(kpi_gacha_uu_rate)へ登録する
     *
     * @param array $log_item_data
     * @param string $tran_date
     * @return boolean
     */
    public function editLogItemDataforKpiGachaUuRate ($log_item_data, $tran_date)
    {
		$shop_m =& $this->backend->getManager('Shop');

        $sql = "select * from m_gacha_list where date_start <= ? and date_end >= ? and type in(?, ?, ?, ?)";
//        $param = array($tran_date . ' 23:59:59', $tran_date . ' 00:00:00', '3', '4');
        $param = array($tran_date . ' 23:59:59', $tran_date . ' 00:00:00',
			Pp_ShopManager::GACHA_TYPE_MEDAL,
			Pp_ShopManager::GACHA_TYPE_EVENT,
			Pp_ShopManager::GACHA_TYPE_MEDAL11,
			Pp_ShopManager::GACHA_TYPE_EVENT11
		);
        $res = $this->db_r->GetAll($sql, $param);
        if ( $res === false) {
            return false;
        }

        $total_box = array(
            'uu_item_pay' => 0,
            'uu_item_free' => 0,
            'uu_item_total' => 0,
            'gacha_uu_rate' => 0,
            'date_gacha_tally' => $tran_date,
        );
        foreach($res as $k => $v){
            $total_box['gacha_id'] = $v['gacha_id'];
            $total_box['gacha_name'] = $v['comment'];
            $tmp[$v['gacha_id'] . '_1'] = $total_box; // 1 は Pp_UserManager::OS_IPHONE の意
            $tmp[$v['gacha_id'] . '_2'] = $total_box; // 2 は Pp_UserManager::OS_ANDROID の意
            $tmp[$v['gacha_id']] = $total_box;
            $tmp[$v['gacha_id'] . '_1']['ua'] = 1;
            $tmp[$v['gacha_id'] . '_2']['ua'] = 2;
            $tmp[$v['gacha_id']]['ua'] = 0;
        }

        foreach($log_item_data as $data_k => $data_v){
            $sum_count = $data_v['sum_count'];
            switch($data_v['service_flg']){
            case 0:
                $tmp[$data_v['gacha_id'] . '_' . $data_v['ua']]['uu_item_pay'] = $sum_count;
                $tmp[$data_v['gacha_id']]['uu_item_pay'] += $sum_count;
                break;
            case 1:
                $tmp[$data_v['gacha_id'] . '_' . $data_v['ua']]['uu_item_free'] = $sum_count;
                $tmp[$data_v['gacha_id']]['uu_item_free'] += $sum_count;
                break;
            }
            $tmp[$data_v['gacha_id'] . '_' . $data_v['ua']]['uu_item_total'] += $sum_count;
            $tmp[$data_v['gacha_id']]['uu_item_total'] += $sum_count;

        }

        foreach($tmp as $gacha_k => $gacha_v){
            $data = $gacha_v;
            if ( $gacha_v['uu_item_pay'] != 0 && $gacha_v['uu_item_total'] != 0){
                $data['gacha_uu_rate'] = round((($gacha_v['uu_item_pay'] / $gacha_v['uu_item_total']) * 100) , 2);
            }
            $res = $this->insertKpiGachaUuRate($data);
            if (Ethna::isError($res)) {
                return false;
            }
        }
        return true;
    }

    /**
     * KPI ガチャ毎課金UUを登録する
     *
     * @param data
     * @return mixed
     */
    public function insertKpiGachaUuRate($data)
    {
        $now = date('Y-m-d H:i:s', time());
        $param = array(
            $data['ua'],
            $data['date_gacha_tally'],
            $data['gacha_id'],
            $data['gacha_name'],
            $data['uu_item_pay'],
            $data['uu_item_free'],
            $data['uu_item_total'],
            $data['gacha_uu_rate'],
            $now,
            $now,
        );

        $sql = "INSERT INTO kpi_gacha_uu_rate ("
             . " ua,"
             . " date_gacha_tally,"
             . " gacha_id,"
             . " gacha_name,"
             . " uu_item_pay,"
             . " uu_item_free,"
             . " uu_item_total,"
             . " gacha_uu_rate,"
             . " date_created,"
             . " date_modified"
             . " ) value (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->_executeQuery($sql, $param);

    }

    /**
     *
     *
     *
     */
    public function editDailyViewKpiGachaUuRate($data, $total_data)
    {
        $tmp_date = '';
        $tmp_list = array();
        $view_list = array();
        foreach($data as $k => $v) {
            if($tmp_date != $v['date_gacha_tally']){
                if($tmp_date != ''){
                    $view_list[] = $tmp_list;
                }
                $tmp_date = $v['date_gacha_tally'];
                $tmp_list = array(
                    'date_gacha_tally' =>  $v['date_gacha_tally'],
                    'date_gacha_tally_day' =>  $v['date_gacha_tally_day'],
                );
            }
            $tmp_list['list'][$v['gacha_id']] = $v;

        }
        $view_list[] = $tmp_list;

        // タイトル行のガチャ名を取得
        $name_list = array();
//        foreach($tmp_list['list'] as $k => $v){
        foreach($data as $k => $v){
            //$name_list[$v['gacha_id']] = mb_convert_encoding($v['gacha_name'], 'UTF-8', 'Shift-JIS');
            $name_list[$v['gacha_id']] = $v['gacha_name'];

        }

        // 各ガチャの合計値行を編集
        $tmp_list = array(
            'date_gacha_tally' =>  '合計',
            'date_gacha_tally_day' =>  '',
        );
        foreach($total_data as $k => $v) {
            $tmp_list['list'][$v['gacha_id']] = $v;
        }
        $view_list[] = $tmp_list;

        return array('view_list' => $view_list, 'name_list' => $name_list);

    }

    /**
     * KPI アイテム毎課金UUのCSVファイル作成
     *
     * @param array $kpi_continuance_data
     * @return mixed
     */
    public function createCsvFileKpiGachaUuRate($kpi_uu_data, $kpi_uu_sum_data, $kpi_name_list)
    {
        $log_name = 'kpi_gacha_uu_rate';
        $file_path = KPIDATA_PATH_ITEM_DATA;

		if (!is_dir(KPIDATA_PATH_ITEM_DATA)) {
			mkdir(KPIDATA_PATH_ITEM_DATA, 0777, true); // 後でターミナル上のjugmonユーザで削除できるように、0777にする。
		}

        $rand_num = mt_rand();
        $today_date = date('Ymd', time());
        $file_name = $log_name .'_' . $today_date . '_' . $rand_num;
        if (!$fp=@fopen($file_path . '/' . $file_name, 'a')){
            return false;
        }

        $title_list = array(
            '日付',
            '曜日',
        );
        foreach($kpi_name_list as $name_v){
            $tmp_title = array(
                $name_v . ' 有料メダル使用UU',
                $name_v . ' 無料メダル使用UU',
                $name_v . ' 総メダル使用UU',
                $name_v . ' 課金UU',
            );
            $title_list = array_merge($title_list , $tmp_title);
        }

        $title_str = implode(',', $title_list);
        fwrite($fp, mb_convert_encoding($title_str, "Shift_JIS", "UTF-8") . "\r\n");

        $data_list = array();
        foreach ($kpi_uu_data as $data_k => $data_v){
            $data_list = array(
                $data_v['date_gacha_tally'],
                $data_v['date_gacha_tally_day'],
            );
//            foreach($data_v['list'] as $list_k => $list_v){
			foreach ($kpi_name_list as $name_k => $name_v) {
				if (isset($data_v['list'][$name_k])) {
					$list_v = $data_v['list'][$name_k];
					$tmp_data_list = array(
						$list_v['uu_item_pay'],
						$list_v['uu_item_free'],
						$list_v['uu_item_total'],
						$list_v['gacha_uu_rate'] . '%',
					);
				} else {
					$tmp_data_list = array('', '', '', '');
				}

                $data_list = array_merge($data_list, $tmp_data_list);
            }
            $str = implode(',', $data_list);
            fwrite($fp, mb_convert_encoding($str, "Shift-JIS", "UTF-8") . "\r\n");
        }
        fclose($fp);

        return $file_name;

    }

	public function getCountInGachaRate($search_param)
	{
		return $this->_getCount('kpi_gacha_rate', $search_param);
	}

	public function getListInGachaRate($search_param)
	{
		$sql = "
SELECT
  date_tally
  , gacha_id
  , name
  , count_drop
  , uu_play
  , rate
FROM
  kpi_gacha_rate
WHERE
  1 = 1
";

		return $this->_getList($sql, $search_param);
	}

	public function createCsvInGachaRate($data)
	{
		$csv_title = array(
				'日付',
				'ガチャID',
				'ガチャ名',
				'回転数',
				'利用UU',
				'回転率',
		);
		return parent::createCsv($csv_title, $data, 'kpi_gacha_rate');
	}

	public function getCountInGachaUser($search_param)
	{
		return $this->_getCount('kpi_user_gacha', $search_param);
	}

	public function getListInGachaUser($search_param)
	{
		$sql = "
SELECT
  gacha_id
  , name
  , pp_id
  , sum(count_play) as sum_count_play
FROM
  kpi_user_gacha
WHERE
  1 = 1
{where_cond}
GROUP BY
  gacha_id,
  pp_id
";
		$param = $this->_createCondition($search_param);
		$sql = str_replace('{where_cond}', $param['where'], $sql);

		$db = $this->backend->getDB('logex_r');
		$data = $db->GetAll($sql, $param['param']);

		if (!$data)
		{
			return array();
		}
		else
		{
			return $data;
		}
	}

	public function createCsvInGachaUser($data)
	{
		$csv_title = array(
				'ガチャID',
				'ガチャ名',
				'ユーザID',
				'実行数',
		);
		return parent::createCsv($csv_title, $data, 'kpi_user_gacha');
	}
}
