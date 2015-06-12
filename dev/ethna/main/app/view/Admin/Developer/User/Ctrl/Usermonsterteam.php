<?php
/**
 *  Admin/Developer/User/Ctrl/Usermonsterteam.php
 *
 *  1行を編集できるだけのビュー。
 *  Pp_Action_AdminDeveloperUserCtrlから呼ばれる。
 *  t_user_baseのカラム数が多くてCtrlableGridを使用すると表示が横に伸びすぎるので、
 *  CtrlableGridは使用せず、1行についての各カラム内容を縦に並べて表示する為に、このビューを作成した。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_usermonsterteam ctrl implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserCtrlUsermonsterteam extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$user_m =& $this->backend->getManager('User');
		$monster_m =& $this->backend->getManager('AdminMonster');
		$team_m =& $this->backend->getManager('Team');
		
		$user_id = $this->af->get('id');
		
		$user_base = $user_m->getUserBase($user_id);
		$monster_master = $monster_m->getMasterMonsterAssoc();
		$monster_list = $monster_m->getUserMonsterListForApiResponseAd($user_id);
		$team_list = $team_m->getUserTeamList($user_id);
		
	//error_log("monster_list=".print_r($monster_list,true));
	//error_log("team_list=".print_r($team_list,true));
		$monster_team = array();
		//チーム所属分
		foreach($team_list as $key => $val) {
			$tkey = $val['team_id'].'-'.$val['position'];
			if ($val['user_monster_id'] >= 0) {
				$monster_id = $monster_list[($val['user_monster_id'])]['monster_id'];
				$monster_list[($val['user_monster_id'])]['name'] = $monster_master[$monster_id]['name_ja'];
				$monster_list[($val['user_monster_id'])]['cost'] = $monster_master[$monster_id]['cost'];
				$monster_list[($val['user_monster_id'])]['leader'] = $val['leader_flg'];
				$monster_list[($val['user_monster_id'])]['team_id'] = $val['team_id'];
				$monster_list[($val['user_monster_id'])]['position'] = $val['position'];
				$monster_team[$tkey] = $monster_list[($val['user_monster_id'])];
			} else {
				$monster_null = array();
				$monster_null['user_monster_id'] = $val['user_monster_id'];
				$monster_null['leader_flg'] = $val['leader_flg'];
				$monster_null['team_id'] = $val['team_id'];
				$monster_null['position'] = $val['position'];
				$monster_team[$tkey] = $monster_null;
			}
		}
		
		$this->af->setApp('base',         $user_base);
		$this->af->setApp('monster_cnt',  count($monster_list));
		$this->af->setApp('monster_team', $monster_team);
		$this->af->setApp('team_list',    $team_list);
		
		parent::preforward();
    }
}
?>