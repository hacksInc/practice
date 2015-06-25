<?php
/**
 *  Admin/Announce/Loginbonus/Content/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_loginbonus_content_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceLoginbonusContentCreateExec extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'login_bonus_id',
        'name',
        'date_start',
        'date_end',
        'dist_type0',
        'dist_type1',
        'dist_type2',
        'dist_type3',
        'dist_type4',
        'dist_type5',
        'dist_type6',
        'dist_type7',
        'dist_type8',
        'dist_type9',
        'number0',
        'number1',
        'number2',
        'number3',
        'number4',
        'number5',
        'number6',
        'number7',
        'number8',
        'number9',
        'item_id0',
        'item_id1',
        'item_id2',
        'item_id3',
        'item_id4',
        'item_id5',
        'item_id6',
        'item_id7',
        'item_id8',
        'item_id9',
        'lv0',
        'lv1',
        'lv2',
        'lv3',
        'lv4',
        'lv5',
        'lv6',
        'lv7',
        'lv8',
        'lv9',
    );
}

/**
 *  admin_announce_loginbonus_content_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceLoginbonusContentCreateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_loginbonus_content_create_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
/*
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }
*/

    /**
     *  admin_announce_loginbonus_content_create_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$loginbonus_m =& $this->backend->getManager('AdminLoginbonus');
		$present_m =& $this->backend->getManager('Present');
		$item_m =& $this->backend->getManager('Item');
		$admin_m =& $this->backend->getManager('Admin');
		$login_bonus_id = $this->af->get('login_bonus_id');
		$name = $this->af->get('name');
		$date_start = $this->af->get('date_start');
		$date_end = $this->af->get('date_end');
		
		//upd$param = array($columns['date_start'], $columns['date_end'], $columns['name'], $columns['account_upd'], $columns['login_bonus_id']);
		//ins$param = array($columns['login_bonus_id'], $columns['date_start'], $columns['date_end'], $columns['name'], $columns['account_reg']);
		
		//入力フォームの値を配列にする
		$data = array();
		for ($i = 0; $i < 10; $i++) {
			$data[$i] = array();
			foreach(array('dist_type','number','item_id','lv') as $val) {
				$data[$i][$val] = $this->af->get("$val$i");
			}
		}
		
		//MySQLトランザクション開始	
		$db =& $this->backend->getDB();
		$transaction = ($db->db->transCnt == 0); // トランザクション開始するか
		if ($transaction) $db->begin();
		
		$columns = array(
					'login_bonus_id' => $login_bonus_id,
					'name'           => $name,
					'date_start'     => $date_start,
					'date_end'       => $date_end,
					'account_reg'    => $this->session->get('lid'),
					'account_upd'    => $this->session->get('lid'),
		);
		$ret = $loginbonus_m->insertLoginBonus($columns);
		if (!$ret || Ethna::isError($ret)) {
			if ($transaction) $db->rollback();
			$this->af->setAppNe('err_msg', $ret);
	        return 'admin_announce_loginbonus_content_error';
		}
		
		//付与アイテムのタイプ（1:アイテム/2:モンスター/5:合成メダル(コイン)/6:マジカルメダル）
		//１件ずつ保存
		foreach($data as $key => $val) {
			$lv = 0;
			$item_num = $val['number'];
			switch ($val['dist_type']) {
				case Pp_PresentManager::DIST_TYPE_MEDAL://マジカルメダル（無料）
					$item_type = Pp_PresentManager::TYPE_MAGICAL_MEDAL;
					$item_id = 0;
					break;
				case Pp_PresentManager::DIST_TYPE_COIN://合成メダル
					$item_type = Pp_PresentManager::TYPE_MEDAL;
					$item_id = 0;
					break;
				case Pp_PresentManager::DIST_TYPE_G_TICKET://ゴールドチケット
					$item_type = Pp_PresentManager::TYPE_ITEM;
					$item_id = Pp_ItemManager::ITEM_TICKET_GACHA_RARE;
					break;
				case Pp_PresentManager::DIST_TYPE_B_TICKET://ブロンズチケット
					$item_type = Pp_PresentManager::TYPE_ITEM;
					$item_id = Pp_ItemManager::ITEM_TICKET_GACHA_FREE;
					break;
				case Pp_PresentManager::DIST_TYPE_MONSTER://モンスター
					$item_type = Pp_PresentManager::TYPE_MONSTER;
					$item_id = $val['item_id'];//モンスターID
					$lv = $val['lv'];
					break;
			}
			$columns = array(
						'login_bonus_id' => $login_bonus_id,
						'stamp'          => $key+1,
						'item_type'      => $item_type,
						'item_id'        => $item_id,
						'item_num'       => $item_num,
						'lv'             => $lv,
			);
			$ret = $loginbonus_m->insertLoginBonusItem($columns);
			if (!$ret || Ethna::isError($ret)) {
				if ($transaction) $db->rollback();
				$this->af->setAppNe('err_msg', $ret);
		        return 'admin_announce_loginbonus_content_error';
			}
		}
		
		//MySQLトランザクション完了
		if ($transaction) $db->commit();
		
	//	return 'admin_announce_loginbonus_content_index';
		header( "Location: /admin/announce/loginbonus/content/index" );
		exit;
    }
}

?>