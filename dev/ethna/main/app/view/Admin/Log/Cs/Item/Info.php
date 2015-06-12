<?php
/**
 *  Admin/Log/Cs/Item/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_item_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsItemInfo extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
		$api_transaction_id = $this->af->get('api_transaction_id');

		// アイテム情報
		$item_data = $logdata_view_item_m->getItemDataByApiTransactionId($api_transaction_id);

		$this->af->setApp('item_log_list', $item_data['data']);
		$this->af->setApp('item_log_count', $item_data['count']);

		$this->af->setApp('form_template', $this->af->form_template);

	}
}
