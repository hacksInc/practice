<?php
/**
 *  Admin/Log/Cs/Stress/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_stress_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsStressInfo extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$logdata_view_stress_m = $this->backend->getManager('LogdataViewStress');
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$character_m = $this->backend->getManager('Character');
		$api_transaction_id = $this->af->get('api_transaction_id');

		// ストレスケア履歴情報
		$ut_stress_data = array();
		$stress_data = $logdata_view_stress_m->getStressDataByApiTransactionId($api_transaction_id);
		foreach($stress_data['data'] as $data) {
			$stress = $character_m->getUserCharacter($data['pp_id'], $data['character_id']);
			if ($stress) $ut_stress_data[] = $stress;
		}
		$character_master_data = $character_m->getMasterCharacterAssoc();

		$this->af->setApp('stress_list', $ut_stress_data);
		$this->af->setApp('stress_count', count($ut_stress_data));
		$this->af->setApp('character_master', $character_master_data);

		$this->af->setApp('form_template', $this->af->form_template);

	}
}
