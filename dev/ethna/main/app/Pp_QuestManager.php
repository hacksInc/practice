<?php
/**
 *  Pp_QuestManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'array_column.php';

/**
 *  Pp_QuestManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_QuestManager extends Ethna_AppManager
{
	/** クエストタイプ：ノーマル */
	const QUEST_TYPE_NORMAL = 1;
	/** クエストタイプ：イベント */
	const QUEST_TYPE_EVENT = 2;

	//曜日・時間限定で以下の効果が発動する（ウィークリー以外）
	const TIMED_TYPE_DROP_BOX       = 1;//宝箱ドロップ率　○倍
	const TIMED_TYPE_PLAYER_EXP     = 2;//獲得経験値　　○倍
	const TIMED_TYPE_DROP_ENEMY     = 3;//敵ドロップ率　○倍
	const TIMED_TYPE_GOLD           = 4;//獲得メダル　　○倍
	const TIMED_TYPE_STAMINA        = 5;//スタミナ変更　○倍

	/** クエスト状態：無し */
	const QUEST_STATUS_NONE = 0;

	/** クエスト状態：開始 */
	const QUEST_STATUS_START = 1;

	/** クエスト状態：クリア */
	const QUEST_STATUS_CLEAR = 2;
	
	/** ドロップ種別：ドロップ無し */
	const DROP_TYPE_NONE = 0;
	
	/** ドロップ種別：モンスター卵 */
	const DROP_TYPE_MONSTER = 1;
	
	/** ドロップ種別：鍵 */
	const DROP_TYPE_KEY = 2;
	
	/** ドロップ種別：宝箱 */
	const DROP_TYPE_BOX = 3;
	
	/** ドロップ種別：バッジ */
	const DROP_TYPE_BADGE = 4;
	
	/** ドロップ種別：素材 */
	const DROP_TYPE_MATERIAL = 5;
	
	/** 友達以外の助っ人抽出条件とする最終ログイン時間の下位範囲（秒） */
//	const HELPER_OTHERS_LOGIN_DATE_LOWER_RANGE = 50400; // 14時間は50400秒
	const HELPER_OTHERS_LOGIN_DATE_LOWER_RANGE = 21600; //  6時間は21600秒
	
	/** 友達の助っ人抽出条件とする最終ログイン時間の下位範囲（秒） */
	const HELPER_FRIEND_LOGIN_DATE_LOWER_RANGE = 259200; // 24時間×3日は259200秒
	
	/** 友達以外の助っ人抽出条件とするランクの下位範囲 */
	const HELPER_OTHERS_RANK_LOWER_RANGE = 5;

	/** 友達以外の助っ人抽出条件とするランクの上位範囲 */
	const HELPER_OTHERS_RANK_UPPER_RANGE = 5;
	
	/** 友達以外の助っ人の最大抽出数 */
	const HELPER_OTHERS_MAX_NUM = 12;
	
	/** 宝箱ドロップ時のマスタ設定 */
