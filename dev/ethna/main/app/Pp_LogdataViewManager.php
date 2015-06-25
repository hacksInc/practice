<?php
/**
 *  Pp_LogDataViewManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_LogDataManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataViewManager extends Ethna_AppManager
{

	protected $_log_csv_template = array(
		'id' => array(
			'title' => 'ログId',
			'item' => 'id',
		),
		'device_type' => array(
			'title' => '端末種別',
			'item' => 'device_type',
		),
		'api_transaction_id' => array(
			'title' => 'api_transaction_id',
			'item' => 'api_transaction_id',
		),
		'pp_id' => array(
			'title' => 'ユーザーID',
			'item' => 'pp_id',
		),
		'name' => array(
			'title' => 'ニックネーム',
			'item' => 'name',
		),
		'rank' => array(
			'title' => 'ランク',
			'item' => 'rank',
		),
		'processing_type' => array(
			'title' => '処理タイプ',
			'item' => 'processing_type',
		),
		'processing_type_name' => array(
			'title' => '処理タイプ名',
			'item' => 'processing_type_name',
		),
		'item_id' => array(
			'title' => 'アイテムID',
			'item' => 'item_id',
		),
		'item_name' => array(
			'title' => 'アイテム名',
			'item' => 'item_name',
		),
		'service_flg' => array(
			'title' => 'サービスフラグ',
			'item' => 'service_flg',
		),
		'count' => array(
			'title' => '増減数',
			'item' => 'count',
		),
		'num' => array(
			'title' => '所持数',
			'item' => 'num',
		),
		'num_prev' => array(
			'title' => '所持数（処理前）',
			'item' => 'num_prev',
		),
		'date_login' => array(
			'item' => 'date_login',
			'title' => 'ログイン日時',
		),
		'date_created' => array(
			'item' => 'date_created',
			'title' => 'ログ記録日時',
		),
		'present_id' => array(
			'item' => 'present_id',
			'title' => 'プレゼントID',
		),
		'present_type' => array(
			'item' => 'present_type',
			'title' => 'プレゼントタイプ',
		),
		'present_type_name' => array(
			'item' => 'present_type_name',
			'title' => 'プレゼントタイプ名',
		),
		'present_monster_lv' => array(
			'item' => 'lv',
			'title' => 'モンスターレベル',
		),
		'present_number' => array(
			'item' => 'number',
			'title' => 'プレゼント数',
		),
		'present_category' => array(
			'item' => 'present_category',
			'title' => '配布物のカテゴリ',
		),
		'present_value' => array(
			'item' => 'present_value',
			'title' => '配布物のID',
		),
		'status' => array(
			'item' => 'status',
			'title' => 'ステータス',
		),
		'status_prev' => array(
			'item' => 'status_prev',
			'title' => 'ステータス（処理前）',
		),
		'status_name' => array(
			'item' => 'status_name',
			'title' => 'ステータス名',
		),
		'old_status' => array(
			'item' => 'old_status',
			'title' => '変更前ステータス',
		),
		'old_status_name' => array(
			'item' => 'old_status_name',
			'title' => '変更前ステータス名',
		),

		'user_monster_id' => array(
			'item' => 'user_monster_id',
			'title' => 'ユーザーモンスターID',
		),
		'monster_id' => array(
			'item' => 'monster_id',
			'title' => 'モンスターID',
		),
		'monster_name' => array(
			'item' => 'monster_name',
			'title' => 'モンスター名',
		),
		'rare' => array(
			'item' => 'rare',
			'title' => 'レアリティ',
		),
		'add_exp' => array(
			'item' => 'add_exp',
			'title' => '加算経験値',
		),
		'exp' => array(
			'item' => 'exp',
			'title' => '経験値',
		),
		'lv' => array(
			'item' => 'lv',
			'title' => 'レベル',
		),
		'hp' => array(
			'item' => 'hp',
			'title' => 'HP',
		),
		'attack' => array(
			'item' => 'attack',
			'title' => 'レアリティ',
		),
		'hp_plus' => array(
			'item' => 'hp_plus',
			'title' => 'HPボーナス',
		),
		'attack_plus' => array(
			'item' => 'attack_plus',
			'title' => '攻撃力ボーナス',
		),
		'heal_plus' => array(
			'item' => 'heal_plus',
			'title' => '回復ボーナス',
		),
		'skill_lv' => array(
			'item' => 'skill_lv',
			'title' => 'スキルレベル',
		),
		'old_user_monster_id' => array(
			'item' => 'old_user_monster_id',
			'title' => '旧 ユーザーモンスターID',
		),
		'old_monster_id' => array(
			'item' => 'old_monster_id',
			'title' => '旧 モンスターID',
		),
		'old_monster_name' => array(
			'item' => 'old_monster_name',
			'title' => '旧 モンスター名',
		),
		'old_rare' => array(
			'item' => 'old_rare',
			'title' => '旧 レアリティ',
		),
		'old_exp' => array(
			'item' => 'old_exp',
			'title' => '旧 経験値',
		),
		'old_lv' => array(
			'item' => 'old_lv',
			'title' => '旧 レベル',
		),
		'old_hp' => array(
			'item' => 'old_hp',
			'title' => '旧 HP',
		),
		'old_attack' => array(
			'item' => 'old_attack',
			'title' => '旧 レアリティ',
		),
		'old_hp_plus' => array(
			'item' => 'old_hp_plus',
			'title' => '旧 HPボーナス',
		),
		'old_attack_plus' => array(
			'item' => 'old_attack_plus',
			'title' => '旧 攻撃力ボーナス',
		),
		'old_heal_plus' => array(
			'item' => 'old_heal_plus',
			'title' => '旧 回復ボーナス',
		),
		'old_skill_lv' => array(
			'item' => 'old_skill_lv',
			'title' => '旧 スキルレベル',
		),
		'cost' => array(
			'item' => 'cost',
			'title' => 'コスト',
		),
		'monster_cnt' => array(
			'item' => 'monster_cnt',
			'title' => '売却モンスター数',
		),
		'sell_price' => array(
			'item' => 'sell_price',
			'title' => '売却合計金額',
		),
		'monster_status' => array(
			'item' => 'status',
			'title' => '増減ステータス',
		),
		'monster_status_name' => array(
			'item' => 'status_name',
			'title' => '生成/消滅',
		),
		'sell_id' => array(
			'item' => 'sell_id',
			'title' => '購入商品ID',
		),
		'price' => array(
			'item' => 'price',
			'title' => '売却金額',
		),
		'account_name' => array(
			'item' => 'account_name',
			'title' => 'アカウント(API)名',
		),
		'area_id' => array(
			'item' => 'area_id',
			'title' => 'エリアID',
		),
		'area_name' => array(
			'item' => 'area_name',
			'title' => 'エリア名',
		),
		'quest_id' => array(
			'item' => 'quest_id',
			'title' => 'クエストID',
		),
		'quest_name' => array(
			'item' => 'quest_name',
			'title' => 'クエスト名',
		),
		'team_id' => array(
			'item' => 'team_id',
			'title' => 'チームID',
		),
		'play_id' => array(
			'item' => 'play_id',
			'title' => 'プレイID',
		),
		'quest_st' => array(
			'item' => 'quest_st',
			'title' => 'クエストステータス',
		),
		'quest_st_name' => array(
			'item' => 'quest_st_name',
			'title' => 'クエストステータス名',
		),
		'active_team_id' => array(
			'item' => 'active_team_id',
			'title' => 'アクティブチームID',
		),
		'helper_user_id' => array(
			'item' => 'helper_user_id',
			'title' => '助っ人ユーザーID',
		),
		'helper_user_name' => array(
			'item' => 'helper_user_name',
			'title' => '助っ人ニックネーム',
		),
		'helper_monster_id' => array(
			'item' => 'helper_monster_id',
			'title' => '助っ人モンスターID',
		),
		'helper_monster_name' => array(
			'item' => 'helper_monster_name',
			'title' => '助っ人モンスター名',
		),
		'continue_cnt' => array(
			'item' => 'continue_cnt',
			'title' => 'コンティニュー回数',
		),
		'game_total' => array(
			'item' => 'game_total',
			'title' => 'ゲーム総数',
		),
		'bonus_big' => array(
			'item' => 'bonus_big',
			'title' => 'ボーナスビッグ',
		),
		'bonus_reg' => array(
			'item' => 'bonus_reg',
			'title' => 'ボーナスreg',
		),
		'overkill' => array(
			'item' => 'overkill',
			'title' => 'オーバーキル',
		),
		'drop_gold' => array(
			'item' => 'drop_gold',
			'title' => '取得ゴールド',
		),
		'get_exp' => array(
			'item' => 'get_exp',
			'title' => '取得EXP',
		),
		'bonus_type' => array(
			'item' => 'bonus_type',
			'title' => 'ボーナスタイプ',
		),
		'bonus_cd' => array(
			'item' => 'bonus_cd',
			'title' => 'ボーナスコード',
		),
		'bonus_overkill' => array(
			'item' => 'bonus_overkill',
			'title' => 'ボーナスオーバーキル',
		),
		'lose_battle_no' => array(
			'item' => 'lose_battle_no',
			'title' => '敗退戦闘No',
		),
		'gameover_type' => array(
			'item' => 'gameover_type',
			'title' => 'ゲームオーバー種別',
		),
		'ach_id' => array(
			'item' => 'ach_id',
			'title' => 'アチーブメントID',
		),
		'achievement_id' => array(
			'item' => 'achievement_id',
			'title' => '勲章ID',
		),
		'achievement_rank' => array(
			'item' => 'achievement_rank',
			'title' => '勲章ランク',
		),
		'achievement_name' => array(
			'item' => 'achievement_name',
			'title' => '勲章名',
		),
		'achievement_description' => array(
			'item' => 'achievement_description',
			'title' => '勲章詳細',
		),
		'uipw_hash' => array(
			'item' => 'uipw_hash',
			'title' => 'UIPW',
		),
		'age_verification' => array(
			'item' => 'age_verification',
			'title' => '年齢認証値',
		),
		'age_verification_name' => array(
			'item' => 'age_verification_name',
			'title' => '年齢認証名',
		),
		'ma_purchased_mix' => array(
			'item' => 'ma_purchased_mix',
			'title' => '月額最大利用可能金額',
		),
		'old_name' => array(
			'item' => 'old_name',
			'title' => '旧 ニックネーム',
		),
		'old_uipw_hash' => array(
			'item' => 'old_uipw_hash',
			'title' => '旧 UIPW',
		),
		'old_age_verification' => array(
			'item' => 'old_age_verification',
			'title' => '旧 年齢認証値',
		),
		'old_age_verification_name' => array(
			'item' => 'old_age_verification_name',
			'title' => '旧 年齢認証名',
		),
		'old_ma_purchased_mix' => array(
			'item' => 'old_ma_purchased_mix',
			'title' => '旧 月額最大利用可能金額',
		),
		'login_date' => array(
			'item' => 'login_date',
			'title' => 'ログイン日時',
		),
		'old_login_date' => array(
			'item' => 'old_login_date',
			'title' => '前回 ログイン日時',
		),
		'tutorial_status' => array(
			'item' => 'tutorial_status',
			'title' => 'チュートリアルステータス',
		),
		'tutorial_status_name' => array(
			'item' => 'tutorial_status_name',
			'title' => 'チュートリアルステータス名',
		),
		'gacha_type' => array(
			'item' => 'type',
			'title' => 'ガチャタイプ',
		),
		'gacha_id' => array(
			'item' => 'gacha_id',
			'title' => 'ガチャID',
		),
		'gacha_name' => array(
			'item' => 'gacha_name',
			'title' => 'ガチャ名',
		),
		'photo_id' => array(
			'item' => 'photo_id',
			'title' => '排出フォトID',
		),
		'photo_lv' => array(
			'item' => 'photo_lv',
			'title' => '排出後フォトLv',
		),
		'lot_count' => array(
			'item' => 'lot_count',
			'title' => 'ガチャ回数',
		),
		'order_id' => array(
			'item' => 'order_id',
			'title' => 'order_id',
		),
		'list_id' => array(
			'item' => 'list_id',
			'title' => 'list_id',
		),
		'rarity' => array(
			'item' => 'rarity',
			'title' => 'レアリティ',
		),
		'u_user_id' => array(
			'title' => '申請者ユーザーID',
			'item' => 'u_user_id',
		),
		'u_name' => array(
			'title' => '申請者ニックネーム',
			'item' => 'u_name',
		),
		'u_rank' => array(
			'title' => '申請者ランク',
			'item' => 'u_rank',
		),
		'u_old_friend_rest' => array(
			'title' => '申請者処理前フレンド枠残数',
			'item' => 'u_old_friend_rest',
		),
		'u_friend_rest' => array(
			'title' => '申請者処理後フレンド枠残数',
			'item' => 'u_friend_rest',
		),
		'u_friend_max_num' => array(
			'title' => '申請者フレンドMAX数',
			'item' => 'u_friend_max_num',
		),
		'u_reader_monster_id' => array(
			'title' => '申請者リーダーモンスターID',
			'item' => 'u_reader_monster_id',
		),
		'u_reader_monster_name' => array(
			'title' => '申請者リーダーモンスター名',
			'item' => 'u_reader_monster_name',
		),
		'u_reader_monster_rare' => array(
			'title' => '申請者リーダーモンスターレアリティ',
			'item' => 'u_reader_monster_rare',
		),
		'u_reader_monster_lv' => array(
			'title' => '申請者リーダーモンスターレベル',
			'item' => 'u_reader_monster_lv',
		),
		'u_reader_monster_skill_lv' => array(
			'title' => '申請者リーダーモンスタースキルレベル',
			'item' => 'u_reader_monster_skill_lv',
		),
		'f_user_id' => array(
			'title' => '受理者ユーザーID',
			'item' => 'f_user_id',
		),
		'f_name' => array(
			'title' => '受理者ニックネーム',
			'item' => 'f_name',
		),
		'f_rank' => array(
			'title' => '受理者ランク',
			'item' => 'f_rank',
		),
		'f_old_friend_rest' => array(
			'title' => '受理者処理前フレンド枠残数',
			'item' => 'f_old_friend_rest',
		),
		'f_friend_rest' => array(
			'title' => '受理者処理後フレンド枠残数',
			'item' => 'f_friend_rest',
		),
		'f_friend_max_num' => array(
			'title' => '受理者フレンドMAX数',
			'item' => 'f_friend_max_num',
		),
		'f_reader_monster_id' => array(
			'title' => '受理者リーダーモンスターID',
			'item' => 'f_reader_monster_id',
		),
		'f_reader_monster_name' => array(
			'title' => '受理者リーダーモンスター名',
			'item' => 'f_reader_monster_name',
		),
		'f_reader_monster_rare' => array(
			'title' => '受理者リーダーモンスターレアリティ',
			'item' => 'f_reader_monster_rare',
		),
		'f_reader_monster_lv' => array(
			'title' => '受理者リーダーモンスターレベル',
			'item' => 'f_reader_monster_lv',
		),
		'f_reader_monster_skill_lv' => array(
			'title' => '受理者リーダーモンスタースキルレベル',
			'item' => 'f_reader_monster_skill_lv',
		),
		'comment_id' => array(
			'title' => 'コメントID',
			'item' => 'comment_id',
		),
		'character_id' => array(
			'title' => 'キャラクターID',
			'item' => 'character_id',
		),
		'crime_coef' => array(
			'title' => '犯罪係数',
			'item' => 'crime_coef',
		),
		'crime_coef_prev' => array(
			'title' => '犯罪係数（処理前）',
			'item' => 'crime_coef_prev',
		),
		'body_coef' => array(
			'title' => '身体係数',
			'item' => 'body_coef',
		),
		'body_coef_prev' => array(
			'title' => '身体係数（処理前）',
			'item' => 'body_coef_prev',
		),
		'intelli_coef' => array(
			'title' => '知能係数',
			'item' => 'intelli_coef',
		),
		'intelli_coef_prev' => array(
			'title' => '知能係数（処理前）',
			'item' => 'intelli_coef_prev',
		),
		'mental_coef' => array(
			'title' => '心的係数',
			'item' => 'mental_coef',
		),
		'mental_coef_prev' => array(
			'title' => '心的係数（処理前）',
			'item' => 'mental_coef_prev',
		),
		'ex_stress_care' => array(
			'title' => '臨時ストレスケア回数',
			'item' => 'ex_stress_care',
		),
		'ex_stress_care_prev' => array(
			'title' => '臨時ストレスケア回数（処理前）',
			'item' => 'ex_stress_care_prev',
		),
		'mission_id' => array(
			'title' => 'ミッションID',
			'item' => 'mission_id',
		),
		'accompany_character_id' => array(
			'title' => '同行サポートキャラID',
			'item' => 'accompany_character_id',
		),
		'result_type' => array(
			'title' => '結果種別',
			'item' => 'result_type',
		),
		'zone' => array(
			'title' => '最後にいたゾーン番号',
			'item' => 'zone',
		),
		'start_created' => array(
			'title' => '開始日時',
			'item' => 'start_created',
		),
		'end_created' => array(
			'title' => '終了日時',
			'item' => 'end_created',
		),
		'area_stress' => array(
			'title' => 'エリアストレス',
			'item' => 'area_stress',
		),
		'area_stress_prev' => array(
			'title' => 'エリアストレス（処理前）',
			'item' => 'area_stress',
		),
	);

	/**
	 * limit 句の生成を行う
	 *
	 * @param array $sort
	 * @return string $order_by
	 */
	protected function _createSqlPhraseLimit($limit_cnt, $offset)
	{
		// limit句の編集
		$limit = '';
		if (!is_null($limit_cnt)) {
			$limit = " LIMIT " . $limit_cnt;
			if (!is_null($offset) && $offset != ''){
				$limit = $limit . " OFFSET " . $offset;
			}
		}

		return $limit;
	}

	/**
	 * order by 句の生成を行う
	 *
	 * @param array $sort
	 * @return string $order_by
	 */
	protected function _createSqlPhraseOrderBy($sort)
	{
		$order_by = '';
		if (!is_null($sort) && is_array($sort)){
			foreach($sort as $k => $v){
				$tmp[] = $k . " " . $v;
			}
			$order_by = " ORDER BY " . implode(",", $tmp);
		}

		return $order_by;
	}

	/**
	 * ページャの作成
	 *
	 * @access  public
	 * @return  void
	 */
	public function getPager($total, $offset, $count){
		$pager = Ethna_Util::getDirectLinkList($total, $offset, $count);
		$next = $offset + $count;
		if($next < $total){
			$last = ceil($total / $count);
			$this->af->setApp('hasnext', true);
			$this->af->setApp('next', $next);
			$this->af->setApp('last', ($last * $count) - $count);
		}
		$prev = $offset - $count;
		if($offset - $count >= 0){
			$this->af->setApp('hasprev', true);
			$this->af->setApp('prev', $prev);
		}
		$this->af->setApp('current', $offset);
		$this->af->setApp('link', 'localhost');
		$this->af->setApp('pager', $pager);
	}

	/**
	 * csvファイル作成を行う
	 *
	 * @param string $file_path
	 * @param string $log_name
	 * @param array $log_data
	 * @param array $title_log_data
	 * @return mixed $file_name 正常時
	 *               Ethna_Error エラー時
	 */
	public function createCsvFile($file_path, $log_name, $log_data, $log_item)
	{
		//        $rand_num = mt_rand();
		//        $today_date = date('Ymd', time());
		//        $file_name = $log_name .'_' . $today_date . '_' . $rand_num;
		$file_name = $this->createCsvFileName($log_name);
		if (!is_dir($file_path)) {
			mkdir($file_path, 0777, true);
		}
		if (!$fp=@fopen($file_path . '/' . $file_name, 'a')){
			return false;
		}

		$cnt = 0;

		// タイトル行出力
		foreach ($log_item as $csv_k => $csv_v){
			$title_log_data[] = $this->_log_csv_template[$csv_v]['title'];
		}
		$title_str = implode(',', $title_log_data);
		fwrite($fp, mb_convert_encoding($title_str, "Shift_JIS", "UTF-8") . "\r\n");

		//
		foreach ($log_data as $data_k => $data_v){
			foreach ($log_item as $csv_k => $csv_v){
				$tmp_log_data[$csv_v] = $data_v[$this->_log_csv_template[$csv_v]['item']];
			}
			$str = implode(',', $tmp_log_data);
			fwrite($fp, mb_convert_encoding($str, "Shift-JIS", "UTF-8") . "\r\n");
			$cnt++;
		}

		fclose($fp);
		return $file_name;
	}

	/**
	 * csvファイル名作成を行う
	 *
	 * @param string $log_name
	 * @return string $file_name
	 */
	function createCsvFileName($log_name)
	{
		$rand_num = mt_rand();
		$today_date = date('Ymd', time());
		$file_name = $log_name .'_' . $today_date . '_' . $rand_num;

		return $file_name;
	}
}
