<?php
/**
 *  Pp_LogDataManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */
require_once 'Pp_LogdataManager.php';

/**
 *  Pp_LogDataManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogdataFriendManager extends Pp_LogDataManager
{

    const FRIEND_INCREASE_STATUS_REQUEST = 1; // フレンド申請
    const FRIEND_INCREASE_STATUS_DELETE = 2;  // 削除
    const FRIEND_INCREASE_STATUS_ACCEPT = 3;  // フレンド承認
    const FRIEND_INCREASE_STATUS_BLOCK = 4;   // ブロック

    /**
     * フレンドリクエスト送信時のログを記録する
     *
     * @param array $input_params
     * @param array $toll_item
     * @param array $user_data
     * @return mixed 
     */
    public function trackingFriendRequest($input_params, $user_base, $friend_user_base)
    {

        $monster_m = $this->backend->getManager('Monster');

        $friend_log_data = array(
            'api_transaction_id' => $input_params['api_transaction_id'],
            'user_id' => $input_params['user_id'],
            'ua' => $user_base['ua'],
            'name' => $user_base['name'],
            'rank' => $user_base['rank'],
            'request_user_id' => $input_params['user_id'],
            'request_name' => $user_base['name'],
            'friend_user_id' => $input_params['friend_id'],
            'friend_name' => $friend_user_base['name'],
            'status' => self::FRIEND_INCREASE_STATUS_REQUEST,
            'date_friend' => date('Y-m-d H:i:s', time()),
            'old_status' => $input_params['old_status'],
            'old_date_friend' => $input_params['old_date_friend'],
            'processing_type' => $input_params['processing_type'],
            'processing_type_name' => $input_params['processing_type_name'],
            'area_id' => '',
            'account_name' => $input_params['account_name'],
        );
        $result = $this->insertLogFriendData($friend_log_data);

        // フレンド申請者情報
        // やる前に、ユーザーのリーダーモンスター情報を取得せねばいかん
        $user_monster_data = $monster_m->getActiveLeaderList(array($input_params['user_id']));
        $monster_master_data = $this->_getMasterMonster($user_monster_data[0]['monster_id']);
        $friend_user_log_data = array(
            'api_transaction_id' => $input_params['api_transaction_id'],
            'user_id' => $input_params['user_id'],
            'ua' => $user_base['ua'],
            'name' => $user_base['name'],
            'rank' => $user_base['rank'],
            'old_friend_rest' => $user_base['friend_rest'],
            'friend_rest' => $user_base['new_friend_rest'],
            'friend_max_num' => $user_base['friend_max'],
            'reader_monster_user_id' => $user_monster_data[0]['user_monster_id'],
            'reader_monster_id' => $user_monster_data[0]['monster_id'],
            'reader_monster_name' => $monster_master_data['name_ja'],
            'reader_monster_rare' => $monster_master_data['m_rare'],
            'reader_monster_lv' => $user_monster_data[0]['lv'],
            'reader_monster_skill_lv' => $user_monster_data[0]['skill_lv'],
        );
        $result = $this->insertLogFriendUserData($friend_user_log_data);

        // フレンド申請受側情報
        // やる前に、ユーザーのリーダーモンスター情報を取得せねばいかん
        $user_monster_data = $monster_m->getActiveLeaderList(array($input_params['friend_id']));
        $monster_master_data = $this->_getMasterMonster($user_monster_data[0]['monster_id']);
        $friend_user_log_data = array(
            'api_transaction_id' => $input_params['api_transaction_id'],
            'user_id' => $input_params['friend_id'],
            'ua' => $friend_user_base['ua'],
            'name' => $friend_user_base['name'],
            'rank' => $friend_user_base['rank'],
            'old_friend_rest' => $friend_user_base['friend_rest'],
            'friend_rest' => $friend_user_base['new_friend_rest'],
            'friend_max_num' => $friend_user_base['friend_max'],
            'reader_monster_user_id' => $user_monster_data[0]['user_monster_id'],
            'reader_monster_id' => $user_monster_data[0]['monster_id'],
            'reader_monster_name' => $monster_master_data['name_ja'],
            'reader_monster_rare' => $monster_master_data['m_rare'],
            'reader_monster_lv' => $user_monster_data[0]['lv'],
            'reader_monster_skill_lv' => $user_monster_data[0]['skill_lv'],
        );
        $result = $this->insertLogFriendUserData($friend_user_log_data);

        return true;
    }
}
