<?php
/**
 *  Admin/Developer/User/Ctrl/Usermonsterchg.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_usermonsterchg Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlUsermonsterchg extends Pp_AdminActionForm
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
		'user_monster_id',
		'lv',
		'exp',
		'hp_plus',
		'attack_plus',
		'heal_plus',
		'skill_lv',
		'badge_num',
		'badges',
		'func', 
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
 *  admin_developer_user_ctrl_usermonsterchg action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlUsermonsterchg extends Pp_AdminActionClass
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
		$user_monster_id = $this->af->get('user_monster_id');
		$lv = $this->af->get('lv');
		$exp = $this->af->get('exp');
		$hp_plus = $this->af->get('hp_plus');
		$attack_plus = $this->af->get('attack_plus');
		$heal_plus = $this->af->get('heal_plus');
		$skill_lv = $this->af->get('skill_lv');
		$badge_num = $this->af->get('badge_num');
		$badges = $this->af->get('badges');
		$func = $this->af->get('func');
		
		$user_base = $user_m->getUserBase($user_id);
		$monster_master = $monster_m->getMasterMonsterAssoc();
		$monster_list = $monster_m->getUserMonsterListForApiResponseAd($user_id);
		$team_list = $team_m->getUserTeamList($user_id);
		$user_monster = $monster_list[$user_monster_id];
		
		//削除
		if ($func == 'delete') {
			//チームから外す
			foreach($team_list as $key => $val) {
				$tkey = $val['team_id'].'-'.$val['position'];
				if ($val['user_monster_id'] == $user_monster_id) {
					$ret = $team_m->setUserTeam($user_id, $val['team_id'], $val['position'], -1);
					if (!$ret || Ethna::isError($ret)) {
						$this->af->setAppNe('err_msg', $ret);
						return 'admin_developer_user_ctrl_error';
					}
				}
			}
			//削除
			$ret = $monster_m->delete($user_id, $user_monster_id);
			if (!$ret || Ethna::isError($ret)) {
				return 'admin_developer_user_ctrl_error';
			}
			//最新情報にする
			$monster_list = $monster_m->getUserMonsterListForApiResponseAd($user_id);
		}
		//更新
		if ($func == 'update') {
			$ret = $monster_m->setUserMonster($user_id, $user_monster_id, array(
				'monster_id' => $user_monster['monster_id'],
				'exp' => $exp,
				'lv' => $lv,
				'skill_lv' => $skill_lv,
				'hp_plus' => $hp_plus,
				'attack_plus' => $attack_plus,
				'heal_plus' => $heal_plus,
				'badge_num' => $badge_num,
				'badges' => $badges,
			));
			if (!$ret || Ethna::isError($ret)) {
				return 'admin_developer_user_ctrl_error';
			}
		}
		
		$this->af->setApp('base',         $user_base);
		$this->af->setApp('monster_cnt',  count($monster_list));
		$this->af->setApp('func',         $func);
		$this->af->setApp('monster_data', $user_monster);
		
        return 'admin_developer_user_ctrl_usermonsterchg';
    }
}

?>