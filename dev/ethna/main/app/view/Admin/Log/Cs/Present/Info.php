<?php
/**
 *  Admin/Log/Cs/Present/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_present_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsPresentInfo extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $logdata_view_present_m = $this->backend->getManager('LogdataViewPresent');
        $logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
        $logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
        $api_transaction_id = $this->af->get('api_transaction_id');

        // アイテム情報
        $item_data = $logdata_view_item_m->getItemDataByApiTransactionId($api_transaction_id);
        $monster_data = $logdata_view_monster_m->getMonsterDataByApiTransactionId($api_transaction_id);
        $present_data = $logdata_view_present_m->getPresentLogDataByApiTransactionId($api_transaction_id);

        $this->af->setApp('present_log_list', $present_data['data']);
        $this->af->setApp('present_log_count', $present_data['count']);
        $this->af->setApp('item_log_list', $item_data['data']);
        $this->af->setApp('item_log_count', $item_data['count']);
        $this->af->setApp('monster_log_list', $monster_data['data']);
        $this->af->setApp('monster_log_count', $monster_data['count']);

        $this->af->setApp('form_template', $this->af->form_template);

    }
}
