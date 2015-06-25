<?php
/**
 *  Admin/Announce/Loginbonus/Content/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_loginbonus_content_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceLoginbonusContentUpdateInput extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$present_m =& $this->backend->getManager('Present');
		$item_m =& $this->backend->getManager('Item');
		$loginbonus_m =& $this->backend->getManager('AdminLoginbonus');
		$login_bonus_id = $this->af->get("id");
		$lb = $loginbonus_m->getLoginbonusId($login_bonus_id);
		$lbi = $loginbonus_m->getLoginbonusItem($login_bonus_id);
		$data = array();
		foreach($lbi as $key => $val) {
			$idx = $val['stamp']-1;
			$data[$idx]['number'] = $val['item_num'];
			$data[$idx]['lv'] = $val['lv'];
			$data[$idx]['item_id'] = 0;
			//item_typeからdist_typeに変換
			switch ($val['item_type']) {
				case Pp_PresentManager::TYPE_MAGICAL_MEDAL://マジカルメダル
					$data[$idx]['dist_type'] = Pp_PresentManager::DIST_TYPE_MEDAL;
					break;
				case Pp_PresentManager::TYPE_MEDAL://合成メダル
					$data[$idx]['dist_type'] = Pp_PresentManager::DIST_TYPE_COIN;
					break;
				case Pp_PresentManager::TYPE_ITEM://アイテム
					if ($val['item_id'] == Pp_ItemManager::ITEM_TICKET_GACHA_RARE)
						$data[$idx]['dist_type'] = Pp_PresentManager::DIST_TYPE_G_TICKET;//ゴールドチケット
					if ($val['item_id'] == Pp_ItemManager::ITEM_TICKET_GACHA_FREE)
						$data[$idx]['dist_type'] = Pp_PresentManager::DIST_TYPE_B_TICKET;//ブロンズチケット
					break;
				case Pp_PresentManager::TYPE_MONSTER://モンスター
					$data[$idx]['dist_type'] = Pp_PresentManager::DIST_TYPE_MONSTER;
					$data[$idx]['item_id'] = $val['item_id'];
					break;
			}
		}
		$name = $lb['name'];
		$date_start = $lb['date_start'];
		$date_end = $lb['date_end'];

		$this->af->setApp('login_bonus_id', $login_bonus_id);
		$this->af->setApp('name', $name);
		$this->af->setApp('date_start', $date_start);
		$this->af->setApp('date_end', $date_end);
		$this->af->setApp('data', $data);
		
		$this->af->setApp('dist_type_options', $present_m->DIST_TYPE_OPTIONS);
		
		parent::preforward();
    }
}
?>