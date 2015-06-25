<?php
/**
 *  Admin/Developer/User/Ctrl/Usermonsterteamchg.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_usermonsterteamchg Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlUsermonsterteamchg extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'id' => array(
            // Form definition
			// VAR_TYPE_INTにすると空文字列を渡されたときにエラーになるので文字列として扱う
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'id',            // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => '/[0-9]*/',      // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        ),
		
		'team_id',
		'leader_flag1',
		'leader_flag2',
		'leader_flag3',
		'leader_flag4',
		'leader_flag5',
		'user_monster_id1',
		'user_monster_id2',
		'user_monster_id3',
		'user_monster_id4',
		'user_monster_id5',
    );

    /**
     *  Form input value convert filter : sample
     *
     *  @access protected
     *  @param  mixed   $value  Form Input Value
     *  @return mixed           Converted result.
     */
    /*
    function _filter_sample($value)
    {
        //  convert to upper case.
        return strtoupper($value);
    }
    */
}

/**
 *  admin_developer_user_ctrl_usermonsterteamchg action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlUsermonsterteamchg extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_user_edit Action.
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
     *  admin_developer_user_edit action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$developer_m =& $this->backend->getManager('Developer');
		
		$user_m =& $this->backend->getManager('User');
		$monster_m =& $this->backend->getManager('AdminMonster');
		$team_m =& $this->backend->getManager('Team');
		
		$user_id = $this->af->get('id');
		$team_id = $this->af->get('team_id');
		$leader_flag = array();
		$leader_flag[1] = $this->af->get('leader_flag1');
		$leader_flag[2] = $this->af->get('leader_flag2');
		$leader_flag[3] = $this->af->get('leader_flag3');
		$leader_flag[4] = $this->af->get('leader_flag4');
		$leader_flag[5] = $this->af->get('leader_flag5');
		$user_monster_id = array();
		$user_monster_id[1] = $this->af->get('user_monster_id1');
		$user_monster_id[2] = $this->af->get('user_monster_id2');
		$user_monster_id[3] = $this->af->get('user_monster_id3');
		$user_monster_id[4] = $this->af->get('user_monster_id4');
		$user_monster_id[5] = $this->af->get('user_monster_id5');
		
		//保存
		for ($i = 1; $i <= 5; $i++) {
			$ret = $team_m->setUserTeam($user_id, $team_id, $i, $user_monster_id[$i], $leader_flag[$i]);
			if (!$ret || Ethna::isError($ret)) {
				$this->af->setAppNe('err_msg', $ret);
				return 'admin_developer_user_ctrl_usermonsterteam';
			}
		}
		
		$user_base = $user_m->getUserBase($user_id);
		$monster_master = $monster_m->getMasterMonsterAssoc();
		$monster_list = $monster_m->getUserMonsterListForApiResponseAd($user_id);
		$team_list = $team_m->getUserTeamList($user_id);
		
		$monster_team = array();
		//チーム所属分
		foreach($team_list as $key => $val) {
			if ($team_id == $val['team_id']) {
				$tkey = $val['team_id'].'-'.$val['position'];
				if ($user_monster_id[($val['position'])] >= 0) {
					$monster_id = $monster_list[($user_monster_id[($val['position'])])]['monster_id'];
					$monster_list[($user_monster_id[($val['position'])])]['name'] = $monster_master[$monster_id]['name_ja'];
					$monster_list[($user_monster_id[($val['position'])])]['cost'] = $monster_master[$monster_id]['cost'];
					$monster_list[($user_monster_id[($val['position'])])]['leader'] = $leader_flag[($val['position'])];
					$monster_list[($user_monster_id[($val['position'])])]['team_id'] = $val['team_id'];
					$monster_list[($user_monster_id[($val['position'])])]['position'] = $val['position'];
					$monster_team[$tkey] = $monster_list[($user_monster_id[($val['position'])])];
				} else {
					$monster_null = array();
					$monster_null['user_monster_id'] = $val['user_monster_id'];
					$monster_null['leader_flg'] = $val['leader_flg'];
					$monster_null['team_id'] = $val['team_id'];
					$monster_null['position'] = $val['position'];
					$monster_team[$tkey] = $monster_null;
				}
			}
		}
		
		$this->af->setApp('base',         $user_base);
		$this->af->setApp('monster_cnt',  count($monster_list));
		$this->af->setApp('monster_team', $monster_team);
		$this->af->setApp('team_list',    $team_list);
		
        return 'admin_developer_user_ctrl_usermonsterteamchg';
    }
}

?>