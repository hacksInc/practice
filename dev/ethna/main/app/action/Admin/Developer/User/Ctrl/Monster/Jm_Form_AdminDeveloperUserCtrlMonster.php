<?php

require_once 'array_column.php';

/**
 *	admin_developer_user_ctrl_monster_* で共通のアクションフォーム定義
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */
class Pp_Form_AdminDeveloperUserCtrlMonster extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'user_id' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,    // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'ユーザID',		  // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => false,			  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
			),
			
			'user_monster_ids' => array(
				// Form definition
				'type'		  => array(VAR_TYPE_INT), // Input type
				'form_type'   => FORM_TYPE_TEXT,	  // Form type
				'name'		  => 'モンスターユニークID', // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => false,			  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
			),
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}
		
		parent::__construct($backend);
	}
	
    /**
     *  ユーザ定義検証メソッド(フォーム値間の連携チェック等)
     *
     *  @access protected
     */
    function _validatePlus()
    {
		$user_id = $this->get('user_id');
		$user_monster_ids = $this->get('user_monster_ids');
		
		// ユーザIDとモンスターユニークIDの組み合わせをチェック
		if ($user_id && !empty($user_monster_ids)) {
			$team_m =& $this->backend->getManager('Team');
			$monster_m =& $this->backend->getManager('AdminMonster');
			
			$team_list = $team_m->getUserTeamList($user_id);
			$team_user_monster_id_assoc = array();
			if (!empty($team_list)) {
				foreach ($team_list as $team) {
					$team_user_monster_id = $team['user_monster_id'];
					if (($team_user_monster_id == Pp_TeamManager::USER_MONSTER_ID_HELPER) ||
						($team_user_monster_id == Pp_TeamManager::USER_MONSTER_ID_EMPTY)
					) {
						continue;
					}
					
					$team_user_monster_id_assoc[$team_user_monster_id] = true;
				}
			}
			
			$user_monster_assoc = $monster_m->getUserMonsterAssocForAdmin($user_id);
			if (empty($user_monster_assoc)) {
				$user_monster_assoc = array();
			}
			
			foreach ($user_monster_ids as $user_monster_id) {
				// チームに所属していたら一括削除は禁止
				// そもそも前のページで選択肢にないはず
				if (isset($team_user_monster_id_assoc[$user_monster_id])) {
					$this->ae->add(null, "チームに所属しています [{$user_monster_id}]", E_FORM_INVALIDVALUE);
				}

				// 所持していないモンスターは削除禁止
				// そもそも前のページで選択肢にないはず
				if (!isset($user_monster_assoc[$user_monster_id])) {
					$this->ae->add(null, "所持していません [{$user_monster_id}]", E_FORM_INVALIDVALUE);
				}
			}
		}
    }
}
