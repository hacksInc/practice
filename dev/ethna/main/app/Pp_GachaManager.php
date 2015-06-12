<?php
/**
 *	Pp_GachaManager.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	Pp_GachaManager
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_GachaManager extends Ethna_AppManager
{
	/**
	 * ガチャID指定で、一次抽選テーブルよりレア度を取得する。
	 *
	 * @param int $gacha_id ガチャID
	 */
	function getRarity($gacha_id)
	{

		// ガチャリスト管理テーブル(t_gacha_list_info)から当該ガチャIDのレコードを取得する。
		$gacha_list_info = $this->getGachaListInfo($gacha_id);

		// ガチャリスト管理テーブル(t_gacha_list_info)の当該ガチャIDのガチャカウントをカウントアップする。
		$this->incGachaListInfo($gacha_id);

		// レア度に応じてレア度抽選ボックスを作り、１つ選び出してレア度を決定する。（カウントアップできるもの選び出せるまでループする。）
		// 一次抽選テーブル(t_gacha_category_info)から、当該ガチャIDに該当するレコードを全て取得する。
		$gacha_category_info = $this->getGachaCategoryInfo($gacha_id);

		// レア度を引くための抽選ボックスを作る
		$rarity_box = array();
		$gachaCntSum = 1;
		foreach ($gacha_category_info as $aCategoryInfo) {
			$weightCnt = intval($aCategoryInfo['weight']);
			for ($i = 0; $i < $weightCnt; $i++) {
				$rarity_box[] = array(
					'rarity' => $aCategoryInfo['rarity'],
					'weight' => $aCategoryInfo['weight'],
					'gacha_cnt' => $aCategoryInfo['gacha_cnt']
				);
			}
			$gachaCntSum += intval($aCategoryInfo['gacha_cnt']);
		}
		shuffle($rarity_box);

		// カウントアップできるかどうか判断するための周回数を出す。現在何周目かを計算する。
		$categoryMax = intval($gacha_list_info['category_max']);
		$lap = ceil($gachaCntSum / $categoryMax);

		// 最終的返すレア度
		$theRarity = 0;

		// カウントアップできるものが引けるまでループ
		for ($i = 0; $i < $categoryMax; $i++) {
			$aRarity = $rarity_box[$i];
			$weight = $aRarity['weight'];
			$gacha_cnt = $aRarity['gacha_cnt'];
			if ($lap*$weight > $gacha_cnt) {
				$theRarity = $aRarity['rarity'];
				break;
			}
		}

		// 当該、ガチャIDとレア度で一次抽選テーブルのカウントを更新する。
		$this->incGachaCategoryInfo($gacha_id, $theRarity);

		return $theRarity;
	}


	/**
	 * ガチャIDとレア度を指定して、モンスターIDを含むガチャデータを取得する。
	 *
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レア度
	 */
	function drawMonster($gacha_id, $rarity)
	{
		// 二次抽選テーブル(t_gacha_item_info)から、当該ガチャID+レア度に応じたモンスター一覧を取得する。
		$gacha_item_info = $this->getGachaItemInfo($gacha_id, $rarity);

		// 確率に応じて抽選ボックスを作り、モンスターID１つ選び出す。（カウントアップできるもの選び出せるまでループする。）
		// レア度を引くための抽選ボックスを作る
		$monster_box = array();
		$gachaCntSum = 1; // 現在の発行数の合計値
		$weightSum = 0; // 周回数を割り出すための確率の合計値

		foreach ($gacha_item_info as $aItemInfo) {
			for ($i = 0; $i < intval($aItemInfo['weight']); $i++) {
				$monster_box[] = array(
					'monster_id' => $aItemInfo['monster_id'],
					'monster_lv' => $aItemInfo['monster_lv'],
					'weight' => $aItemInfo['weight'],
					'gacha_cnt' => $aItemInfo['gacha_cnt'],
					'badge_expand' => $aItemInfo['badge_expand'],
					'badges' => $aItemInfo['badges']
				);
			}
			$gachaCntSum += $aItemInfo['gacha_cnt'];
			$weightSum	+= $aItemInfo['weight'];
		}
		shuffle($monster_box);

		// カウントアップできるかどうか判断するための周回数を出す。現在何周目かを計算する。
		$lap = ceil($gachaCntSum / $weightSum);

		// 最終的に返すデータ
		$theMoster = null;
		$theMosterId = 0;

		// カウントアップできるものが引けるまでループ
		for ($i = 0; $i < intval($weightSum); $i++) {
			$aMonster = $monster_box[$i];
			$weight = $aMonster['weight'];
			$gacha_cnt = $aMonster['gacha_cnt'];

			if ($lap*$weight > $gacha_cnt) {
				$theMosterId = $aMonster['monster_id'];
				$theMoster = $aMonster;
				break;
			}
		}

		// 二次抽選テーブル(t_gacha_item_info)の当該ガチャID+レア度+モンスターIDのガチャカウントをカウントアップする。
		$this->incGachaItemInfo($gacha_id, $rarity, $theMosterId);

		return $theMoster;
	}


	/**
	 * ガチャID指定でガチャリスト管理情報を１件取得する
	 *
	 * @param int $gacha_id ガチャID
	 */
	function getGachaListInfo($gacha_id)
	{
		$param = array($gacha_id);
		$sql = "SELECT * FROM t_gacha_list_info WHERE gacha_id = ?";

		return $this->db_r->GetRow($sql, $param);
	}

	/**
	 * ガチャID指定で一次抽選テーブル情報を取得する
	 *
	 * @param int $gacha_id ガチャID
	 */
	function getGachaCategoryInfo($gacha_id)
	{
		$param = array($gacha_id);
		$sql = "SELECT * FROM t_gacha_category_info WHERE gacha_id = ?";

		return $this->db_r->GetAll($sql, $param);
	}


	/**
	 * ガチャID、レア度指定で二次抽選テーブル情報を取得する
	 *
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レア度
	 */
	function getGachaItemInfo($gacha_id, $rarity)
	{
		$param = array($gacha_id, $rarity);
		$sql = "SELECT * FROM t_gacha_item_info WHERE gacha_id = ? AND rarity = ?";

		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * ガチャID指定で、ガチャリスト管理情報のカウントアップを行う。
	 *
	 * @param int $gacha_id ガチャID
	 */
	function incGachaListInfo($gacha_id)
	{
		$param = array($gacha_id);
		$sql = "UPDATE t_gacha_list_info SET gacha_cnt=gacha_cnt+1"
			 . " WHERE gacha_id = ? LIMIT 1";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

	/**
	 * ガチャID、レア度指定で、一次抽選テーブルのカウントアップを行う。
	 *
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レア度
	 */
	function incGachaCategoryInfo($gacha_id, $rarity)
	{
		$param = array($gacha_id, $rarity);
		$sql = "UPDATE t_gacha_category_info SET gacha_cnt=gacha_cnt+1"
			 . " WHERE gacha_id = ? AND rarity = ? LIMIT 1";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

	/**
	 * ガチャID、レア度、モンスターID指定で、二次抽選情報のカウントアップを行う。
	 *
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レア度
	 * @param int $monster_id モンスターID
	 */
	function incGachaItemInfo($gacha_id, $rarity, $monster_id)
	{
		$param = array($gacha_id, $rarity, $monster_id);
		$sql = "UPDATE t_gacha_item_info SET gacha_cnt=gacha_cnt+1"
			 . " WHERE gacha_id = ? AND rarity = ? AND monster_id = ? LIMIT 1";

		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

// 	/**
// 	 * ガチャの発行結果を記録する。
// 	 *
// 	 * @param array $columns 'gacha_id'   ガチャID
// 	 *						 'rarity'	  レア度
// 	 *						 'monster_id' モンスターID
// 	 *						 'user_id'	  ユーザID
// 	 */
// 	function setLogGachaDrawList($columns)
// 	{
// 		// TODO Pp_LogDataManagerクラスに移動する。

// 		$affected_rows = 0;
// 		// UPDATE実行
// 		$param = array_values($columns);
// 		$param[] = $columns['gacha_id'];
// 		$param[] = $columns['rarity'];
// 		$param[] = $columns['monster_id'];
// 		$param[] = $columns['user_id'];
// 		$sql = "UPDATE log_gacha_draw_list SET "
// 				. implode("=?,", array_keys($columns)) . "=? "
// 					 . " WHERE gacha_id = ? AND rarity = ? AND monster_id = ? AND user_id = ?";
// 		if (!$this->db->execute($sql, $param)) {
// 			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
// 					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
// 		}
// 		// 影響した行数を確認
// 		$affected_rows = $this->db->db->affected_rows();

// 		if ($affected_rows > 1) {
// 			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
// 		}

// 		if ($affected_rows == 0) {
// 			// INSERT実行
// 			$param = array($columns['gacha_id'], $columns['rarity'], $columns['monster_id'], $columns['user_id'], $columns['date_draw']);
// 			$sql = "INSERT INTO log_gacha_draw_list (gacha_id,rarity,monster_id,user_id,date_created,date_draw)"
// 					. " VALUES(?, ?, ?, ?, NOW(), ?)";
// 			$this->backend->logger->log(LOG_DEBUG, "INSERT INTO t_gacha_order_info=".print_r($param,true));
// 			if (!$this->db->execute($sql, $param)) {
// 				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
// 						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
// 			}
// 		}
// 		return true;
// 	}












	/**
	 * ガチャID指定で、一次抽選テーブルよりレア度を取得する。（おまけガチャ）
	 *
	 * @param int $gacha_id ガチャID
	 */
	function getRarityExtra($gacha_id)
	{

		// ガチャリスト管理テーブル(t_gacha_extra_list_info)から当該ガチャIDのレコードを取得する。
		$gacha_extra_list_info = $this->getGachaExtraListInfo($gacha_id);

		// ガチャリスト管理テーブル(t_gacha_extra_list_info)の当該ガチャIDのガチャカウントをカウントアップする。
		$this->incGachaExtraListInfo($gacha_id);

		// レア度に応じてレア度抽選ボックスを作り、１つ選び出してレア度を決定する。（カウントアップできるもの選び出せるまでループする。）
		// 一次抽選テーブル(t_gacha_extra_category_info)から、当該ガチャIDに該当するレコードを全て取得する。
		$gacha_extra_category_info = $this->getGachaExtraCategoryInfo($gacha_id);

		// レア度を引くための抽選ボックスを作る
		$rarity_box = array();
		$gachaCntSum = 1;
		foreach ($gacha_extra_category_info as $aExtraCategoryInfo) {
			$weightCnt = intval($aExtraCategoryInfo['weight']);
			for ($i = 0; $i < $weightCnt; $i++) {
				$rarity_box[] = array(
						'rarity' => $aExtraCategoryInfo['rarity'],
						'weight' => $aExtraCategoryInfo['weight'],
						'gacha_cnt' => $aExtraCategoryInfo['gacha_cnt']
				);
			}
			$gachaCntSum += intval($aExtraCategoryInfo['gacha_cnt']);
		}
		shuffle($rarity_box);

		// カウントアップできるかどうか判断するための周回数を出す。現在何周目かを計算する。
		$categoryMax = intval($gacha_extra_list_info['category_max']);
		$lap = ceil($gachaCntSum / $categoryMax);

		// 最終的返すレア度
		$theRarity = 0;

		// カウントアップできるものが引けるまでループ
		for ($i = 0; $i < $categoryMax; $i++) {
			$aRarity = $rarity_box[$i];
			$weight = $aRarity['weight'];
			$gacha_cnt = $aRarity['gacha_cnt'];
			if ($lap*$weight > $gacha_cnt) {
				$theRarity = $aRarity['rarity'];
				break;
			}
		}

		// 当該、ガチャIDとレア度で一次抽選テーブルのカウントを更新する。
		$this->incGachaExtraCategoryInfo($gacha_id, $theRarity);

		return $theRarity;
	}

	/**
	 * ガチャIDとレア度を指定して、モンスターIDを含むガチャデータを取得する。（おまけガチャ）
	 *
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レア度
	 */
	function drawMonsterExtra($gacha_id, $rarity)
	{
		// 二次抽選テーブル(t_gacha_extra_item_info)から、当該ガチャID+レア度に応じたモンスター一覧を取得する。
		$gacha_extra_item_info = $this->getGachaExtraItemInfo($gacha_id, $rarity);

		// 確率に応じて抽選ボックスを作り、モンスターID１つ選び出す。（カウントアップできるもの選び出せるまでループする。）
		// レア度を引くための抽選ボックスを作る
		$monster_box = array();
		$gachaCntSum = 1; // 現在の発行数の合計値
		$weightSum = 0; // 周回数を割り出すための確率の合計値

		foreach ($gacha_extra_item_info as $aExtraItemInfo) {
			for ($i = 0; $i < intval($aExtraItemInfo['weight']); $i++) {
				$monster_box[] = array(
					'monster_id' => $aExtraItemInfo['monster_id'],
					'monster_lv' => $aExtraItemInfo['monster_lv'],
					'weight' => $aExtraItemInfo['weight'],
					'gacha_cnt' => $aExtraItemInfo['gacha_cnt']
				);
			}
			$gachaCntSum += $aExtraItemInfo['gacha_cnt'];
			$weightSum	+= $aExtraItemInfo['weight'];
		}
		shuffle($monster_box);

		// カウントアップできるかどうか判断するための周回数を出す。現在何周目かを計算する。
		$lap = ceil($gachaCntSum / $weightSum);

		// 最終的に返すデータ
		$theMoster = null;
		$theMosterId = 0;

		// カウントアップできるものが引けるまでループ
		for ($i = 0; $i < intval($weightSum); $i++) {
			$aMonster = $monster_box[$i];
			$weight = $aMonster['weight'];
			$gacha_cnt = $aMonster['gacha_cnt'];

			if ($lap*$weight > $gacha_cnt) {
				$theMosterId = $aMonster['monster_id'];
				$theMoster = $aMonster;
				break;
			}
		}

		// 二次抽選テーブル(t_gacha_extra_item_info)の当該ガチャID+レア度+モンスターIDのガチャカウントをカウントアップする。
		$this->incGachaExtraItemInfo($gacha_id, $rarity, $theMosterId);

		return $theMoster;
	}

	/**
	 * ガチャID指定でガチャリスト管理情報を１件取得する（おまけガチャ）
	 *
	 * @param int $gacha_id ガチャID
	 */
	function getGachaExtraListInfo($gacha_id)
	{
		$param = array($gacha_id);
		$sql = "SELECT * FROM t_gacha_extra_list_info WHERE gacha_id = ?";

		return $this->db_r->GetRow($sql, $param);
	}

	/**
	 * ガチャID指定で一次抽選テーブル情報を取得する（おまけガチャ）
	 *
	 * @param int $gacha_id ガチャID
	 */
	function getGachaExtraCategoryInfo($gacha_id)
	{
		$param = array($gacha_id);
		$sql = "SELECT * FROM t_gacha_extra_category_info WHERE gacha_id = ?";

		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * ガチャID、レア度指定で二次抽選テーブル情報を取得する（おまけガチャ）
	 *
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レア度
	 */
	function getGachaExtraItemInfo($gacha_id, $rarity)
	{
		$param = array($gacha_id, $rarity);
		$sql = "SELECT * FROM t_gacha_extra_item_info WHERE gacha_id = ? AND rarity = ?";

		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * ガチャID指定で、ガチャリスト管理情報のカウントアップを行う。（おまけガチャ）
	 *
	 * @param int $gacha_id ガチャID
	 */
	function incGachaExtraListInfo($gacha_id)
	{
		$param = array($gacha_id);
		$sql = "UPDATE t_gacha_extra_list_info SET gacha_cnt=gacha_cnt+1"
			 . " WHERE gacha_id = ? LIMIT 1";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

	/**
	 * ガチャID、レア度指定で、一次抽選テーブルのカウントアップを行う。（おまけガチャ）
	 *
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レア度
	 */
	function incGachaExtraCategoryInfo($gacha_id, $rarity)
	{
		$param = array($gacha_id, $rarity);
		$sql = "UPDATE t_gacha_extra_category_info SET gacha_cnt=gacha_cnt+1"
			 . " WHERE gacha_id = ? AND rarity = ? LIMIT 1";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

	/**
	 * ガチャID、レア度、モンスターID指定で、二次抽選情報のカウントアップを行う。（おまけガチャ）
	 *
	 * @param int $gacha_id ガチャID
	 * @param int $rarity レア度
	 * @param int $monster_id モンスターID
	 */
	function incGachaExtraItemInfo($gacha_id, $rarity, $monster_id)
	{
		$param = array($gacha_id, $rarity, $monster_id);
		$sql = "UPDATE t_gacha_extra_item_info SET gacha_cnt=gacha_cnt+1"
			 . " WHERE gacha_id = ? AND rarity = ? AND monster_id = ? LIMIT 1";

		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

	function getPhotoGachaList()
	{
		$db = $this->backend->getDB('m_r');
		$sql = "SELECT * FROM m_photo_gacha ORDER BY gacha_id ASC";
		return $db->GetAll($sql);
	}

	function getPhotoGacha($gacha_id)
	{
		$db = $this->backend->getDB('m_r');
		$sql = "SELECT * FROM m_photo_gacha WHERE gacha_id = ?";
		return $db->GetRow($sql, array($gacha_id));
	}

	function getTranPhotoGacha($gacha_id)
	{
		$db = $this->backend->getDB('cmn_r');
		$sql = "SELECT * FROM ct_photo_gacha WHERE gacha_id = ?";
		return $db->GetRow($sql, array($gacha_id));
	}

	function getPhotoGachaLineupList($gacha_id)
	{
		$db = $this->backend->getDB('m_r');
		$sql = "SELECT * FROM m_photo_gacha_lineup WHERE gacha_id = ? ORDER BY photo_id ASC";
		return $db->GetAll($sql, array($gacha_id));
	}

	function getPhotoGachaLineup($gacha_id, $photo_id)
	{
		$db = $this->backend->getDB('m_r');
		$sql = "SELECT * FROM m_photo_gacha_lineup WHERE gacha_id = ? AND photo_id = ?";
		return $db->GetRow($sql, array($gacha_id, $photo_id));
	}

	function getTranPhotoGachaBox($gacha_id, $photo_id)
	{
		$db = $this->backend->getDB('cmn_r');
		$sql = "SELECT * FROM ct_photo_gacha_box WHERE gacha_id = ? AND photo_id = ?";
		return $db->GetRow($sql, array($gacha_id, $photo_id));
	}

	function getStageList()
	{
		$db = $this->backend->getDB('m_r');
		$sql = "SELECT * FROM m_stage WHERE type = '1' ORDER BY stage_id ASC";
		return $db->GetAll($sql);
	}

	function getMissionList()
	{
		$db = $this->backend->getDB('m_r');
		$sql = "SELECT * FROM m_mission ORDER BY mission_id ASC";
		return $db->GetAll($sql);
	}

	function getPhotoList()
	{
		$db = $this->backend->getDB('m_r');
		$sql = "SELECT * FROM m_photo ORDER BY photo_id ASC";
		return $db->GetAll($sql);
	}	
	
	function updatePhotoGacha($param)
	{
		$param = array(
				$param['name_ja'],
				$param['type'],
				empty($param['stage_id']) ? null: $param['stage_id'],
				empty($param['mission_id']) ? null: $param['mission_id'],
				$param['price'],
				$param['sort_no'],
				$param['date_start'],
				$param['date_end'],
				$param['gacha_id'],
			
		);
		$sql = "UPDATE m_photo_gacha SET name_ja = ?, type= ?, stage_id = ?, mission_id = ?, price = ?, sort_no = ?, date_start = ?, date_end = ?  WHERE gacha_id = ? ";
	
		$db = $this->backend->getDB('m');
		if (!$db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(),$db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		// 値を変更しないまま更新されると影響件数は0となる為コメントアウト(ON UPDATE CURRENT_TIMESTAMPのカラムなし)
		/*
		// 影響した行数を確認
		$affected_rows = $db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		*/
		return true;
	}

	function updatePhotoGachaLineup($param)
	{
		$param = array(
				$param['weight'],
				$param['gacha_id'],
				$param['photo_id'],
		);
		$sql = "UPDATE m_photo_gacha_lineup SET weight = ? WHERE gacha_id = ? AND photo_id = ?";
	
		$db = $this->backend->getDB('m');
		if (!$db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(),$db->db->ErrorMsg(), __FILE__, __LINE__);
		}
	
		// 値を変更しないまま更新されると影響件数は0となる為コメントアウト(ON UPDATE CURRENT_TIMESTAMPのカラムなし)
		/*
			// 影響した行数を確認
		$affected_rows = $db->db->affected_rows();
		if ($affected_rows != 1) {
		return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		*/
		return true;
	}
	
	function updateTrnPhotoGachaBox($param)
	{
		$param = array(
				$param['weight'],
				$param['gacha_id'],
				$param['photo_id'],
		);
		$sql = "UPDATE ct_photo_gacha_box SET weight = ? WHERE gacha_id = ? AND photo_id = ?";
	
		$db = $this->backend->getDB('cmn');
		if (!$db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(),$db->db->ErrorMsg(), __FILE__, __LINE__);
		}
	
		// 値を変更しないまま更新されると影響件数は0となる為コメントアウト(ON UPDATE CURRENT_TIMESTAMPのカラムなし)
		/*
		 // 影響した行数を確認
		$affected_rows = $db->db->affected_rows();
		if ($affected_rows != 1) {
		return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		*/
		return true;
	}

	function deletePhotoGachaLineup($param)
	{
		$param = array(
				$param['gacha_id'],
				$param['photo_id'],
		);
		$sql = "DELETE from m_photo_gacha_lineup WHERE gacha_id = ? AND photo_id = ?";
		
		$db = $this->backend->getDB('m');
		if (!$db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(),$db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$affected_rows = $db->db->affected_rows();
		if ($affected_rows != 1) {
		return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
	
		return true;
	}
	
	function deleteTrnPhotoGachaBox($param)
	{
		$param = array(
				$param['gacha_id'],
				$param['photo_id'],
		);
		$sql = "DELETE from ct_photo_gacha_box WHERE gacha_id = ? AND photo_id = ?";
	
		$db = $this->backend->getDB('cmn');
		if (!$db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(),$db->db->ErrorMsg(), __FILE__, __LINE__);
		}
	
		$affected_rows = $db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		return true;
	}
	
	function initializeCount($gacha_id)
	{
		$sql = "UPDATE ct_photo_gacha SET drop_count = 0, date_reset = now() WHERE gacha_id = ?";

		$db = $this->backend->getDB('cmn');
		if (!$db->execute($sql, array($gacha_id))) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(),$db->db->ErrorMsg(), __FILE__, __LINE__);
		}
	
		 // 影響した行数を確認
		$affected_rows = $db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		$sql = "UPDATE ct_photo_gacha_box SET gacha_cnt = 0 WHERE gacha_id = ?";

		if (!$db->execute($sql, array($gacha_id))) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(),$db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	function deleteGachaMaster($gacha_id)
	{
		$param = array($gacha_id);
		$sql = "DELETE mpg, mpgl FROM m_photo_gacha mpg LEFT OUTER JOIN m_photo_gacha_lineup mpgl ON mpg.gacha_id = mpgl.gacha_id WHERE mpg.gacha_id = ?";

		$db = $this->backend->getDB('m');
		if (!$db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(),$db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	function deleteTrnGacha($gacha_id)
	{
		$param = array($gacha_id);
		$sql = "DELETE cpg, cpgb FROM ct_photo_gacha cpg LEFT OUTER JOIN ct_photo_gacha_box cpgb ON cpg.gacha_id = cpgb.gacha_id WHERE cpg.gacha_id = ?";

		$db = $this->backend->getDB('cmn');
		if (!$db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(),$db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	function insertPhotoGacha($param)
	{
		$param = array(
				$param['gacha_id'],
				$param['name_ja'],
				$param['type'],
				empty($param['stage_id']) ? null: $param['stage_id'],
				empty($param['mission_id']) ? null: $param['mission_id'],
				$param['price'],
				$param['sort_no'],
				$param['date_start'],
				$param['date_end'],
		);
		$sql = "INSERT INTO m_photo_gacha(gacha_id, name_ja, type, stage_id, mission_id, price, sort_no, date_start, date_end) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
	
		$db = $this->backend->getDB('m');
		if( !$db->execute( $sql, $param ))
		{	// 追加エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(), $db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
	
		return true;
	}

	function insertTrnPhotoGacha($param)
	{
		$param = array(
				$param['gacha_id'],
				0,
				'0000-00-00 00:00:00',
		);
		$sql = "INSERT INTO ct_photo_gacha(gacha_id, drop_count, date_reset, date_modified) VALUES(?, ?, ?, now())";
	
		$db = $this->backend->getDB('cmn');
		if( !$db->execute( $sql, $param ))
		{	// 追加エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(), $db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
	
		return true;
	}

	function insertPhotoGachaLineup($param)
	{
		$param = array(
				$param['gacha_id'],
				$param['photo_id'],
				$param['weight'],
		);
		$sql = "INSERT INTO m_photo_gacha_lineup(gacha_id, photo_id, weight) VALUES(?, ?, ?)";

		$db = $this->backend->getDB('m');
		if( !$db->execute( $sql, $param ))
		{	// 追加エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(), $db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
	
		return true;
	}

	function insertTrnPhotoGachaBox($param)
	{
		$param = array(
				$param['gacha_id'],
				$param['photo_id'],
				$param['weight'],
		);
		$sql = "INSERT INTO ct_photo_gacha_box(gacha_id, photo_id, weight) VALUES(?, ?, ?)";
	
		$db = $this->backend->getDB('cmn');
		if( !$db->execute( $sql, $param ))
		{	// 追加エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$db->db->ErrorNo(), $db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
	
		return true;
	}
}
?>