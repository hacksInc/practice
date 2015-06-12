<?php
/**
 *  Pp_KpiViewMonsterManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'array_column.php';
require_once 'Pp_KpiViewManager.php';

/**
 *  Pp_KpiViewMonsterManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_KpiViewMonsterManager extends Pp_KpiViewManager
{
	/**
	 * DB接続(pp-ini.phpの'dsn_logex'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_logex = null;
	
	/**
	 * DB接続(pp-ini.phpの'dsn_logex_r'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_logex_r = null;
	
	/**
	 * モンスター使用率KPIデータを作成する
	 * 
	 * 一部の項目については、引数で指定した日時に関わらず、この関数を実行する時点の情報になるので注意。
	 * @param string $date_monster_tally 集計対象日(Y-m-d H:i:s) 時刻は0時0分0秒のみ指定可 通常の日次バッチの処理では昨日を指定すればOK
	 * @return bool 成否
	 */
	function makeKpiMonsterUse($date_monster_tally)
	{
		// 各種リソース初期化
		if (!$this->db_logex) {
			$this->db_logex =& $this->backend->getDB('logex');
		}
		
		if (!$this->db_logex_r) {
			$this->db_logex_r =& $this->backend->getDB('logex_r');
		}

		// 引数チェック
		$unixtime = strtotime($date_monster_tally);
		if (!$unixtime || 
			(date('H', $unixtime) != 0) ||
			(date('i', $unixtime) != 0) ||
			(date('s', $unixtime) != 0)
		) {
			error_log(sprintf("Invalid date_monster_tally[%s]", $date_monster_tally));
			return false;
		}

		// 集計実行
		$ret1 = $this->makeTmpQuestTeamTally($date_monster_tally);
		$ret2 = $this->makeKpiMasterMonster($date_monster_tally);
		$ret3 = $this->makeKpiUserMonster($date_monster_tally);
		$ret4 = $this->makeKpiQuestStartMonster($date_monster_tally);

		$all_ok = (($ret1 === true) && ($ret2 === true) && ($ret3 === true) && ($ret4 === true));
		if (!$all_ok) {
			error_log("makeKpiMonsterUse failed.");
			return false;
		}
		
		return true;
	}
	
	/**
	 * クエストチーム集計一時データを作成する
	 * 
	 * この関数を実行後、getMaxIdFromDateTallyEnd関数を用いることで、一時データを取得できる。
	 * @param string $date_monster_tally 集計対象日(Y-m-d H:i:s) 注意点はmakeKpiMonsterUse関数と同じ
	 * @return bool 成否
	 */
	protected function makeTmpQuestTeamTally($date_tally_end)
	{
		// 前日より過去の最大ID（一昨日の最後のID）を求める
		$date_prev = date('Y-m-d H:i:s', strtotime($date_tally_end) - 86400);
		$max_id_prev = $this->getMaxIdFromDateTallyEnd($date_prev);
		
		// 当日より過去の最大ID（昨日の最後のID）を求める
		$param = array();
		$sql = "SELECT MAX(id) FROM log_quest_team_data WHERE";
		if (is_numeric($max_id_prev) && $max_id_prev) {
			$param[] = $max_id_prev;
			$sql .= " id > ? AND";
		}
		
		$param[] = $date_tally_end;
		$sql .= " date_log < ?"; 
		
		$max_id = $this->db_logex_r->GetOne($sql, $param);
		if ($max_id === false) {
			error_log(sprintf("makeTmpQuestTeamTally failed on select. max_id_prev=[%d]", $max_id_prev));
			return false;
		}

		if (!$max_id) {
			return true;
		}
		
		// 記録する
		$columns = array(
			'date_tally_end' => $date_tally_end,
			'max_id'         => $max_id,
		);
		$ret = $this->db_logex->db->AutoExecute('tmp_quest_team_tally', $columns, 'INSERT');
		if ($ret === false) {
			error_log(sprintf("makeTmpQuestTeamTally failed on insert. max_id=[%d]", $max_id));
		}
		
		return $ret;
	}
	
	/**
	 * 集計対象期間終了日時から最大管理IDを取得する
	 * 
	 * あらかじめmakeTmpQuestTeamTally関数で求めておいた値を取得する。
	 * @param string $date_tally_end 集計対象期間終了日時(Y-m-d H:i:s)
	 * @return int|false 最大管理ID（エラー時はfalse）
	 */
	protected function getMaxIdFromDateTallyEnd($date_tally_end)
	{
		$param = array($date_tally_end);
		$sql = "SELECT max_id FROM tmp_quest_team_tally WHERE date_tally_end = ?";
		
		return $this->db_logex->GetOne($sql, $param);
	}
	
	/**
	 * KPI_モンスターマスタを作成する
	 * 
	 * @param string $date_monster_tally 集計対象日(Y-m-d H:i:s) 注意点はmakeKpiMonsterUse関数と同じ
	 * @return bool 成否
	 */
	protected function makeKpiMasterMonster($date_monster_tally)
	{
		$monster_m = $this->backend->getManager('Monster');
		
		$all_ok = true;
		$all = $monster_m->getMasterMonsterAssoc();
		foreach ($all as $monster_id => $columns) {
			$columns = array(
				'monster_id'         => $monster_id,
				'monster_name'       => $columns['name_ja'],
				'm_rare'             => $columns['m_rare'],
				'monster_type_id'    => $columns['monster_type_id'],
				'tribe'              => $columns['tribe'],
				'date_monster_tally' => $date_monster_tally,
			);
			
			$ret = $this->db_logex->db->AutoExecute('kpi_master_monster', $columns, 'INSERT');
			if ($ret === false) {
				$all_ok = false;
				error_log(sprintf("makeKpiMasterMonster failed on insert. monster_id=[%d]", $monster_id));
			}
		}
		
		return $all_ok;
	}
	
	/**
	 * KPI_所持モンスターを作成する
	 * 
	 * 引数で指定した日時に関わらず、この関数を実行する時点の情報になるので注意。
	 * @param string $date_monster_tally 集計対象日(Y-m-d H:i:s) 注意点はmakeKpiMonsterUse関数と同じ
	 * @return bool 成否
	 */
	protected function makeKpiUserMonster($date_monster_tally)
	{
		// 集計する
		$sql = "SELECT monster_id, "
		     . " AVG(lv) AS lv_avg,"
		     . " AVG(skill_lv) AS skill_lv_avg,"
		     . " COUNT(*) AS current_count"
			 . " FROM t_user_monster"
			 . " GROUP BY monster_id";
		$result =& $this->db_r->query($sql);
		if (Ethna::isError($result)) {
			Ethna::raiseError('エラー CODE[%d] MESSAGE[%s]', E_DB_QUERY, $this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg());
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $this->db_r->db->ErrorMsg());
			return false;
		}

		$all_ok = true;
		while ($row = $result->FetchRow()) {
			$columns = array(
				'monster_id'         => $row['monster_id'],
				'lv_avg'             => $row['lv_avg'],
				'skill_lv_avg'       => $row['skill_lv_avg'],
				'current_count'      => $row['current_count'],
				'date_monster_tally' => $date_monster_tally,
			);
			
			// 集計結果をモンスターIDごとに記録する
			$ret = $this->db_logex->db->AutoExecute('kpi_user_monster', $columns, 'INSERT');
			if ($ret === false) {
				$all_ok = false;
				error_log(sprintf("makeKpiUserMonster failed on insert. monster_id=[%d]", $row['monster_id']));
			}
		}
		
		return $all_ok;
	}
	
	/**
	 * KPI_クエスト開始モンスターを作成する
	 * 
	 * @param string $date_monster_tally 集計対象日(Y-m-d H:i:s) 注意点はmakeKpiMonsterUse関数と同じ
	 * @return bool 成否
	 */
	protected function makeKpiQuestStartMonster($date_monster_tally)
	{
		$date_log_start = $date_monster_tally;
		$date_log_end = date('Y-m-d H:i:s', strtotime($date_log_start) + 86400);
		
		// 集計対象日より過去の、管理IDの最大値を求める
		$max_id = $this->getMaxIdFromDateTallyEnd($date_monster_tally);
		if (!$max_id) {
			$max_id = 0;
		}
		
		// 上記で求めた管理IDとdate_logの範囲内で集計を行なう
		$param = array($max_id, $date_log_start, $date_log_end);
		$sql = "SELECT monster_id, leader_flg, COUNT(*) AS cnt"
			 . " FROM log_quest_team_data"
			 . " WHERE id > ?"
			 . " AND ? <= date_log AND date_log < ?"
			 . " GROUP BY monster_id, leader_flg"
		     . " ORDER BY monster_id, leader_flg";
		$result =& $this->db_logex_r->query($sql, $param);
		if (Ethna::isError($result)) {
			Ethna::raiseError('エラー CODE[%d] MESSAGE[%s]', E_DB_QUERY, $this->db_logex_r->db->ErrorNo(), $this->db_logex_r->db->ErrorMsg());
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $this->db_logex_r->db->ErrorMsg());
			return false;
		}

        $logdata_m = $this->backend->getManager('Logdata');
		$map = array(
			Pp_LogDataManager::LOG_QUEST_TEAM_LEADER_FLG_MEMBER => 'member_count',
			Pp_LogDataManager::LOG_QUEST_TEAM_LEADER_FLG_LEADER => 'leader_count',
			Pp_LogDataManager::LOG_QUEST_TEAM_LEADER_FLG_HELPER => 'helper_count',
		);

		$all_ok = true;
		$columns = null;
		$monster_id_prev = null;
		while ($row = $result->FetchRow()) {
			$monster_id = $row['monster_id'];
			
			if ($monster_id_prev != $monster_id) {
				if ($monster_id_prev) {
					// 集計結果をモンスターIDごとに記録する
					$ret = $this->db_logex->db->AutoExecute('kpi_quest_start_monster', $columns, 'INSERT');
					if ($ret === false) {
						$all_ok = false;
						error_log(sprintf("makeKpiQuestStartMonster failed on insert. monster_id=[%d]", $columns['monster_id']));
					}
				}
				
				$columns = array(
					'monster_id'         => $monster_id,
					'date_monster_tally' => $date_monster_tally,
				);
			}
			
			$colname = $map[$row['leader_flg']];
			$columns[$colname] = $row['cnt'];
			
			$monster_id_prev = $monster_id;
		}
		
		if ($columns) {
			// 集計結果をモンスターIDごとに記録する
			$ret = $this->db_logex->db->AutoExecute('kpi_quest_start_monster', $columns, 'INSERT');
			if ($ret === false) {
				$all_ok = false;
				error_log(sprintf("makeKpiQuestStartMonster failed on insert. monster_id=[%d]", $columns['monster_id']));
			}
		}
		
		return $all_ok;
	}
	
	/**
	 * モンスター使用率を表形式で取得する
	 * 
	 * 戻り値の書式について：
	 * ・ヘッダ行とデータ部がつながった配列。（連想配列ではない）
	 * ・Util::assembleCSVへ渡すとCSVに変換できる。
	 * @param int $monster_id モンスターマスタID
	 * @param string $date_from 取得対象とする集計日の開始時点（端点含む）
	 * @param string $date_to 取得対象とする集計日の終了時点（端点含まない）
	 * @return array モンスター使用率情報（1行目はヘッダ部、2行目以降がデータ部）
	 */
	function getKpiMonsterUseTable($monster_id, $date_from, $date_to)
	{
		if (!$this->db_logex_r) {
			$this->db_logex_r =& $this->backend->getDB('logex_r');
		}

		$info = array(
			// SELECT用カラム名, 表示用ラベル, 合計算出対象か
			array("DATE_FORMAT(u.date_monster_tally, '%Y-%m-%d')", '日付', false),
			array('u.lv_avg',        'ベースLv平均',       false),
			array('u.skill_lv_avg',  'スキルLv平均',       false),
			array('u.current_count', '流通量',             false),
			array('q.leader_count',  'リーダー使用のべ数', true),
			array('q.member_count',  'メンバー使用のべ数', true),
			array('q.helper_count',  'ヘルプ使用のべ数',   true),
		);
		
		$names       = array_column($info, 0);
		$labels      = array_column($info, 1);
		$sum_targets = array_column($info, 2);
		
		$param = array($monster_id, $date_from, $date_to);
		$sql = "SELECT " . implode(',', $names)
		     . " FROM kpi_user_monster u"
		     . " LEFT JOIN kpi_quest_start_monster q"
		     . " ON u.monster_id = q.monster_id"
		     . " AND u.date_monster_tally = q.date_monster_tally"
		     . " WHERE u.monster_id = ?"
		     . " AND u.date_monster_tally >= ?"
		     . " AND u.date_monster_tally < ?"
		     . " ORDER BY u.date_monster_tally";
		
		$old_mode = $this->db_logex_r->db->SetFetchMode(ADODB_FETCH_NUM);
		$data = $this->db_logex_r->db->GetAll($sql, $param);
		$this->db_logex_r->db->SetFetchMode($old_mode);

		$footer = array('合計');
		$col_cnt = count($sum_targets);
		for ($i = 1; $i < $col_cnt; $i++) {
			if ($sum_targets[$i]) {
				$footer[$i] = array_sum(array_column($data, $i));
			} else {
				$footer[$i] = '';
			}
		}
		
		$table = array_merge(array($labels), $data, array($footer));
 
		return $table;
	}
	
	/**
	 * KPI用モンスターマスタ情報を表形式で取得する
	 * 
	 * 戻り値の書式について：
	 * ・ヘッダ行とデータ部がつながった配列。（連想配列ではない）
	 * ・データ部は、引数で指定された日付以降で最も早い日付の1行
	 * ・Util::assembleCSVへ渡すとCSVに変換できる。
	 * @param int $monster_id モンスターマスタID
	 * @param string $date_from 取得対象とする集計日の開始時点（端点含む）
	 * @return array モンスターマスタ情報（1行目はヘッダ部、2行目がデータ部）
	 */
	function getKpiMonsterMasterTable($monster_id, $date_from)
	{
		if (!$this->db_logex_r) {
			$this->db_logex_r =& $this->backend->getDB('logex_r');
		}
		
        $monster_m = $this->backend->getManager('AdminMonster');
		
		$table = array(array(
			'モンスターID', 'モンスター名', 'レアリティ', 'タイプ', '種族',
		));
		
		$param = array($monster_id, $date_from);
		$sql = "SELECT monster_name, m_rare, monster_type_id, tribe"
		     . " FROM kpi_master_monster"
		     . " WHERE monster_id = ?"
		     . " AND date_monster_tally >= ?"
		     . " ORDER BY date_monster_tally"
		     . " LIMIT 1";
		
		if ($row = $this->db_logex_r->GetRow($sql, $param)) {
			$table[] = array(
				$monster_id,
				$row['monster_name'],
				$row['m_rare'],
				$monster_m->getMonsterTypeName($row['monster_type_id']),
				$monster_m->getTribeName($row['tribe']),
			);
		}
		
		return $table;
	}
	
    /**
     * KPI モンスター使用率のCSVファイル作成
     *
     * @param array $kpi_monster_use モンスター使用率KPIデータ （getKpiMonsterUseTable関数の戻り値と同じ書式で）
     * @param array $kpi_monster_master KPI用モンスターマスタ情報 （getKpiMonsterMasterTable関数の戻り値と同じ書式で）
     * @return string|bool 出力先ファイル名 （失敗時はfalse）
     */
    public function createCsvFileKpiMonsterUse($kpi_monster_use, $kpi_monster_master = null)
    {
		// ファイル出力準備
        $log_name = 'kpi_monster_use';
        $file_path = KPIDATA_PATH_MONSTER_DATA;

        $rand_num = mt_rand();
        $today_date = date('Ymd', time());
        $file_name = $log_name .'_' . $today_date . '_' . $rand_num;
		
		// CSV組み立て
		$table = array();
		if ($kpi_monster_master) {
			$table = array_merge($kpi_monster_master, array());
		}
		
		$table = array_merge($table, $kpi_monster_use);
		
		$csv = Util::assembleCsv($table);

		// ファイル出力
		$old_umask = umask(0000); // 後でターミナル上のjugmonユーザで削除できるように、0000にする。
		if (!is_dir(KPIDATA_PATH_MONSTER_DATA)) {
			mkdir(KPIDATA_PATH_MONSTER_DATA, 0777, true); // 後でターミナル上のjugmonユーザで削除できるように、0777にする。
		}

        if (!file_put_contents($file_path . '/' . $file_name, $csv)){
            return false;
        }

		umask($old_umask);
		
        return $file_name;
    }
	
}
?>
