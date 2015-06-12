<?php
/**
 *  Pp_AdminSkillManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 *  @see  \\Fscave02\プロジェクト\SP12-004\企画\仕様書\スキル\スキルデータ管理_参考資料_130719.xls
 */

/**
 *  Pp_AdminSkillManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminSkillManager extends Ethna_AppManager
{
	/** スキルマスター アプリ用データ（JSON変換用）取得 */
	function getMasterSkillListForAppliData()
	{
		$sql = <<<EOD
SELECT skill_id, name_jp, name_en, name_es, summary_jp, summary_en, summary_es, 
  max_skill_lv, need_turn, funcnum, func_id_1, func_id_2, func_id_3
FROM m_skill
EOD;
		return $this->db_r->GetAll($sql);
	}

	/** スキル効果マスター アプリ用データ（JSON変換用）取得 */
	function getMasterLeaderSkillListForAppliData()
	{
		$sql = <<<EOD
SELECT skillID, name_jp, name_en, name_es, summary_jp, summary_en, summary_es, 
  funcNum, func_id_1, func_id_2, func_id_3
FROM m_leader_skill
EOD;
		return $this->db_r->GetAll($sql);
	}

	/** リーダースキルマスター アプリ用データ（JSON変換用）取得 */
	function getMasterSkillEffectListForAppliData()
	{
		$sql = <<<EOD
SELECT func_id, func_type, target,
  ( skill_attr_fire | 
    (skill_attr_ice   << 1) | 
    (skill_attr_plant << 2) | 
    (skill_attr_light << 3) | 
    (skill_attr_dark  << 4) |
    (skill_attr_user  << 5) ) AS skill_attr,
  ( status_change_posion |
    (status_change_unknown1 << 1) |
    (status_change_unknown2 << 2) |
    (status_change_unknown3 << 3) |
    (status_change_unknown4 << 4) ) AS status_change,
  param_1, param_2, param_3, 
  penetration, 
  ( trs_cond_attr_fire |
    (trs_cond_attr_ice   << 1) |
    (trs_cond_attr_plant << 2) |
    (trs_cond_attr_light << 3) |
    (trs_cond_attr_dark  << 4) |
    (trs_cond_attr_user  << 5) ) AS trs_cond_attr,
  ( trs_cond_type_power |
    (trs_cond_type_defence   << 1) |
    (trs_cond_type_support   << 2) |
    (trs_cond_type_balance   << 3) |
    (trs_cond_type_sell      << 4) |
    (trs_cond_type_gousei    << 5) |
    (trs_cond_type_evolution << 6) |
    (trs_cond_type_special   << 7) ) AS trs_cond_type,
  ( trs_cond_gender_man | 
    (trs_cond_gender_woman << 1) ) AS trs_cond_gender,
  ( trs_cond_kind_human |
    (trs_cond_kind_animal   << 1) |
    (trs_cond_kind_material << 2) |
    (trs_cond_kind_fairy    << 3) |
    (trs_cond_kind_hero     << 4) |
    (trs_cond_kind_dragon   << 5) |
    (trs_cond_kind_devil    << 6) |
    (trs_cond_kind_god      << 7) ) AS trs_cond_kind,
  efct_target, 
  efct_no, 
  damagewaittime
FROM m_skill_effect
EOD;

		return $this->db_r->GetAll($sql);
	}
	
	/** リーダースキル効果マスター アプリ用データ（JSON変換用）取得 */
	function getMasterLeaderSkillEffectListForAppliData()
	{
		$sql = <<<EOD
SELECT func_id, func_type, param_1, param_2,
  ( trs_cond_attr_fire |
    (trs_cond_attr_ice   << 1) |
    (trs_cond_attr_plant << 2) |
    (trs_cond_attr_light << 3) |
    (trs_cond_attr_dark  << 4) |
    (trs_cond_attr_user  << 5) ) AS trs_cond_attr,
  ( trs_cond_type_power |
    (trs_cond_type_defence   << 1) |
    (trs_cond_type_support   << 2) |
    (trs_cond_type_balance   << 3) |
    (trs_cond_type_sell      << 4) |
    (trs_cond_type_gousei    << 5) |
    (trs_cond_type_evolution << 6) |
    (trs_cond_type_special   << 7) ) AS trs_cond_type,
  ( trs_cond_gender_man |
    (trs_cond_gender_woman << 1) ) AS trs_cond_gender,
  ( trs_cond_kind_human |
    (trs_cond_kind_animal   << 1) |
    (trs_cond_kind_material << 2) |
    (trs_cond_kind_fairy    << 3) |
    (trs_cond_kind_hero     << 4) |
    (trs_cond_kind_dragon   << 5) |
    (trs_cond_kind_devil    << 6) |
    (trs_cond_kind_god      << 7) ) AS trs_cond_kind,
  ( trs_cond_etc_hp100 |
    (trs_cond_etc_unknown1 << 1) |
    (trs_cond_etc_unknown2 << 2) |
    (trs_cond_etc_unknown3 << 3) ) AS trs_cond_etc,
  efct_no
FROM m_leader_skill_effect
EOD;

		return $this->db_r->GetAll($sql);
	}
}
?>
