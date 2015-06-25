<?php
/**
 *  Admin/Log/Cs/Character/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_character_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsCharacterInfo extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$logdata_view_character_m = $this->backend->getManager('LogdataViewCharacter');
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$character_m = $this->backend->getManager('Character');
		$api_transaction_id = $this->af->get('api_transaction_id');

		// キャラクター履歴情報
		$ut_character_data = array();
		$character_data = $logdata_view_character_m->getCharacterDataByApiTransactionId($api_transaction_id);
		foreach($character_data['data'] as $data) {
			$character = $character_m->getUserCharacter($data['pp_id'], $data['character_id']);
			if ($character) $ut_character_data[] = $character;
		}
		$character_master_data = $character_m->getMasterCharacterAssoc();

		$this->af->setApp('character_list', $ut_character_data);
		$this->af->setApp('character_count', count($ut_character_data));
		$this->af->setApp('character_master', $character_master_data);

		$this->af->setApp('form_template', $this->af->form_template);

	}
}
