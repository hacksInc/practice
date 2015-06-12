<?php
/**
 *  Admin/Log/Cs/Photo/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_photo_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsPhotoInfo extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */

	function preforward()
	{

		$logdata_view_m = $this->backend->getManager('LogdataViewPhoto');
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
		$logdata_view_achievement_m = $this->backend->getManager('LogdataViewAchievement');
		$api_transaction_id = $this->af->get('api_transaction_id');

		// フォト取得情報
		$photo_log_data = $logdata_view_m->getPhotoLogDataByApiTransactionId($api_transaction_id);

		$this->af->setApp('photo_log_list', $photo_log_data['data'][0]);
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
