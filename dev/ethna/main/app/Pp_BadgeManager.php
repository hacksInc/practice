<?php
/**
 *  Pp_BadgeManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_BadgeManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_BadgeManager extends Ethna_AppManager
{

	/**
	 * バッジマスタ（一覧または1件）を取得する
	 *
	 * @param int $badge_id バッジID（指定すると1件取得。省略すると全件取得）
	 * @return array
	 */
	function getMasterBadge($badge_id = null)
	{
		if ($badge_id === null) {
			$sql = "SELECT * FROM m_badge"
			     . " ORDER BY badge_id";
			$data = $this->db_r->GetAll($sql);
			return $data;
		} else {
			$sql = "SELECT * FROM m_badge"
			     . " WHERE badge_id = ?";
			return $this->db_r->GetRow($sql, array($badge_id));
		}
	}

	/**
	 * 生成可能なバッジ一覧を取得する
	 * バッジIDのみ返す
	 *
	 * @param int $user_id
	 * @return array
	 */
	function getMasterBadgeListCreatable()
	{
		$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$param = array($now, $now);
		$sql = "SELECT bm.badge_id FROM m_badge bm"
		     . " WHERE bm.is_create = 1 AND bm.create_start_time <= ? AND ? < bm.create_end_time"
		     . " ORDER BY bm.badge_id";
		return $this->db->GetAll($sql, $param);
	}

	/**
	 * バッジ素材マスタ（一覧または1件）を取得する
	 *
	 * @param int $material_id バッジ素材ID（指定すると1件取得。省略すると全件取得）
	 * @return array
	 */
	function getMasterBadgeMaterial($material_id = null)
	{
		if ($material_id === null) {
			$sql = "SELECT * FROM m_badge_material"
			     . " ORDER BY material_id";
			$data = $this->db_r->GetAll($sql);
			return $data;
		} else {
			$sql = "SELECT * FROM m_badge_material"
			     . " WHERE material_id = ?";
			return $this->db_r->GetRow($sql, array($material_id));
		}
	}

	/**
	 * バッジスキルマスタ（一覧または1件）を取得する
	 *
	 * @param int $skill_id バッジスキルID（指定すると1件取得。省略すると全件取得）
	 * @return array
	 */
	function getMasterBadgeSkill($skill_id = null)
	{
		if ($skill_id === null) {
			$sql = "SELECT * FROM m_badge_skill"
			     . " ORDER BY skill_id";
			$data = $this->db_r->GetAll($sql);
			return $data;
		} else {
			$sql = "SELECT * FROM m_badge_skill"
			     . " WHERE skill_id = ?";
			return $this->db_r->GetRow($sql, array($skill_id));
		}
	}

	/**
	 * 所持バッジ
	 */

	/**
	 * 所持バッジ情報を取得する
	 *
	 * @param int $user_id
	 * @param int $badge_id
	 * @return array
	 */
	function getUserBadge($user_id, $badge_id)
	{
		$param = array($user_id, $badge_id);
		$sql = "SELECT * FROM t_user_badge"
		     . " WHERE user_id = ? AND badge_id = ?";

		return $this->db->GetRow($sql, $param);
	}
	function getUserBadgeWithMaster($user_id, $badge_id)
	{
		$param = array($user_id, $badge_id);
		$sql = "SELECT ub.*, bm.* FROM t_user_badge ub, m_badge bm"
		     . " WHERE ub.user_id = ? AND ub.badge_id = ? AND ub.badge_id = bm.badge_id";

		return $this->db->GetRow($sql, $param);
	}

	/**
	 * バッジ所持数が上限を超えるか？
	 *
	 * @param int $user_id
	 * @param int $badge_id
	 * @param int $num_add 増やす個数
	 * @return book 真偽
	 */
	function isUserBadgeNumOutbalance($user_id, $badge_id, $num_add = 0)
	{
		$user_badge = $this->getUserBadge($user_id, $badge_id);
		$num = $user_badge ? $user_badge['num'] : 0;

		if ($num_add) {
			$num += $num_add;
		}

		$master_badge = $this->getMasterBadge($badge_id);

		return ($num > $master_badge['limit_num']);
	}

	/**
	 * 所持バッジ一覧を取得する
	 *
	 * @param int $user_id
	 * @return array
	 */
	function getUserBadgeList($user_id)
	{
		$param = array($user_id);
		$sql = "SELECT ub.badge_id, ub.num FROM t_user_badge ub, m_badge bm"
		     . " WHERE ub.user_id = ? AND ub.badge_id = bm.badge_id AND ub.num > 0"
		     . " ORDER BY bm.badge_id";
		return $this->db->GetAll($sql, $param);
	}

	/**
	 * 生成可能な所持バッジ一覧を取得する
	 * 作ってから思ったけど、この関数、意味ないよな…
	 *
	 * @param int $user_id
	 * @return array
	 */
	function getUserBadgeListCreatable($user_id)
	{
		$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$param = array($user_id, $now, $now);
		$sql = "SELECT ub.badge_id, ub.num FROM t_user_badge ub, m_badge bm"
		     . " WHERE ub.user_id = ? AND ub.badge_id = bm.badge_id AND ub.num > 0 AND bm.is_create = 1 AND bm.create_start_time <= ? AND ? < bm.create_end_time"
		     . " ORDER BY bm.badge_id";
		return $this->db->GetAll($sql, $param);
	}

	/**
	 * 表示可能な所持バッジ一覧を取得する
	 *
	 * @param int $user_id
	 * @return array
	 */
	function getUserBadgeListViewable($user_id)
	{
		$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$param = array($user_id, $now, $now);
		$sql = "SELECT ub.badge_id, ub.num FROM t_user_badge ub, m_badge bm"
		     . " WHERE ub.user_id = ? AND ub.badge_id = bm.badge_id AND ub.num > 0 AND bm.view_start_time <= ? AND ? < bm.view_end_time"
		     . " ORDER BY bm.badge_id";
		return $this->db->GetAll($sql, $param);
	}

	/**
	 * 所持バッジID一覧を取得する
	 *
	 * @param int $user_id
	 * @return array
	 */
	function getUserBadgeIdList($user_id)
	{
		$param = array($user_id);
		$sql = "SELECT badge_id FROM t_user_badge"
		     . " WHERE user_id = ?"
		     . " ORDER BY badge_id";
		return $this->db->GetCol($sql, $param);
	}

	/**
	 * バッジを増減させる
	 *
	 * @param int $user_id
	 * @param int $badge_id
	 * @param int $num 増減値
	 * @return array
	 */
	 //トラッキングとKPIの処理は外しておく
	function addUserBadge($user_id, $badge_id, $num)
	{
		// 増減しない場合
		if ($num == 0) {
			return true;
		}

		$badge_data = $this->getUserBadge(
			$user_id,
			$badge_id
		);
		$num_new = $num;
		//データがある
		if ($badge_data) {
			$num_new = $badge_data['num'] + $num;
		}
		$ret = $this->setUserBadge($user_id, $badge_id, array('num' => $num_new));

		return($ret);
	}

	/**
	 *
	 * @param int $user_id
	 * @param int $badge_id
	 * @param int $num 増減値
	 *
	 * 所持上限までしか増やさない
	 */
	 //トラッキングとKPIの処理は外しておく
	function addUserBadgeUpperLimit($user_id, $badge_id, $num)
	{
		$badge_data = $this->getUserBadgeWithMaster(
			$user_id,
			$badge_id
		);
		$num_new = $num;
		$num_old = 0;
		//データがある
		if ($badge_data) {
			$num_old = $badge_data['num'];
			$num_new = $num_old + $num;
			if ($num_new > $badge_data['limit_num']) $num_new = $badge_data['limit_num'];
			if ($num_new < 0) $num_new = 0;
		}
		if ($num_new != $num_old) {
			$ret = $this->setUserBadge($user_id, $badge_id, array('num' => $num_new));
		} else $ret = true;
		return($ret);
	}

	/**
	 * バッジ増加用のKPIタグをセットする
	 *
	 * @param int $user_id ユーザID
	 * @param int $badge_id バッジID
	 * @param int $abs_value 増加値（絶対値）
	 */
