<?php
/**
 *  Admin/Log/Cs/Achievement/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_achievement_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsAchievement extends Pp_Form_AdminLogCs
{
}

/**
 *  admin_log_cs_achievement_info Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsAchievementInfo extends Pp_Form_AdminLogCs
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'api_transaction_id' => array('require' => true),
	);

	/**
	 *  Form input value convert filter : sample
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
		/*
		 function _filter_sample($value)
		 {
			 //  convert to upper case.
			 return strtoupper($value);
		 }
		 */
}

/**
 *  admin_log_cs_achievement_info action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsAchievementInfo extends Pp_AdminActionClass
{

	/**
	 *  preprocess of admin_log_cs_achievement_info Action.
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
			return 'admin_log_cs_achievement_info';
		}

		return null;

	}

	/**
	 *  admin_log_cs_achievement_info action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{

		$logdata_view_m = $this->backend->getManager('LogdataViewAchievement');
		$logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$logdata_view_quest_m = $this->backend->getManager('LogdataViewQuest');
		$logdata_view_gacha_m = $this->backend->getManager('LogdataViewGacha');
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

		return 'admin_log_cs_achievement_info';
	}
}
