<?php
/**
 * Admin/Log/Cs/Monster/Evolution/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_monster_evolution_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsMonsterEvolutionInfo extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $logdata_view_m = $this->backend->getManager('LogdataViewMonster');
        $logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
        $logdata_view_achievement_m = $this->backend->getManager('LogdataViewAchievement');
        $api_transaction_id = $this->af->get('api_transaction_id');

        // モンスター強化合成情報
        $monster_evolution_log_data = $logdata_view_m->getMonsterEvolutionDataByApiTransactionId($api_transaction_id);
        if ($monster_evolution_log_data['count'] <= 0) {
            $this->af->setApp('monster_log_count', 0);
            return 'admin_log_cs_monster_evolution_info';
        }

        // 増減モンスター情報
        $monster_list = $logdata_view_m->getMonsterDataByApiTransactionId($api_transaction_id);

        // 増減アイテム情報
        $evolution_item_list = $logdata_view_item_m->getItemDataByApiTransactionId($api_transaction_id);

        // 勲章付与情報
        $achievement_list = $logdata_view_achievement_m->getAchievementDataByApiTransactionId($api_transaction_id);

        $this->af->setApp('monster_evolution_log', $monster_evolution_log_data['data'][0]);
        $this->af->setApp('monster_evolution_log_count', $monster_evolution_log_data['count']);
        $this->af->setApp('item_log_list', $evolution_item_list['data'][0]);
        $this->af->setApp('monster_log_list', $monster_list['data']);
        $this->af->setApp('achievement_log_list', $achievement_list['data']);
        $this->af->setApp('achievement_log_count', $achievement_list['count']);
        $this->af->setApp('form_template', $this->af->form_template);
    }
}
