<?php
/**
 *  Admin/Log/Cs/Achievement/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_achievement_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsAchievementInfo extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$logdata_view_achievement_m = $this->backend->getManager('LogdataViewAchievement');
		$api_transaction_id = $this->af->get('api_transaction_id');

		// ŒMÍ•t—^î•ñ
		$achievement_data = $logdata_view_achievement_m->getAchievementDataByApiTransactionId($api_transaction_id);

		$this->af->setApp('achievement_log_list', $achievement_data['data']);
		$this->af->setApp('achievement_log_count', $achievement_data['count']);

		$this->af->setApp('form_template', $this->af->form_template);

	}
}
