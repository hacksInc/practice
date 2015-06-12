<?php
/**
 * Admin/Log/Cs/Quest/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_quest_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsQuestInfo extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {

        $logdata_view_m = $this->backend->getManager('LogdataViewQuest');
        $logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
        $logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
        $logdata_view_achievement_m = $this->backend->getManager('LogdataViewAchievement');
        $api_transaction_id = $this->af->get('api_transaction_id');

        $quest_log_data = $logdata_view_m->getQuestDataByApiTransactionId($api_transaction_id);

        $quest_log_count = $logdata_view_m->getQuestLogDataCount($search_params);
        if ($quest_log_data['count'] == 0) {
            $this->af->setApp('quest_log_count', 0);
            return 'admin_log_cs_quest_info';
        }

        // 増減モンスター情報
        $monster_list = $logdata_view_monster_m->getMonsterDataByApiTransactionId($api_transaction_id);

        // 増減アイテム情報
        $item_list = $logdata_view_item_m->getItemDataByApiTransactionId($api_transaction_id);

        // 勲章付与情報
        $achievement_list = $logdata_view_achievement_m->getAchievementDataByApiTransactionId($api_transaction_id);
$this->backend->logger->log(LOG_INFO, '************************ achievement_list result==[' . print_r($achievement_list, true) . ']');

        $this->af->setApp('quest_log_list', $quest_log_data['data'][0]);
        $this->af->setApp('quest_log_count', $quest_log_data['count']);
        $this->af->setApp('item_log_list', $item_list['data']);
        $this->af->setApp('item_log_count', $item_list['count']);
        $this->af->setApp('monster_log_list', $monster_list['data']);
        $this->af->setApp('monster_log_count', $monster_list['count']);
        $this->af->setApp('achievement_log_list', $achievement_list['data']);
        $this->af->setApp('achievement_log_count', $achievement_list['count']);
        $this->af->setApp('form_template', $this->af->form_template);
    }
}
