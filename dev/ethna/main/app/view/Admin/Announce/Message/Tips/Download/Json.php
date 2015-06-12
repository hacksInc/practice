<?php
/**
 *  Admin/Announce/Message/Tips/Download/Json.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_tips_download_json view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageTipsDownloadJson extends Pp_AdminViewClass
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
		$message_m = $this->backend->getManager('AdminMessage');
		$skill_m =& $this->backend->getManager('AdminSkill');
		//$table = $this->af->get('table');
		$table = 'm_tips_message';
		
		// m_help_message, m_error_message, m_help_message, m_helpbar_message, m_tip_message,
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
		}
		
		if (count($float_cols) > 0) {
			$this->string_cols = array_merge($this->string_cols, $float_cols);
		}
/*	
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
		} */
	    $list = $message_m->getMessageTipsListForJson();
        
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
		
		$this->list = $list;
    }
	
	function forward ()
	{
//		$table = $this->af->get('table');
		$table = 'm_tips_message';
		$list = $this->list;
		
		$json = jm_view_json_encode($list);
		
//		header('Content-type: application/json');
//		header('Content-Length: ' . strlen($json));
		
		$filename = "jm_" . $table . date( "Ymd" ) . ".json";
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Length: " . strlen($json));
		header("Content-Type: application/octet-stream");

		echo $json;
	}
}
