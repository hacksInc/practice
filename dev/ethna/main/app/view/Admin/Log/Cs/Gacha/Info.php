<?php
/**
 *  Admin/Log/Cs/Gacha/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_gacha_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsGachaInfo extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */

	function preforward()
	{

		$logdata_view_m = $this->backend->getManager('LogdataViewGacha');
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
		$logdata_view_achievement_m = $this->backend->getManager('LogdataViewAchievement');
		$api_transaction_id = $this->af->get('api_transaction_id');

		// ガチャ情報
		$gacha_log_data = $logdata_view_m->getGachaLogDataByApiTransactionId($api_transaction_id);
		$gacha_prize_log_data = $logdata_view_m->getGachaPrizeLogDataByApiTransactionId($api_transaction_id);

		// 取得モンスター情報
		$monster_log_data = $logdata_view_monster_m->getMonsterDataByApiTransactionId($api_transaction_id);

		$get_monster_data = '';
		for($i=0;$i<$gacha_log_data['data'][0]['lot_count'];$i++){
			$tmp_monster_data['prize'] = $gacha_prize_log_data['data'][$i];
			$tmp_monster_data['monster'] = $monster_log_data['data'][$i];
			$get_monster_data[] = $tmp_monster_data;
		}

		// 消費アイテム情報
		$item_log_data = $logdata_view_item_m->getItemDataByApiTransactionId($api_transaction_id);

		// 勲章付与情報
		$achievement_data = $logdata_view_achievement_m->getAchievementDataByApiTransactionId($api_transaction_id);
			/*        if ($quest_log_data['count'] > 0) {
				return 'admin_log_cs_gacha_info';
			}*/
		$this->af->setApp('gacha_log_list', $gacha_log_data['data'][0]);
		$this->af->setApp('gacha_prize_log_list', $gacha_prize_log_data['data']);
		$this->af->setApp('gacha_prize_log_count', $gacha_prize_log_data['count']);
		$this->af->setApp('item_log_list', $item_log_data['data']);
		$this->af->setApp('item_log_count', $item_log_data['count']);
		$this->af->setApp('monster_log_list', $monster_log_data['data']);
		$this->af->setApp('get_monster_log_list', $get_monster_data);
		$this->af->setApp('monster_log_count', $monster_log_data['count']);
		$this->af->setApp('achievement_log_list', $achievement_data['data']);
		$this->af->setApp('achievement_log_count', $achievement_data['count']);

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
