<?php
/**
 *  Admin/Log/Cs/Accounting/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_accounting_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsAccountingInfo extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$logdata_view_accounting_m = $this->backend->getManager('LogdataViewAccounting');
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$api_transaction_id = $this->af->get('api_transaction_id');

		// 課金アイテム購入情報
		$accounting_data = $logdata_view_accounting_m->getAccountingDataByApiTransactionId($api_transaction_id);

		// アイテム情報
		$item_data = $logdata_view_item_m->getItemDataByApiTransactionId($api_transaction_id);

		$this->af->setApp('accounting_log_list', $accounting_data['data']);
		$this->af->setApp('accounting_log_count', $accounting_data['count']);
		$this->af->setApp('item_log_list', $item_data['data']);
		$this->af->setApp('item_log_count', $item_data['count']);

		$this->af->setApp('form_template', $this->af->form_template);

	}
}
