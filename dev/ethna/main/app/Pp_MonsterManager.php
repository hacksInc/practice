<?php
/**
 *  Pp_MonsterManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_MonsterManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_MonsterManager extends Ethna_AppManager
{
	/** モンスター図鑑ステータス：敵として出会った */
	const BOOK_STATUS_MET = 1;

	/** モンスター図鑑ステータス：入手したことがある */
	const BOOK_STATUS_GOT = 2;
	
	/** モンスター図鑑の各カラムのバイト長  300ビットを16進数表記の文字列にして75バイト */
	const BOOK_COL_LEN = 75;
	
	/** モンスター図鑑のカラム個数 */
	const BOOK_COL_NUM  = 7;
	
	/** m_monsterのattribute_idとt_user_achievement_countのカラム名との対応関係 */
	var $ATTRIBUTE_ID_TO_ACHIEVEMENT_COLNAME = array(
		1 => 'get_monster_fire',
		2 => 'get_monster_water',
		3 => 'get_monster_tree',
		4 => 'get_monster_light',
		5 => 'get_monster_dark',
	);
	
	/**
	 * t_user_monsterの処理で使用するキー名の配列
	 */
	var $USER_MONSTER_PROCESS_COLNAME_LIST = array(
		'user_monster_id',
		'monster_id',
		'exp',
		'lv',
		'hp_plus',
		'attack_plus',
		'heal_plus',
		'skill_lv',
		'badge_num',
		'badges',
	);
	
	/**
	 * t_user_monsterのデータをAPI戻り値にする際に使用するキー名の配列
	 */
	var $USER_MONSTER_API_RESPONSE_COLNAME_LIST = array(
		'user_monster_id',
		'monster_id',
		'exp',
		'lv',
		'hp_plus',
		'attack_plus',
		'heal_plus',
		'skill_lv',
		'badge_num',
		'badges',
	);

	/**
	 * ユーザー所持モンスター一覧
	 * @var array $user_monster_list[user_id] = array
	 */
	protected $user_monster_list = array();

	/**
	 * ユーザー所持モンスター一覧（付加情報付き）
	 * @var array $user_monster_list_ex[user_id] = array
	 */
	protected $user_monster_list_ex = array();
	
	/**
	 * ユーザー所持モンスター一覧（user_monster_idがキー）
	 * @var array $user_monster_assoc[user_id] = array
	 */
	protected $user_monster_assoc = array();
	
	/**
	 * モンスター図鑑一覧（monster_idがキー）
	 * 
	 * この変数上では、各行のdate_met, date_gotが存在しない場合があるが、
	 * その場合でも function getUserMonsterbookAssocの戻り値には付加される。
	 * @var array $user_monster_assoc[user_id] = array
	 */
	protected $user_monster_book_assoc = array();
	
	/**
	 * モンスター図鑑（ビットフラグ）
	 * @var array $user_monster_book_bits[user_id] = array
	 */
	protected $user_monster_book_bits = array();
	
	/**
	 * モンスター図鑑（ビットフラグ）はINSERT対象か
	 * @var array $user_monster_book_bits_insert_flg[user_id] = bool
	 */
	protected $user_monster_book_bits_insert_flg = array();
	
	/**
	 * モンスター図鑑（ビットフラグ）がUPDATE対象の場合のカラム名
	 * @var array $user_monster_book_bits_update_colnames[user_id] = array(カラム名, カラム名, ...)
	 */
	protected $user_monster_book_bits_update_colnames = array();
	
	/**
	 * モンスターIDと図鑑DB用索引の対応
	 * $monster_id_to_book_idx[monster_id] = book_idx
	 */
	protected $monster_id_to_book_idx = null;
	
	/**
	 * 図鑑DB用索引とモンスターIDの対応
	 * $book_idx_to_monster_id[book_idx] = monster_id
	 */
	protected $book_idx_to_monster_id = null;
	
	/** 現在日時(Y-m-d H:i:s) */
	protected $now = null;

	/**
	 * コンストラクタ
	 */
	function __construct ( &$backend )
	{
		parent::__construct( $backend );
		
		$this->now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
	}

	/**
	 * ユーザー所持モンスターを付加情報つきで取得する
	 */
	function getUserMonsterEx($user_id, $user_monster_id)
	{
		$this->loadUserMonsterAssoc($user_id);
		
		if ($this->user_monster_assoc[$user_id] && 
			isset($this->user_monster_assoc[$user_id][$user_monster_id])
		) {
			return $this->user_monster_assoc[$user_id][$user_monster_id];
		}
	}
	
	/**
	 * ユーザー所持モンスター一覧を取得する
	 */
	function getUserMonsterList($user_id)
	{
		$this->loadUserMonsterList($user_id);
		
		return $this->user_monster_list[$user_id];
	}
	
	/**
	 * API応答用のユーザ所持モンスター一覧を取得する
	 */
	function getUserMonsterListForApiResponse($user_id)
	{
		$this->loadUserMonsterList($user_id);

		$list = array();
		if (is_array($this->user_monster_list[$user_id])) {
			$list = Pp_Util::arrayMultiColumns($this->user_monster_list[$user_id], $this->USER_MONSTER_API_RESPONSE_COLNAME_LIST);
		}
		
		return $list;
	}

	/**
	 * ユーザー所持モンスター一覧（付加情報付き）を取得する
	 */
	function getUserMonsterListEx($user_id)
	{
		$this->loadUserMonsterListEx($user_id);
		
		return $this->user_monster_list_ex[$user_id];
	}

	/**
	 * ユーザー所持モンスター一覧（user_monster_idがキー）を取得する
	 */
	function getUserMonsterAssoc($user_id)
	{
		$this->loadUserMonsterAssoc($user_id);
		
		return $this->user_monster_assoc[$user_id];
	}

	/**
	 * ユーザー所持モンスター一覧関連のキャッシュを消去する
	 * 
	 * @param int $user_id ジャグモン内ユーザID
	 * @return void
	 */
	function clearUserMonsterListCache($user_id)
	{
		 if (isset($this->user_monster_list[$user_id])) {
			 unset($this->user_monster_list[$user_id]);
		 }

		 if (isset($this->user_monster_list_ex[$user_id])) {
			 unset($this->user_monster_list_ex[$user_id]);
		 }

		 if (isset($this->user_monster_assoc[$user_id])) {
			 unset($this->user_monster_assoc[$user_id]);
		 }
	}
	
	/**
	 * モンスターマスター情報を取得する
	 * 
	 * @param array $monster_id モンスターマスタID
	 * @return array モンスターマスター情報（m_monsterテーブルのカラム名がキー）
	 */
	function getMasterMonster($monster_id)
	{
		//TODO 毎回クエリ発行せず、一旦Webサーバに全件取得して共有メモリキャッシュとかの方がいいのか？
		$param = array($monster_id);
		$sql = "SELECT * FROM m_monster WHERE monster_id = ?";

		return $this->db_r->db->GetRow($sql, $param);
	}

	/**
	 * モンスターマスター情報一覧（monster_idがキー）を取得する
	 * 
	 * @param array $monster_id_list モンスターマスタIDの配列。省略すると全件取得になる。
	 * @return array モンスターマスター情報一覧（monster_idがキー）
	 */
	function getMasterMonsterAssoc($monster_id_list = null)
	{
		//TODO 都度必要なIDだけSQL発行する方式でいいのか？　一旦Webサーバに全件取得して共有メモリキャッシュとかの方がいいのか？
		$sql = "SELECT * FROM m_monster";
		if (!$monster_id_list) {
			return $this->db_r->db->GetAssoc($sql);
		} else {
			$sql .= " WHERE monster_id IN(" . str_repeat('?,', count($monster_id_list) - 1) . "?)";
			return $this->db_r->db->GetAssoc($sql, $monster_id_list);
		}
	}

	/**
	 * モンスターユニークIDからモンスターマスター情報一覧を取得する
	 */
	function getMasterMonsterAssocFromUserMonsterIdList($user_id, $user_monster_id_list)
	{
		$this->loadUserMonsterAssoc($user_id);

		$monster_id_list = array();
		foreach ($user_monster_id_list as $user_monster_id) {
			$monster_id_list[] = $this->user_monster_assoc[$user_id][$user_monster_id]['monster_id'];
		}

		return $this->getMasterMonsterAssoc($monster_id_list);
	}
	
	/**
	 * モンスターマスター情報一覧（monster_idがキー）をレアリティ指定で取得する
	 * 
	 * @param int $rare レアリティ（1～6）
	 * @return array モンスターマスター情報一覧（monster_idがキー）
	 */
	function getMasterMonsterListByRare($rare, $all = true, $offset = 0, $limit = 20 )
	{
		if ( $all ) {
			$param = array( $rare );
		//	$sql = "SELECT * FROM m_monster WHERE m_rare = ? AND gacha_flag = 1";
			$sql = "SELECT * FROM m_monster WHERE gacha_flag = ?";
		} else {
			$param = array( $rare, $offset, $limit );
		//	$sql = "SELECT * FROM m_monster WHERE m_rare = ? AND gacha_flag = 1 ORDER BY monster_id ASC LIMIT ?, ?";
			$sql = "SELECT * FROM m_monster WHERE gacha_flag = ? ORDER BY monster_id ASC LIMIT ?, ?";
		}
	//	return $this->db->db->GetAssoc($sql, $rare);
		return $this->db_r->GetAll($sql, $rare);
	}

    /**
     * 売却前のモンスターチェックをする
     * 
     * @param array $user_base 
     * @param array $sell_monster_list モンスターユニークIDの配列
     * @return boolean
     */
    function checkSellMonster($user_base, $sell_monster_list)
    {

        // 所持チェック＆チーム所属チェック
        if (!$this->isDeletable($user_base['user_id'], $sell_monster_list)) {
            return Ethna::raiseError("Not deletable.", E_USER_ERROR);
        }

        return true;

    }

    /**
     * 売却する
     * 
     * @param array $user_base 
     * @param array $user_monster_list モンスターユニークIDの配列
     * @return boolean
     */
    function sell($user_base, $sell_monster_list, $user_monster_assoc, $master_monster_assoc)
    {

        $user_m = $this->backend->getManager('User');
        $user_id = $user_base['user_id'];

        $price_sum = 0;
        foreach ($sell_monster_list as $user_monster_id) {
            $user_monster = $user_monster_assoc[$user_monster_id];
            $master_monster = $master_monster_assoc[$user_monster['monster_id']];

            $tracking_columns = $this->getBeforeTrackingColumnsFromUserMonster($user_monster);

            // 売却額を求める
            // （売却値段*レベル）+（1000*各+補正値の合計）
            //TODO レベルはDBに持つのではなく都度計算に変更しましょうという話だったかも…　要確認
            $price = ($master_monster['sell_price'] * $user_monster['lv']) +
                (1000 * ($user_monster['hp_plus'] + $user_monster['attack_plus'] + $user_monster['heal_plus']));
            $price_sum += $price;

            // 削除
            $ret = $this->delete($user_id, $user_monster_id, $tracking_columns);
            if (!$ret || Ethna::isError($ret)) {
                return $ret;
            }
            $sell_monster_data[$user_monster_id] = array_merge($user_monster, array('price' => $price));

        }

        // 入金
        //上限チェック
        $user_base['gold'] += $price_sum;
        if ($user_base['gold'] > 999999999) $user_base['gold'] = 999999999;

        $ret = $user_m->setUserBase($user_id, array('gold' => $user_base['gold']));
        if (!$ret || Ethna::isError($ret)) {
            return $ret;
        }

        // KPI
        $user_m->setUserBaseIncreaseKpi($user_id, 'gold', $price_sum);

        return array(
            'price_sum' => $price_sum,
            'sell_monster_data' => $sell_monster_data,
        );

    }

	/**
	 * スキルマスター情報を取得する
	 * 
	 * @param array $skill_id スキルID
	 * @return array スキル情報（m_skillテーブルのカラム名がキー）
	 */
	function getMasterSkill($skill_id)
	{
		//TODO 毎回クエリ発行せず、一旦Webサーバに全件取得して共有メモリキャッシュとかの方がいいのか？
		$param = array($skill_id);
		$sql = "SELECT * FROM m_skill WHERE skill_id = ?";

		return $this->db_r->db->GetRow($sql, $param);
	}
	
    /**
     * モンスター合成前のモンスター所持チェックを行う
     * 
     * @param int $user_id
     * @param int $base_user_monster_id ベースモンスターのモンスターユニークID
     * @param array $material_user_monster_id_list 素材モンスターのモンスターユニークIDの配列
     * @return bool|Ethna_Error true:成功, falseまたはEthna_Error:失敗
     */
    function checkSynthesisMonster($user_id, $base_user_monster_id, $material_user_monster_id_list, $user_monster_assoc)
    {

        // ベースモンスターの所持チェック
        if ($this->_checkHaveMonsterSynthesisBase($base_user_monster_id, $user_monster_assoc, $user_id) === false){
            error_log("monster:checkSynthesisMonster own error base=$base_user_monster_id");
            return false;
        }

        // 素材モンスターの所持チェック＆チーム所属チェック
        if ($this->_checkHaveMonsterSynthesisMaterial($material_user_monster_id_list, $user_id) === false){
            error_log("monster:checkSynthesisMonster material error base=$base_user_monster_id material=".print_r($material_user_monster_id_list,true));
            return false;
        }

        // ベースモンスターと素材モンスターが異なる事を確認
        if ($this->_checkDuplicateMonsterSynthesis($base_user_monster_id, $material_user_monster_id_list, $user_id) === false){
            error_log("monster:checkSynthesisMonster base material error base=$base_user_monster_id material=".print_r($material_user_monster_id_list,true));
            return false;
        }

        return true;

    }

    /**
     * パワーアップ合成を実行する
     *
     * @param int $user_id
     * @param int $base_user_monster_id ベースモンスターのモンスターユニークID
     * @param array $material_user_monster_id_list 素材モンスターのモンスターユニークIDの配列
     * @return bool|Ethna_Error true:成功, falseまたはEthna_Error:失敗
     */
    function execPowerupSynthesis($user_id, $base_user_monster_id, $material_monster_list, $exp_data, $gold_data, $user_monster_assoc, $master_monster_assoc)
    {
        $material_user_monster_id_num = count($material_monster_list);

        $base_user_monster = $user_monster_assoc[$base_user_monster_id];
        $base_master_monster = $master_monster_assoc[$base_user_monster['monster_id']];

        $base_tracking_columns = $this->getBeforeTrackingColumnsFromUserMonster($base_user_monster);

        $material_tracking_columns_assoc = array();

        // コインの消費
        $user_m = $this->backend->getManager('User');
        $ret = $user_m->setUserBase($user_id, array(
            'gold' => $gold_data['gold'] - $gold_data['cost'],
        ));
        if (!$ret || Ethna::isError($ret)) {
            return $ret;
        }

        $this->backend->logger->log(LOG_INFO, 
            'execPowerupSynthesis. gold=[' . $gold_data['cost'] . '] base_gold=[' . $gold_base['gold'] . ']'
        );

        // t_user_monsterに存在する引数の素材モンスターユニークID（user_monster_id）をキーにデリートする
        foreach ($material_monster_list as $material_user_monster_id) {
            $ret = $this->delete($user_id, $material_user_monster_id, 
                $material_tracking_columns_assoc[$material_user_monster_id]
            );
            if (!$ret || Ethna::isError($ret)) {
                return $ret;
            }
        }

        $columns = array(
            'exp' => $exp_data['new_exp'],
            'lv' => $exp_data['new_lv'],
            'skill_lv' => $exp_data['skill_lv'],
        );
		$ret = $this->setUserMonster($user_id, $base_user_monster_id, $columns, $base_tracking_columns);
		if (!$ret || Ethna::isError($ret)) {
			return $ret;
		}
		
		// TODO:t_user_baseから合成に必要な進化メダルを減らして保存　…パワーアップ合成でも進化メダル減らすのかどうか不明
		
		// KPI
		$this->backend->getManager('Periodlog')->logPeriodUserAccumu(
			$user_id, null,
			Pp_PeriodlogManager::ACTION_TYPE_SYNTHESIS_MATERIAL_NUM, 
			Pp_PeriodlogManager::PERIOD_TYPE_DAILY,
			$material_user_monster_id_num
		);

		$user_m->setUserBaseDecreaseKpi($user_id, 'gold', $gold_data['cost']);
		
		return true;
	}

    /**
     * 合成した場合に消費す費用(コイン)を計算する
     *
     * @param array $user_base
     * @param array $base_monster
     * @param array $material_monster_list
     * @return array 
     */
    public function getGoldPowerupMonsterSynthesis($user_base, $base_monster, $material_monster_list)
    {
        // 合成モンスター数
        $material_monster_num = count($material_monster_list);

        // ベースモンスターのレベル×10×合成するモンスターの数
        $cost = $base_monster['lv'] * 10 * $material_monster_num;

        // ゴールドが足りなければエラー
        if ($user_base['gold'] < $cost) {
            return false;
        }

        return array('gold' => $user_base['gold'], 'cost' => $cost);

    }

    /**
     * 合成した場合に得られる経験値を計算する
     *
     * @param array $base_monster
     * @param array $material_monster_list
     * @return array 
     */
    public function getExpPowerupMonsterSynthesis($base_monster, $material_monster_list, $user_monster_list, $master_monster_list)
    {

        // 経験値を求める
        $material_monster_num = count($material_monster_list);

        $base_user_monster = $user_monster_list[$base_monster];
        $base_master_monster = $master_monster_list[$base_user_monster['monster_id']];

        //スキルのマスタデータを取得
        $skill_id = $master_monster_list[$base_user_monster['monster_id']]['skill_id'];
        $skill_data = $this->getMasterSkill($skill_id);

        $exp_sum = 0;
        $skill_lv_up = 0;
        foreach ($material_monster_list as $material_monster) {
            $skill_lv = 0;
            $attribute_match = 0;
            $skill_match = 0;
            $material_user_monster = $user_monster_list[$material_monster];
            $material_master_monster = $master_monster_list[$material_user_monster['monster_id']];

            $material_tracking_columns_assoc[$material_monster] = $this->getBeforeTrackingColumnsFromUserMonster($material_user_monster);

            // 素材モンスターに「＋」が付いている場合、＋1毎に1000の費用が加算
            /*foreach (array('hp_plus', 'attack_plus', 'heal_plus') as $colname) {
                $gold += $material_user_monster[$colname] * 1000;
            }*/

            //8.増加経験値×素材レベルをベースモンスターのt_user_monsterの経験値に加える
            // t_user_monsterのmonster_idをキーにm_monsterからモンスターマスタデータを取得し、増加経験値（exp）を参照する
            //※ベースと素材の属性一致で1.5倍のボーナスが加算
            //---
            //増加経験値×素材レベル
            $exp = $material_master_monster['synthesis_exp'] * $material_user_monster['lv'];
            if ($base_master_monster['attribute_id'] == $material_master_monster['attribute_id']) {
                $exp = floor($exp * 1.5);
                $attribute_match = 1;
            }
            $exp_sum += $exp;

            // 同じスキルを持つモンスターを合成すると一定確率でスキルLVがアップするので素材とベースのスキルとスキルLVを確認する
            // 確率は素材モンスターのスキルレベルを元に確率テーブルを参照（やめた）
            // 確率は一律18％（やめた）
            // 確率はベースモンスターのスキルレベルを元に確率テーブルを参照
            if ($base_master_monster['skill_id'] == $material_master_monster['skill_id'] && $base_master_monster['skill_id'] != 0) {
                $skill_match = 1;
            //	$probability = $this->getSynthesisSkillLvUpProbability($material_user_monster['skill_lv']);//確率テーブル（やめた）
            //  $probability = 18;//一律18％
            	$probability = $this->getSynthesisSkillLvUpProbability($base_user_monster['skill_lv'] + $skill_lv_up, $skill_data['max_skill_lv']);//確率テーブルwithスキル最大レベル
                if (mt_rand(0, 99) < $probability) {
                    $skill_lv_up += 1;
                    $skill_lv = 1;
                }
            }

            // ログ出力用データ
            $material_monster_result[$material_monster]['add_exp'] = $exp;
            $material_monster_result[$material_monster]['add_skill_lv'] = $skill_lv;
            $material_monster_result[$material_monster]['attribute_match'] = $attribute_match;
            $material_monster_result[$material_monster]['skill_match'] = $skill_match;
        }

        // 経験値からレベルを算出
        $base_exp_type = $master_monster_list[$base_user_monster['monster_id']]['exp_type'];
        $new_exp = $base_user_monster['exp'] + $exp_sum;
        $new_lv = $this->getMonsterLv($new_exp, $base_exp_type);

        //最大レベルに達していないかチェック
        $base_max_lv = $master_monster_list[$base_user_monster['monster_id']]['max_lv'];
        if ($new_lv >= $base_max_lv) {
            $new_lv = $base_max_lv;
            $new_exp = $this->getMonsterExp($new_lv, $base_exp_type);
        }

        //スキルマスタの取得処理は上に移動
        $skill_lv = $base_user_monster['skill_lv'];//現在のスキルレベル
        //スキルのレベルアップ
        if ($skill_lv_up > 0 && $skill_data['max_skill_lv'] > $base_user_monster['skill_lv']) {
            //$columns['skill_lv'] = $base_user_monster['skill_lv'] + $skill_lv_up;//この関数内で使用されていないからコメントアウト
            $skill_lv = $base_user_monster['skill_lv'] + $skill_lv_up;
            if ($skill_lv > $skill_data['max_skill_lv']) $skill_lv = $skill_data['max_skill_lv'];//上限チェック
        }

        return array(
            'exp_sum' => $exp_sum,
            'skill_lv_up' => $skill_lv_up,
            'new_exp' => $new_exp,
            'new_lv' => $new_lv,
            'skill_lv' => $skill_lv,
            'material_monster_result' => $material_monster_result,
        );

    }

	protected function getBeforeTrackingColumnsFromUserMonster($user_monster)
	{
		return array(
			'monster_id_before'  => $user_monster['monster_id'],
			'exp_before'         => $user_monster['exp'],
			'lv_before'          => $user_monster['lv'],
			'hp_plus_before'     => $user_monster['hp_plus'],
			'attack_plus_before' => $user_monster['attack_plus'],
			'heal_plus_before'   => $user_monster['heal_plus'],
			'skill_lv_before'    => $user_monster['skill_lv'],
		);
	}

    /**
     * 進化合成を実行する
     * 
     * @param int $user_id
     * @param int $base_user_monster_id ベースモンスターのモンスターユニークID
     * @param array $material_user_monster_id_list 素材モンスターのモンスターユニークID（配列）
     * @return bool|Ethna_Error true:成功, falseまたはEthna_Error:失敗
     */
    function execEvolutionSynthesis($user_id, $base_user_monster_id, $material_user_monster_id_list, $gold_data, $user_monster_assoc, $master_monster_assoc)
    {

        // 所持モンスター情報取得
        $base_user_monster = $user_monster_assoc[$base_user_monster_id];
        $base_master_monster = $master_monster_assoc[$base_user_monster['monster_id']];
        $evolution_data = $base_master_monster['evolution_material'];

        $base_tracking_columns = $this->getBeforeTrackingColumnsFromUserMonster($base_user_monster);

        // モンスターマスター情報取得
        $material_user_monsters = array();
        //$material_tracking_columns = $this->getBeforeTrackingColumnsFromUserMonster($material_user_monster);
        foreach ($material_user_monster_id_list as $material_user_monster_id) {
            $material_user_monsters[] = $material_user_monster = $user_monster_assoc[$material_user_monster_id];
            $material_tracking_columns_assoc[$material_user_monster_id] = $this->getBeforeTrackingColumnsFromUserMonster($material_user_monster);
        }

        // 進化メダル所持チェック
        $ret = $this->hasEnoughEvolutionMedal($user_id, $base_master_monster);
        if (!$ret || Ethna::isError($ret)) {
            $this->backend->logger->log(LOG_INFO, 'Medal shortage. user_id=[' . $user_id . '] base_user_monster_id=[' . $base_user_monster_id . ']');
            return $ret;
        }

        //進化してもスキルが同じか？同じだったらスキルレベルを引き継ぐ
        $evolution_monster_id = $base_master_monster['evolution_monster_id'];
        $new_skill_lv = 1;
        // $evolution_master_monster = $master_monster_assoc[$evolution_monster_id];//進化後のマスタデータ
        $evolution_master_monster = $this->getMasterMonster($evolution_monster_id);//進化後のマスタデータ
        if ($base_master_monster['skill_id'] == $evolution_master_monster['skill_id']) {
            $new_skill_lv = $base_user_monster['skill_lv'];
        }

        // 進化させる
        // m_monsterからベースモンスターの進化先monster_idを参照し、ベースモンスターのt_user_monsterのmonster_idを進化先monster_idに変える
        // また、ベースモンスターの経験値は0になる
        // スキルレベルは進化前後で同じ時のみ引き継ぐ事ができる
        // 付加パラメータについては不明だったので、当初の仕様通り引き継がせる
        $ret = $this->setUserMonster($user_id, $base_user_monster_id, array(
            'monster_id' => $evolution_monster_id,
            'exp' => 0,
            'lv' => 1,
            'skill_lv' => $new_skill_lv,
            'hp_plus' => 0,
            'attack_plus' => 0,
            'heal_plus' => 0,
        ), $base_tracking_columns);
        if (!$ret || Ethna::isError($ret)) {
            return $ret;
        }

        //モンスター図鑑を更新
        $evolution_monster_id = $base_master_monster['evolution_monster_id'];
        $ret = $this->setUserMonsterBook($user_id, $evolution_monster_id, Pp_MonsterManager::BOOK_STATUS_GOT);
        if (!$ret || Ethna::isError($ret)) {
            return $ret;
        }

        //勲章システム（モンスター属性別取得個数）
        $this->achievementGetMonster($user_id, $evolution_monster_id);

        // 素材モンスターを削除
        // t_user_monsterに存在する引数の素材モンスターユニークID（user_monster_id）をキーにデリート（または無効化）する
        foreach ($material_user_monster_id_list as $material_user_monster_id) {
            $ret = $this->delete($user_id, $material_user_monster_id, $material_tracking_columns_assoc[$material_user_monster_id]);
            if (!$ret || Ethna::isError($ret)) {
                return $ret;
            }
        }

        // ゴールド消費
        $user_m = $this->backend->getManager('User');
        $ret = $user_m->setUserBase($user_id, array(
            'gold' => $gold_data['gold'] - $gold_data['cost'],
        ));
        if (!$ret || Ethna::isError($ret)) {
            return $ret;
        }

        //進化メダルを減らす処理もここで
        //進化メダル所持チェックは↑で行なっているからここでは行わない
        $medal_idx = 0;
        $item_m = $this->backend->getManager('Item');
        foreach (array(
            'evolution_medal1', 'evolution_medal2', 'evolution_medal3', 'evolution_medal4', 'evolution_medal5'
        ) as $colname) {
            //進化メダルのアイテムID
            $medal_item_id = Pp_ItemManager::ITEM_RARE_MEDAL1 + $medal_idx;
            //減らす
            $ret = $item_m->addUserItem($user_id, $medal_item_id, (-1 * $base_master_monster[$colname]));
            if (!$ret || Ethna::isError($ret)) {
                return $ret;
            }
            $medal_idx++;
        }

        // KPI
        $this->backend->getManager('Periodlog')->logPeriodUserAccumu(
            $user_id, null,
            Pp_PeriodlogManager::ACTION_TYPE_SYNTHESIS_MATERIAL_NUM, 
            Pp_PeriodlogManager::PERIOD_TYPE_DAILY
        );

        return true;
    }

    /**
     * 進化合成ベースモンスターチェック
     *
     * @param array
     * @return mixed
     */
    public function checkBaseEvolutionMonster($base_user_monster, $base_master_monster)
    {

        // ベースモンスターのレベル最大チェック
        // t_user_monsterのmonster_idをキーにm_monsterからモンスターマスタデータを取得し、
        // ベースモンスターのレベルが最大かどうかをチェックし、違っていたらエラー
        if ($base_user_monster['lv'] < $base_master_monster['max_lv']) {
            return Ethna::raiseError("Base level is not max. [%d] [%d]", E_USER_ERROR, $base_user_monster['lv'], $base_master_monster['max_lv']);
        }

        // ベースモンスターの進化可否チェック
        // m_monsterからベースモンスターの進化先monster_idを参照し、進化可能なモンスターかどうかチェックし、進化できないモンスターだったらエラー
        $evolution_monster_id = $base_master_monster['evolution_monster_id'];
        if ($evolution_monster_id <= 0) {
            return Ethna::raiseError("evolution_monster_id[%d]", E_USER_ERROR, $base_master_monster['evolution_monster_id']);
        }

        return true;

    }

    /**
     * 進化合成素材モンスターチェック
     *
     * @param array $evolution_material_monster
     * @param array $material_user_monster
     * @return mixed
     */
    public function checkMaterialEvolutionMonster($evolution_material_monster, $material_user_monsters)
    {

        // 進化合成に必要なモンスターを全て所持しているかチェック
        $evolution_monsters = array();
        if (strlen($evolution_material_monster) > 0) {
            $evolution_monsters = explode(",", $evolution_material_monster);
        }

        // 必要なモンスターが設定されているかチェック
        if (count($evolution_monsters) == 0) {
            return Ethna::raiseError("Need monsters nothing.", E_USER_ERROR);
        }

        // モンスターの数が合っているかチェック
        if (count($evolution_monsters) != count($material_user_monsters)) {
            return Ethna::raiseError("Different material monsters num. need monsters num[%d] material user monsters num[%d]", E_USER_ERROR, 
                    count($evolution_monsters), count($material_user_monsters));
        }

        // というか、このチェックは何をやっているの？？
        //初期化
        $monster_num_max = 0;
        $evolution_monsters_chk = array();
        foreach ($evolution_monsters as $key => $val) {
            $evolution_monsters_chk[$key] = array();
            $evolution_monsters_chk[$key]['monster_id'] = $val;
            $evolution_monsters_chk[$key]['own'] = 0;
            $monster_num_max++;
        }

        //チェックする
        $monster_num = 0;
        $material_id = array();
        foreach ($material_user_monsters as $keym => $valm) {
            $material_id[] = $valm['monster_id'];
            foreach ($evolution_monsters_chk as $keye => $vale) {
                if ($evolution_monsters_chk[$keye]['own'] == 0 && $vale['monster_id'] == $valm['monster_id']) {
                    $evolution_monsters_chk[$keye]['own'] = 1;
                    $monster_num++;
                    break;
                }
            }
        }
        // (ここまで！)というか、このチェックは何をやっているの？？

        //必要なモンスターの数が合わない
        if ($monster_num_max != $monster_num) {
            $materials = implode(",", $material_id);
            return Ethna::raiseError("Different material monsters. need monsters[%s] material user monsters[%s]", E_USER_ERROR, 
                    $evolution_material_monster, $materials);
        }

        return true;

    }

    /**
     * 進化合成した場合に消費す費用(コイン)を計算する
     *
     * @param array $user_base
     * @param array $base_monster
     * @param array $material_monster_list
     * @return array 
     */
    public function getGoldEvolutionMonsterSynthesis($user_base, $base_user_monster, $material_monster_list)
    {
        // 必要ゴールドチェック
        // ベースモンスターのレベル×10×合成するモンスターの数(1)
        //// 素材モンスターに「＋」が付いている場合、＋1毎に1000の費用が加算
        $cost = $base_user_monster['lv'] * 10;
        //foreach (array('hp_plus', 'attack_plus', 'heal_plus') as $colname) {
        //	$cost += $material_user_monster[$colname] * 1000;
        //}

        // ゴールドが足りなければエラー
        if ($user_base['gold'] < $cost) {
            return Ethna::raiseError("user_base[%d] gold[%d]", E_USER_ERROR, $user_base['gold'], $cost);
        }

        return array('gold' => $user_base['gold'], 'cost' => $cost);

    }

	/**
	 * モンスター図鑑へ登録する
	 * 
	 * @param int $user_id ユーザID
	 * @param int $monster_id モンスターマスタID
	 * @param int $status ステータス（敵として出会ったか、入手したことがあるか）
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserMonsterBook($user_id, $monster_id, $status)
	{
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':setUserMonsterBook:user_id=[' . $user_id . '] monster_id=[' . $monster_id . '] status=[' . $status . ']');		
		$this->setUserMonsterBookVar($user_id, $monster_id, $status);
		$ret = $this->saveUserMonsterBookBits($user_id);
		
		return $ret;
	}

	/**
	 * モンスター図鑑へ登録する（変数へのセットのみ）
	 * 
	 * @param int $user_id ユーザID
	 * @param int $monster_id モンスターマスタID
	 * @param int $status ステータス（敵として出会ったか、入手したことがあるか）
	 * @param string $date 取得日時or遭遇日時（省略可）Y-m-d H:i:s
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserMonsterBookVar($user_id, $monster_id, $status, $date = null)
	{
//error_log('setUserMonsterBookVar:[' . $user_id . '][' . $monster_id . '][' . $status . '][' . $date . ']');

		$this->loadUserMonsterBook($user_id);
		
		// ビットフラグを求める
		$new_row = $this->encodeUserMonsterBookDiff(
			$this->user_monster_book_bits[$user_id], $monster_id, $status, $date
		);

		if (Ethna::isError($new_row)) { // 空配列の場合はエラーではないので、!$new_rowのチェックはせず、Ethna::isErrorのチェックのみ行なう。
			return $new_row;
			}

		// 変更があったカラムをこのオブジェクト内の変数に保持
		foreach ($new_row as $k => $v) {
			$this->user_monster_book_bits[$user_id][$k] = $v;
			
			if (!$this->user_monster_book_bits_insert_flg[$user_id]) {
				if (!in_array($k, $this->user_monster_book_bits_update_colnames[$user_id])) {
					$this->user_monster_book_bits_update_colnames[$user_id][] = $k;
				}
			}
		}
		
//		$this->user_monster_book_assoc[$user_id] = $this->decodeUserMonsterBookBits($this->user_monster_book_bits[$user_id]);
		$this->user_monster_book_assoc[$user_id][$monster_id] = array(
			'user_id'    => $user_id,
			'monster_id' => $monster_id,
			'status'     => $status,
		);
		
			return true;
		}
		
	/**
	 * 図鑑DB用索引の値を除算する
	 * 
	 * 戻り値の書式について：
	 * array('colname_suffix' => カラム名末尾の番号, 
	 *    'hex_position' => カラム内で16進数で何桁目か, …カラムはchar asciiなので、カラムの末尾から数えて何文字目かに相当　ゼロ始まりで数える 
	 *    'char_position' => カラム内で何文字目か, …カラム(char ascii)の先頭（左端）からゼロ始まりで数える。self::BOOK_COL_LEN - $hex_position - 1と同じ 
	 *    'bit_position' => 16進数の1桁の中で何ビット目か, …ゼロ始まりで数える 
	 * )
	 * @param int $book_idx 図鑑DB用索引値
	 * @return array 引数に対応する除算後の情報
	 */
	function divideBookIdx($book_idx)
	{
		$colname_suffix = floor($book_idx / (4 * self::BOOK_COL_LEN));
		$hex_position = floor($book_idx / 4) % self::BOOK_COL_LEN;
		$bit_position = $book_idx % 4;
		
		$char_position = self::BOOK_COL_LEN - $hex_position - 1;
		
		return array(
			'colname_suffix' => $colname_suffix,
			'hex_position'   => $hex_position,
			'char_position'  => $char_position,
			'bit_position'   => $bit_position,
		);
	}
	
	/**
	 * モンスター図鑑情報をエンコードする（発生した差分のみ取得）
	 * 
	 * @param array $row ビットフラグ情報（t_user_monster_book_bitsテーブルの1行に相当する連想配列。キーはカラム名）
	 * @param int $monster_id モンスターマスタID
	 * @param int $status ステータス(self::BOOK_STATUS_～)
	 * @param string $date 日付(Y-m-d H:i:s)　省略可
	 * @return array|Ethna_Error 差分が発生したビットフラグ情報（キーがt_user_monster_book_bitsテーブルのカラム名の連想配列） エラー時はEthna_Errorオブジェクト
	 */
	function encodeUserMonsterBookDiff($row, $monster_id, $status, $date = null)
	{
		if ($date === null) {
			$date = $this->now;
		}
		
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':encodeUserMonsterBookBits: $monster_id=[' . $monster_id . '] status=[' . $status . '] date=[' . $date . ']');		
		
		// マスター情報を取得
		$this->loadMasterMonsterBookIdx();
		$book_idx = $this->monster_id_to_book_idx[$monster_id];
		if (!is_numeric($book_idx)) {
			return Ethna::raiseError("Invalid book_idx. monster_id[%d] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$monster_id, __FILE__, __LINE__);
		}
		
		// ビット位置を求める
		$divided = $this->divideBookIdx($book_idx);
		$colname_suffix = $divided['colname_suffix'];
		$char_position = $divided['char_position'];
		$bit_position = $divided['bit_position'];
				
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':encodeUserMonsterBookBits: book_idx=[' . $book_idx . '] colname_suffix=[' . $colname_suffix . '] char_position=[' . $char_position . '] bit_position=[' . $bit_position . ']');		

		// ビット演算を行なう
		if ($status == self::BOOK_STATUS_MET)      $colname = 'met';
		else if ($status == self::BOOK_STATUS_GOT) $colname = 'got';
		
		$date_colname = 'date_' . $colname;
		
		$colname .= $colname_suffix;
		$col = $row[$colname];

		$hex = intval($col[$char_position], 16); // 16進の1桁分の値
		$new_hex = $hex | (1 << $bit_position);

		// 変更があるカラムを求める
		$new_row = array();
		if ($new_hex != $hex) {
			$new_col = $col;
			$new_col[$char_position] = sprintf("%x", $new_hex);

			$new_row[$colname] = $new_col;
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':encodeUserMonsterBookBits: colname=[' . $colname . '] col=[' . $col . '] new_col=[' . $new_col .']');		

			if (!$row[$date_colname] || ($row[$date_colname] < $date)) {
				$new_row[$date_colname] = $date;
			}
		}
		
		return $new_row;
	}
	
	/**
	 * モンスター図鑑を保存する（変数にセットされた内容をDBへ登録）
	 * 
	 * @param int $user_id ユーザID
	 * @param bool $replace INSERT文の代わりにREPLACE文を使用するか
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function saveUserMonsterBookBits($user_id, $replace = true)
	{
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':saveUserMonsterBookBits:user_id=[' . $user_id . ']');

		if (isset($this->user_monster_book_bits_insert_flg[$user_id]) && 
			$this->user_monster_book_bits_insert_flg[$user_id]
		) {
			// INSERTの場合
			$columns = $this->user_monster_book_bits[$user_id];
			$param = array_values($columns);
			$param[] = $this->now;
			$sql = ($replace ? "REPLACE" : "INSERT")
			     . " INTO t_user_monster_book_bits(" . implode(",", array_keys($columns)) .",date_created)"
			     . " VALUES(" . str_repeat("?,", count($columns)) . "?)";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

			$this->user_monster_book_bits_insert_flg[$user_id] = false;
			
		} else if (isset($this->user_monster_book_bits_update_colnames[$user_id]) &&
			!empty($this->user_monster_book_bits_update_colnames[$user_id])
		) {
			// UPDATEの場合
			$all = $this->user_monster_book_bits[$user_id];
			$columns = array();
			foreach ($this->user_monster_book_bits_update_colnames[$user_id] as $colname) {
				$columns[$colname] = $all[$colname];
			}
			
			foreach (array('date_met', 'date_got') as $colname) {
				if (strcmp($all[$colname], $this->now) === 0) {
					$columns[$colname] = $all[$colname];
				}
			}
		
			$param = array_values($columns);
			$param[] = $user_id;
			$sql = "UPDATE t_user_monster_book_bits SET "
			     . implode(" = ?,", array_keys($columns)) . " = ? "
			     . "WHERE user_id = ?";
			if (!$this->db->execute($sql, $param)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}
			
			$this->user_monster_book_bits_update_colnames[$user_id] = array();
		}
		
		return true;
	}
	
	/**
	 * 初期モンスター図鑑情報を登録する
	 * 
	 * チュートリアル中で会ったモンスターなど。
	 * 初期ユーザーモンスターの図鑑登録は、この関数では行わない。（createInitialUserMonster関数内で行う）
	 * @param int $user_id ユーザID
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setInitialUserMonsterBook($user_id)
	{
		foreach (array(
			25002713 => self::BOOK_STATUS_MET, 
			24000222 => self::BOOK_STATUS_MET, 
			21000311 => self::BOOK_STATUS_GOT,
		) as $monster_id => $status) {
			$ret = $this->setUserMonsterBookVar($user_id, $monster_id, $status);
			if (!$ret || Ethna::isError($ret)) {
				return $ret;
			}
		}
		
		$ret = $this->saveUserMonsterBookBits($user_id);
		if (!$ret || Ethna::isError($ret)) {
			return $ret;
		}
		
		return true;
	}

	/**
	 * ユーザ所持モンスターを生成する
	 * 
	 * @param int $user_id ユーザID
	 * @param int $monster_id モンスターマスタID
	 * @param array $columns パラメータ情報（省略可）
	 *                        $columns['exp'] = 経験値
	 *                        $columns['lv'] = レベル
	 *                        $columns['skill_lv'] = スキルレベル
	 * @param array $tracking_columns トラッキング情報（省略可）
	 *                                 $tracking_columns[カラム名] = 値
	 *                                 ※カラム名はlog_trackingテーブルのカラム名と同じにすること。（"how"等）
	 * @param bool $user_update_flg ユーザー情報を更新するか（複数のモンスターを生成する際、ユーザーごとの情報（獲得合計モンスター数や勲章システム（モンスター属性別取得個数））の更新は別途、最後に1回だけ実行した方がSQLクエリ数を節約できるので、その制御用）
	 * @return array 生成結果
	 *                $return['user_monster_id'] = モンスターユニークID
	 *                $return['exp'] = 経験値
	 *                $return['lv'] = レベル
	 *                $return['skill_lv'] = スキルレベル
	 */
	function createUserMonster($user_id, $monster_id, $columns = null, $tracking_columns = null, $user_update_flg = true)
	{
		if (!$columns) {
			$columns = array();
		}
		
		if (!$tracking_columns) {
			$tracking_columns = array();
		}
		
		// パラメータが指定されていなかったら初期値（固定値 or マスタテーブル情報）を用意する
		$lv = isset($columns['lv']) ? $columns['lv'] : 1;
		
		$master_row = null;
		if (!isset($columns['exp']) || !isset($columns['skill_lv']) || !isset($columns['badge_num'])) {
			$param = array($monster_id, $lv);
			$sql = "SELECT l.total_exp, m.skill_lv, m.badge_num FROM m_monster m, m_monster_lv l WHERE m.monster_id = ? AND m.exp_type = l.exp_type AND l.lv = ?";
			$master_row = $this->db_r->GetRow($sql, $param);
		}
		
		if (isset($columns['exp'])) {
			$exp = $columns['exp'];
		} else {
			$exp = $master_row['total_exp'];
		}
		
		if (isset($columns['skill_lv'])) {
			$skill_lv = $columns['skill_lv'];
		} else {
			$skill_lv = $master_row['skill_lv'];
		}
		
		if (isset($columns['badge_num'])) {
			$badge_num = $columns['badge_num'];
		} else {
			$badge_num = $master_row['badge_num'];
		}

		if (isset($columns['badges'])) {
			$badges = $columns['badges'];
		} else {
			$badges = '';
		}

		//TODO hp_plus, attack_plus, heal_plus は何か処理必要か？

		// 生成
		if (is_null($badges)) {
			$param = array($user_id, $monster_id, $exp, $lv, $skill_lv, $badge_num);
			$sql = "INSERT INTO t_user_monster(user_id, monster_id, exp, lv, skill_lv, badge_num, date_created)"
				 . " VALUES(?, ?, ?, ?, ?, ?, NOW())";
		} else {
			$param = array($user_id, $monster_id, $exp, $lv, $skill_lv, $badge_num, $badges);
			$sql = "INSERT INTO t_user_monster(user_id, monster_id, exp, lv, skill_lv, badge_num, badges, date_created)"
				 . " VALUES(?, ?, ?, ?, ?, ?, ?, NOW())";
		}
		if ( !$this->db->execute( $sql, $param ) ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$user_monster_id = $this->db->db->Insert_ID();
		
		// トラッキング処理
//		$merged_columns = $tracking_columns;
//		$merged_columns['user_monster_id'] = $user_monster_id;
//		$merged_columns['monster_id_after'] = $monster_id;
//		$merged_columns['exp_after'] = $exp;
//		$merged_columns['lv_after'] = $lv;
//		$merged_columns['skill_lv_after'] = $skill_lv;
//		$merged_columns['crud'] = 'C';
//		$this->backend->getManager('Tracking')->log($user_id, $merged_columns);

		if ($user_update_flg) {
			// 獲得合計モンスター数 monster_get_total を+1する
			$ret = $this->backend->getManager('User')->setUserBaseDiff($user_id, 
				array(
					'monster_get_total' => 1,
				)
			);
			if (!$ret || Ethna::isError($ret)) {
				return $ret;
			}
		
			//勲章システム（モンスター属性別取得個数）
			$this->achievementGetMonster($user_id, $monster_id);
		}

		return array(
			'user_monster_id' => $user_monster_id,
			'user_id'         => $user_id,
			'monster_id'      => $monster_id,
			'exp'             => $exp,
			'lv'              => $lv,
			'skill_lv'        => $skill_lv,
		);
	}

	/**
	 * 勲章システムで入手モンスターの属性毎に+1する
	 * 
	 * @param int $user_id ユーザID
	 * @param int $monster_id モンスターマスタID
	 * @return array なし
	 */
	function achievementGetMonster($user_id, $monster_id)
	{
		//勲章システムのために属性を取得する
		$param = array($monster_id);
		$sql = "SELECT m.attribute_id FROM m_monster m WHERE m.monster_id = ?";
		$attr_id = $this->db_r->GetOne($sql, $param);
		//error_log("attr_id=$attr_id ($attr_name[$attr_id])");
		//勲章システム
		$columns = array(
						$this->ATTRIBUTE_ID_TO_ACHIEVEMENT_COLNAME[$attr_id] => 1,
					);
		$ret = $this->backend->getManager('User')->setUserAchievementCountDiff($user_id, $columns);
	}
	
	/**
	 * 勲章システムで入手モンスターの属性毎に+する（複数モンスター対応）
	 * 
	 * @param int $user_id ユーザID
	 * @param array $monster_num_assoc モンスター数の連想配列（キーがモンスターマスタID、値がモンスター数）
	 * @return array なし
	 */
	function achievementGetMonsterMulti($user_id, $monster_num_assoc)
	{
		//勲章システムのために属性を取得する
		$param = array_keys($monster_num_assoc);
		$sql = "SELECT monster_id, attribute_id FROM m_monster WHERE monster_id = ?"
		     . str_repeat(" OR monster_id = ?", count($monster_num_assoc) - 1);
		$rows = $this->db_r->GetAll($sql, $param);
		
//		//error_log("attr_id=$attr_id ($attr_name[$attr_id])");
//		//勲章システム
		$columns = array();
		foreach ($rows as $row) {
			$attr_id = $row['attribute_id'];
			$monster_id = $row['monster_id'];
			
			$attr_name = $this->ATTRIBUTE_ID_TO_ACHIEVEMENT_COLNAME[$attr_id];
			if (!isset($columns[$attr_name])) {
				$columns[$attr_name] = 0;
			}
			
			$columns[$attr_name] += $monster_num_assoc[$monster_id];
		}
		
		$ret = $this->backend->getManager('User')->setUserAchievementCountDiff($user_id, $columns);
	}
	
	/**
	 * 初期ユーザーモンスターを生成する
	 * 
	 * 御三家のどれかと、御三家typeごとに異なる初期所持モンスターを生成する。
	 * 生成したモンスターの図鑑へのセット処理も、この関数内で行なう。
	 * チーム関連のセット処理も、この関数内で行なう。
	 * 最初の1回しか実行できないようにする為のチェックは、この関数では行わないので、呼び出し元アクションで行う事。
	 * この関数の戻り値の連想配列のキーは、createUserMonster関数の戻り値に依存するので注意
	 * 戻り値の書式は以下の通り
	 * $return[initial_monster_id]['user_monster_id'] = モンスターユニークID
	 *                            ['exp'] = 経験値
	 *                            ['lv'] = レベル
	 *                            ['skill_lv'] = スキルレベル
	 *                            ['monster_id'] = モンスターマスタID
	 * @see function createUserMonster
	 * @param int $user_id
	 * @param int $type 御三家タイプ(1～3)
	 * @return array|object 成功時:この関数内で生成するユーザ所持モンスター情報一覧, 失敗時:Ethna_Errorまたは偽値
	 */
	function createInitialUserMonster($user_id, $type)
	{
		$team_m = $this->backend->getManager('Team');

		$master_initial_monster = $this->getMasterInitialMonsterAssoc(array($type, 0));
		$master_initial_team = $team_m->getMasterInitialTeamList($type);
		
		// マスターデータを検証
		foreach ($master_initial_team as $row) {
			$initial_monster_id = $row['initial_monster_id'];
			if (($initial_monster_id == Pp_TeamManager::USER_MONSTER_ID_EMPTY) || 
			    ($initial_monster_id == Pp_TeamManager::USER_MONSTER_ID_HELPER)
			) {
				continue;
			} else if (!isset($master_initial_monster[$initial_monster_id])) {
				return Ethna::raiseError("Invalid master data." . $row['initial_monster_id']);
			}
		}
				
		// ユーザ所持モンスターを生成
		$user_initial_monster = array();
		$monster_id_list = array();
		$monster_num_assoc = array();
		foreach ($master_initial_monster as $initial_monster_id => $row) {
			$monster_id = $row['monster_id'];
			
			$columns = array(
				'monster_id' => $monster_id,
				'lv' => $row['lv'],
			);
			
			if ($row['exp'] != -1) {
				$columns['exp'] = $row['exp'];
			}

			$monster = $this->createUserMonster($user_id, $monster_id, $columns, null, false);
			if (!$monster || Ethna::isError($monster)) {
				return $monster;
			}
			
			$monster['monster_id'] = $monster_id;
			
			$user_initial_monster[$initial_monster_id] = $monster;
			
			if (!in_array($monster_id, $monster_id_list)) {
				$monster_id_list[] = $monster_id;
			}
			
			if (!isset($monster_num_assoc[$monster_id])) {
				$monster_num_assoc[$monster_id] = 0;
			}
			$monster_num_assoc[$monster_id] += 1;
		}

		$this->clearUserMonsterListCache($user_id);

		// 獲得合計モンスター数 monster_get_total を+する
		$ret = $this->backend->getManager('User')->setUserBaseDiff($user_id, 
			array(
				'monster_get_total' => count($user_initial_monster),
			)
		);
		if (!$ret || Ethna::isError($ret)) {
			return $ret;
		}
		
		//勲章システム（モンスター属性別取得個数）
		$this->achievementGetMonsterMulti($user_id, $monster_num_assoc);
		
		//モンスター図鑑を更新
		foreach ($monster_id_list as $monster_id) {
			$ret = $this->setUserMonsterBookVar($user_id, $monster_id, Pp_MonsterManager::BOOK_STATUS_GOT);
			if (!$ret || Ethna::isError($ret)) {
				return $ret;
			}
		}

		$ret = $this->saveUserMonsterBookBits($user_id);
		if (!$ret || Ethna::isError($ret)) {
			return $ret;
		}
		
		// チームにセット
		foreach ($master_initial_team as $row) {
			$initial_monster_id = $row['initial_monster_id'];
			if (($initial_monster_id == Pp_TeamManager::USER_MONSTER_ID_EMPTY) || 
			    ($initial_monster_id == Pp_TeamManager::USER_MONSTER_ID_HELPER)
			) {
				$user_monster_id = $initial_monster_id;
			} else {
				$user_monster_id = $user_initial_monster[$initial_monster_id]['user_monster_id'];
			}
			
			for ($team_id = 0; $team_id < Pp_TeamManager::MAX_TEAM_NUM; $team_id++) {
				$position = $row['position'];
				
				// ２番目以降のチームでは、左端のリーダーと右端のフレンド枠の間の場所については何もしない。
				if ((0 < $team_id) && (1 < $position) && ($position < Pp_TeamManager::MAX_POSITION)) {
					continue;
				}

				$ret = $team_m->setUserTeam($user_id, $team_id, $position, $user_monster_id, $row['leader_flg']);
				if (!$ret || Ethna::isError($ret)) {
					return $ret;
				}
			}
		}
		
		return $user_initial_monster;
	}
	
	/**
	 * 初期モンスターマスタ情報一覧（initial_monster_idがキー）を取得する
	 * 
	 * @param mixed $types 御三家タイプ（配列で複数指定も可）
	 * @return array 初期モンスターマスタ情報一覧（initial_monster_idがキー）
	 */
	function getMasterInitialMonsterAssoc($types)
	{
		$param = is_array($types) ? $types : array($types);
		$sql = "SELECT * FROM m_initial_monster WHERE type IN("
		     . str_repeat('?,', count($types) - 1) . "?)";

		return $this->db_r->db->GetAssoc($sql, $param);
	}
	
	/**
	 * アクティブなリーダーモンスター及びユーザー情報を取得する
	 * 
	 * @param array $user_id_list ユーザIDの配列
	 * @return array モンスター及びユーザー情報
	 */
	function getActiveLeaderList($user_id_list)
	{
		if (!is_array($user_id_list) || (count($user_id_list) == 0)) {
			return null;
		}
		
		$monster_sql = 'm.' . implode(', m.', $this->USER_MONSTER_PROCESS_COLNAME_LIST);
        $base_sql = "SELECT b.user_id, b.name, b.login_date,b.rank,b.lamp,b.quest_id,b.area_id,"
		     . " " . $monster_sql
		     . " FROM t_user_base b, t_user_team t, t_user_monster m"
		     . " WHERE b.user_id = t.user_id"
		     . " AND b.active_team_id = t.team_id"
		     . " AND t.user_monster_id = m.user_monster_id"
		     . " AND t.leader_flg = 1"
            . " AND b.user_id IN(";

        $unit_m = $this->backend->getManager('Unit');
        $unit_user_list = $unit_m->cacheGetUnitFromUserIdList($user_id_list);

        $ary = array();
        foreach($unit_user_list as $unit => $user_ids) {
            $sql = $base_sql
                . str_repeat("?,", count($user_ids) - 1) . "?)";

            $rows = $unit_m->getAllSpecificUnit($sql, $user_ids, $unit, false);
            $ary = array_merge($ary, $rows);
        }

		//起動していないユーザのモンスターはバッジ無しにしておく（本当はモンスターマスタから取得すべきだけど）
		if (!empty($ary)) {
			foreach ($ary as $arkey => $arval) {
				if ($ary[$arkey]['badge_num'] == -1) {
					$ary[$arkey]['badge_num'] = 0;
					$ary[$arkey]['badges'] = '';//badge_numが0ならbadgesも''だわな
				}
			}
		}

        return $ary;
	}
	
	/**
	 * 所持モンスターを更新する
	 * 
	 * @param int $user_id ジャグモン内ユーザID
	 * @param int $user_monster_id モンスターユニークID
	 * @param array $columns パラメータ情報
	 *                        $columns[カラム名] = 値
	 *                        ※カラム名はt_user_monsterテーブルのカラム名と同じにすること。
	 * @param array $tracking_columns トラッキング情報（省略可）
	 *                                 $tracking_columns[カラム名] = 値
	 *                                 ※カラム名はlog_trackingテーブルのカラム名と同じにすること。（"how"等）
	 * @return bool|object  成否（成功時:true, 失敗時:Ethna_Errorまたはfalse）
	 */
	function setUserMonster($user_id, $user_monster_id, $columns, $tracking_columns = null)
	{
		if (!$tracking_columns) {
			$tracking_columns = array();
		}

		$param = array_values($columns);
		$param[] = $user_monster_id;
		$param[] = $user_id;
		$sql = "UPDATE t_user_monster SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE user_monster_id = ? AND user_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		// トラッキング処理
//		$merged_columns = $tracking_columns;
//		$merged_columns['user_monster_id'] = $user_monster_id;
//		foreach (array('monster_id', 'exp', 'lv', 'hp_plus', 'attack_plus', 'heal_plus', 'skill_lv') as $colname) {
//			if (isset($columns[$colname]) && !isset($tracking_columns[$colname . '_after'])) {
//				$merged_columns[$colname . '_after'] = $columns[$colname];
//			}
//		}
//		$merged_columns['crud'] = 'U';
//		$this->backend->getManager('Tracking')->log($user_id, $merged_columns);

		return true;
	}
	
	protected function loadUserMonsterList($user_id)
	{
		if (isset($this->user_monster_list[$user_id])) {
			return;
		}
		
		$colnames_clause = implode(',', $this->USER_MONSTER_PROCESS_COLNAME_LIST);
		
        $unit_m = $this->backend->getManager('Unit');
        $unit = $unit_m->cacheGetUnitFromUserId($user_id);
		
		$param = array($user_id);
		$sql = "SELECT " . $colnames_clause
			 . " FROM t_user_monster"
			 . " WHERE user_id = ?";
		
        $this->user_monster_list[$user_id] = $unit_m->getAllMultiUnit($sql, $param, $unit, false);
	}
	
	protected function loadUserMonsterListEx($user_id)
	{
		if (isset($this->user_monster_list_ex[$user_id])) {
			return;
		}
		
		$list = array();
		$assoc = $this->getUserMonsterAssoc($user_id);
		if (is_array($assoc)) foreach ($assoc as $monster) {
			$list[] = $monster;
		}
		
		$this->user_monster_list_ex[$user_id] = $list;
	}
	
	protected function loadUserMonsterAssoc($user_id)
	{
		if (isset($this->user_monster_assoc[$user_id])) {
			return;
		}
		
		$monster_list = $this->getUserMonsterList($user_id);
		$monster_assoc = array();
		if (is_array($monster_list)) foreach ($monster_list as $monster) {
			$monster_assoc[$monster['user_monster_id']] = $monster;
		}
		
		$team_list = $this->backend->getManager('Team')->getUserTeamList($user_id);
		if (is_array($team_list)) foreach ($team_list as $team) {
			$user_monster_id = $team['user_monster_id'];
			if (isset($monster_assoc[$user_monster_id])) {
				// 1体の所持モンスターが複数チームに所属している可能性があるので、team_idは保持できない。代わりにteam_flgセットする。
				$monster_assoc[$user_monster_id]['team_flg'] = true;
			}
		}
		
		$this->user_monster_assoc[$user_id] = $monster_assoc;
	}

	/**
	 * モンスター図鑑をDBから読み込む
	 * 
	 * 読み込んだ情報は、このクラス内の変数に保持する
	 * @param int $user_id 
	 * @return void
	 */
	protected function loadUserMonsterBook($user_id)
	{
		if (isset($this->user_monster_book_bits[$user_id])) {
			return;
		}
		
		// 初期化
		$this->user_monster_book_bits_update_colnames[$user_id] = array();
		
		// 新テーブルからの取得を試みる
		$book_bits = $this->getUserMonsterBookBits($user_id);
		if (is_array($book_bits) && !empty($book_bits)) {
			// 新テーブルの書式→旧テーブルの書式に変換
			$this->user_monster_book_assoc[$user_id] = $this->decodeUserMonsterBookBits($book_bits);
			$this->user_monster_book_bits[$user_id] = $book_bits;
			$this->user_monster_book_bits_insert_flg[$user_id] = false;
			return;
		}
		
		// 旧テーブルからの取得を試みる
		$book = $this->_getUserMonsterBookAssoc($user_id);
		if (is_array($book) && !empty($book)) {
			$this->user_monster_book_assoc[$user_id] = $book;
			$this->user_monster_book_bits_insert_flg[$user_id] = true;

			// 旧テーブルの書式→新テーブルの書式に変換
			$book_bits = $this->getEmptyUserMonsterBookBits($user_id);
			$this->user_monster_book_bits[$user_id] = $book_bits;
			foreach ($book as $row) {
				if ($row['status'] == self::BOOK_STATUS_GOT)      $date = $row['date_got'];
				else if ($row['status'] == self::BOOK_STATUS_MET) $date = $row['date_met'];
				else                                              $date = null;
				
				$this->setUserMonsterBookVar($user_id, $row['monster_id'], $row['status'], $date);
			}
			
			return;
		}
		
		// 空の図鑑情報（ビットフラグ）を用意する
		$book_bits = $this->getEmptyUserMonsterBookBits($user_id);
		$this->user_monster_book_assoc[$user_id] = $this->decodeUserMonsterBookBits($book_bits);
		$this->user_monster_book_bits[$user_id] = $book_bits;
		$this->user_monster_book_bits_insert_flg[$user_id] = true;
	}

	/**
	 * モンスター図鑑情報を旧テーブル(t_user_monster_book)から取得する
	 * 
	 * この関数内ではキャッシュしないので、キャッシュが必要なら呼び元で制御すること
	 */
	protected function _getUserMonsterBookAssoc($user_id)
	{
		$param = array($user_id);
		$sql = "SELECT t.monster_id AS id, t.*"
			 . " FROM t_user_monster_book t"
			 . " WHERE user_id = ?";
		
		return $this->db->db->GetAssoc($sql, $param);
	}
	
	/**
	 * モンスター図鑑情報を新テーブル(t_user_monster_book_bits)から取得する
	 * 
	 * この関数内ではキャッシュしないので、キャッシュが必要なら呼び元で制御すること
	 */
	protected function getUserMonsterBookBits($user_id)
	{
		$param = array($user_id);
		$sql = "SELECT * FROM t_user_monster_book_bits WHERE user_id = ?";
		
		return $this->db->GetRow($sql, $param);
	}
	
	/**
	 * モンスター図鑑のビットフラグ情報をデコードする
	 * 
	 * @param array $row  ビットフラグ情報（t_user_monster_book_bitsテーブルの1行に相当する連想配列）
	 * @return array デコード後のモンスター図鑑情報（t_user_monster_bookテーブルの複数行に相当する配列。配列内の各要素は、t_user_monster_bookテーブルの1行に相当する連想配列）
	 */
	protected function decodeUserMonsterBookBits($row)
	{
		if (!is_array($row) || empty($row)) {
			return null;
		}
		
		$this->loadMasterMonsterBookIdx();
		
		// 全ビットを見る
		$hash = array(); // $hash[モンスターID] = ステータス
		foreach (array(
			'met' => self::BOOK_STATUS_MET,
			'got' => self::BOOK_STATUS_GOT
		) as $type => $status) {
			$pos = 0;
			for ($i = 0; $i < self::BOOK_COL_NUM; $i++) {
				$name = $type . $i;
				$col = $row[$name];
				
				$j = self::BOOK_COL_LEN;
				while ($j--) {
					$hex = intval($col[$j], 16);
					
					for ($k = 0; $k < 4; $k++) { // 4は16進の1桁のビット数
						if ($hex & (1 << $k)) {
							$monster_id = $this->book_idx_to_monster_id[$pos];
							$hash[$monster_id] = $status;
						}
						
						$pos++;
					}
				}
			}
		}
		
		// 旧テーブル(t_user_monster_book)の書式に変換
		$rows = array();
		foreach ($hash as $monster_id => $status) {
			$rows[$monster_id] = array(
				'user_id'       => $row['user_id'],
				'monster_id'    => $monster_id,
				'status'        => $status,
			);
		}
		
		return $rows;
	}
	
	/**
	 * モンスターマスタの図鑑DB用索引をDBから読み込む
	 */
	protected function loadMasterMonsterBookIdx()
	{
		if ($this->monster_id_to_book_idx) {
			// OK
			return;
		}
		
		$monster_id_to_book_idx = $this->cacheGetMonsterIdToBookIdx();
		
		$this->monster_id_to_book_idx = $monster_id_to_book_idx;
		$this->book_idx_to_monster_id = array_flip($monster_id_to_book_idx);
	}
	
	/**
	 * モンスターIDと図鑑DB用索引の対応をキャッシュまたはDBから取得する
	 */
	protected function cacheGetMonsterIdToBookIdx()
	{
//	    $cache =& Ethna_CacheManager::getInstance('memcache');
//		$cache_key = basename(BASE) . '_monster_id_to_book_idx';
//        $monster_id_to_book_idx = $cache->get($cache_key, 30);
//		
//		if (is_array($monster_id_to_book_idx)) {
//			return $monster_id_to_book_idx;
//		}
//
//		$this->backend->logger->log(LOG_INFO, 'cacheGetMonsterIdToBookIdx reloading. cache_key=[%s]', $cache_key);
		
		$monster_id_to_book_idx = array();
		$sql = "SELECT monster_id, book_idx FROM m_monster";
		$result =& $this->db_r->query($sql);
		while ($row = $result->FetchRow()) {
			$monster_id_to_book_idx[$row['monster_id']] = $row['book_idx'];
		}
			
        if (empty($monster_id_to_book_idx)) {
			$this->backend->logger->log(LOG_ERR, 'cacheGetMonsterIdToBookIdx failed.');
//		} else {
//	        $cache->set($cache_key, $monster_id_to_book_idx);
		}
		
		return $monster_id_to_book_idx;
	}
	
	/**
	 * 空のモンスター図鑑の行を取得する
	 * 
	 * t_user_monster_book_bitsテーブルにINSERTする為のカラム情報を取得する。
	 * （この関数内ではINSERTしない）
	 * @param int $user_id ジャグモン内ユーザID
	 * @return array 行の情報（カラム名 => 値 の連想配列）
	 */
	protected function getEmptyUserMonsterBookBits($user_id)
	{
		$row = array(
			'user_id' => $user_id,
			'date_got' => null,
			'date_met' => null,
		);
		
		$value = str_repeat('0', self::BOOK_COL_LEN);
		
		foreach (array('met', 'got') as $type) {
			for ($i = 0; $i < self::BOOK_COL_NUM; $i++) {
				$name = $type . $i;
				$row[$name] = $value;
			}
		}
		
		return $row;
	}

	/**
	 * モンスター図鑑一覧（monster_idがキー）を取得する
	 * 
	 * 旧テーブル(t_user_monster_book)の書式で取得できる。
	 */
	function getUserMonsterBookAssoc($user_id)
	{
		$this->loadUserMonsterBook($user_id);
		
		$date_met = $this->user_monster_book_bits[$user_id]['date_met'];
		$date_got = $this->user_monster_book_bits[$user_id]['date_got'];
		
		$assoc = $this->user_monster_book_assoc[$user_id];
		foreach ($assoc as $monster_id => $row) {
			$assoc[$monster_id]['date_met'] = $date_met;
			$assoc[$monster_id]['date_got'] = $date_got;
		}
		
		return $assoc;
	}
	
	/**
	 * モンスターを削除可能か
	 * 
	 * 売却や合成で削除してよいかを判定する。
	 * 削除してよいのは、所持していて、かつチーム所属していない場合。
	 * @param int $user_id ジャグモン内ユーザID
	 * @param array|int $user_monster_id_list モンスターユニークID（配列でも1件でも可）
	 * @return bool 正否
	 */
	protected function isDeletable($user_id, $user_monster_id_list)
	{
		if (!is_array($user_monster_id_list)) {
			$user_monster_id_list = array($user_monster_id_list);
		}
		
		$user_monster_assoc = $this->getUserMonsterAssoc($user_id);

		foreach ($user_monster_id_list as $user_monster_id) {
			// 所持チェック＆チーム所属チェック
			if (!isset($user_monster_assoc[$user_monster_id]) ||
				isset($user_monster_assoc[$user_monster_id]['team_flg'])
			) {
				if (isset($user_monster_assoc[$user_monster_id]['team_flg']))
					error_log("monster:isDeletable Team err");
				else
					error_log("monster:isDeletable Own err");
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * モンスターを削除する
	 * 
	 * @param int $user_id ジャグモン内ユーザID
	 * @param int|array $user_monster_id モンスターユニークID
	 * @param array $tracking_columns トラッキング情報（省略可）
	 *                                 $tracking_columns[カラム名] = 値
	 *                                 ※カラム名はlog_trackingテーブルのカラム名と同じにすること。（"how"等）
	 * @return bool|Ethna_Error 成功の場合はtrue, 失敗の場合はEthna_Error
	 */
	function delete($user_id, $user_monster_id, $tracking_columns = null)
	{
		if (!$tracking_columns) {
			$tracking_columns = array();
		}

		$param = array($user_monster_id, $user_id);
		$sql = "DELETE FROM t_user_monster WHERE user_monster_id = ? AND user_id = ?";
		// TODO ↓このチェック処理（クエリ実行エラーチェック＆件数が1件かチェック）が頻出する…まとめよう
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		// トラッキング処理
//		$merged_columns = $tracking_columns;
//		$merged_columns['user_monster_id'] = $user_monster_id;
//		$merged_columns['crud'] = 'D';
//		$this->backend->getManager('Tracking')->log($user_id, $merged_columns);

		return true;
	}

	/**
	 * 合成でのスキルレベルアップ確率を求める
	 * 
	 * @param int $lv ベースのスキルレベル
	 * @return int レベルアップ確率（％）
	 */
	protected function getSynthesisSkillLvUpProbability($lv, $max=12)
	{
		if ($max != 8 && $max != 10 && $max != 12) $max = 12;
		//ベースのスキルレベル => レベルアップ確率（％）
		//通常の設定
		$map = array(
			 8 => array( 1 => 48, 2 => 30, 3 => 23, 4 => 20, 5 => 16, 6 => 14, 7 => 10),
			10 => array( 1 => 49, 2 => 31, 3 => 24, 4 => 21, 5 => 17, 6 => 15, 7 => 11, 8 =>  9, 9 =>  9),
			12 => array( 1 => 50, 2 => 32, 3 => 25, 4 => 22, 5 => 18, 6 => 16, 7 => 12, 8 => 10, 9 => 10, 10 => 10, 11 => 10)
		);
//        return isset($map[$lv]) ? $map[$lv] : $map[12];
		$ret = isset($map[$max][$lv]) ? $map[$max][$lv] : 0;
		//期間によって確率を変える
		$date = date('YmdHis', $_SERVER['REQUEST_TIME']);
		if ('20140505000000' <= $date && $date <= '20140511235959') {
			$ret = floor($ret * 1.7);//一律1.7倍
		}
		return $ret;
	}

	/**
	 * モンスターのレベルを求める
	 * 
	 * @param int $total_exp 経験値
	 * @param int $exp_type 経験値タイプ
	 * @return int レベル
	 */
	protected function getMonsterLv($total_exp, $exp_type)
	{
		$param = array($exp_type, $total_exp);
		$sql = "SELECT lv FROM m_monster_lv"
			 . " WHERE exp_type = ? AND total_exp <= ?"
			 . " ORDER BY total_exp DESC LIMIT 1";

//		return $this->db->GetOne($sql, $param);
		$all = $this->db_r->GetAll($sql, $param);
		if (is_array($all) && isset($all[0])) {
			return $all[0]['lv'];
		}
	}
	
	/**
	 * モンスターの経験値を求める
	 * 
	 * @param int $lv レベル
	 * @param int $exp_type 経験値タイプ
	 * @return int 経験値
	 */
	protected function getMonsterExp($lv, $exp_type)
	{
		$param = array($exp_type, $lv);
		$sql = "SELECT total_exp FROM m_monster_lv"
			 . " WHERE exp_type = ? AND lv = ?";

//		return $this->db->GetOne($sql, $param);
		return $this->db_r->GetOne($sql, $param);
	}
	
	/**
	 * 十分な進化メダルを所持しているか
	 * 
	 * @param int $user_id ジャグモン内ユーザID
	 * @param array $monster モンスターマスター情報（m_monsterの各カラム名をキーとする連想配列）
	 * @return bool 正否
	 */
	protected function hasEnoughEvolutionMedal($user_id, $monster)
	{
		// ベースモンスターの必要進化メダルを取得する
		// t_user_baseから合成に必要な進化メダルを所持しているか判定し、足りなかったらエラー
		// 必要な進化メダルはm_monsterのevolution_medal1～5を参照する
		// ※複数種類の進化メダルが必要な場合もある
		$medal_idx = 0;
		$item_m = $this->backend->getManager('Item');
		foreach (array(
			'evolution_medal1', 'evolution_medal2', 'evolution_medal3', 'evolution_medal4', 'evolution_medal5'
		) as $colname) {
			//進化メダルのアイテムID
			$medal_item_id = Pp_ItemManager::ITEM_RARE_MEDAL1 + $medal_idx;
			//所持アイテムを取得
			$item_data = $item_m->getUserItem(
				$user_id,
				$medal_item_id
			);
			//所持メダル数
			$have_medal = 0;
			if ($item_data) {
				$have_medal = $item_data['num'];
			}
			// 所持アイテムにある進化メダル所持数が足りているか判定
			if ($have_medal < $monster[$colname]) {
				return false;
			}
			$medal_idx++;
		}
		
		return true;
	}
	
	/*
	protected function setLogMonterData($columns)
	{
		$param = array_values($columns);
		$sql = "INSERT INTO log_monster_data(" . implode(",", array_keys($columns))
			 . ", date_created)"
			 . " VALUES(" . str_repeat("?,", count($columns)) . "NOW())";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
	*/

    /**
     * ユーザーの所持しているモンスターであるかチェックする
     *
     * @param integer $monster_id
     * @param array $monster_list
     * @param string $user_id
     * @return boolean
     */
    private function _checkHaveMonsterSynthesisBase($monster_id, $monster_list, $user_id)
    {

        // モンスターの所持チェック
        if (!isset($monster_list[$monster_id])) {
            $this->backend->logger->log(LOG_INFO, 'Base user monster does not exists. user_id=[' . $user_id . '] base_user_monster_id=[' . $monster_id . ']');
            return false;
        }

        return true;

    }

    /**
     * 合成素材モンスターがユーザーの所持しているモンスターであるかチェックする
     *
     * @param array   $material_monster_list 素材となるモンスターのuser_master_id一覧
     * @param string  $user_id ユーザーID
     * @return boolean
     */
    private function _checkHaveMonsterSynthesisMaterial($material_monster_list, $user_id)
    {

        // 素材モンスターの所持チェック＆チーム所属チェック
        if (!$this->isDeletable($user_id, $material_monster_list)) {
            $this->backend->logger->log(LOG_INFO, 'Meterial user monster does not exists. user_id=[' . $user_id . '] material_user_monster_id=[' . print_r($material_monster_list, true) . ']');
            return false;
        }

        return true;

    }

    /**
     * 合成時にベースモンスターと素材モンスターで同じものを指定していないかをチェックする
     *
     * @param integer $base_monster_id       ベースとなるモンスターのuser_monster_id
     * @param array   $material_monster_list 素材となるモンスターのuser_master_id一覧
     * @param string  $user_id ユーザーID
     * @return boolean
     */
    private function _checkDuplicateMonsterSynthesis($base_user_monster_id, $material_monster_list, $user_id)
    {

        // 素材モンスターの所持チェック＆チーム所属チェック
        // ベースモンスターと素材モンスターが異なる事を確認（素材モンスターの配列にベースモンスターと同じIDが含まれているかどうか）
        if (in_array($base_user_monster_id, $material_monster_list)) {
            $this->backend->logger->log(LOG_INFO, 'Base monster and material monster is same. user_id=[' . $user_id . '] base_user_monster_id=[' . $base_user_monster_id . ']');
            return false;
        }

        return true;

    }

    /**
     * モンスターの現在の攻撃力を取得する
     *
     * @param integer $def_hp 初期HP
     * @param integer $max_hp 最大LVのHP
     * @param integer $now_lv 現在のLV
     * @param integer $max_lv 最大LV
     * @return integer $hp HP
     */
    public function getMonsterHp ($def_hp, $max_hp, $now_lv, $max_lv)
    {
        if ($now_lv >= $max_lv){
            return $max_hp;
        }

        if ($now_lv <= 1){
            return $def_hp;
        }

        $hp = $def_hp + intval(floatval($max_hp - $def_hp) / floatval($max_lv - 1) * floatval($now_lv - 1));

        return $hp;

    }

    /**
     * モンスターの現在の攻撃力を取得する
     *
     * @param integer $def_attack 初期攻撃力
     * @param integer $max_attack 最大LVの攻撃力
     * @param integer $now_lv 現在のLV
     * @param integer $max_lv 最大LV
     * @return integer $attack 攻撃力
     */
    public function getMonsterAttack ($def_attack, $max_attack, $now_lv, $max_lv)
    {
        if ($now_lv >= $max_lv){
            return $max_attack;
        }

        if ($now_lv <= 1){
            return $def_attack;
        }

        $attack = $def_attack + intval(floatval($max_attack - $def_attack) / floatval($max_lv - 1) * floatval($now_lv - 1));

        return $attack;

    }
}