/*
	function setUserBadgeIncreaseKpi($user_id, $badge_id, $abs_value)
	{
		$kpi_m = $this->backend->getManager('Kpi');
		switch ($badge_id) {
			case self::ITEM_TICKET_GACHA_FREE:
				$kpi_m->log($kpi_m->getPlatform($user_id)."-jgm-bronze_gacha_ticket_distribution",1,$abs_value,"",$user_id,"","","");
				break;

			case self::ITEM_TICKET_GACHA_RARE:
				$kpi_m->log($kpi_m->getPlatform($user_id)."-jgm-gold_gacha_ticket_distribution",1,$abs_value,"",$user_id,"","","");
				break;
		}
	}
*/

	/**
	 * バッジ情報をセットする
	 *
	 * @param int $user_id
	 * @param int $badge_id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	 //トラッキングとKPIの処理は外しておく
	function setUserBadge($user_id, $badge_id, $columns )
	{
		//個数が未指定
		if (!isset($columns['num'])) {
			return Ethna::raiseError("badge num none", E_USER_ERROR);
		}
		//個数がマイナス
		if ($columns['num']<0) {
			return Ethna::raiseError("badge num minus", E_USER_ERROR);
		}

		// UPDATE実行
		$param = array_values($columns);
		$param[] = $user_id;
		$param[] = $badge_id;
		$sql = "UPDATE t_user_badge SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE user_id = ? AND badge_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$crud = 'U';

		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		if ($affected_rows == 0) {
			// INSERT実行
			$param = array($user_id, $badge_id, $columns['num']);
			$sql = "INSERT INTO t_user_badge(user_id, badge_id, num, date_created)"
				 . " VALUES(?, ?, ?, NOW())";
			if (!$this->db->execute($sql, $param)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}

			$crud = 'C';
		}

		return true;
	}

	/**
	 * バッジ情報を削除する
	 *
	 * @param int $user_id
	 * @param int $badge_id
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function deleteUserBadge($user_id, $badge_id)
	{
		$param = array($user_id, $badge_id);
		$sql = "DELETE FROM t_user_badge"
		     . " WHERE user_id = ? AND badge_id = ?";

		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

	/**
	 * 所持バッジ素材
	 */

	/**
	 * 所持バッジ素材情報を取得する
	 *
	 * @param int $user_id
	 * @param int $material_id
	 * @return array
	 */
	function getUserBadgeMaterial($user_id, $material_id)
	{
		$param = array($user_id, $material_id);
		$sql = "SELECT * FROM t_user_badge_material"
		     . " WHERE user_id = ? AND material_id = ?";

		return $this->db->GetRow($sql, $param);
	}
	function getUserBadgeMaterialWithMaster($user_id, $material_id)
	{
		$param = array($user_id, $material_id);
		$sql = "SELECT ub.*, bm.* FROM t_user_badge_material ub, m_badge_material bm"
		     . " WHERE ub.user_id = ? AND ub.material_id = ? AND ub.material_id = bm.material_id";

		return $this->db->GetRow($sql, $param);
	}

	/**
	 * バッジ素材所持数が上限を超えるか？
	 *
	 * @param int $user_id
	 * @param int $material_id
	 * @param int $num_add 増やす個数
	 * @return book 真偽
	 */
	function isUserBadgeMaterialNumOutbalance($user_id, $material_id, $num_add = 0)
	{
		$user_badge = $this->getUserBadgeMaterial($user_id, $material_id);
		$num = $user_badge ? $user_badge['num'] : 0;

		if ($num_add) {
			$num += $num_add;
		}

		$master_badge = $this->getMasterBadgeMaterial($material_id);

		return ($num > $master_badge['limit_num']);
	}

	/**
	 * 所持バッジ素材一覧を取得する
	 *
	 * @param int $user_id
	 * @return array
	 */
	function getUserBadgeMaterialList($user_id)
	{
		$param = array($user_id);
		$sql = "SELECT ub.material_id, ub.num FROM t_user_badge_material ub, m_badge_material bm"
		     . " WHERE ub.user_id = ? AND ub.material_id = bm.material_id AND ub.num > 0"
		     . " ORDER BY bm.material_id";
		return $this->db->GetAll($sql, $param);
	}

	/**
	 * 表示可能な所持バッジ素材一覧を取得する
	 *
	 * @param int $user_id
	 * @return array
	 */
	function getUserBadgeMaterialListViewable($user_id)
	{
		$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$param = array($user_id, $now, $now);
		$sql = "SELECT ub.material_id, ub.num, bm.name_ja FROM t_user_badge_material ub, m_badge_material bm"
		     . " WHERE ub.user_id = ? AND ub.material_id = bm.material_id AND ub.num > 0 AND bm.start_time <= ? AND ? < bm.end_time"
		     . " ORDER BY bm.material_id";
		return $this->db->GetAll($sql, $param);
	}
	/**
	 * 表示可能な所持バッジ素材一覧を取得する（マスタデータ付き）
	 *
	 * @param int $user_id
	 * @return array
	 */
	function getUserBadgeMaterialListViewableWithMaster($user_id)
	{
		$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$param = array($user_id, $now, $now);
		$sql = "SELECT ub.material_id, ub.num, bm.* FROM t_user_badge_material ub, m_badge_material bm"
		     . " WHERE ub.user_id = ? AND ub.material_id = bm.material_id AND ub.num > 0 AND bm.start_time <= ? AND ? < bm.end_time"
		     . " ORDER BY bm.material_id";
		return $this->db->GetAll($sql, $param);
	}

	/**
	 * 所持バッジ素材ID一覧を取得する
	 *
	 * @param int $user_id
	 * @return array
	 */
	function getUserBadgeMaterialIdList($user_id)
	{
		$param = array($user_id);
		$sql = "SELECT material_id FROM t_user_badge_material"
		     . " WHERE user_id = ?"
		     . " ORDER BY material_id";
		return $this->db->GetCol($sql, $param);
	}

	/**
	 * バッジ素材を増減させる
	 *
	 * @param int $user_id
	 * @param int $material_id
	 * @param int $num 増減値
	 * @return array
	 */
	 //トラッキングとKPIの処理は外しておく
	function addUserBadgeMaterial($user_id, $material_id, $num)
	{
		// 増減しない場合
		if ($num == 0) {
			return true;
		}

		$badge_data = $this->getUserBadgeMaterial(
			$user_id,
			$material_id
		);
		$num_new = $num;
		//データがある
		if ($badge_data) {
			$num_new = $badge_data['num'] + $num;
		}
		$ret = $this->setUserBadgeMaterial($user_id, $material_id, array('num' => $num_new));

		return($ret);
	}

	/**
	 *
	 * @param int $user_id
	 * @param int $material_id
	 * @param int $num 増減値
	 *
	 * 所持上限までしか増やさない
	 */
	 //トラッキングとKPIの処理は外しておく
	function addUserBadgeMaterialUpperLimit($user_id, $material_id, $num)
	{
		$badge_data = $this->getUserBadgeMaterialWithMaster(
			$user_id,
			$material_id
		);
		$num_new = $num;
		$num_old = 0;
		//データがある
		if ($badge_data) {
			$num_old = $badge_data['num'];
			$num_new = $num_old + $num;
			if ($num_new > $badge_data['limit_num']) $num_new = $badge_data['limit_num'];
			if ($num_new < 0) $num_new = 0;
		}
		if ($num_new != $num_old) {
			$ret = $this->setUserBadgeMaterial($user_id, $material_id, array('num' => $num_new));

		} else $ret = true;
		return($ret);
	}

	/**
	 * バッジ素材増加用のKPIタグをセットする
	 *
	 * @param int $user_id ユーザID
	 * @param int $material_id バッジ素材ID
	 * @param int $abs_value 増加値（絶対値）
	 */