//	const DROP_BOX_GOLD = 1; //ゴールド
//	const DROP_BOX_KEY  = 2; //鍵
	const DROP_BOX_TICKET_BRONZE = 1; //ブロンズチケット
	const DROP_BOX_TICKET_GOLD   = 2; //ゴールドチケット
	
	/** クエストに入る際に消費するもの */
	const USE_TYPE_STAMINA    = 1; //スタミナ
	
	/** ボーナスタイプ */
	const BONUS_TYPE_BB = 1; //ビッグボーナス
	const BONUS_TYPE_RB = 2; //レギュラーボーナス
	
	/** デフォルトのボーナス敵グループ */
	const BONUS_GROUP_DEFAULT = 9100; 
	const BONUS_GROUP_TUTORIAL10 = 7100; 
	const BONUS_GROUP_TUTORIAL11 = 7200; 
	const BONUS_GROUP_TUTORIAL12 = 7300; 
	
	/** 夢幻遊戯のエリアID */
	const MUGEN_AREA_ID_MIN = 10400099; 
	const MUGEN_AREA_ID_MAX = 10400101; 
	
	/**
	 * エリアマスター情報（クエストタイプとエリアIDがキー）
	 * @var array $master_area_assoc[クエストタイプ][エリアID] = m_areaのレコード情報
	 */
	protected $master_area_assoc = array();
	
	/**
	 * クエストマスター情報（クエストタイプとクエストIDがキー）
	 * @var array $master_quest_assoc[クエストタイプ][クエストID] = m_questのレコード情報
	 */
	protected $master_quest_assoc = array();
	
	/**
	 * マップマスター情報（マップIDがキー）
	 * @var array $master_map_assoc[マップID] = m_mapのレコード情報
	 */
	protected $master_map_assoc = array();
	
	/**
	 * DB接続(pp-ini.phpの'dsn_cmn_r'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn_r = null;
	
	/**
	 * マップマスター情報一覧を取得する
	 * 
	 * @return array
	 */
	function getMasterMapAssoc()
	{
		$this->loadMasterMapAssoc();
		return $this->master_map_assoc;
	}
	
	protected function loadMasterMapAssoc()
	{
		$lang = $this->config->get('lang');
		$sql = "SELECT m.map_id AS id, m.name_$lang AS name" 
			 . " FROM m_map m"
			 . " ORDER BY map_id";
		$this->master_map_assoc = $this->db_r->db->GetAssoc($sql);
	}

	
	/**
	 * ユーザーのエリア情報一覧を付加情報つきで取得する
	 * 
	 * 未クリアだが選択可能なエリアも含む
	 * @param int $user_id
	 * @param int $quest_type
	 * @return array 
	 */
	function getUserAreaAssocEx($user_id, $quest_type)
	{
		// 引数のquest_typeによって処理が変わる
		if ($quest_type == self::QUEST_TYPE_NORMAL) {
			// ノーマルクエストの場合
			// ユーザの進行状態を参照して、それまでクリアしたもの全てと、クリアしていないものの中でIDが一番小さいもの１つを取得し、アプリに渡す
			$master_area_assoc = $this->getMasterAreaAssoc($quest_type);
			$user_area_assoc = $this->getUserAreaAssoc($user_id, $quest_type);
			$user_area_assoc_ex = array();
			$uncleared_min_flg = false;
			foreach ($master_area_assoc as $area_id => $row) {
				if (isset($user_area_assoc[$area_id])) {
					$status = $user_area_assoc[$area_id]['status'];
				} else {
					$status = self::QUEST_STATUS_NONE;
				}

				$row['status'] = $status;
				
				if ($status == self::QUEST_STATUS_CLEAR) {
					$user_area_assoc_ex[$area_id] = $row;
				} else if (!$uncleared_min_flg) {
					$user_area_assoc_ex[$area_id] = $row;
					$uncleared_min_flg = true;
				}
			}
			
			return $user_area_assoc_ex;
			
		} else {
			// ノーマルクエスト以外の場合
			// ユーザの進行状態とは関係なく、期間限定などの条件によって現在開放されているものを全て取得し、アプリに渡す
			// 曜日限定など特殊な条件の場合はマスタにカラムを追加して判定できるようにする
			// （クエストマスタを読み出すloadMasterQuestAssocで行なっている）
			$master_area_assoc = $this->getMasterAreaAssoc($quest_type);
			$user_area_assoc = $this->getUserAreaAssoc($user_id, $quest_type);
			$user_area_assoc_ex = array();
			$uncleared_min_flg = false;
			foreach ($master_area_assoc as $area_id => $row) {
				if (isset($user_area_assoc[$area_id])) {
					$status = $user_area_assoc[$area_id]['status'];
				} else {
					$status = self::QUEST_STATUS_NONE;
				}
				$row['status'] = $status;
				$user_area_assoc_ex[$area_id] = $row;
			}
			
			return $user_area_assoc_ex;
		}
	}

	/**
	 * ユーザーのエリア情報一覧を取得する
	 * 
	 * DBに履歴が記録されている分のみ
	 * @param int $user_id
	 * @return array
	 */
	protected function getUserAreaAssoc($user_id)
	{
//TODO マスターデータと同様にキャッシュ        
		$param = array($user_id);
		$sql = "SELECT t.area_id AS id, t.* FROM t_user_area t"
			 . " WHERE user_id = ?"
			 . " ORDER BY area_id";
		
		return $this->db->db->GetAssoc($sql, $param);
	}
	
	/**
	 * ユーザーのエリア情報を1件取得する
	 * 
	 * @param int $user_id
	 * @param int $area_id
	 * @return array
	 */
	protected function getUserArea($user_id, $area_id)
	{
		$param = array($user_id, $area_id);
		$sql = "SELECT * FROM t_user_area"
		     . " WHERE user_id =  ?"
		     . " AND area_id = ?";
		
		return $this->db->GetRow($sql, $param);
	}
	
	/**
	 * ユーザーのエリア情報をセットする
	 * 
	 * @param int $user_id
	 * @param int $area_id
	 * @param int $status
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function setUserArea($user_id, $area_id, $status)
	{
		$row = $this->getUserArea($user_id, $area_id);
		
		// 必要な操作を判別
		if (!$row) {
			$param = array($user_id, $area_id, $status);
			$sql = "INSERT INTO t_user_area(user_id, area_id, status, date_created)"
				 . " VALUES(?, ?, ?, NOW())";
		} else if ($status == self::QUEST_STATUS_CLEAR && $row['status'] != self::QUEST_STATUS_CLEAR) {
			$param = array($status, $user_id, $area_id);
			$sql = "UPDATE t_user_area SET status = ?"
				 . " WHERE user_id = ? AND area_id = ?";
		} else {
			// 何もしないでOK
			return true;
		}

		// SQLクエリ実行
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("setUserArea rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		return true;
	}

	/**
	 * エリアマスター情報一覧を取得する
	 * 
	 * @param int $quest_type
	 * @return array
	 */
	function getMasterAreaAssoc($quest_type)
	{
		$this->loadMasterAreaAssoc($quest_type);

		return $this->master_area_assoc[$quest_type];
	}

	/**
	 * エリアマスター情報を取得する
	 * 
	 * @param int $area_id
	 * @return array エリアマスター情報1件の連想配列
	 */
	function getMasterArea($area_id)
	{
		static $pool = array();
		
		if (!isset($pool[$area_id])) {
			$param = array($area_id);
			$sql = "SELECT * FROM m_area WHERE area_id = ?";
			$pool[$area_id] = $this->db_r->GetRow($sql, $param);
		}
		
		return $pool[$area_id];
	}

	/**
	 * API応答用のエリアマスター情報を取得する
	 * 
	 * エリアマスター情報の内、API応答時に必要な情報のみを取得する
	 * @param int $user_id
	 * @return array API応答用のエリアマスター情報1件の連想配列
	 */
	function getMasterAreaForApiResponse($area_id)
	{
		$area = $this->getMasterArea($area_id);

		$compact = array();
		if (is_array($area)) {
//			foreach (array('name_ja', 'name_en', 'name_es', 
//				'boss_flag', 'bg_id', 'use_type', 'needful_stamina', 'battle_num', 
//				'key_drop', 'box_drop', 'portion1_drop', 'portion2_drop', 'exp', 'attack_seven', 'attack_bar'
//			) as $name) {
//				$compact[$name] = $area[$name];
//			}
			// 抽出するキー一覧
			$select_keys = array(
					'name_ja' => null,
					'name_en' => null,
					'name_es' => null,
					'boss_flag' => null,
					'bg_id' => null,
					'use_type' => null,
					'needful_stamina' => null,
					'battle_num' => null,
					'key_drop' => null,
					'box_drop' => null,
					'portion1_drop' => null,
					'portion2_drop' => null,
					'exp' => null,
					'attack_seven' => null,
					'attack_bar' => null
			);

			// 対応キーの抽出
			$compact = array_intersect_key($area, $select_keys);
		}
		
		return $compact;
	}
	
	/**
	 * エリアボス係数マスター情報を取得する
	 * 
	 * @param int $area_id
	 * @return array エリアボス係数マスター情報1件の連想配列
	 */
	function getMasterAreaBossCoefficient($area_id)
	{
		static $pool = array();
		
		if (!isset($pool[$area_id])) {
			$param = array($area_id);
			$sql = "SELECT * FROM m_area_boss_coefficient WHERE area_id = ?";
			$pool[$area_id] = $this->db_r->GetRow($sql, $param);
		}
		
		return $pool[$area_id];
	}
	
	/**
	 * エリアボス討伐情報を取得する
	 */
	function getUserAreaBoss($user_id, $area_id)
	{
		$param = array($user_id, $area_id);
		$sql = "SELECT * FROM t_user_area_boss"
			 . " WHERE user_id = ? AND area_id = ?";
		
		return $this->db->GetRow($sql, $param);
	}
	
	/**
	 * エリアボス討伐情報をセットする
	 * 
	 * @param int $user_id
	 * @param int $area_id
	 * @param int $boss_lv
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクトまたはfalse
	 */
	protected function setUserAreaBoss($user_id, $area_id, $boss_lv)
	{
		$row = $this->getUserAreaBoss($user_id, $area_id);
		
		// 必要な操作を判別
		if (!$row) {
			$param = array($user_id, $area_id, $boss_lv);
			$sql = "INSERT INTO t_user_area_boss(user_id, area_id, boss_lv, date_created)"
				 . " VALUES(?, ?, ?, NOW())";
		} else {
			$param = array($boss_lv, $user_id, $area_id);
			$sql = "UPDATE t_user_area_boss SET boss_lv = ?"
				 . " WHERE user_id = ? AND area_id = ?";
		}

		// SQLクエリ実行
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("setUserAreaBoss rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		return true;
	}

	/**
	 * クエストマスター情報一覧を取得する
	 * 
	 * @param int $quest_type
	 * @return array
	 */
	function getMasterQuestAssoc($quest_type)
	{
		$this->loadMasterQuestAssoc($quest_type);

		return $this->master_quest_assoc[$quest_type];
	}
	
	/**
	 * クエスト敵マスタ情報を付加情報付きで取得する
	 * 
	 * 敵IDをキーとする、m_monsterテーブルの一部情報も付加した連想配列を取得できる
	 * @param int $quest_id
	 * @return array $array[enemy_id][カラム名] = カラム値
	 */
	function getMasterQuestEnemyAssocEx($quest_id)
	{
		static $pool = array();
		
		if (!isset($pool[$quest_id])) {
			$param = array($quest_id);
			$sql = "SELECT qe.enemy_id, qe.*, m.attribute_id, m.drop_monster_id, m.attack_turn FROM m_quest_enemy qe, m_monster m WHERE qe.monster_id = m.monster_id AND qe.quest_id = ?";
			$pool[$quest_id] = $this->db_r->db->GetAssoc($sql, $param);
		}
		
		return $pool[$quest_id];
	}
	
	/**
	 * クエスト敵マスタ情報を1件、付加情報つきで取得する
	 * 
	 * @param int $quest_id
	 * @param int $enemy_id
	 * @return array $array[カラム名] = カラム値
	 */
	function getMasterQuestEnemyEx($quest_id, $enemy_id)
	{
		$assoc = $this->getMasterQuestEnemyAssocEx($quest_id);
		if (isset($assoc[$enemy_id])) {
			return $assoc[$enemy_id];
		}
	}

	/**
	 * クエストマスター情報を取得する
	 * 
	 * @param int $quest_id
	 * @return array クエストマスター情報1件の連想配列
	 */
	function getMasterQuest($quest_id)
	{
		static $pool = array();
		
		if (!isset($pool[$quest_id])) {
			$param = array($quest_id);
			$sql = "SELECT * FROM m_quest WHERE quest_id = ?";
			$pool[$quest_id] = $this->db_r->GetRow($sql, $param);
		}
		
		return $pool[$quest_id];
	}

    /**
     * マップマスター情報を取得する
     * 
     * @param int $quest_id
     * @return array クエストマスター情報1件の連想配列
     */
    function getMasterMap($map_id)
    {
        $param = array($map_id);
        $sql = "SELECT * FROM m_map WHERE map_id = ?";
        return $this->db_r->GetRow($sql, $param);
    }

	/**
	 * エリアクリア済みか判定する
	 */
	function isAreaCleared($user_id, $area_id)
	{
		$param = array($user_id, $area_id, self::QUEST_STATUS_CLEAR);
		$sql = "SELECT COUNT(*) AS cnt WHERE t_user_area"
			 . " WHERE user_id = ? AND area_id = ? AND status = ?";
		$cnt = $this->db->GetOne($sql, $param);

		return ($cnt > 0);
	}
	
	/**
	 * 助っ人モンスターをテンポラリテーブルにセットする
	 * 
	 * @param int $user_id ユーザID（助っ人呼び出し元）
	 * @param int $helper_user_monster_id モンスターユニークID
	 * @param int $helper_user_id ユーザID（助っ人側）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setTmpHelperUserMonster($user_id, $helper_user_monster_id, $helper_user_id)
	{
		$param = array($user_id, $helper_user_monster_id, $helper_user_id);
		$sql = "INSERT INTO tmp_helper_user_monster("
			 . " user_id, helper_user_monster_id, helper_user_id,"
			 . " monster_id, exp, lv, hp_plus, attack_plus, heal_plus, skill_lv, date_created)"
			 . " SELECT ?, user_monster_id, user_id,"
			 . " monster_id, exp, lv, hp_plus, attack_plus, heal_plus, skill_lv, NOW()"
			 . " FROM t_user_monster"
			 . " WHERE user_monster_id = ? AND user_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("setTmpHelperUserMonster rows[%d]", E_USER_ERROR, $affected_rows);
		}
				
		return true;
	}
	function setTmpHelperUserMonsterData($user_id, $helper_user_monster_id, $helper_user_id, $columns)
	{
		$param = array($user_id, $helper_user_monster_id, $helper_user_id,
				$columns['monster_id'], $columns['exp'], $columns['lv'], $columns['hp_plus'], $columns['attack_plus'], $columns['heal_plus'], $columns['skill_lv']
		);
		$sql = "INSERT INTO tmp_helper_user_monster("
			 . " user_id, helper_user_monster_id, helper_user_id,"
			 . " monster_id, exp, lv, hp_plus, attack_plus, heal_plus, skill_lv, date_created)"
			 . " VALUES (?, ?, ?,"
			 . " ?, ?, ?, ?, ?, ?, ?, NOW())";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("setTmpHelperUserMonster rows[%d]", E_USER_ERROR, $affected_rows);
		}
				
		return true;
	}
	
	/**
	 * 助っ人モンスターをテンポラリテーブルから取得する
	 * 
	 * @param int $user_id ユーザID（助っ人呼び出し元）
	 * @return bool|object 成功時:このテーブルの連想配列
	 */
	function getTmpHelperUserMonster($user_id)
	{
		$param = array($user_id);
		$sql = "SELECT * FROM tmp_helper_user_monster WHERE user_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return $this->db->GetRow($sql, $param);
	}
	
	/**
	 * 助っ人モンスターをテンポラリテーブルから削除する
	 * 
	 * @param int $user_id ユーザID（助っ人呼び出し元）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function deleteTmpHelperUserMonster($user_id)
	{
		$param = array($user_id);
		$sql = "DELETE FROM tmp_helper_user_monster WHERE user_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
//		$affected_rows = $this->db->db->affected_rows();
//		if ($affected_rows != 1) {
//			return Ethna::raiseError("deleteTmpHelperUserMonster rows[%d]", E_USER_ERROR, $affected_rows);
//		}
		return true;
	}

	/**
	 * 十分なスタミナがあるか？
	 * 
	 * クエスト開始時にチェックが必要
	 * 残り体力が必要体力の分だけ持っているかチェックし、足りなければエラー
	 * 必要体力はm_areaから参照する
	 * @param int $user_id
	 * @param int $area_id
	 * @return bool|object ある:true, ない:false, その他エラー:Ethna_Errorオブジェクト
	 */
	function hasEnoughStamina($user_id, $area_id)
	{
		$master_area = $this->getMasterArea($area_id);
		$user_base = $this->backend->getManager('User')->getUserBase($user_id);

		if (!$master_area) {
			return Ethna::raiseError("user_id[%d] area_id[%d]", E_USER_ERROR, $user_id, $area_id);
		} else if (Ethna::isError($master_area)) {
			return $master_area;
		}

		if (!$user_base) {
			return Ethna::raiseError("user_id[%d] area_id[%d]", E_USER_ERROR, $user_id, $area_id);
		} else if (Ethna::isError($user_base)) {
			return $user_base;
		}

		$needful_stamina = $master_area['needful_stamina'];
		$master_quest = $this->getMasterQuest($master_area['quest_id']);
		//時限効果がスタミナ減だったら
		if ($master_quest['effect_type'] == self::TIMED_TYPE_STAMINA) {
			$needful_stamina = ceil(($master_area['needful_stamina'] * $master_quest['effect_value']) / 100);//切り上げ
		}

		$this->backend->logger->log(LOG_INFO,
				'needful_stamina:' . $needful_stamina . " stamina:" . $user_base['stamina']);
		
		return ($needful_stamina <= $user_base['stamina']);
	}

	/**
	 * ボーナス敵マスタ情報を付加情報付きで取得する
	 * 
	 * 敵IDをキーとする、m_monsterテーブルの一部情報も付加した連想配列を取得できる
	 * @param int $group_id
	 * @return array $array[enemy_id][カラム名] = カラム値
	 */
	function getMasterBonusEnemy($group_id)
	{
		static $pool = array();
		
		if (!isset($pool[$group_id])) {
			$param = array($group_id);
			$sql = "SELECT be.*, m.attribute_id, m.drop_monster_id, m.attack_turn FROM m_bonus_enemy be, m_monster m WHERE be.monster_id = m.monster_id AND be.group_id = ? ORDER BY be.enemy_id";
			$pool[$group_id] = $this->db_r->db->GetAll($sql, $param);
		}
		
		return $pool[$group_id];
	}
	
	/**
	 * クエスト敵マスタ情報を1件、付加情報つきで取得する
	 * 
	 * @param int $group_id
	 * @param int $enemy_id
	 * @return array $array[カラム名] = カラム値
	 */
	function getMasterBonusEnemyEx($group_id, $enemy_id)
	{
		$assoc = $this->getMasterBonusEnemy($group_id);
		if (isset($assoc[$enemy_id])) {
			return $assoc[$enemy_id];
		}
	}



	/**
	 * クエスト開始する
	 * 
	 * @param int $user_id
	 * @param int $area_id
	 * @param string $play_id
	 * @return array $array['quest_battle']     = API戻り値'quest_battle'として使うための情報
	 *                     ['drop_enemy']       = API戻り値'drop_enemy'として使うための情報
	 *                     ['drop_boss_normal'] = API戻り値'drop_boss_normal'として使うための情報
	 *                     ['drop_boss_over']   = API戻り値'drop_boss_over'として使うための情報
	 */
	function start($user_id, $area_id, $play_id)
	{
		$master_area = $this->getMasterArea($area_id);

		$area_result = array(
			'quest_battle' => array(),
			'drop_enemy' => array(),
			'drop_boss_normal' => array(),
			'drop_boss_over' => array(),
		);
		
		//3.エリアマスタ（m_area）からバトル回数（battle_num）を取得し以下の処理をバトル回数分繰り返す
		//  ※最後はボスなので処理が分かれる
		$battle_num = $master_area['battle_num'];

		//今回のクエスト情報を取得
		$quest_id = $master_area['quest_id'];
		$master_quest = $this->getMasterQuest($quest_id);

		//クエストリストを取得する
		$quest_list = $this->getMasterQuestAssoc($master_quest['type']);
		foreach($quest_list as $val) {
			if ($val['quest_id'] == $quest_id) {
				$master_quest_data = $val;
				break;
			}
		}
		//クエストが有効でない場合
		if (!isset($master_quest_data)) {
			error_log("Invalid quest_id=$quest_id area_id=$area_id");
			return false;
		}

		//曜日を取得する（クエスト開始時の曜日が基準）
		//$week_element = date('w', $_SERVER['REQUEST_TIME']);//曜日 0 (日曜)から 6 (土曜)
		//if ($week_element == 0) $week_element = 6;
		$effect_type = $master_quest['effect_type'];//$master_quest_dataから変更
		$effect_value = $master_quest['effect_value'];
	//error_log("effect_type  = $effect_type");
	//error_log("effect_value = $effect_value");

		//4.m_battleからエリアIDをキーに敵の出現数を取得
		//  その際、既にクリア済みのエリアだったらm_battle.flag=1のデータがあるかどうか取得してみて、あればそれを使う
		//  なかったらm_battle.flag=0のデータを使う
		//  クリア済でなければm_battle.flag=0
		//  ※クリア済だったらm_battle.flag<=1で1件取得でも良いかも
	//	$is_area_cleared = $this->isAreaCleared($user_id, $area_id);
	//	$flag = $is_area_cleared ? 1 : 0;
	//クリア済みかどうかで判定せず、必ずflag=0として参照する
		$flag = 0;
		for ($no = 1; $no <= $battle_num; $no++) {
			$master_battle = $this->getMasterBattleByFlag($area_id, $no, $flag);
			if (strlen($master_battle['enemy_ids']) > 0) {
				//6.敵を指定して出現させたいバトルの場合は抽選は行わない
				//  敵を指定したい場合はm_battle.enemy_idsにカンマ区切りの文字列（数字）が入っている
				//  指定しない場合はm_battle.enemy_idsはnullになっている
				$enemy_id_list = explode(',', $master_battle['enemy_ids']);
				//！追加仕様！
				//  出現数指定が1で$enemy_id_listが複数ある場合はm_battle.enemy_probabilityに入っている確率から抽選する
				if ($master_battle['num'] == 1 && count($enemy_id_list) > 1) {
					//確率が入っているなら
					if (strlen($master_battle['enemy_probability']) > 0) {
						$enemy_probability_list = explode(',', $master_battle['enemy_probability']);
						$pip = mt_rand(0, 99);
						$threshold = 0;
						foreach ($enemy_probability_list as $key => $row) {
							$threshold += $enemy_probability_list[$key];
							if ($pip >= $threshold) {
								continue;
							}
							$enemy_id = $enemy_id_list[$key];
							break;
						}
					} else {
						//確率が入っていなければデータミスなので先頭の敵IDをセットする
						$enemy_id = $enemy_id_list[0];
					}
					$enemy_id_list = array($enemy_id);//敵１体で上書き
				}
			} else {
				//5.エリア毎のモンスター出現確率から敵の出現数の分だけ抽選する
				//  抽選の際にはモンスターの大きさも考慮する
				//  出現確率はm_area_enemyからエリアIDをキー全件取得する
				$enemy_id_list = $this->getEnemyIdListRand($area_id, $master_battle['num'], $quest_id);
				if (!$enemy_id_list || Ethna::isError($enemy_id_list)) {
					error_log("enemy_id_list=".print_r($enemy_id_list,true));
					return $enemy_id_list;
				}
			}
			$battle_result = $this->startBattle($area_id, $enemy_id_list, $no, $user_id, $effect_type, $effect_value, $master_quest['type']);
			if (!$battle_result || Ethna::isError($battle_result)) {
				error_log("battle_result=".print_r($battle_result,true));
				return $battle_result;
			}
			
			foreach (array_keys($area_result) as $key) {
				if (count($battle_result[$key]) > 0) {
					$area_result[$key] = array_merge($area_result[$key], $battle_result[$key]);
				}
			}
		}
		//ボーナス戦
		$bonus_result = $this->bonusBattle($area_id, $effect_type, $effect_value, $master_quest['type'], $user_id);
		foreach (array_keys($bonus_result) as $key) {
			if (count($bonus_result[$key]) > 0) {
				$area_result[$key] = $bonus_result[$key];
			}
		}

		//19.t_user_baseから必要な体力を減らして保存
		//   クエストの種類によっては体力ではなくチケットの場合もある
		//   体力消費した時間も記録する
		//   時間から計算して体力回復させる処理は
		//   t_user_baseを参照する処理の中に追加する
		//スタミナ消費
		if ($master_area['use_type'] == self::USE_TYPE_STAMINA) {
			$needful_stamina = $master_area['needful_stamina'];
			//時限効果がスタミナ減だったら
			if ($effect_type == self::TIMED_TYPE_STAMINA) {
				$needful_stamina = ceil(($master_area['needful_stamina'] * $effect_value) / 100);//切り上げ
			}
			$ret = $this->backend->getManager('User')->setUserBaseDiff($user_id, 
					array(
						'stamina' => (-1 * $needful_stamina),
		//				'date_stamina_update' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),//ToDo:必要だと思うけど、とりあえずコメントアウト
					)
				);
			if (!$ret || Ethna::isError($ret)) {
				error_log("start err stamina ret=$ret");
				return $ret;
			}
		}

		//敵にインデックス番号を割り振る
		foreach ($area_result['quest_battle'] as $key => $val) {
			$area_result['quest_battle'][$key]['enemy_idx'] = $key;
		}
		$area_result['play_id'] = $play_id;
		$area_result['effect'] = $effect_type;
		$area_result['effectval'] = $effect_value;
		$area_result['needful_stamina'] = $needful_stamina;
		$area_result['battle_num'] = $battle_num;
		
		// Api/Quest/Startに移しました
		//21.今回のクエストで発生するドロップアイテムのデータをDBに保持しておく
		// 既に存在していれば削除する
		//$this->deleteTmpUserQuestStart($user_id);
		//$ret = $this->setTmpUserQuestStart($user_id, $area_result);
		//if (!$ret || Ethna::isError($ret)) {
		//	error_log("start err set tmpuserqueststart ret=$ret");
		//	return $ret;
		//}

		//エリアのクリアデータを取得
		$user_area = $this->getUserArea($user_id, $area_id);
		//データが無かったらスタート状態にする
		if ($user_area == NULL) {
			// エリアのステータスを更新
			$ret = $this->setUserArea($user_id, $area_id, self::QUEST_STATUS_START);
			if (!$ret || Ethna::isError($ret)) {
				error_log("start err setuserarea ret=$ret");
				return $ret;
			}
		}

		return $area_result;
	}

	/**
	 * クエストをクリアする
	 * 
	 * @param int $user_id
	 * @param int $area_id エリアID
	 * @param int $quest_id クエストID
	 * @param int $play_id プレイID
	 * @param int $medal 獲得メダル数（未使用）
	 * @param array $overkill オーバーキル情報 array(array(idx,status),array(idx,status), …)idx毎にstatus（0:通常 1:オーバーキル）が設定されている
	 * @param type $bikkuri ビックリ玉取得個数	アプリ側で条件判定する
	 * @param array $drop_medal 獲得した進化用メダル1～5の各枚数	配列
	 * @param int $drop_gold 獲得したゴールドの枚数
	 * @param array $monster_list 出現させたモンスター一覧	助っ人の分も一緒に送ってもらえると助かる
	 * @param int $bonus_type ボーナスタイプ 1:ビッグボーナス 2:レギュラーボーナス 0:なし
	 * @param int $bonus_cd ボーナスチェックコード
	 * @param array $bonus_overkill オーバーキル情報 array(array(idx,status),array(idx,status), …)idx毎にstatus（0:通常 1:オーバーキル）が設定されている
	 * @param int $tutorial アプリから送られたチュートリアル値 送られてなければ0
	 * @param array $append_monster_list オーバーキル時の追加モンスター一覧（最大２件まで）
	 * @return array $array['get_exp']   = API戻り値'get_exp'として使うための情報
	 *                      ['drop_data'] = API戻り値'drop_data'として使うための情報
	 */
	function clear($user_id, $area_id, $quest_id, $play_id, $medal, $overkill, $bikkuri, $drop_medal, $drop_gold, $monster_list, $bonus_type, $bonus_cd, $bonus_overkill, $tutorial, $append_monster_list=null)
	{
		$user_m = $this->backend->getManager('User');
		$monster_m = $this->backend->getManager('Monster');
		$item_m = $this->backend->getManager('Item');

		// 更新するユーザ基本情報
		$user_base_new = array();

		// モンスター図鑑が増えたかのフラグ
		$user_monster_book_flg = false;

		//2.m_areaからエリア名や獲得経験値を取得	
		$master_area = $this->getMasterArea($area_id);

	//	if ($quest_id != $master_area['quest_id']) {
	//	//	$this->af->setApp('status_detail_code', SDC_QUEST_ERROR, true);
	//		return Ethna::raiseError("user_id[%d] area_id[%d] quest_id[%d]", E_USER_ERROR, $user_id, $area_id, $quest_id);
	//	}
		
		$tracking_columns = array('area_id' => $area_id, 'quest_id' => $quest_id);

		//3.開始前に保存しておいたドロップアイテムのデータを取得し
		//  アプリから渡されたオーバーキルのデータからドロップアイテムを確定させる
		$start_content = $this->getTmpUserQuestStart($user_id);
		//プレイIDのチェック
	//	if ($play_id != $start_content['play_id']) {
	//	//	$this->af->setApp('status_detail_code', SDC_QUEST_PLAYID_INVALID, true);
	//		return Ethna::raiseError("play_id invalid user_id[%d]", E_USER_ERROR, $user_id);
	//	}
		
		//今回のクエスト情報を取得
		$master_quest = $this->getMasterQuest($quest_id);
		//時限効果を取得
		$effect_type = $start_content['effect'];
		$effect_value = $start_content['effectval'];
		
		$drop_result = array(); // drop_boss_normal,drop_boss_overの内で確定した分と、drop_enemyをあわせた内容
		$quest_battle = $start_content['quest_battle'];
		foreach ($quest_battle as $key => $val) {
			$idx = $quest_battle[$key]['enemy_idx'];
			$over = -1;
			foreach($overkill as $ovkey => $ovval) {
				if ($overkill[$ovkey]['idx'] == $idx) {
					$over = $overkill[$ovkey]['status'];
					break;
				}
			}
			$drop_data = array();
			//オーバーキル
			if ($over == 1) {
				$drop_data['drop_type']       = $quest_battle[$key]['over_drop_type'];
				$drop_data['drop_monster_id'] = $quest_battle[$key]['over_drop_monster_id'];
				$drop_data['monster_drop_lv'] = $quest_battle[$key]['over_monster_drop_lv'];
			} else {
				$drop_data['drop_type']       = $quest_battle[$key]['normal_drop_type'];
				$drop_data['drop_monster_id'] = $quest_battle[$key]['normal_drop_monster_id'];
				$drop_data['monster_drop_lv'] = $quest_battle[$key]['normal_monster_drop_lv'];
			}
			if ($drop_data['drop_type'] != 0) $drop_result[] = $drop_data;
		}

		//4.獲得経験値をt_user_baseの経験値に加算する
		//  獲得ゴールドや獲得進化メダルも加算する
		//  経験値からランクアップの確認を行い、次のランクアップに必要な経験値も計算する
		//  ランクアップ時には体力を最大まで回復させる
		//  ---
		//  獲得経験値はm_areaのexp（エリア固定）
		//
		$gold_probability = 100;//１倍
		//時限効果でメダルの払い出し枚数を変える
		if ($effect_type == self::TIMED_TYPE_GOLD) $gold_probability = $effect_value;
		
		$user_base = $user_m->getUserBase($user_id);
		//エラー
		if ($user_base === false) {
		//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
			return 'error_500';
		}
		//存在しない
		if (count($user_base) == 0) {
		//	$this->af->setApp('status_detail_code', SDC_USER_NONEXISTENCE, true);
			return 'error_500';
		}
		
		//渡ってきたチュートリアルの値が不正
		if ($tutorial > 0) {
			if ($user_base['tutorial'] >= $tutorial) {
				return 'error_'.SDC_QUEST_CLEAR_ERROR;
			} else {
				$user_base_new['tutorial'] = $tutorial;
			}
		}
		
		$exp_probability = 100;//１倍
		//時限効果で経験値を変える
		if ($effect_type == self::TIMED_TYPE_PLAYER_EXP) $exp_probability = $effect_value;
		$get_exp = floor(($master_area['exp'] * $exp_probability) / 100);
		$user_base_new['exp'] = $user_base['exp'] + $get_exp;
		//最大経験値を超えていたら
		if ($user_base_new['exp'] > Pp_UserManager::PLAYER_MAX_EXP) {
			$user_base_new['exp'] = Pp_UserManager::PLAYER_MAX_EXP;
		//	$get_exp = $user_base_new['exp'] - $user_base['exp'];
		}
		$user_base_new['gold'] = $user_base['gold'] + floor(($drop_gold * $gold_probability) / 100);
		//上限チェック
		if ($user_base_new['gold'] > 999999999) $user_base_new['gold'] = 999999999;
	//	$user_base_new['medal'] = $user_base['medal'] + $medal;
		
		/*仕様落ち
		//進化用メダルの付与（５種類分）
		$medal_idx = 0;
		foreach($drop_medal as $value) {
			if ($value > 0 && (Pp_ItemManager::ITEM_RARE_MEDAL1 + $medal_idx) <= Pp_ItemManager::ITEM_RARE_MEDAL5) {
				//進化用メダルを増やす
				$ret = $item_m->addUserItemUpperLimit($user_id, Pp_ItemManager::ITEM_RARE_MEDAL1 + $medal_idx, $value, 
					$tracking_columns
				);
				if (!$ret || Ethna::isError($ret)) {
				//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
					return $ret;
				}
			}
			$medal_idx++;
		}
		*/
		
		//5.今回出現したモンスターのリストをアプリから送ってもらい、
		//  初めて出会ったモンスターは図鑑に登録する
		foreach ($monster_list as $tmp_id) {
			$ret = $monster_m->setUserMonsterBookVar($user_id, $tmp_id, Pp_MonsterManager::BOOK_STATUS_MET);
			if (!$ret || Ethna::isError($ret)) {
			//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
				return $ret;
			}
			
			$user_monster_book_flg = true;
		}
		
		//ボーナス用データ判定
		//error_log("bonus_type=$bonus_type");
		//error_log("bonus_cd=$bonus_cd");
		//error_log("bonus_overkill=".print_r($bonus_overkill,true));
		if ($bonus_type > 0) {
			if ($bonus_type == self::BONUS_TYPE_BB) {
				if ($start_content['bonus_flag']['bb'] != 1) {
					error_log("bonus_type=$bonus_type not enter BB");
					return 'error_'.SDC_QUEST_BONUS_TYPE_ERROR;
				}
			}
			if ($bonus_type == self::BONUS_TYPE_RB) {
				if ($start_content['bonus_flag']['rb'] != 1) {
					error_log("bonus_type=$bonus_type not enter RB");
					return 'error_'.SDC_QUEST_BONUS_TYPE_ERROR;
				}
			}
			if ($bonus_cd!=0 && $start_content['bonus_flag']['cd'] != $bonus_cd) {
				error_log("bonus_code error");
				return 'error_'.SDC_QUEST_BONUS_CODE_ERROR;
			}
			$bonus_battle = $start_content['bonus_battle'];
			
			foreach ($bonus_battle as $key => $val) {
				$idx = $bonus_battle[$key]['enemy_idx'];
				$over = -1;
				foreach($bonus_overkill as $ovkey => $ovval) {
					if ($bonus_overkill[$ovkey]['idx'] == $idx) {
						$over = $bonus_overkill[$ovkey]['status'];
						break;
					}
				}
				$drop_data = array();
				//オーバーキル
				if ($over == 1) {
					$drop_data['drop_type']       = $bonus_battle[$key]['over_drop_type'];
					$drop_data['drop_monster_id'] = $bonus_battle[$key]['over_drop_monster_id'];
					$drop_data['monster_drop_lv'] = $bonus_battle[$key]['over_monster_drop_lv'];
				} else {
					$drop_data['drop_type']       = $bonus_battle[$key]['normal_drop_type'];
					$drop_data['drop_monster_id'] = $bonus_battle[$key]['normal_drop_monster_id'];
					$drop_data['monster_drop_lv'] = $bonus_battle[$key]['normal_monster_drop_lv'];
				}
				//error_log("bonus_drop=".print_r($drop_data,true));
				if ($drop_data['drop_type'] != 0) $drop_result[] = $drop_data;
			}
		}
		/*
		//オーバーキル時の追加ドロップモンスター（最大２件まで）
		//今後、実装あるかも（動作未確認）
		error_log("append_monster_list=".print_r($append_monster_list,true));
		if (count($append_monster_list) > 0 && count($append_monster_list) <= 2) {
			foreach($append_monster_list as $val) {
				$drop_data = array();
				$drop_data['drop_type']       = self::DROP_TYPE_MONSTER;
				$drop_data['drop_monster_id'] = $val;
				$drop_data['monster_drop_lv'] = 1;
				$drop_result[] = $drop_data;
			}
		}
		*/
		//6.ドロップアイテムのデータからドロップアイテムしたモンスターや鍵、宝箱（ゴールド）を加える
		//  初めて入手モンスターは図鑑に登録する
		//  ---
		//  ドロップしたモンスター卵のmonster_idはm_monsterのdrop_monster_idを参照する 
        $get_monster = '';
        $get_item = '';
		foreach ($drop_result as $key => $row) {
			$drop_result[$key]['user_monster_id'] = 0;
			switch ($row['drop_type']) {
				case self::DROP_TYPE_MONSTER:
					//$ret = $this->createUserMonsterByEnemyId($user_id, $quest_id, $row['enemy_id']);
					$ret = $this->backend->getManager('Monster')->createUserMonster(
						$user_id, $row['drop_monster_id'], array('lv' => $row['monster_drop_lv']),
						$tracking_columns
					);
					if (!$ret || Ethna::isError($ret)) {
					//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
						return $ret;
					}
					$ret['drop_type'] = $row['drop_type'];
                    $get_monster[] = $ret;
                    $drop_result[$key]['user_monster_id'] = $ret['user_monster_id'];
					//モンスター図鑑を更新
					$ret = $monster_m->setUserMonsterBookVar($user_id, $row['drop_monster_id'], Pp_MonsterManager::BOOK_STATUS_GOT);
					if (!$ret || Ethna::isError($ret)) {
					//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
						return $ret;
					}
					$user_monster_book_flg = true;
                    break;
				case self::DROP_TYPE_BOX:
					//宝箱の場合はチケットを増やす
					if ($row['drop_monster_id'] == self::DROP_BOX_TICKET_BRONZE) {
						//ブロンズチケットを1個増やす
						$ret = $item_m->addUserItemUpperLimit($user_id, Pp_ItemManager::ITEM_TICKET_GACHA_FREE, 1,
							$tracking_columns,
							true
						);
						if (!$ret || Ethna::isError($ret)) {
						//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
							return $ret;
						}
                        $tmp_get_item['item_id'] = Pp_ItemManager::ITEM_TICKET_GACHA_FREE;
                        $tmp_get_item['number'] = 1;
                        $get_item[] = $tmp_get_item;
					}
					if ($row['drop_monster_id'] == self::DROP_BOX_TICKET_GOLD) {
						//ゴールドチケットを1個増やす
						$ret = $item_m->addUserItemUpperLimit($user_id, Pp_ItemManager::ITEM_TICKET_GACHA_RARE, 1,
							$tracking_columns,
							true
						);
						if (!$ret || Ethna::isError($ret)) {
						//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
							return $ret;
						}
                        $tmp_get_item['item_id'] = Pp_ItemManager::ITEM_TICKET_GACHA_RARE;
                        $tmp_get_item['number'] = 1;
                        $get_item[] = $tmp_get_item;
					}
				default:
					break;
			}
		}
		$clear_bonus = 0;
		$clear_present = '';
		//エリアのクリアデータを取得
		$user_area = $this->getUserArea($user_id, $area_id);
		//クリア済みでなければクリア状態とする
		if ($user_area['status'] != self::QUEST_STATUS_CLEAR) {
			// エリアのステータスを更新
			$ret = $this->setUserArea($user_id, $area_id, self::QUEST_STATUS_CLEAR);
			if (!$ret || Ethna::isError($ret)) {
			//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
				return $ret;
			}
			if ($master_area['last_area'] == 1) {
				$clear_bonus = 1;
				$present_m = $this->backend->getManager('Present');
				//プレゼントのデータをセット
				$present = array(
							'user_id_to'   => $user_id,
							'comment_id'   => Pp_PresentManager::COMMENT_QUESTCLEAR,
							'comment'      => '',
							'type'         => Pp_PresentManager::TYPE_MAGICAL_MEDAL,
							'item_id'      => 0,
							'lv'           => 0,
							'badge_expand' => 0,
							'badges'       => '',
							'lv'           => 0,
							'number'       => 50,
						);
				//プレゼントを贈る
				$ret = $present_m->setUserPresent(Pp_PresentManager::PPID_FROM_ADMIN, Pp_PresentManager::ID_NEW_PRESENT, $present);
				if (!$ret || Ethna::isError($ret)) {
				//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
					return $ret;
				}
				$clear_present = array_merge($present, array('present_id' => $ret));
			}
		}
		
		//クリアしたエリアIDが新しかったら更新する
		if ($master_quest['type'] == self::QUEST_TYPE_NORMAL) {
			$user_area_assoc_now = $this->getUserAreaAssocEx($user_id, self::QUEST_TYPE_NORMAL);
			end($user_area_assoc_now);//配列の最後
			$now_area_id = key($user_area_assoc_now);//最後のキー
			//error_log("now_area_id=$now_area_id");
			//error_log("old_area_id=".$user_base['area_id']);
			//新しいarea_idが現在の値より大きかったら更新する
			if ($user_base['area_id'] < $now_area_id) {
				$now_area = $this->getMasterArea($now_area_id);
				$user_base_new['area_id'] = $now_area_id;
				$user_base_new['quest_id'] = $now_area['quest_id'];
			}
		}
        //8.t_user_baseを保存する
        $rankup_data = $user_m->checkUserRankUp($user_id, $user_base_new['exp'], $user_base['rank']);
        if ($rankup_data !== false){
            $user_base_new = array_merge($user_base_new, $rankup_data);
        }
        $ret = $user_m->setUserBase($user_id, $user_base_new);

		if (!$ret || Ethna::isError($ret)) {
		//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
			return $ret;
		}
		//  t_user_areaに今回クリアしたクエストを保存する
		$ret = $this->deleteTmpUserQuestStart($user_id);
		if (!$ret || Ethna::isError($ret)) {
		//	$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
			return $ret;
		}
		
		// モンスター図鑑に追加があったらDBに反映する
		if ($user_monster_book_flg) {
			$ret = $monster_m->saveUserMonsterBookBits($user_id);
			if (!$ret || Ethna::isError($ret)) {
				return $ret;
			}
		}
		
		// KPI
		if ($user_base_new['gold'] > $user_base['gold']) {
			$user_m->setUserBaseIncreaseKpi($user_id, 'gold', $user_base_new['gold'] - $user_base['gold']);
		}
				
        return array(
            'get_exp' => $get_exp,
            'drop_data' => $drop_result,
            'clear_bonus' => $clear_bonus,
            'clear_present' => $clear_present,
            'get_monster' => $get_monster,
            'get_item' => $get_item,
            'rankup_data' => $rankup_data,
        );
	}

	/**
	 * クエストをゲームオーバーにする
	 * 
	 * @param int $user_id
	 * @param int $area_id エリアID
	 * @param int $quest_id クエストID
	 * @param array $monster_list 出現させたモンスター一覧	ゲームオーバーになるまでに出現したモンスター 助っ人の分も一緒に送ってもらえると助かる
	 * @return bool|object 成功時:true, 失敗時:falseまたはEthna_Error
	 */
	function gameover($user_id, $area_id, $quest_id, $monster_list, $delTmpUsr = true)
	{
		$monster_m = $this->backend->getManager('Monster');

		//5.今回出現したモンスターのリストをアプリから送ってもらい、
		//  初めて出会ったモンスターは図鑑に登録する
		foreach ($monster_list as $tmp_id) {
			$ret = $monster_m->setUserMonsterBookVar($user_id, $tmp_id, Pp_MonsterManager::BOOK_STATUS_MET);
			if (!$ret || Ethna::isError($ret)) {
				return $ret;
			}
		}
		
		if (!empty($monster_list)) {
			$ret = $monster_m->saveUserMonsterBookBits($user_id);
			if (!$ret || Ethna::isError($ret)) {
				return $ret;
			}
		}
		
		if ($delTmpUsr) {
			//3.開始前に保存しておいたドロップアイテムのデータを削除	
			$ret = $this->deleteTmpUserQuestStart($user_id);
			if (!$ret || Ethna::isError($ret)) {
				return $ret;
			}
		}

		return true;
	}

	/**
     * 旅人の一覧を一時保持テーブルからランダムに取得する
     * 取得件数が０件の場合は、ユーザーのランクに近いユーザーからMAX件数分取得する
     */
    function getHelperList($user_id) {

        if (!$this->db_cmn_r) {
            $this->db_cmn_r =& $this->backend->getDB('cmn_r');
        }

        $config = $this->backend->config->get('helper_config');
        $MAX_RANK = intval($config['helper_rank_range_max']);
        $MIN_RANK = intval($config['helper_rank_range_min']);

        $user_base = $this->backend->getManager('User')->getUserBase($user_id);

        $friend_m = $this->backend->getManager('Friend');
        $friend_list = $friend_m->getFriendList($user_id, Pp_FriendManager::STATUS_FRIEND);
        $friend_id_list = array_column($friend_list, 'friend_id');

        $lowRank = intval($user_base['rank']) - $MIN_RANK;
        $highRank = intval($user_base['rank']) + $MAX_RANK;

        $param = array(
            $lowRank < 0 ? 0: $lowRank,
            $highRank,
            $user_id
        );

        if (count($friend_id_list) > 0) {
            $param = array_merge($param, $friend_id_list);
        }

        $sql = "SELECT t.user_id"
            . " FROM tmp_helper_users t, "
            . " (SELECT id FROM tmp_helper_users "
            . " WHERE rank BETWEEN ? AND ? AND user_id NOT IN ( "
            . " ?" . str_repeat(",?", count($friend_id_list)) . ")"
            . " ORDER BY RAND() "
            . " LIMIT 0, ". self::HELPER_OTHERS_MAX_NUM. ") tmp "
            . " WHERE tmp.id = t.id ";

        $helper_id_list = $this->db_cmn_r->getCol($sql, $param);

        if (Ethna::isError($helper_id_list)) {
            return $helper_id_list;
        }

        if (count($helper_id_list) == 0) { // ランクが抜きん出ているユーザーなどのレアケース
            $param = array(
                intval($user_base['rank'])
            );

            $sql = "SELECT t.user_id "
            . " FROM tmp_helper_users t, "
            . " (SELECT id FROM tmp_helper_users "
            . " ORDER BY RAND()) tmp "
            . " WHERE tmp.id = t.id "
            . " ORDER BY ABS(rank - ?) "
            . " LIMIT ". self::HELPER_OTHERS_MAX_NUM;

            $helper_id_list = $this->db_cmn_r->getCol($sql, $param);
            if (Ethna::isError($helper_id_list)) {
                return $helper_id_list;
            }
        }

        return $this->backend->getManager('Monster')->getActiveLeaderList(
            $helper_id_list
        );
    }

	/**
	 * フレンドの助っ人一覧を取得する
	 */
	function getHelperFriendList($user_id)
	{
		//1.友達全員のリストを作る
		//  最終ログイン時間で新しい順にソートする
		//  最終ログイン時間もリストに含む
		//---
		//最終ログイン時間は最終プレイ時間になるかもしれないが、今回は最終ログイン時間で統一
		//---
		$friend_m = $this->backend->getManager('Friend');
		$friend_list = $friend_m->getFriendList($user_id, Pp_FriendManager::STATUS_FRIEND);
		if (Ethna::isError($friend_list)) {
			return $friend_list;
		}

		//2.連れて行けるようになる時間を記録したDBを参照して、
		//  その時間を経過していない友達はリストから排除する
		//	※連れて行けるようになる時間を記録したDBというのは、
		//　連れて行った時に記録するもの。
		//　基本的には連れて行った日時＋１時間を保存するが、
		//　友達がプレイヤーよりレベルが上の場合は
		//　（友達レベル－自分のレベル）÷10×1時間とするが、
		//　上限は３時間とする
		//---
		//友達のDBテーブル構造を
		//自分のID、友達のID、連れて行ける時間
		//にした方が余計なテーブルを作らずに済む
		//全体において数値は後日調整される可能性あり
		//---
		$helper_id_list = array();
		foreach ($friend_list as $key => $row) {
			//未来の日時＝クールタイム中
			if (strtotime($row['date_bring']) > $_SERVER['REQUEST_TIME']) {
				continue;
			}
			$helper_id_list[] = $row['friend_id'];
		}
		//リーダーモンスターのデータの他にログイン日時も取得する
		$helper_list = $this->backend->getManager('Monster')->getActiveLeaderList(
			$helper_id_list
		);
		
		$helper_friend_list = array();
		if (count($helper_list) > 0) {
			foreach ($helper_list as $key => $row) {
				//3日以上過去の人は省く
				if (strtotime($row['login_date']) < strtotime("-3 day", $_SERVER['REQUEST_TIME'])) {
					continue;
				}
				$helper_friend_list[] = $row;
			}
		}
		return $helper_friend_list;
	}
	
	/**
	 * フレンド以外の助っ人一覧を取得する
	 */
	function getHelperOthersList($user_id)
	{
		//1.友達を除く全ユーザの中から以下の条件で12人をランダムで抽出する
		//  ・最終ログイン時間が14時間以内
		//  ・自分のレベル±5
		//  ※同じ人は抽出しない
		//　  連れて行けるようになる時間は見ないので
		//　  次回も出てくる可能性はある

		//1.1 友達を除く準備
		$friend_m = $this->backend->getManager('Friend');
		$friend_id_list = $friend_m->getFriendIdList(
			$user_id,
			Pp_FriendManager::STATUS_FRIEND
		);
		if ((!$friend_id_list && !is_array($friend_id_list)) || 
			Ethna::isError($friend_id_list)
		) {
			return $friend_id_list;
		}
		
		//1.2 ランダム準備
		$rand = mt_rand(0, 32768);
		
		//1.3 自分のレベル準備
		$user_base = $this->backend->getManager('User')->getUserBase($user_id);
		
		//1.4 抽出実行
		$param = array(
			date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - self::HELPER_OTHERS_LOGIN_DATE_LOWER_RANGE),
			$user_base['rank'] - self::HELPER_OTHERS_RANK_LOWER_RANGE,
			$user_base['rank'] + self::HELPER_OTHERS_RANK_UPPER_RANGE,
			$user_id,
		);
		if (count($friend_id_list) > 0) {
			$param = array_merge($param, $friend_id_list);
		}
		$sql = "SELECT b.user_id"
		     . " FROM t_user_base b, t_user_team t"
		     . " WHERE b.user_id = t.user_id"
		     . " AND b.active_team_id = t.team_id"
		     . " AND t.leader_flg = 1" 
		     . " AND t.user_monster_id > 0" 
		     . " AND b.tutorial >= 10"
		     . " AND b.access_ctrl != ". Pp_UserManager::USER_ACCCESS_DENY	//アクセス拒否ユーザは外す
		     . " AND b.login_rand > $rand"
		     . " AND b.login_date >= ?"
		     . " AND b.rank BETWEEN ? AND ?"
		     . " AND b.user_id NOT IN("
		     . " ?" . str_repeat(",?", count($friend_id_list)) . ")"
		     . " AND b.friend_rest > 0"
		     . " ORDER BY b.login_rand"
		     . " LIMIT " . self::HELPER_OTHERS_MAX_NUM;
		$helper_id_list = $this->db->getCol($sql, $param);

		return $this->backend->getManager('Monster')->getActiveLeaderList(
			$helper_id_list
		);
	}

	/**
	 * フレンドの助っ人を連れて行った時間を記録する
	 * 
	 * 連れて行ける時間になっていない場合はエラーと判定する処理も行なう
	 * @param int $user_id
	 * @param int $helper_id
	 * @return bool|object 成功時:true, 失敗時:falseまたはEthna_Errorオブジェクト
	 */
	function setHelperFriendBringDate($user_id, $helper_id)
	{
		$user_m = $this->backend->getManager('User');
		$friend_m = $this->backend->getManager('Friend');
		
		$friend = $friend_m->getUserFriend($user_id, $helper_id);
		if (Ethna::isError($friend)) {
			return $friend;
		}
		
		// フレンドでなければ処理不要
		if (!$friend) {
			//OK
			return true;
		}
		if ($friend['status'] != Pp_FriendManager::STATUS_FRIEND) {
			//OK
			return true;
		}
		// 連れて行ける時間になっていない場合はエラー
		if ($friend['date_bring']!= NULL && strtotime($friend['date_bring']) > $_SERVER['REQUEST_TIME']) {
			return false;
		}
		//	※連れて行けるようになる時間を記録したDBというのは、
		//　連れて行った時に記録するもの。
		//　基本的には連れて行った日時＋１時間を保存するが、
		//　友達がプレイヤーよりレベルが上の場合は
		//　（友達レベル－自分のレベル）÷10×1時間とするが、
		//　上限は３時間とする
		$user = $user_m->getUserBase($user_id);
		$helper = $user_m->getUserBase($helper_id);
		
		$hours = 1;

		if ($helper['rank'] > $user['rank']) {
			$hours = ceil(($helper['rank'] - $user['rank']) / 10);
		}
		
		if ($hours > 3) {
			$hours = 3;
		}
		
		$date_bring = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + $hours * 3600);
		
		$ret = $friend_m->setUserFriend($user_id, $helper_id, array('date_bring' => $date_bring, 'status' => $friend['status']));
		if (!$ret || Ethna::isError($ret)) {
			return $ret;
		}
		
		return true;
	}
	protected function loadMasterAreaAssoc($quest_type)
	{
		if (isset($this->master_area_assoc[$quest_type])) {
			return;
		}
		
		$lang = $this->config->get('lang');
		$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$param = array($quest_type, $now, $now);
		$sql = "SELECT a.area_id AS id, a.area_id, a.last_area, a.quest_id, a.no, a.name_$lang AS name, a.boss_flag, a.bg_id, a.use_type, a.needful_stamina, a.battle_num, a.attack_seven, a.attack_bar, a.date_start, a.date_end"
			 . " FROM m_area a, m_quest q"
			 . " WHERE a.quest_id = q.quest_id"
			 . " AND q.type = ?"
			 . " AND a.date_start <= ?"
			 . " AND a.date_end > ?"
			 . " ORDER BY a.area_id";
		
		$this->master_area_assoc[$quest_type] = $this->db_r->db->GetAssoc($sql, $param);
	}
	
	protected function loadMasterQuestAssoc($quest_type)
	{
		if (isset($this->master_quest_assoc[$quest_type])) {
			return;
		}
		
		$lang = $this->config->get('lang');
		$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$param = array($quest_type, $now, $now);
		$sql = "SELECT q.quest_id AS id, q.quest_id, q.map_id, q.map_no, q.name_$lang AS name," 
			 . " q.type, q.no, q.effect_type, q.effect_value, q.date_start, q.date_end, q.time_start, q.time_end, q.week_element"
			 . ", kind, comment_event, comment_baloon"
			 . " FROM m_quest q"
			 . " WHERE type = ?"
			 . " AND date_start <= ?"
			 . " AND date_end > ?"
			 . " ORDER BY q.no";
		$this->master_quest_assoc[$quest_type] = $this->db_r->db->GetAssoc($sql, $param);
		
		//イベントクエストの時だけ別途条件あり
		if ($quest_type == self::QUEST_TYPE_EVENT) {
			//曜日を指定したい場合はm_quest.week_elementにカンマ区切りの文字列（数字）が入っている
			//指定しない場合はm_quest.week_elementはnullになっている
			//開始・終了日時を指定したい場合はm_quest.time_start,time_endにカンマ区切りの文字列（数字）が入っている
			//指定しない場合はm_quest.time_start,time_endはnullになっている
			$tmp = $this->master_quest_assoc[$quest_type];
			$this->master_quest_assoc[$quest_type] = array();//初期化
			foreach($tmp as $val) {
				//条件判定
				$enable = false;//まずは無効
				//曜日の指定がないか？
				if (strlen($val['week_element']) == 0) {
					$enable = true;
				} else {
					//指定アリ
					$week_element = date('w', $_SERVER['REQUEST_TIME']);//曜日 0 (日曜)から 6 (土曜)
					$week_element_list = explode(',', $val['week_element']);
					foreach ($week_element_list as $key => $row) {
						//指定の週と一致した
						if ($week_element == $row) {
							$enable = true;
							$val['week_element'] = $row;
							break;
						}
					}
				}
				//週の条件が一致した
				if ($enable) {
					//条件判定
					$enable = false;//まずは無効
					//開始時間と終了時間の指定がないか？
					if (strlen($val['time_start']) == 0 && strlen($val['time_end']) == 0) {
						$enable = true;
					} else {
						//指定アリ
						$now_time = date('H:i:s', $_SERVER['REQUEST_TIME']);//時分秒
						$time_start_list = explode(',', $val['time_start']);
						$time_end_list = explode(',', $val['time_end']);
						foreach ($time_start_list as $key => $row) {
							//指定の時間帯と一致した
							if ($time_start_list[$key] <= $now_time && $time_end_list[$key] > $now_time) {
								$enable = true;
								$val['time_start'] = $time_start_list[$key];
								$val['time_end'] = $time_end_list[$key];
								break;
							}
						}
					}
				}
				//有効
				if ($enable) $this->master_quest_assoc[$quest_type][] = $val;
			}
		}
		if ($quest_type == self::QUEST_TYPE_NORMAL) {
			//ノーマルクエストでは時限特殊効果の判定を行う
			$tmp = $this->master_quest_assoc[$quest_type];
			$this->master_quest_assoc[$quest_type] = array();//初期化
			foreach($tmp as $val) {
				//条件判定
				$enable = false;//まずは無効
				//効果の指定があるか？
				if ($val['effect_type'] > 0) {
					//曜日の指定がある？
			//error_log("week_element=".$val['week_element']);
					if (strlen($val['week_element']) > 0) {
						//$weidx = -1;
						$week_element = date('w', $_SERVER['REQUEST_TIME']);//曜日 0 (日曜)から 6 (土曜)
						$week_element_list = explode(',', $val['week_element']);
						foreach ($week_element_list as $key => $row) {
							//$weidx++;//指定された要素番号
							//指定の週と一致した
							if ($week_element == $row) {
								$enable = true;
							//	$val['week_element'] = $row;
								break;
							}
						}
					} else {
						$enable = true;
					}
			//error_log("enable=".($enable==true?1:0));
					//週の条件が一致した
					if ($enable) {
						//条件判定
						$enable = false;//まずは無効
						//開始時間と終了時間の指定がある？
						if (strlen($val['time_start']) > 0 && strlen($val['time_end']) > 0) {
							//指定アリ
							$now_time = date('H:i:s', $_SERVER['REQUEST_TIME']);//時分秒
			//error_log("now_time=$now_time");
							$time_start_list = explode(',', $val['time_start']);
							$time_end_list = explode(',', $val['time_end']);
							//$weidx2 = -1;
							foreach ($time_start_list as $key => $row) {
								//$weidx2++;//指定された要素番号
								//if ($weidx == $weidx2) {//曜日と同じ要素番号になった
									//指定の時間帯と一致した
			//error_log("time=".$time_start_list[$key]." - ".$time_end_list[$key]);
									if ($time_start_list[$key] <= $now_time && $time_end_list[$key] > $now_time) {
										$enable = true;
										$val['time_start'] = $time_start_list[$key];
										$val['time_end'] = $time_end_list[$key];
										break;
									}
								//}
							}
						} else {
							/*
							$enable = true;
							$val['time_start'] = '00:00:00';
							$val['time_end'] = '23:59:59';
							*/
							$enable = false;
						}
					}
				}
				if (!$enable) {
					$val['effect_type'] = 0;//効果なし
					$val['effect_value'] = 100;
					$val['time_start'] = NULL;
					$val['time_end'] = NULL;
				}
				$val['week_element'] = NULL;
				$this->master_quest_assoc[$quest_type][] = $val;
			}
		}
	}

	/**
	 * ドロップアイテム抽選とクエスト敵情報取得を行い、1つのバトルでのAPI戻り値用の情報を組み立てる
	 * 
	 * @param int $area_id エリアID
	 * @param array $enemy_id_list enemy_idの配列
	 * @param int $user_id ユーザID
	 * @param int $effect_type （ドロップ率を変えるため）
	 * @param int $effect_value 効果の値
	 * @param int $quest_type クエストタイプ
	 * @return array $array['quest_battle']     = API戻り値'quest_battle'の1バトル分の要素として使うための情報
	 *                     ['drop_enemy']       = API戻り値'drop_enemy'のドロップ分の要素として使うための情報
	 *                     ['drop_boss_normal'] = API戻り値'drop_boss_normal'のドロップ分の要素として使うための情報
	 *                     ['drop_boss_over']   = API戻り値'drop_boss_over'のドロップ分の要素として使うための情報
	 */
	protected function startBattle($area_id, $enemy_id_list, $battle_no, $user_id, $effect_type, $effect_value, $quest_type)
	{
		$master_area = $this->getMasterArea($area_id);
		$quest_id = $master_area['quest_id'];
		
		//2.出現モンスター一覧をm_quest_enemyからquest_idをキーに取得する
		//  ※ボスデータもm_quest_enemyに含める
		//  　ボスはm_quest_enemy.boss_flag=1
		//  　ボスは中盤に出てくる場合もある
		$master_quest_enemy_assoc = $this->getMasterQuestEnemyAssocEx($quest_id);

		$drop_monster_flg = false; // モンスター卵のドロップが出現したかのフラグ
		$drop_key_flg = false;     // 鍵のドロップが出現したかのフラグ
		$drop_box_flg = false;     // 宝箱のドロップが出現したかのフラグ 
		
		// この関数内で求めたドロップ判定結果に基づくバトル情報を格納する連想配列
		$battle_result = array(
			'quest_battle' => array(),
			'drop_enemy' => array(),
			'drop_boss_normal' => array(),
			'drop_boss_over' => array(),
		);
		
		//7.1つのバトルでの出現敵が確定したのでドロップアイテムの抽選を行う
		//  確定した敵の1体目から出現数分だけ以下の処理を繰り返す
		$drop_boss_tmp_id = 0; // ボスバトルドロップアイテム一時ID
		$enemy_idx = 0;
		foreach ($enemy_id_list as $enemy_id) {
			$master_quest_enemy = $master_quest_enemy_assoc[$enemy_id];
			if ($master_quest_enemy == null) {
				error_log('empty master_quest_enemy id=' . $enemy_id ." area_id=" . $area_id);
			}
			$boss_flag = $master_quest_enemy['boss_flag'];
			$boss_lv = 0;
			
			//時限効果で$effect_type==3の時は敵のドロップ率を変える
			if ($effect_type == self::TIMED_TYPE_DROP_ENEMY) {
				$master_quest_enemy['monster_drop_normal'] = floor(($master_quest_enemy['monster_drop_normal'] * $effect_value) / 100);
				$master_quest_enemy['monster_drop_overkill'] = floor(($master_quest_enemy['monster_drop_overkill'] * $effect_value) / 100);
			}
			//ターン数の処理
			$turn_base = $turn_1st = $master_quest_enemy['turn_base'];
			if (mt_rand(0, 99) < $master_quest_enemy['turn_prob']) $turn_1st += $master_quest_enemy['turn_1st'];
			//万一、0以下にならないように
			if ($turn_base <= 0) $turn_base = 1;
			if ($turn_1st <= 0) $turn_1st = 1;
			
			//カンマ区切りの文字列を数値の配列に置き換える
			$acttbls = array('acttbl_sec', 'acttbl_con');
			foreach ($acttbls as $actval) {
				$master_quest_enemy[$actval] = Pp_Util::convertCsvToIntArray($master_quest_enemy[$actval]);
			}
			
			$quest_battle = array(
				'enemy_idx' => -1,
				'monster_id' => $master_quest_enemy['monster_id'],
				'attribute_id' => $master_quest_enemy['attribute_id'],
				'boss_flag' => $master_quest_enemy['boss_flag'],
				'hp' => $master_quest_enemy['hp'],
				'attack' => $master_quest_enemy['attack'],
				'defense' => $master_quest_enemy['defense'],
			//	'attack_turn' => $master_quest_enemy['attack_turn'],
			//	'pattern' => $master_quest_enemy['pattern'],//2014/06(ver1.3)削除
			//	'drop_type' => self::DROP_TYPE_NONE, // 最初はNONEにしておき、ドロップしたら書き換える。
				'em_type' => $master_quest_enemy['em_type'],
				'em_drop_normal' => $master_quest_enemy['em_drop_normal'],
				'em_drop_overkill' => $master_quest_enemy['em_drop_overkill'],
				'normal_drop_type' => self::DROP_TYPE_NONE,//通常時ドロップ種別(0:無し 1:モンスター 2:鍵 3:宝箱)
				'normal_drop_monster_id' => 0,//通常時ドロップするモンスターのモンスターマスタID
				'normal_monster_drop_lv' => 0,//通常時ドロップするモンスターのレベル
				'over_drop_type' => self::DROP_TYPE_NONE,//オーバーキル時ドロップ種別(0:無し 1:モンスター 2:鍵 3:宝箱)
				'over_drop_monster_id' => 0,//オーバーキル時ドロップするモンスターのモンスターマスタID
				'over_monster_drop_lv' => 0,//オーバーキル時ドロップするモンスターのレベル
				'boss_lv' => $boss_lv,//ボスレベル
				'battle_num' => $battle_no - 1,//出現バトル番号
				'enemy_id' => $enemy_id,
				'turn_base' => $turn_base,//基礎攻撃ターン数
				'turn_1st' => $turn_1st,//初撃ターン数
				//2014/06(ver1.3)追加
				'acttbl_pri' => $master_quest_enemy['acttbl_pri'],//アクションテーブルプライマリ
				'acttbl_sec' => $master_quest_enemy['acttbl_sec'],//アクションテーブルセカンダリ
				'acttbl_con' => $master_quest_enemy['acttbl_con'],//アクションテーブル条件
			);

			//ドロップするモンスターも抽選する
			if (mt_rand(0, 999) < $master_quest_enemy['drop_monster_prob'])
				$drop_monster_id = $master_quest_enemy['drop_monster_id2'];
			else
				$drop_monster_id = $master_quest_enemy['drop_monster_id1'];
			$drop_monster_tmp = array(
				'drop_type' => self::DROP_TYPE_MONSTER,
				'drop_monster_id' => $drop_monster_id,
				'monster_drop_lv' => $master_quest_enemy['monster_drop_lv'],
				'enemy_id' => $enemy_id,
			);
			if (!$boss_flag) {
				//ドロップアイテム（モンスター卵か鍵か宝箱）の抽選
				
				//8.モンスター卵のドロップ抽選を行う
				//  抽選された敵番号に該当するm_quest_enemyのドロップ率（monster_drop_normal）で抽選
				//  モンスター卵のドロップが確定したら、そのバトルでのドロップアイテムは確定なので、残りの敵についてはモンスター卵のドロップ抽選は行わない
				//  ドロップするモンスター卵のmonster_idは同じにし、初期レベルをm_quest_enemyのmonster_drop_lvにする	
				if (!$drop_monster_flg) {
					$rand = mt_rand(0, 999);//千分率に変更
					if ($rand < $master_quest_enemy['monster_drop_normal']) {
						$drop_monster_flg = true;
					//	$quest_battle['drop_type'] = self::DROP_TYPE_MONSTER;
					//	$battle_result['drop_enemy'][] = $drop_monster_tmp;
						//通常で出すならオーバーキルでも出す
						$quest_battle['normal_drop_type'] = $quest_battle['over_drop_type'] = self::DROP_TYPE_MONSTER;
						$quest_battle['normal_drop_monster_id'] = $quest_battle['over_drop_monster_id'] = $drop_monster_tmp['drop_monster_id'];
						$quest_battle['normal_monster_drop_lv'] = $quest_battle['over_monster_drop_lv'] = $drop_monster_tmp['monster_drop_lv'];
					}
				}
				
				//12.出現敵の分だけ処理 8 へ戻る
			} else {
				//ボスのドロップアイテム（モンスター卵か鍵か宝箱）の抽選
				//オーバーキルかどうか、それぞれの抽選結果を渡す
				//各ボスを通常で倒したかオーバーキルだったかはアプリで判定する（ボスごとにオーバーキルだったかどうかの値をクリア時にアプリから受け取る）

				//14.ボスは処理が変わる
				//   エリア毎の出現ボスのデータを取得する
				//   ボスの数の分だけ以下の処理を繰り返す	
				
				//15.オーバーキルではない通常でのドロップアイテム抽選を行う	
				//16.オーバーキル状態でのドロップアイテム抽選を行う	
				foreach (array(
					'monster_drop_normal' => 'drop_boss_normal',
					'monster_drop_overkill' => 'drop_boss_over',
				) as $tmp_master_key => $tmp_result_key) {
					if (!$drop_monster_flg) {
						$rand = mt_rand(0, 999);//千分率に変更
						if ($rand < $master_quest_enemy[$tmp_master_key]) {
							$drop_monster_flg = true;
							$drop_boss_tmp_id++;
							$drop_monster_tmp['drop_boss_tmp_id'] = $drop_boss_tmp_id;
						//	$quest_battle['drop_type'] = self::DROP_TYPE_MONSTER;
						//	$battle_result[$tmp_result_key][] = $drop_monster_tmp;
							//通常で出すならオーバーキルでも出す
							if ($tmp_result_key == 'drop_boss_normal') {
								$quest_battle['normal_drop_type'] = $quest_battle['over_drop_type'] = self::DROP_TYPE_MONSTER;
								$quest_battle['normal_drop_monster_id'] = $quest_battle['over_drop_monster_id'] = $drop_monster_tmp['drop_monster_id'];
								$quest_battle['normal_monster_drop_lv'] = $quest_battle['over_monster_drop_lv'] = $drop_monster_tmp['monster_drop_lv'];
							}
							if ($tmp_result_key == 'drop_boss_over') {
								$quest_battle['over_drop_type'] = self::DROP_TYPE_MONSTER;
								$quest_battle['over_drop_monster_id'] = $drop_monster_tmp['drop_monster_id'];
								$quest_battle['over_monster_drop_lv'] = $drop_monster_tmp['monster_drop_lv'];
							}
						}
					}
				}
				
				//19.ボスの分だけ処理 15 へ戻る
			}
			//宝箱
			if (!$drop_monster_flg && !$drop_key_flg && !$drop_box_flg) {
				$box_drop = $master_area['box_drop'];
				//時限効果で$effect_type==3の時は敵のドロップ率を変える
				if ($effect_type == self::TIMED_TYPE_DROP_BOX) {
					$box_drop = floor(($box_drop * $effect_value) / 100);
				}
			//error_log("box_drop=$box_drop");
				if (mt_rand(0, 99) < $box_drop) {
					$drop_box_flg = true;
				//	$quest_battle['drop_type'] = self::DROP_TYPE_BOX;
				//	$battle_result['drop_enemy'][] = array(
				//		'drop_type' => self::DROP_TYPE_BOX,
				//		'enemy_id' => $enemy_id,
				//	);
					$quest_battle['normal_drop_type'] = $quest_battle['over_drop_type'] = self::DROP_TYPE_BOX;
					//宝箱の抽選
					//ゴールドチケット　10％
					//ブロンズチケット　90％
					//モンスタードロップ用の変数を流用する
					$box_rand = mt_rand(0, 99);
					if ($box_rand >= 0 && $box_rand < 10) {
						$quest_battle['normal_drop_monster_id'] = $quest_battle['over_drop_monster_id'] = self::DROP_BOX_TICKET_GOLD;//
						$quest_battle['normal_monster_drop_lv'] = $quest_battle['over_monster_drop_lv'] = 1;//1個
					}
					if ($box_rand >= 10 && $box_rand < 100) {
						$quest_battle['normal_drop_monster_id'] = $quest_battle['over_drop_monster_id'] = self::DROP_BOX_TICKET_BRONZE;//
						$quest_battle['normal_monster_drop_lv'] = $quest_battle['over_monster_drop_lv'] = 1;//1個
					}
					//イベントクエストだけ確率を変える(ゴールドチケット100％)
					if ($quest_id == 10603000 || $quest_id == 10604000) {
						$quest_battle['normal_drop_monster_id'] = $quest_battle['over_drop_monster_id'] = self::DROP_BOX_TICKET_GOLD;//
						$quest_battle['normal_monster_drop_lv'] = $quest_battle['over_monster_drop_lv'] = 1;//1個
					}
				}
			}
			$enemy_idx++;

			//10.どのモンスターがモンスター卵または鍵か宝箱をドロップしたかを保持しておく
			//   （あとでアプリに渡す）	
			//18.処理 10、11 と同様の処理を行う	
			$battle_result['quest_battle'][] = $quest_battle;
		}
		return $battle_result;
	}
	
	protected function bonusBattle($area_id, $effect_type, $effect_value, $quest_type, $user_id)
	{
		$master_area = $this->getMasterArea($area_id);
		$bbattle_result = array(
			'bonus_flag' => array(),
			'bonus_battle' => array(),
		);
		//ボーナスバトルの抽選を行う
		$bb = $rb = 0;
		//ボーナスバトルのグループ抽選
		//m_areaから参照する
		$bonus_battle = $master_area['bonus_battle'];
		$rand = mt_rand(0, 99);
		//期間によって確率を変える
		$date = date('YmdHis', $_SERVER['REQUEST_TIME']);
		if ('20140505000000' <= $date && $date <= '20140511235959') {
			//期間内の設定
			if ($rand < 50) $rb = 1;//50%の確率でRB突入
			if ($rand < 90) $bb = 1;//90%の確率でBB突入
		} else {
			//通常の設定
			if ($rand < 30) $rb = 1;//30%の確率でRB突入
			if ($rand < 70) $bb = 1;//70%の確率でBB突入
		}
		$bonus_group = -1;
		//設定が書かれていたら
		if ($bonus_battle != NULL && strlen($bonus_battle) > 0) {
			//カンマ区切りで分割する
			$bonus_battle_list = explode(',', $bonus_battle);
			$rand = mt_rand(0, 99);
			$per_total = 0;
			foreach($bonus_battle_list as $bval) {
				//{}を削除
				$bval = str_replace("{","",$bval);
				$bval = str_replace("}","",$bval);
				//コロン区切りで分割する
				$bbdata = explode(':', $bval);
				$per_total += $bbdata[1];//合計の確率値
				if ($rand < $per_total) {
					$bonus_group = $bbdata[0];
					break;
				}
			}
		}
		if ($bonus_group < 0) $bonus_group = self::BONUS_GROUP_DEFAULT;//デフォルトのグループ値
		
		//夢幻遊戯以外の時は
		if (!($area_id >= MUGEN_AREA_ID_MIN && $area_id <= MUGEN_AREA_ID_MAX)) {
			//tutorialとbbの値によってはボーナスバトルの値を変える
			$user_base = $this->backend->getManager('User')->getUserBase($user_id);
			if ($user_base['tutorial'] == 10 && $user_base['bonus_big'] <= 1) {
				$bonus_group = self::BONUS_GROUP_TUTORIAL10;
				$bb = 1;
			}
			if ($user_base['tutorial'] == 11 && $user_base['bonus_big'] <= 3) {
				$bonus_group = self::BONUS_GROUP_TUTORIAL11;
				$bb = 1;
			}
			if ($user_base['tutorial'] == 12 && $user_base['bonus_big'] <= 6) {
				$bonus_group = self::BONUS_GROUP_TUTORIAL12;
				$bb = 1;
			}
		}
		
		//DBからグループ単位で取得
		$bonus_group_data = $this->getMasterBonusEnemy($bonus_group);
		$battle_num = 0;
		foreach($bonus_group_data as $bgval) {
			$battle_num++;
			//カンマ区切りの文字列を数値の配列に置き換える
			$acttbls = array('acttbl_sec', 'acttbl_con');
			foreach ($acttbls as $actval) {
				$bgval[$actval] = Pp_Util::convertCsvToIntArray($bgval[$actval]);
			}

			$bonus_battle = array(
				'enemy_idx' => $bgval['enemy_id'] - 1,
				'monster_id' => $bgval['monster_id'],
				'boss_flag' => $bgval['boss_flag'],
				'attribute_id' => $bgval['attribute_id'],
				'hp' => $bgval['hp'],
				'attack' => $bgval['attack'],
				'defense' => $bgval['defense'],
			//	'pattern' => $bgval['pattern'],//2014/06(ver1.3)削除
				'normal_drop_type' => self::DROP_TYPE_NONE,//通常時ドロップ種別(0:無し 1:モンスター)
				'normal_drop_monster_id' => 0,//通常時ドロップするモンスターのモンスターマスタID
				'normal_monster_drop_lv' => 0,//通常時ドロップするモンスターのレベル
				'over_drop_type' => self::DROP_TYPE_NONE,//オーバーキル時ドロップ種別(0:無し 1:モンスター)
				'over_drop_monster_id' => 0,//オーバーキル時ドロップするモンスターのモンスターマスタID
				'over_monster_drop_lv' => 0,//オーバーキル時ドロップするモンスターのレベル
				'battle_num' => $bgval['enemy_id'] - 1,//出現バトル番号
				'turn_base' => 2,//基礎攻撃ターン数
				'turn_1st' => 2,//初撃ターン数
				//2014/06(ver1.3)追加
				'acttbl_pri' => $bgval['acttbl_pri'],//アクションテーブルプライマリ
				'acttbl_sec' => $bgval['acttbl_sec'],//アクションテーブルセカンダリ
				'acttbl_con' => $bgval['acttbl_con'],//アクションテーブル条件
			);
			//時限効果で$effect_type==3の時は敵のドロップ率を変える
			if ($effect_type == self::TIMED_TYPE_DROP_ENEMY) {
				$bgval['monster_drop_normal'] = floor(($bgval['monster_drop_normal'] * $effect_value) / 100);
				$bgval['monster_drop_overkill'] = floor(($bgval['monster_drop_overkill'] * $effect_value) / 100);
			}
			//ドロップするモンスターも抽選する
			if (mt_rand(0, 999) < $bgval['drop_monster_prob'])
				$drop_monster_id = $bgval['drop_monster_id2'];
			else
				$drop_monster_id = $bgval['drop_monster_id1'];
			//ドロップ抽選
			$rand = mt_rand(0, 999);//千分率に変更
			if ($rand < $bgval['monster_drop_normal']) {
				$bonus_battle['normal_drop_type'] = self::DROP_TYPE_MONSTER;
				$bonus_battle['normal_drop_monster_id'] = $drop_monster_id;
				$bonus_battle['normal_monster_drop_lv'] = $bgval['monster_drop_lv'];
			}
			if ($rand < $bgval['monster_drop_overkill']) {
				$bonus_battle['over_drop_type'] = self::DROP_TYPE_MONSTER;
				$bonus_battle['over_drop_monster_id'] = $drop_monster_id;
				$bonus_battle['over_monster_drop_lv'] = $bgval['monster_drop_lv'];
			}
			$bbattle_result['bonus_battle'][] = $bonus_battle;
		}
		//BBとRBの抽選結果
		$bbattle_result['bonus_flag']['bb'] = $bb;
		$bbattle_result['bonus_flag']['rb'] = $rb;
		$bbattle_result['bonus_flag']['cd'] = mt_rand(10000000, 99999999);
		$bbattle_result['bonus_battle_num'] = $battle_num;
		
		return $bbattle_result;
	}
	
	/**
	 * バトルマスタ情報をフラグに応じて1件取得する
	 * 
	 * @param int $area_id
	 * @param int $no
	 * @param int $flag
	 * @return array バトルマスタ情報1行についての連想配列
	 */
	protected function getMasterBattleByFlag($area_id, $no, $flag)
	{
		static $pool = array();
		
		if (!isset($pool[$area_id])) {
			$param = array($area_id);
			$sql = "SELECT * FROM m_battle WHERE area_id = ?";
			foreach ($this->db_r->GetAll($sql, $param) as $row) {
				$pool[$area_id][$row['no']][$row['flag']] = $row;
			}
		}

		$flags = array($flag);
		if ($flag == 1) { // flagに1が指定された場合
			$flags[] = 0; // データが無かった場合はflagが0について参照する
		}
		
		foreach ($flags as $flag_tmp) if (
			isset($pool[$area_id]) && 
			isset($pool[$area_id][$no]) && 
			isset($pool[$area_id][$no][$flag_tmp])
		) {
			return $pool[$area_id][$no][$flag_tmp];
		}
	}

	/**
	 * 敵IDの配列をランダムに取得する
	 * 
	 * サイズが限界を超えない組み合わせを取得できる
	 * @param int $area_id エリアID
	 * @param int $num 何体の敵を取得するか
	 * @return array 敵IDの配列
	 */
	protected function getEnemyIdListRand($area_id, $num, $quest_id)
	{
		// サイズのマスター設定
		// Ｓ＝6体並ぶ、Ｍ＝5体並ぶ、Ｌ＝3体並ぶ、ＬＬ・ＸＬ＝1体並ぶ
		// 最小公倍数 ÷ 単体並ぶか で求めた値を使用する
		$size_map = array(
			1 => 5,  // S
			2 => 6,  // M
			3 => 10, // L
			4 => 30, // LL
			5 => 30, // XL
		);

		// 何体並ぶかの最小公倍数
		$size_lcm = 30;

		// DBから確率設定を取得
		static $pool = array();
		if (!isset($pool[$area_id])) {
			$param = array($area_id, $quest_id);
			$sql = "SELECT ae.*, m.monster_size_id FROM m_area_enemy ae, m_quest_enemy qe, m_monster m"
				 . " WHERE ae.enemy_id = qe.enemy_id AND qe.monster_id = m.monster_id"
				 . " AND ae.area_id = ? AND qe.quest_id = ? ";
			$pool[$area_id] = $this->db_r->GetAll($sql, $param);
		}
		
		// 所定のサイズに収まる組み合わせが得られるまで、ランダムにモンスターを取り出す事を繰り返す
		$cnt = 0;
		while (1) {
			$enemy_id_list = array();
			$size_sum = 0;
			for ($i = 0; $i < $num; $i++) {
				$pip = mt_rand(0, 99);
				$threshold = 0;
				foreach ($pool[$area_id] as $row) {
					$threshold += $row['probability'];
					if ($pip >= $threshold) {
						continue;
					}
					$enemy_id_list[] = $row['enemy_id'];
					$size_sum += $size_map[$row['monster_size_id']];
					break;
				}
			}
			
			if ($size_sum <= $size_lcm) {
				return $enemy_id_list;
			}

			// 念の為、無限ループ防止
			$cnt++;
			if ($cnt > 1000) {
				return Ethna::raiseError("area_id[%d] num[%d]", E_USER_ERROR, $area_id, $num);
			}
		}
	}

	/**
	 * ユーザのクエスト開始情報をテンポラリテーブルにセットする
	 * 
	 * @param int $user_id ユーザID
	 * @param int $content 内容（function start()の戻り値と同じ）
	 * @param int $logset ログにも保存するかどうか（省略可・省略時は保存する）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	public function setTmpUserQuestStart($user_id, $content, $logset = true)
	{
		$param = array($user_id, json_encode($content));
		$sql = "INSERT INTO tmp_user_quest_start(user_id, content, date_created)"
			 . " VALUES(?, ?, NOW())";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("setTmpUserQuestStart rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
	//	if ($logset) {
	//		//ログにも保存する
	//		$this->setLogQuest($user_id, $content);
	//	}
		
		return true;
	}
	
	/**
	 * ユーザのクエスト開始情報をテンポラリテーブルにセットする（ユニット指定あり）
	 * 
	 * @param int $user_id ユーザID
	 * @param int $content 内容（function start()の戻り値と同じ）
	 * @param int $unit 指定ユニット
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	public function setTmpUserQuestStartForUnit($user_id, $content, $unit = null)
	{
		$param = array($user_id, json_encode($content));
		$sql = "INSERT INTO tmp_user_quest_start(user_id, content, date_created)"
			 . " VALUES(?, ?, NOW())";
		/*
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("setTmpUserQuestStart rows[%d]", E_USER_ERROR, $affected_rows);
		}
		*/
		if (is_null($unit) === false) {
			$unit_m = $this->backend->getManager('Unit');
			$res = $unit_m->executeForUnit($unit, $sql, $param, false);
			if ($res->ErrorNo) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$res->ErrorNo, $res->ErrorMsg, __FILE__, __LINE__);
			}
			//return $res->insert_id;
		} else {
			$res = $this->db->execute($sql, $param);
			if ($res === false) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}
			//return $this->db->db->Insert_ID();
		}
		
		return true;
	}
	
	/**
	 * ユーザのクエスト開始情報のテンポラリテーブルにメダル使用フラグをセットする
	 * 
	 * @param int $user_id ユーザID
	 * @param int $medal_use_flg メダル使用フラグ
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	public function setTmpUserQuestStartMedalUseFlg($user_id, $medal_use_flg = 1)
	{
		$param = array($medal_use_flg, $user_id, $medal_use_flg);
		$sql = "UPDATE tmp_user_quest_start SET medal_use_flg = ? WHERE user_id = ? AND medal_use_flg != ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d] FILE[%s] LINE[%d]", E_USER_ERROR, $affected_rows, __FILE__, __LINE__);
		}

		return true;
	}
	
	/**
	 * ユーザのクエスト開始情報をテンポラリテーブルから取得する
	 * 
	 * @param int $user_id
	 * @return array 内容
	 */
	public function getTmpUserQuestStart($user_id)
	{
		$param = array($user_id);
		$sql = "SELECT content FROM tmp_user_quest_start WHERE user_id = ?";
		
		return json_decode($this->db->GetOne($sql, $param), true);
	}
	
	/**
	 * ユーザのクエスト開始情報をテンポラリテーブルから削除する
	 * 
	 * @param int $user_id ユーザID
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	public function deleteTmpUserQuestStart($user_id)
	{
		$param = array($user_id);
		$sql = "DELETE FROM tmp_user_quest_start WHERE user_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

//		$affected_rows = $this->db->db->affected_rows();
//		if ($affected_rows != 1) {
//			return Ethna::raiseError("deleteTmpUserQuestStart rows[%d]", E_USER_ERROR, $affected_rows);
//		}

		return true;
	}
	
	/**
	 * ユーザのクエスト開始情報をテンポラリテーブルから削除する（ユニット指定あり）
	 * 
	 * @param int $user_id ユーザID
	 * @param int $unit 指定ユニット
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	public function deleteTmpUserQuestStartForUnit($user_id, $unit = null)
	{
		$param = array($user_id);
		$sql = "DELETE FROM tmp_user_quest_start WHERE user_id = ?";
		/*
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		*/
		if (is_null($unit) === false) {
			$unit_m = $this->backend->getManager('Unit');
			$res = $unit_m->executeForUnit($unit, $sql, $param, false);
			if ($res->ErrorNo) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$res->ErrorNo, $res->ErrorMsg, __FILE__, __LINE__);
			}
			//return $res->insert_id;
		} else {
			$res = $this->db->execute($sql, $param);
			if ($res === false) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}
			//return $this->db->db->Insert_ID();
		}

