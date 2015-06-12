<?php
/**
 *  Admin/Developer/User/Ctrl/Userarealist.php
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
 *  admin_developer_user_ctrl_userarealist ctrl implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserCtrlUserarealist extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$user_m =& $this->backend->getManager('User');
		$quest_m =& $this->backend->getManager('AdminQuest');
		
		$user_id = $this->af->get('id');
		$user_base = $user_m->getUserBase($user_id);
		
		$q_list_all = array();
		$a_list_all = array();
		$a_cnt = array();
		$area_last = 0;
		for ($i = 1; $i <= 2; $i++) {
			$q_list = $quest_m->getMasterQuestType($i);
			foreach($q_list as $val) {
				$q_list_all[($val['quest_id'])] = $val;
			}
			$a_list = array_values($quest_m->getUserAreaAssocEx($user_id, $i));
			foreach($a_list as $val) {
				$qid = $val['quest_id'];
				if (!isset($a_cnt[$qid])) $a_cnt[$qid] = 0;
				$a_cnt[$qid]++;
				if ($i == 1) $area_last = $val['area_id'];
			}
			foreach($a_list as $val) {
				$qid = $val['quest_id'];
				$val['q_name'] = $q_list_all[$qid]['name'];
				$val['a_cnt'] = $a_cnt[$qid];
				$a_list_all[] = $val;
			}
		}
		$quest_list = array_values($q_list_all);
		$area_list = array_values($a_list_all);
		
		$area_normal_all = $quest_m->getMasterAreaType(Pp_QuestManager::QUEST_TYPE_NORMAL);
		
		$this->af->setApp('base',       $user_base);
		$this->af->setApp('quest_list', $quest_list, true);
		$this->af->setApp('area_list',  $area_list,  true);
		$this->af->setApp('area_last',  $area_last,  true);
		$this->af->setApp('area_normal_all',  $area_normal_all,  true);
		
		parent::preforward();
    }
}
?>