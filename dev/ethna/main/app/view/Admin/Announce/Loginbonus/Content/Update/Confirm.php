<?php
/**
 *  Admin/Announce/Loginbonus/Content/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_loginbonus_content_update_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceLoginbonusContentUpdateConfirm extends Pp_AdminViewClass
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
		$monster_m =& $this->backend->getManager('Monster');

		//入力フォームの値を配列にする
		$data = array();
		for ($i = 0; $i < 10; $i++) {
			$data[$i] = array();
			foreach(array('dist_type','number','item_id','lv') as $val) {
				$data[$i][$val] = $this->af->get("$val$i");
			}
			$data[$i]['monster_name'] = '';
			$data[$i]['dist_type_name'] = $present_m->DIST_TYPE_OPTIONS[($data[$i]['dist_type'])];
		}
		$err_chk = false;
		$err_msg = array();
		//内容をチェック
		foreach($data as $key => $val) {
			if ($val['number'] <= 0) {
				$err_msg[] = ($key+1)."日目：配布数がエラーです";
				$err_chk = true;
			}
			if ($val['dist_type'] == Pp_PresentManager::DIST_TYPE_MONSTER) {
				if ($val['lv'] <= 0) {
					$err_msg[] = ($key+1)."日目：レベルがエラーです";
					$err_chk = true;
				}
				$monster = $monster_m->getMasterMonster($val['item_id']);
				if ($monster == NULL) {
					$err_msg[] = ($key+1)."日目：モンスターIDがエラーです";
					$err_chk = true;
				} else {
					$data[$key]['monster_name'] = $monster['name_ja'];
				}
			}
		}
		
		$this->af->setApp('data', $data);
		$this->af->setApp('err_chk', $err_chk);
		$this->af->setApp('err_msg', $err_msg);
		//$this->af->setApp('dist_type_options', $present_m->DIST_TYPE_OPTIONS);
		
		parent::preforward();
    }
}
?>