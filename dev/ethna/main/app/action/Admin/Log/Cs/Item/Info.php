<?php
/**
 *  Admin/Log/Cs/Item/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_log_cs_item_info Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsItemInfo extends Pp_Form_AdminLogCs
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'api_transaction_id' => array('require' => true),
	);

}

/**
 *  admin_log_cs_item_info action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsItemInfo extends Pp_AdminActionClass
{

	/**
	 *  preprocess of admin_log_cs_item_info Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{

		// アクセス制御
		if ($this->must_login && $this->must_permission) {
			$ret = $this->permit();
			if ($ret) {
				return $ret;
			}
		}

		if ($this->af->validate() > 0) {
			return 'admin_log_cs_item_info';
		}

		return null;

	}

	/**
	 *  admin_log_cs_item_info action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{

		$logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$logdata_view_quest_m = $this->backend->getManager('LogdataViewQuest');
		$logdata_view_gacha_m = $this->backend->getManager('LogdataViewGacha');
		$logdata_view_present_m = $this->backend->getManager('LogdataViewPresent');
		$api_transaction_id = $this->af->get('api_transaction_id');

		// クエストログ情報チェック
		$quest_log_data = $logdata_view_quest_m->getQuestDataByApiTransactionId($api_transaction_id);
		if ($quest_log_data['count'] > 0) {
			return 'admin_log_cs_quest_info';
		}

		// モンスター強化合成情報チェック
		$monster_powerup_log_data = $logdata_view_monster_m->getMonsterPowerupDataByApiTransactionId($api_transaction_id);
		if ($monster_powerup_log_data['count'] > 0) {
			return 'admin_log_cs_monster_powerup_info';
		}

		// モンスター進化合成情報チェック
		$monster_evolution_log_data = $logdata_view_monster_m->getMonsterEvolutionDataByApiTransactionId($api_transaction_id);
		if ($monster_evolution_log_data['count'] > 0) {
			return 'admin_log_cs_monster_evolution_info';
		}

		// ガチャ情報チェック
		$gacha_log_data = $logdata_view_gacha_m->getGachaLogDataByApiTransactionId($api_transaction_id);
		if ($gacha_log_data['count'] > 0) {
			return 'admin_log_cs_gacha_info';
		}

		// プレゼント情報チェック
		$present_log_data = $logdata_view_present_m->getPresentLogDataByApiTransactionId($api_transaction_id);
		if ($present_log_data['count'] > 0) {
			return 'admin_log_cs_present_info';
		}

		return 'admin_log_cs_item_info';
	}
}
