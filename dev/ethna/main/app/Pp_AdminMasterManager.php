<?php
/**
 *  Pp_AdminMasterManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */
require_once 'array_column.php';

/**
 * Pp_AdminMasterManager
 *
 * @author {$author}
 * @access public
 * @package Pp
 */
class Pp_AdminMasterManager extends Ethna_AppManager
{
	/**
	 * m_sp_area_releaseとm_areaの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_sp_area_release_and_m_area()
	{
		$sql = <<< EOF
			SELECT
				msar.area_id
			FROM
				m_sp_area_release msar
			WHERE
				NOT EXISTS(SELECT 1 FROM m_area ma WHERE ma.area_id = msar.area_id)
EOF;
		$chk_parm = array('table' => 'm_area', 'column' => 'area_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_missionとm_areaの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_mission_and_m_area()
	{
		$sql = <<< EOF
			SELECT
				mm.area_id,
				mm.mission_id
			FROM
				m_mission mm
			WHERE
				NOT EXISTS(SELECT 1 FROM m_area ma WHERE ma.area_id = mm.area_id)
EOF;
		$chk_parm = array('table' => 'm_area', 'column' => 'area_id', 'pk' => 'mission_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_missionとm_characterの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_mission_and_m_character()
	{
		$sql = <<< EOF
			SELECT
				mm.mission_id,
				mm.accompany_character_id
			FROM
				m_mission mm
			WHERE
				NOT EXISTS(SELECT 1 FROM m_character mc WHERE mc.character_id = mm.accompany_character_id)
EOF;
		$chk_parm = array('table' => 'm_character', 'column' => 'accompany_character_id', 'pk' => 'mission_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_mission_enemyとm_missionの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_mission_enemy_and_m_mission()
	{
		$sql = <<< EOF
			SELECT
				mme.eu_id,
				mme.mission_id
			FROM
				m_mission_enemy mme
			WHERE
				NOT EXISTS(SELECT 1 FROM m_mission mm WHERE mm.mission_id = mme.mission_id)
EOF;
		$chk_parm = array('table' => 'm_mission', 'column' => 'mission_id', 'pk' => 'eu_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_mission_enemyとm_enemy_aiの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_mission_enemy_and_m_enemy_ai()
	{
		$sql = <<< EOF
			SELECT
				mme.eu_id,
				mme.ai_id
			FROM
				m_mission_enemy mme
			WHERE
				NOT EXISTS(SELECT 1 FROM m_enemy_ai mea WHERE mea.ai_id = mme.ai_id)
EOF;
		$chk_parm = array('table' => 'm_enemy_ai', 'column' => 'ai_id', 'pk' => 'eu_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_photo_gachaとm_missionの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_photo_gacha_and_m_mission()
	{
		$sql = <<< EOF
			SELECT
				mpg.gacha_id,
				mpg.mission_id
			FROM
				m_photo_gacha mpg
			WHERE
				NOT EXISTS(SELECT 1 FROM m_mission mm WHERE mm.mission_id = mpg.mission_id)
EOF;
		$chk_parm = array('table' => 'm_mission', 'column' => 'mission_id', 'pk' => 'gacha_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_photo_gachaとm_stageの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_photo_gacha_and_m_stage()
	{
		$sql = <<< EOF
			SELECT
				mpg.gacha_id,
				mpg.stage_id
			FROM
				m_photo_gacha mpg
			WHERE
				NOT EXISTS(SELECT 1 FROM m_stage ms WHERE ms.stage_id = mpg.stage_id)
EOF;
		$chk_parm = array('table' => 'm_stage', 'column' => 'stage_id', 'pk' => 'gacha_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_photo_gacha_lineupとm_photo_gachaの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_photo_gacha_lineup_and_m_photo_gacha()
	{
		$sql = <<< EOF
			SELECT
				mpgl.gacha_id,
				mpgl.photo_id
			FROM
				m_photo_gacha_lineup mpgl
			WHERE
				NOT EXISTS(SELECT 1 FROM m_photo_gacha mpg WHERE mpg.gacha_id = mpgl.gacha_id)
EOF;
		$chk_parm = array('table' => 'm_photo_gacha', 'column' => 'gacha_id', 'pk' => 'photo_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_photo_gacha_lineupとm_photoの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_photo_gacha_lineup_and_m_photo()
	{
		$sql = <<< EOF
			SELECT
				mpgl.photo_id,
				mpgl.gacha_id
			FROM
				m_photo_gacha_lineup mpgl
			WHERE
				NOT EXISTS(SELECT 1 FROM m_photo mp WHERE mp.photo_id = mpgl.photo_id)
EOF;
		$chk_parm = array('table' => 'm_photo', 'column' => 'photo_id', 'pk' => 'gacha_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_characterとm_missionの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_character_and_m_mission()
	{
		$sql = <<< EOF
			SELECT
				mc.character_id,
				mc.release_mission_id
			FROM
				m_character mc
			WHERE
				NOT EXISTS(SELECT 1 FROM m_mission mm WHERE mm.mission_id = mc.release_mission_id)
				AND mc.release_mission_id <> 0
EOF;
		$chk_parm = array('table' => 'm_mission', 'column' => 'release_mission_id', 'pk' => 'character_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_speechとm_characterの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_speech_and_m_character()
	{
		$sql = <<< EOF
			SELECT
				ms.id,
				ms.character_id
			FROM
				m_speech ms
			WHERE
				NOT EXISTS(SELECT 1 FROM m_character mc WHERE mc.character_id = ms.character_id)
EOF;
		$chk_parm = array('table' => 'm_speech', 'column' => 'character_id', 'pk' => 'id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_sell_listとm_sell_itemの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_sell_list_and_m_sell_item()
	{
		$sql = <<< EOF
			SELECT
				msl.shop_id,
				msl.sell_id
			FROM
				m_sell_list msl
			WHERE
				NOT EXISTS(SELECT 1 FROM m_sell_item msi WHERE msi.sell_id = msl.sell_id)
EOF;
		$chk_parm = array('table' => 'm_sell_item', 'column' => 'sell_id', 'pk' => 'shop_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_sell_listとm_shopの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_sell_list_and_m_shop()
	{
		$sql = <<< EOF
			SELECT
				msl.shop_id,
				msl.sell_id
			FROM
				m_sell_list msl
			WHERE
				NOT EXISTS(SELECT 1 FROM m_shop ms WHERE ms.shop_id = msl.shop_id)
EOF;
		$chk_parm = array('table' => 'm_shop', 'column' => 'shop_id', 'pk' => 'sell_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_sell_itemとm_itemの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_sell_item_and_m_item()
	{
		$sql = <<< EOF
			SELECT
				msi.sell_id,
				msi.item_id
			FROM
				m_sell_item msi
			WHERE
				NOT EXISTS(SELECT 1 FROM m_item mi WHERE mi.item_id = msi.item_id)
EOF;
		$chk_parm = array('table' => 'm_item', 'column' => 'item_id', 'pk' => 'sell_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_help_categoryとm_helpの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_help_category_and_m_help()
	{
		$sql = <<< EOF
			SELECT
				mhc.id,
				mhc.category_id
			FROM
				m_help_category mhc
			WHERE
				NOT EXISTS(SELECT 1 FROM m_help mh WHERE mh.id = mh.id)
EOF;
		$chk_parm = array('table' => 'm_help', 'column' => 'category_id', 'pk' => 'id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_achievement_conditionとm_achievement_groupの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_achievement_condition_and_m_achievement_group()
	{
		$sql = <<< EOF
			SELECT
				mac.ach_id,
				mac.ach_group_id
			FROM
				m_achievement_condition mac
			WHERE
				NOT EXISTS(SELECT 1 FROM m_achievement_group mag WHERE mag.ach_group_id = mac.ach_group_id)
EOF;
		$chk_parm = array('table' => 'm_achievement_group', 'column' => 'ach_group_id', 'pk' => 'ach_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	/**
	 * m_achievement_conditionとm_missionの整合性チェック
	 *
	 * @return array 成功時:チェック結果 失敗時:Ethna_Errorオブジェクト
	 */
	public function chk_m_achievement_condition_and_m_mission()
	{
		$sql = <<< EOF
			SELECT
				mac.ach_id,
				mac.cond_mission_id
			FROM
				m_achievement_condition mac
			WHERE
				NOT EXISTS(SELECT 1 FROM m_mission mm WHERE mm.mission_id = mac.cond_mission_id)
				AND mac.cond_mission_id <> 0
EOF;
		$chk_parm = array('table' => 'm_mission', 'column' => 'cond_mission_id', 'pk' => 'ach_id');
		return $this->_chk_master($sql, $chk_parm);
	}

	private function _chk_master($sql, $param)
	{
		$db = $this->backend->getDB('m_r');
		$res = $db->GetAll($sql);
		if ($res === false)
		{
			return Ethna::raiseError ( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, $db->db->ErrorNo (), $db->db->ErrorMsg (), __FILE__, __LINE__ );
		}
		$cnt = 0;
		$msg = null;
		if (count ( $res ) > 0)
		{
			foreach ( $res as $k => $v )
			{
				if (array_key_exists('pk', $param))
				{
					$msg [] = "{$param['table']}に存在しない{$param['column']}が登録されています。[{$param['pk']}:{$v[$param['pk']]}],[{$param['column']}]:{$v[$param['column']]}";
				}
				else
				{
					$msg [] = "{$param['table']}に存在しない{$param['column']}が登録されています。[{$param['column']}]:{$v[$param['column']]}";
				}
				$cnt ++;
			}
		}
		return array (
				'msg' => $msg,
				'cnt' => $cnt
		);
	}
}
