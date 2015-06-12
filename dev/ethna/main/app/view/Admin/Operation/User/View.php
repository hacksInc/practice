<?php
/**
 *  Admin/Operation/User/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_operation_user_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminOperationUserView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$base = $this->af->getApp('base');
		
		if (!$base) {
			$this->af->ae->add(null, "ユーザーが見つかりません。", E_ERROR_DEFAULT);
		} else {
			$friend_m =& $this->backend->getManager('Friend');
			$item_m =& $this->backend->getManager('Item');
			$present_m =& $this->backend->getManager('AdminPresent');
			$tracking_m =& $this->backend->getManager('AdminTracking');
			$admin_m =& $this->backend->getManager('Admin');
			
			$user_id = $base['user_id'];
			
			// フレンド
			$friend_list = array();
			foreach (array(Pp_FriendManager::STATUS_FRIEND, Pp_FriendManager::STATUS_REQUEST_S) as $status) {
				$friend_list[$status] =  $friend_m->getFriendList($user_id, $status);
			}
			
			// アイテム
			$user_item_list = $item_m->getUserItemList($user_id);
			foreach ($user_item_list as $key => $row) {
				$master_item = $item_m->getMasterItem($row['item_id']);
				if ($master_item) {
					$user_item_list[$key]['name'] = $master_item['name_ja'];
				}
			}
			
			// プレゼント
			$user_present_list = $present_m->getUserPresentListAnyStatus($user_id);
			
			// トラッキングログ
			$tracking_log_list = $tracking_m->getTrackingLogListFromUserId($user_id);
			
			// 魔法のメダル消費ログ
			$user_shop_log_list = $admin_m->getLogUserShopList($user_id);
			
			$this->af->setApp('friend_list', $friend_list);
			$this->af->setApp('item_list',   $user_item_list);
			$this->af->setApp('present_list', $user_present_list);
			$this->af->setApp('tracking_list', $tracking_log_list);
			$this->af->setApp('shop_list', $user_shop_log_list);
		}
    }
}

?>