/*
	function setUserBadgeMaterialIncreaseKpi($user_id, $material_id, $abs_value)
	{
		$kpi_m = $this->backend->getManager('Kpi');
		switch ($material_id) {
			case self::ITEM_TICKET_GACHA_FREE:
				$kpi_m->log($kpi_m->getPlatform($user_id)."-jgm-bronze_gacha_ticket_distribution",1,$abs_value,"",$user_id,"","","");
				break;

			case self::ITEM_TICKET_GACHA_RARE:
				$kpi_m->log($kpi_m->getPlatform($user_id)."-jgm-gold_gacha_ticket_distribution",1,$abs_value,"",$user_id,"","","");
				break;
		}
	}
*/

	/**
	 * バッジ素材情報をセットする
	 *
	 * @param int $user_id
	 * @param int $material_id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	 //トラッキングとKPIの処理は外しておく
	function setUserBadgeMaterial($user_id, $material_id, $columns )
	{
		//個数が未指定
		if (!isset($columns['num'])) {
			return Ethna::raiseError("badge num none", E_USER_ERROR);
		}
		//個数がマイナス
		if ($columns['num']<0) {
			return Ethna::raiseError("badge num minus", E_USER_ERROR);
		}

		// UPDATE実行
		$param = array_values($columns);
		$param[] = $user_id;
		$param[] = $material_id;
		$sql = "UPDATE t_user_badge_material SET "
			 . implode("=?,", array_keys($columns)) . "=? "
			 . " WHERE user_id = ? AND material_id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$crud = 'U';

		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		if ($affected_rows == 0) {
			// INSERT実行
			$param = array($user_id, $material_id, $columns['num']);
			$sql = "INSERT INTO t_user_badge_material(user_id, material_id, num, date_created)"
				 . " VALUES(?, ?, ?, NOW())";
			if (!$this->db->execute($sql, $param)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
			}

			$crud = 'C';
		}

		return true;
	}

	/**
	 * バッジ素材情報を削除する
	 *
	 * @param int $user_id
	 * @param int $material_id
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function deleteUserBadgeMaterial($user_id, $material_id)
	{
		$param = array($user_id, $material_id);
		$sql = "DELETE FROM t_user_badge_material"
		     . " WHERE user_id = ? AND material_id = ?";

		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}


}
?>
