<?php
/**
 *  Pp_AdminMonsterManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_MonsterManager.php';

/**
 *  Pp_AdminMonsterManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminMonsterManager extends Pp_MonsterManager
{
	/** モンスタータイプ名 */
	var $MONSTER_TYPE_NAME = array(
		1 => '攻撃タイプ',
		2 => '体力タイプ',
		3 => '補佐タイプ',
		4 => 'バランスタイプ',
		5 => '売却用',
		6 => '強化合成用',
		7 => '進化素材用',
		8 => '特殊系',
		9 => 'その他',
	);
	
	/** 種族名 */
	var $TRIBE_NAME = array(
		1 => '人',
		2 => '動物',
		3 => '無機物',
		4 => '妖精',
		5 => '英雄',
		6 => '竜種',
		7 => '魔族',
		8 => '神霊',
	);
	
	protected function loadUserMonsterListAd($user_id)
	{
		if (isset($this->user_monster_list[$user_id])) {
			return;
		}
		
		$param = array($user_id);
		$sql = "SELECT user_monster_id, monster_id, exp, lv,"
			 . " hp_plus, attack_plus, heal_plus, skill_lv, badge_num, badges, date_created"
			 . " FROM t_user_monster"
			 . " WHERE user_id = ?";
		
		$this->user_monster_list[$user_id] = $this->db->GetAll($sql, $param);
	}
	
	/**
	 * API応答用のユーザ所持モンスター一覧を取得する
	 */
	function getUserMonsterListForApiResponseAd($user_id)
	{
		$this->loadUserMonsterListAd($user_id);
		
		$list = array();
		if (is_array($this->user_monster_list[$user_id])) {
			foreach ($this->user_monster_list[$user_id] as $monster) {
				$list[($monster['user_monster_id'])] = array(
					'user_monster_id' => $monster['user_monster_id'],
					'monster_id'      => $monster['monster_id'],
					'exp'             => $monster['exp'],
					'lv'              => $monster['lv'],
					'hp_plus'         => $monster['hp_plus'],
					'attack_plus'     => $monster['attack_plus'],
					'heal_plus'       => $monster['heal_plus'],
					'skill_lv'        => $monster['skill_lv'],
					'badge_num'       => $monster['badge_num'],
					'badges'          => $monster['badges'],
					'date_created'    => $monster['date_created'],
				);
			}
		}
		
		return $list;
	}
	
	/**
	 * ユーザー所持モンスター一覧（user_monster_idがキー）を取得する（管理画面用カラム付き）
	 */
	function getUserMonsterAssocForAdmin($user_id)
	{
		return $this->getUserMonsterListForApiResponseAd($user_id);
	}
	
	/**
	 * モンスター図鑑用の変数を消去する
	 * 
	 * 消去するのはこのオブジェクト内の変数のみ。DB操作は行なわない。
	 * @param int $user_id ジャグモン内ユーザID
	 * @return void
	 */
	function clearUserMonsterBookVar($user_id)
	{
//		foreach (array(
//			'user_monster_book_assoc', 
//			'user_monster_book_bits', 
//			'user_monster_book_bits_insert_flg', 
//			'user_monster_book_bits_update_colnames',
//		) as $varname) {
//			if (isset($this->$varname[$user_id])) {
//				unset($this->$varname[$user_id]);
//			}
//		}
		
		if (isset($this->user_monster_book_assoc[$user_id])) {
			unset($this->user_monster_book_assoc[$user_id]);
		}

		if (isset($this->user_monster_book_bits[$user_id])) {
			unset($this->user_monster_book_bits[$user_id]);
		}

		if (isset($this->user_monster_book_bits_insert_flg[$user_id])) {
			unset($this->user_monster_book_bits_insert_flg[$user_id]);
		}

		if (isset($this->user_monster_book_bits_update_colnames[$user_id])) {
			unset($this->user_monster_book_bits_update_colnames[$user_id]);
		}
	}
	
	/**
	 * モンスタータイプ名を取得する
	 * 
	 * 存在しない場合は引数で渡された番号がそのまま返る
	 * @param int $monster_type_id モンスタータイプマスタID
	 * @return string モンスタータイプ名
	 */
	function getMonsterTypeName($monster_type_id)
	{
		if (isset($this->MONSTER_TYPE_NAME[$monster_type_id])) {
			$monster_type_name = $this->MONSTER_TYPE_NAME[$monster_type_id];
		} else {
			$monster_type_name = $monster_type_id;
		}
		
		return $monster_type_name;
	}
	
	/**
	 * 種族名を取得する
	 * 
	 * 存在しない場合は引数で渡された番号がそのまま返る
	 * @param int $tribe 種族
	 * @return string 種族名
	 */
	function getTribeName($tribe)
	{
		if (isset($this->TRIBE_NAME[$tribe])) {
			$tribe_name = $this->TRIBE_NAME[$tribe];
		} else {
			$tribe_name = $tribe;
		}
		
		return $tribe_name;
	}
}
?>