//		$affected_rows = $this->db->db->affected_rows();
//		if ($affected_rows != 1) {
//			return Ethna::raiseError("deleteTmpUserQuestStart rows[%d]", E_USER_ERROR, $affected_rows);
//		}

		return true;
	}
	
	/**
	 * 敵IDからユーザ所持モンスターを生成する
	 * 
	 * ドロップの処理
	 * @param int $quest_id
	 * @param int $enemy_id
	 * @return array MonsterManagerのcreateUserMonsterの戻り値と同じ
	 */
	/*
	protected function createUserMonsterByEnemyId($user_id, $quest_id, $enemy_id)
	{
		$row = $this->getMasterQuestEnemyEx($quest_id, $enemy_id);
		
		return $this->backend->getManager('Monster')->createUserMonster(
			$user_id, $row['drop_monster_id'], array('lv' => $row['monster_drop_lv'])
		);
	}
	*/

	/**
	 * ユーザのクエスト開始情報をログテーブルにセットする
	 * 
	 * @param int $user_id ユーザID
	 * @param int $content 内容（function start()の戻り値と同じ）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	public function setLogQuest($user_id, $content)
	{
		$param = array($user_id, json_encode($content));
		$sql = "INSERT INTO log_quest(user_id, quest_data, date_created)"
			 . " VALUES(?, ?, NOW())";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

    /**
     * クエストマスタの整合性チェック(マップマスタとの比較)
     *
     * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト 
     */
    public function checkConsistencyQuestMasterForMapMaster()
    {
        $msg = '';
        $sql = "SELECT mq.quest_id, mq.map_id, mq.name_ja "
             . "FROM m_quest mq "
             . "WHERE NOT EXISTS(SELECT * FROM m_map mm WHERE mq.map_id = mm.map_id)";
        //$res = $this->db_r->db->GetAll($sql);
        if (!$res = $this->db_r->db->GetAll($sql)){
            // select エラーの場合のエラーを返す
            return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
        }

        $cnt=0;
        if (count($res) > 0){
            foreach($res as $k => $v){
                $msg[] = "[ quest_id ]" . $v['quest_id'] . " : " . $v['map_id'] . "(" . $v['name_ja'] . ")が[ m_map ]に存在しません。";
                $cnt++;
            }
        }
        return array('msg' => $msg, 'cnt' => $cnt);
    }

    /**
     * クエストエネミーマスタの整合性チェック(クエストマスタとの比較)
     *
     * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト 
     */
    public function checkConsistencyQuestEnemyMasterForQuestMaster()
    {
        $msg = '';
        //$sql = "SELECT mqe.quest_id, mqe.enemy_id, mq.name_ja "
        $sql = "SELECT mqe.quest_id, mqe.enemy_id "
             . "FROM m_quest_enemy mqe "
             . "WHERE NOT EXISTS(SELECT * FROM m_quest mq WHERE mqe.quest_id = mq.quest_id)";
        $res = $this->db_r->db->GetAll($sql);
        if ($res === false){
            // select エラーの場合のエラーを返す
            return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
        }

        $cnt=0;
        if (count($res) > 0){
            foreach($res as $k => $v){
               //$msg[] = $v['quest_enemy_id'] . ":" . $v['quest_id'] . "(" . $v['name_ja'] . ")";
                $msg[] = "[ enemy_id ]" . $v['enemy_id'] . " : " . $v['quest_id'] . "が[ m_quest ]に存在しません。";
                $cnt++;
            }
        }
        return array('msg' => $msg, 'cnt' => $cnt);
    }

    /**
     * クエストエネミーマスタの整合性チェック(モンスターマスタとの比較)
     *
     * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト 
     */
    public function checkConsistencyQuestEnemyMasterForMonsterMaster()
    {
        $msg = '';
        //$sql = "SELECT mqe.quest_id, mqe.monster_id, mm.name_ja "
        $sql = "SELECT mqe.quest_id, mqe.monster_id "
             . "FROM m_quest_enemy mqe "
             . "WHERE NOT EXISTS(SELECT * FROM m_monster mm WHERE mqe.monster_id = mm.monster_id)";
        $res = $this->db_r->db->GetAll($sql);
        if ($res === false){
            // select エラーの場合のエラーを返す
            return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
        }

        $cnt=0;
        if (count($res) > 0){
            foreach($res as $k => $v){
               //$msg[] = $v['quest_id'] . ":" . $v['monster_id'] . "(" . $v['name_ja'] . ")";
                $msg[] = "[ quest_id ]" . $v['quest_id'] . " : " . $v['monster_id'] . "が[ m_monster ]に存在しません。";
                $cnt++;
            }
        }
        return array('msg' => $msg, 'cnt' => $cnt);
    }

    /**
     * エリアマスタの整合性チェック(クエストマスタとの比較)
     *
     * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト 
     */
    public function checkConsistencyAreaMasterForQuestMaster()
    {
        $msg = '';
        $sql = "SELECT ma.area_id, ma.quest_id, ma.name_ja "
             . "FROM m_area ma "
             . "WHERE NOT EXISTS(SELECT * FROM m_quest mq WHERE mq.quest_id = ma.quest_id)";
        $res = $this->db_r->db->GetAll($sql);
        if ($res === false){
            // select エラーの場合のエラーを返す
            return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
        }

        $cnt=0;
        if (count($res) > 0){
            foreach($res as $k => $v){
                $msg[] = "[ area_id ]" . $v['area_id'] . " : " . $v['quest_id'] . "(" . $v['name_ja'] . ")が[ m_quest ]に存在しません。";
                $cnt++;
            }
        }
        return array('msg' => $msg, 'cnt' => $cnt);
    }

    /**
     * エリアエネミーマスタの整合性チェック(クエストマスタとの比較)
     *
     * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト 
     */
    public function checkConsistencyAreaEnemyMasterForAreaMaster()
    {
        $msg = '';
        //$sql = "SELECT mae.area_enemy_id, mae.area_id, ma.name_ja "
        $sql = "SELECT mae.enemy_id, mae.area_id "
             . "FROM m_area_enemy mae "
             . "WHERE NOT EXISTS(SELECT * FROM m_area ma WHERE mae.area_id = ma.area_id)";
        $res = $this->db_r->db->GetAll($sql);
        if ($res === false){
            // select エラーの場合のエラーを返す
            return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
        }

        $cnt=0;
        if (count($res) > 0){
            foreach($res as $k => $v){
               //$msg[] = $v['area_enemy_id'] . ":" . $v['area_id'] . "(" . $v['name_ja'] . ")";
                $msg[] = "[ enemy_id ]" . $v['enemy_id'] . " : " . $v['area_id'] . "が[ m_area ]に存在しません。";
                $cnt++;
            }
        }
        return array('msg' => $msg, 'cnt' => $cnt);
    }

    /**
     * エリアエネミーマスタの整合性チェック(モンスターマスタとの比較)
     *
     * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト 
     */
    public function checkConsistencyAreaEnemyMasterForQuestEnemyMaster()
    {
        $msg = '';
        $sql = "SELECT mae.area_id, mae.enemy_id "
             . "FROM m_area_enemy mae "
             . "WHERE NOT EXISTS(SELECT * FROM m_quest_enemy mqe WHERE mae.enemy_id = mqe.enemy_id)";
        $res = $this->db_r->db->GetAll($sql);
        if ($res === false){
            // select エラーの場合のエラーを返す
            return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
        }

        $cnt=0;
        if (count($res) > 0){
            foreach($res as $k => $v){
               //$msg[] = $v['area_enemy_id'] . ":" . $v['monster_id'] . "(" . $v['name_ja'] . ")";
                $msg[] = "[ enemy_id ]" . $v['enemy_id'] . " : " . $v['area_id'] . "が[ m_quest_enemy ]に存在しません。";
                $cnt++;
            }
        }
        return array('msg' => $msg, 'cnt' => $cnt);
    }

    /**
     * エリアボスマスタの整合性チェック(エリアマスタとの比較)
     *
     * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト 
     */
    public function checkConsistencyAreaBossCoefficientMasterForAreaMaster()
    {
        $msg = '';
        //$sql = "SELECT mabc.area_boss_coefficient_id, mabc.area_id, ma.name_ja "
        $sql = "SELECT mabc.area_id "
             . "FROM m_area_boss_coefficient mabc "
             . "WHERE NOT EXISTS(SELECT * FROM m_area ma WHERE mabc.area_id = ma.area_id)";
        $res = $this->db_r->db->GetAll($sql);
        if ($res === false){
            // select エラーの場合のエラーを返す
            return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
        }

        $cnt=0;
        if (count($res) > 0){
            foreach($res as $k => $v){
                //$msg[] = $v['area_boss_coefficient_id'] . ":" . $v['area_id'] . "(" . $v['name_ja'] . ")";
                $msg[] = "[ area_id ]" . $v['area_id'] . "が[ m_area ]に存在しません。";
                $cnt++;
            }
        }
        return array('msg' => $msg, 'cnt' => $cnt);
    }

    /**
     * バトルマスタの整合性チェック(エリアマスタとの比較)
     *
     * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト 
     */
    public function checkConsistencyBattleMasterForAreaMaster()
    {
        $msg = '';
        //$sql = "SELECT mb.battle_id, mb.area_id, ma.name_ja "
        $sql = "SELECT mb.area_id, mb.quest_id "
             . "FROM m_battle mb "
             . "WHERE NOT EXISTS(SELECT * FROM m_area ma WHERE mb.area_id = ma.area_id)";
        $res = $this->db_r->db->GetAll($sql);
        if ($res === false){
            // select エラーの場合のエラーを返す
            return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
        }

        $cnt=0;
        if (count($res) > 0){
            foreach($res as $k => $v){
                //$msg[] = $v['battle_id'] . ":" . $v['area_id'] . "(" . $v['name_ja'] . ")";
                $msg[] = "[ area_id ]" . $v['area_id'] . "が[ m_area ]に存在しません。";
                $cnt++;
            }
        }
        return array('msg' => $msg, 'cnt' => $cnt);
    }

}
