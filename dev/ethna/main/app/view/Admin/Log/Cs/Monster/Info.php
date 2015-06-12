<?php
/**
 *  Admin/Log/Cs/Monster/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_monster_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsMonsterInfo extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
        $api_transaction_id = $this->af->get('api_transaction_id');

        // アイテム情報
        $monster_data = $logdata_view_monster_m->getMonsterDataByApiTransactionId($api_transaction_id);

        $this->af->setApp('monster_log_list', $monster_data['data']);
        $this->af->setApp('monster_log_count', $monster_data['count']);

        $this->af->setApp('form_template', $this->af->form_template);

    }
}
