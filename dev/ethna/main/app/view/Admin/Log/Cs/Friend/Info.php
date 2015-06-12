<?php
/**
 *  Admin/Log/Cs/Friend/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_friend_info view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsFriendInfo extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $logdata_view_friend_m = $this->backend->getManager('LogdataViewFriend');
        $logdata_view_achievement_m = $this->backend->getManager('LogdataViewAchievement');
        $api_transaction_id = $this->af->get('api_transaction_id');

        // アイテム情報
        $friend_data = $logdata_view_friend_m->getFriendDataByApiTransactionId($api_transaction_id);
        $search_params = array(
            'user_id' => $friend_data['data'][0]['user_id'],
            'friend_user_id' => $friend_data['data'][0]['friend_user_id'],
        );
        $friend_info_data = $logdata_view_friend_m->getFriendLogInfoData($search_params);
        $achievement_data = $logdata_view_achievement_m->getAchievementDataByApiTransactionId($api_transaction_id);

        $this->af->setApp('friend_log_list', $friend_info_data['data']);
        $this->af->setApp('friend_log_count', $friend_info_data['count']);
        $this->af->setApp('acheivement_log_list', $achievement_data['data']);
        $this->af->setApp('acheivement_log_count', $achievement_data['count']);

        $this->af->setApp('form_template', $this->af->form_template);

    }
}
