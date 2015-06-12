<?php
/**
 *  Admin/Developer/Master/Download/Json.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_download_json view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterDownloadJson extends Pp_AdminViewClass
{
	protected $list = null;

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$developer_m =& $this->backend->getManager('Developer');
		$skill_m =& $this->backend->getManager('AdminSkill');
		$table = $this->af->get('table');

		// m_dialog_message, m_error_message, m_help_message, m_helpbar_message, m_tip_message,
		// m_skill, m_skill_effect, m_leader_skill, m_leader_skill_effect については、
		// これらテーブルはAPIでは使用せず、管理画面を通してJSON生成するのみなので、
		// intval対象外にするカラム名はPp_ViewClassに記述せず、ここで追加する。
		$float_cols = array();
		if (preg_match('/^m_[a-z]+_message$/', $table)) {
			$this->string_cols = array_merge($this->string_cols, array(
				'message',
			));
		} else if (
			($table == 'm_skill') ||
			($table == 'm_skill_effect') ||
			($table == 'm_leader_skill') ||
			($table == 'm_leader_skill_effect')
		) {
			$this->string_cols = array_merge($this->string_cols, array(
				'name_jp', 'name_en', 'name_es',
				'summary_jp', 'summary_en', 'summary_es'
			));

			$float_cols = array_merge($float_cols, array(
				'damagewaittime', 'param_1', 'param_2', 'param_3'
			));
		} else if (
			($table == 'm_achievement_type') ||
			($table == 'm_achievement_rank') ||
			($table == 'm_boost')
		) {
			// m_achievement_type, m_achievement_rank, m_boost については、
			// intval対象外にするカラム名はテーブル定義から判別する
			$metadata = $developer_m->getMetadata($table, false);
			$tmp_cols = array_keys($metadata['editablegrid_datatype'], 'string');
			if (is_array($tmp_cols) && (count($tmp_cols) > 0)) {
				$this->string_cols = array_merge($this->string_cols, $tmp_cols);
			}
		} else if (
			($table == 'm_monster_action_id') ||
			($table == 'm_monster_action_tbl')
		) {
			$this->string_cols = array_merge($this->string_cols, array(
				'param', 'ref_id', 'rate', 'seq'
			));
		} else if (
			($table == 'm_badge_skill')
		) {
			$this->string_cols = array_merge($this->string_cols, array(
				'name_ja', 'name_en', 'name_es',
				'summary_ja', 'summary_en', 'summary_es',
				'detail', 'skill_type_detail', 'trs_cond_attr', 'trs_dungeon_id', 'trs_monster_id'
			));

			$float_cols = array_merge($float_cols, array(
				'damagewaittime', 'param_1', 'param_2', 'param_3'
			));
		}

		if (count($float_cols) > 0) {
			$this->string_cols = array_merge($this->string_cols, $float_cols);
		}

		if ($table == 'm_skill') {
			$list = $skill_m->getMasterSkillListForAppliData();
		} else if ($table == 'm_skill_effect') {
			$list = $skill_m->getMasterSkillEffectListForAppliData();
		} else if ($table == 'm_leader_skill') {
			$list = $skill_m->getMasterLeaderSkillListForAppliData();
		} else if ($table == 'm_leader_skill_effect') {
			$list = $skill_m->getMasterLeaderSkillEffectListForAppliData();
		} else {
			$list = $developer_m->getMasterList($table, false);
			$list = array_values($list);
		}

		$list = $this->array_intval($list);
		foreach ($float_cols as $key) {
			$i = count($list);
			while ($i--) {
				if (isset($list[$i][$key])) {
					$list[$i][$key] = floatval($list[$i][$key]);
				}
			}
		}

		// m_monster.evolution_materialはDB上ではカンマ区切り文字列だが、管理画面でのJSON出力時に数値の配列に変換する
		// 2013/7/4 久保さんからの要望
		if ($table == 'm_monster') {
			$i = count($list);
			while ($i--) {
				if (strlen($list[$i]['evolution_material']) > 0) {
					$evolution_material = array_map('intval', explode(',', $list[$i]['evolution_material']));
				} else {
					$evolution_material = array();
				}

				$list[$i]['evolution_material'] = $evolution_material;
			}
		}
		//m_monster_action_id、m_monster_action_tblも同様
		//特定のカラムのみ数値の配列に変換して書き換える
		if ($table == 'm_monster_action_id' || $table == 'm_monster_action_tbl') {
			$clm = array('param', 'ref_id', 'rate', 'seq');
			$i = count($list);
			while ($i--) {
				foreach ($clm as $val) {
					if (isset($list[$i][$val])) {
						if (strlen($list[$i][$val]) > 0) {
							$tmp = array_map('intval', explode(',', $list[$i][$val]));
						} else {
							$tmp = array();
						}
						$list[$i][$val] = $tmp;
					}
				}
			}
		}
		//m_boostも同様
		//特定のカラムのみ数値の配列に変換して書き換える
		if ($table == 'm_boost') {
			$this->replaceCsvColumnToIntArray($list, array('target', 'any_param'));
		}

		//m_home_bannerも同様
		//特定のカラムのみ配列に変換して書き換える
		if ($table == 'm_home_banner') {
			$this->replaceCsvColumnToArray($list, array('banner_attribute_value'));
		}

		$this->list = $list;
	}

	function forward ()
	{
		$table = $this->af->get('table');
		$list = $this->list;

		$json = pp_view_json_encode($list);

//		header('Content-type: application/json');
//		header('Content-Length: ' . strlen($json));

		$filename = "pp_" . $table . date( "Ymd" ) . ".json";
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Length: " . strlen($json));
		header("Content-Type: application/octet-stream");

		echo $json;
	}

	/**
	 * カンマ区切り文字列のカラムを数値の配列に書き換える
	 *
	 * 引数で指定された$listを変更するので注意
	 * 指定されたカラムの長さが0だった場合は空の配列に展開される
	 * @param array $list マスターデータ連想配列の配列への参照
	 * @param array $keys 書き換え対象カラム名の配列
	 */
	protected function replaceCsvColumnToIntArray(&$list, $keys)
	{
		$i = count($list);
		while ($i--) {
			foreach ($keys as $key) {
				if (!isset($list[$i][$key])) {
					continue;
				}

				if (strlen($list[$i][$key]) > 0) {
					$tmp = array_map('intval', explode(',', $list[$i][$key]));
				} else {
					$tmp = array();
				}

				$list[$i][$key] = $tmp;
			}
		}
	}

	/**
	 * カンマ区切り文字列のカラムを配列に書き換える
	 *
	 * 引数で指定された$listを変更するので注意
	 * 指定されたカラムの長さが0だった場合は空の配列に展開される
	 * @param array $list マスターデータ連想配列の配列への参照
	 * @param array $keys 書き換え対象カラム名の配列
	 */
	protected function replaceCsvColumnToArray(&$list, $keys)
	{
		$i = count($list);
		while ($i--) {
			foreach ($keys as $key) {
				if (!isset($list[$i][$key])) {
					continue;
				}

				if (strlen($list[$i][$key]) > 0) {
					$tmp = explode(',', $list[$i][$key]);
				} else {
					$tmp = array();
				}
				$list[$i][$key] = $tmp;
			}
		}
	}
}
