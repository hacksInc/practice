<?php
/**
 *  Pp_AdminQuestManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_QuestManager.php';
require_once 'Pp_UserManager.php';

/**
 *  Pp_AdminQuestManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminQuestManager extends Pp_QuestManager
{

    private $db_cmn = null;

    function getMasterAreaAll()
    {
        $lang = $this->config->get('lang');
        $sql = "SELECT a.area_id AS id, a.area_id, a.last_area, a.quest_id, a.no, a.name_$lang AS name, a.boss_flag, a.bg_id, a.use_type, a.needful_stamina, a.battle_num, a.exp, a.attack_seven, a.attack_bar, a.date_start, a.date_end"
            . " FROM m_area a, m_quest q"
            . " WHERE a.quest_id = q.quest_id"
            . " ORDER BY a.area_id";

        return $this->db_r->db->GetAssoc($sql);
    }

    function getMasterAreaType($type)
    {
        $lang = $this->config->get('lang');
        $param = array($type);
        $sql = "SELECT a.area_id AS id, a.area_id, a.last_area, a.quest_id, a.no, a.name_$lang AS name, a.boss_flag, a.bg_id, a.use_type, a.needful_stamina, a.battle_num, a.exp, a.attack_seven, a.attack_bar, a.date_start, a.date_end"
            . " FROM m_area a, m_quest q"
            . " WHERE a.quest_id = q.quest_id AND q.type = ?"
            . " ORDER BY a.area_id";

        return $this->db_r->db->GetAssoc($sql, $param);
    }

    function getMasterQuestAll()
    {
        $lang = $this->config->get('lang');
        $sql = "SELECT q.quest_id AS id, q.quest_id, q.map_id, q.map_no, q.name_$lang AS name,"
            . " q.type, q.no, q.effect_type, q.effect_value, q.date_start, q.date_end, q.time_start, q.time_end, q.week_element"
            . ", kind, comment_event, comment_baloon"
            . " FROM m_quest q"
            . " ORDER BY q.quest_id";
        return $this->db_r->db->GetAssoc($sql);
    }

    function getMasterQuestType($type)
    {
        $lang = $this->config->get('lang');
        $param = array($type);
        $sql = "SELECT q.quest_id AS id, q.quest_id, q.map_id, q.map_no, q.name_$lang AS name,"
            . " q.type, q.no, q.effect_type, q.effect_value, q.date_start, q.date_end, q.time_start, q.time_end, q.week_element"
            . ", kind, comment_event, comment_baloon"
            . " FROM m_quest q"
            . " WHERE q.type = ?"
            . " ORDER BY q.quest_id";
        return $this->db_r->db->GetAssoc($sql, $param);
    }

    function proceedUserArea($user_id, $goal_area_id)
    {
        $user_m = $this->backend->getManager('User');
        $area_normal_all = $this->getMasterAreaType(self::QUEST_TYPE_NORMAL);
        $user_base = $user_m->getUserBase($user_id);
        $user_base_new = array();
        $user_base_new['exp'] = $user_base['exp'];
        foreach($area_normal_all as $key => $val) {
            $area_id = $val['area_id'];
            //エリアのクリアデータを取得
            $user_area = $this->getUserArea($user_id, $area_id);
            //クリア済みでなければクリア状態とする
            if ($user_area['status'] != self::QUEST_STATUS_CLEAR) {
                // エリアのステータスを更新
                $ret = $this->setUserArea($user_id, $area_id, self::QUEST_STATUS_CLEAR);
                //経験値
                $user_base_new['exp'] += $val['exp'];
                if ($user_base_new['exp'] > Pp_UserManager::PLAYER_MAX_EXP)
                    $user_base_new['exp'] = Pp_UserManager::PLAYER_MAX_EXP;
            }
            if ($val['area_id'] == $goal_area_id) break;
        }
        //現在のエリアIDを更新する
        $user_area_assoc_now = $this->getUserAreaAssocEx($user_id, self::QUEST_TYPE_NORMAL);
        end($user_area_assoc_now);//配列の最後
        $now_area_id = key($user_area_assoc_now);//最後のキー
        $now_area = $this->getMasterArea($now_area_id);
        $user_base_new['area_id'] = $now_area_id;
        $user_base_new['quest_id'] = $now_area['quest_id'];
        //
        $rankup_data = $user_m->checkUserRankUp($user_id, $user_base_new['exp'], $user_base['rank']);
        if ($rankup_data !== false){
            $user_base_new = array_merge($user_base_new, $rankup_data);
        }
        $ret = $user_m->setUserBase($user_id, $user_base_new);
        if (!$ret || Ethna::isError($ret)) {
            return $ret;
        }
        return true;
    }

    function getMasterQuestAreaAll()
    {
        $lang = $this->config->get('lang');
        $sql = "SELECT a.area_id AS id, q.map_id, q.quest_id, q.name_$lang AS qname,  a.area_id, a.last_area, a.quest_id, a.no, a.name_$lang AS aname, a.boss_flag, a.bg_id, a.use_type, a.needful_stamina, a.battle_num, a.exp, a.attack_seven, a.attack_bar, a.date_start, a.date_end"
            . " FROM m_area a, m_quest q"
            . " WHERE a.quest_id = q.quest_id"
            . " ORDER BY a.area_id";

        return $this->db_r->db->GetAssoc($sql);
    }

    function getMasterQuestAreaQuestId($quest_id)
    {
        $lang = $this->config->get('lang');
        $param = array(
            $quest_id
        );
        $sql = "SELECT a.area_id AS id, q.map_id, q.quest_id, q.name_$lang AS qname,  a.area_id, a.last_area, a.no, a.name_$lang AS aname, a.boss_flag, a.bg_id, a.use_type, a.needful_stamina, a.battle_num, a.exp, a.attack_seven, a.attack_bar, a.date_start, a.date_end"
            . " FROM m_area a, m_quest q"
            . " WHERE a.quest_id = q.quest_id AND q.quest_id=?"
            . " ORDER BY a.area_id";
        return $this->db_r->db->GetAssoc($sql,$param);
    }

    function beginTransaction() {
        if ($this->db_cmn) $this->db_cmn->begin();
    }

    function commitTransaction() {
        if ($this->db_cmn) $this->db_cmn->commit();
    }

    function rollbackTransaction() {
        if ($this->db_cmn) $this->db_cmn->rollback();
    }


    /**
     * バッチ処理用
     * 古いTMPテーブルの削除
     * @return mixed
     */
    private function _refreshTmpHelperUsers() {

        $delSql = "DELETE FROM tmp_helper_users";
        if (!$this->db_cmn->execute($delSql)) {
            Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                $this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
        }
    }

    /**
     * バッチ処理用
     * 複数ユニットからフレンドの一覧を取得する
     * @return mixed
     */
    private function _getUsersRank() {

        $config = $this->backend->config->get('helper_config');
        $unixTimestamp = $config['last_login_date'];

        $param = array(
            date('Y-m-d H:i:s', $unixTimestamp)
        );

        $sql = "SELECT b.user_id, b.rank "
            . " FROM t_user_base b, t_user_team t"
            . " WHERE b.user_id = t.user_id"
            . " AND b.active_team_id = t.team_id"
            . " AND t.leader_flg = 1"
            . " AND t.user_monster_id > 0"
            . " AND b.access_ctrl != ". Pp_UserManager::USER_ACCCESS_DENY
            . " AND b.tutorial >= 10"
            . " AND b.login_date >= ?";

        $unit_m = $this->backend->getManager('Unit');
        return $unit_m->GetAllMultiUnit($sql, $param, NULL, true);
    }


    /**
     * バッチ処理用
     * 助っ人一覧をt_user_baseから作成する
     */
    public function makeHelperOthersListBatch() {

        if (!$this->db_cmn) {
            $this->db_cmn =& $this->backend->getDB('cmn');
        }

        $this->beginTransaction();

        $rows = $this->_getUsersRank();
        if ($rows && Ethna::isError($rows)) {
            return $rows;
        }

        $this->_refreshTmpHelperUsers();

        // tmpのテーブルにinsert
        $BASE_INSSQL = "INSERT INTO tmp_helper_users (user_id, rank, date_created) values";
        $insSql =  $BASE_INSSQL;

        $count = count($rows);

        $i = 0;
        foreach ($rows as $row) {
            $count--;
            if ($i != 0) $insSql .= ", ";
            $insSql .= "(". $row['user_id']. ", ". $row['rank']. ", ". "now())";
            if (($i != 0 && $i % 10000 === 0) || $count == 0) { // bulk_insert_size×スレッド数に達しないように
                if (!$this->db_cmn->execute($insSql)) {
                    return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                        $this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
                }
                $insSql = $BASE_INSSQL;
                $i = 0;
                continue;
            }
            $i++;
        }
    }

}
