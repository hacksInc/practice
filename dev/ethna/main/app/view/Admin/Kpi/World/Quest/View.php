<?php
/**
 *  Admin/Kpi/World/Quest/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_world_quest_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiWorldQuestView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$date        = $this->af->get('date');
		$format      = $this->af->get('format');
		$arppu_range = $this->af->get('arppu_range');
		$platform    = $this->af->get('platform');
		
		$admin_m   =& $this->backend->getManager('Admin');
		$user_m   =& $this->backend->getManager('AdminUser');
		
		list($arppu_min, $arppu_max) = explode('_', $arppu_range);
		
		if ($platform == 'any') {
			$ua = 0;
		} else {
			$ua = $user_m->getUaFromPlatform($platform);
		}
		$list = $admin_m->getKpiNormalQuestList($date, $arppu_min, $arppu_max, $ua);

		$arppu_range_name = $this->af->form_template['arppu_range']['option'][$arppu_range];
		$platform_name    = $this->af->form_template['platform']['option'][$platform];
		
		if ($format == 'html') {
			// テンプレート変数にアサイン
			$this->af->setApp('list',             $list);
			$this->af->setApp('arppu_range_name', $arppu_range_name);
			$this->af->setApp('platform_name',    $platform_name);
		} else if ($format == 'csv') {
			// CSV準備
			$table = array();

			$table[] = array(
				'達成ノーマルクエスト数', 
				'人数', 
				'プレイヤーランク（平均）', 
				'脱落数', 
				'使用合成素材数（平均）', 
			);

			foreach ($list as $row) {
				$table[] = array(
					$row['clear_num'], 
					$row['user_num'], 
					$row['rank_avg'], 
					$row['escape_num'], 
					$row['synthesis_material_avg'], 
				);
			}
			
			$this->af->setApp('table', $table);
			$this->af->setApp('filename', 'world_quest_' . date('YmdHis', $_SERVER['REQUEST_TIME']) . '.csv');
		}
	}
}

?>
