<?php
/**
 *  Admin/Test/Data/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_test_data_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminTestDataExec extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'user_type' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_RADIO, // Form type
			'name'        => 'user_type',     // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'uid' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'uid',           // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'add' => array(
			// Form definition
			'type'        => array(VAR_TYPE_STRING), // Input type
			'form_type'   => FORM_TYPE_CHECKBOX,     // Form type
			'name'        => 'add',                  // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),

		'item_add' => array(
			// Form definition
			'type'        => array(VAR_TYPE_STRING), // Input type
			'form_type'   => FORM_TYPE_CHECKBOX,     // Form type
			'name'        => 'item_add',             // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),

		'monster_id' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'monster_id',    // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),

		'monster_lv' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'monster_lv',    // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),

		'gold' => array(
			// Form definition
			// このクエリーは数値だが、空文字列を許可する為にVAR_TYPE_STRINGにする
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'gold',          // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => '/[0-9]*/',      // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),

		'gacha_point' => array(
			// Form definition
			// このクエリーは数値だが、空文字列を許可する為にVAR_TYPE_STRINGにする
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'gacha_point',   // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => '/[0-9]*/',      // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),

		'exp' => array(
			// Form definition
			// このクエリーは数値だが、空文字列を許可する為にVAR_TYPE_STRINGにする
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'exp',           // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => '/[0-9]*/',      // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),

		'point_type' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_RADIO, // Form type
			'name'        => 'point_type',     // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
		
		'game_transaction_id' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'game_transaction_id', // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => '/[0-9]*/',      // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),

		'item_count' => array(
			// Form definition
			// このクエリーは数値だが、空文字列を許可する為にVAR_TYPE_STRINGにする
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'item_count',    // Display name
		  
			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => '/[0-9]*/',      // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
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
 *  admin_test_data_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminTestDataExec extends Pp_AdminActionClass
{
//	protected $must_login = false;

	/**
     *  preprocess of admin_test_data_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
		if ($this->af->validate() > 0) {
			return 'admin_error_400';
		}

        return null;
    }

    /**
     *  admin_test_data_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$user_type = $this->af->get('user_type');
		$uid       = $this->af->get('uid');
		$add       = $this->af->get('add');

		$user_m    =& $this->backend->getManager('User');
		$team_m    =& $this->backend->getManager('Team');
		$monster_m =& $this->backend->getManager('Monster');
		$friend_m  =& $this->backend->getManager('Friend');
		$item_m    =& $this->backend->getManager('Item');
		$point_m   =& $this->backend->getManager('Point');

		$remote_addr = Base_Client::getRemoteAddr();
		$action = $this->backend->ctl->getCurrentActionName();
		
		$db =& $this->backend->getDB();
		
		switch ($user_type) {
/*
			// ユーザ生成
			case 'create':
				$account = 'test' . substr(uniqid(), -6);

				$dmpw = 'pass5678';
				$user = $user_m->createUser($account, $dmpw, Pp_UserManager::OS_IPHONE, 1);
				if (!$user || Ethna::isError($user)) {
					$db->rollback();
					return 'admin_error_500';
				}
				
				$user_id = $user['user_id'];

				$ret = $team_m->initPosition($user_id);
				if (!$ret || Ethna::isError($ret)) {
					$db->rollback();
					return 'admin_error_500';
				}

				break;
*/
			
			// ユーザ情報取得
			case 'update':
				$user_id = $uid;
				$user = $user_m->getUserBase($user_id);
				break;
			
			default:
				$db->rollback();
				return 'admin_error_500';
		}

		// ゴールド付与
/*
		if (in_array('gold', $add)) {
			$ret = $user_m->setUserBase($user_id, array(
				'gold' => $user['gold'] + 10000,
			));
			if (!$ret || Ethna::isError($ret)) {
				$db->rollback();
				return 'admin_error_500';
			}
		}
*/
		
		// モンスター付与
		if (in_array('monster', $add)) {
			foreach(
				$db->GetCol("SELECT monster_id FROM m_monster ORDER BY RAND() LIMIT 10")	
				as $monster_id
			) {
				$ret = $monster_m->createUserMonster($user_id, $monster_id, null);
				if (!$ret || Ethna::isError($ret)) {
					$db->rollback();
					return 'admin_error_500';
				}
			}
		}
		
		// フレンド追加
		if (in_array('friend', $add)) {
			foreach ($db->GetCol(
				"SELECT user_id FROM t_user_base WHERE user_id != ? ORDER BY RAND() LIMIT 10",
				array($user_id)
			) as $friend_id) {
				$ret = $friend_m->setUserFriend($user_id, $friend_id, 
						array('status' => Pp_FriendManager::STATUS_FRIEND));
//				if (!$ret || Ethna::isError($ret)) {
//					$db->rollback();
//					return 'admin_error_500';
//				}
				$ret = $friend_m->setUserFriend($friend_id, $user_id,  
						array('status' => Pp_FriendManager::STATUS_FRIEND));

			}
		}
		
		// チーム編成
		if (in_array('team', $add)) {
			$position = 1;
			foreach ($db->GetCol(
				"SELECT user_monster_id FROM t_user_monster WHERE user_id = ? ORDER BY RAND() LIMIT 4",
				array($user_id)
			) as $user_monster_id) {
				$leader_flg = ($position == 1) ? 1 : 0;
				$ret = $team_m->setUserTeam($user_id, 0, $position++, $user_monster_id, $leader_flg);
				if (!$ret || Ethna::isError($ret)) {
					$db->rollback();
					return 'admin_error_500';
				}
			}

			$ret = $team_m->setUserTeam($user_id, 0, $position++, -2, 0);
			if (!$ret || Ethna::isError($ret)) {
				$db->rollback();
				return 'admin_error_500';
			}

			$ret = $user_m->setUserBase($user_id, array(
				'active_team_id' => 0,
			));
			if (!$ret || Ethna::isError($ret)) {
				$db->rollback();
				return 'admin_error_500';
			}
		}
		
		// モンスター付与（ID指定）
		$monster_id = $this->af->get('monster_id');
		$monster_lv = $this->af->get('monster_lv');
		if ($monster_id > 0) {
			$monster_columns = null;
			if ($monster_lv > 0) {
				$monster_columns = array(
					'lv' => $monster_lv,
				);
			}
			
			$master_monster = $monster_m->getMasterMonster($monster_id);
			if (!$master_monster || Ethna::isError($master_monster)) {
				$db->rollback();
				return 'admin_error_500';
			}
			
			$ret = $monster_m->createUserMonster($user_id, $monster_id, $monster_columns);
			if (!$ret || Ethna::isError($ret)) {
				$db->rollback();
				return 'admin_error_500';
			}
		}
		
		// アイテム付与
		$item_add = $this->af->get('item_add');
		foreach ($item_add as $item_id) {
			if (!in_array($item_id, array(1001, 1002, 1003, 1004, 1005))) {
				$db->rollback();
				return 'admin_error_500';
			}
			
			$ret = $item_m->addUserItemUpperLimit($user_id, $item_id, 100);
			if (!$ret || Ethna::isError($ret)) {
				$db->rollback();
				return 'admin_error_500';
			}
		}
		
		// t_user_base関連
		$new_base = array();

		// ゴールド付与
		$gold = $this->af->get('gold');
		if ($gold) {
			$new_base['gold'] = $user['gold'] + $gold;
		}
		
		// ガチャポイント
		$gacha_point = $this->af->get('gacha_point');
		if ($gacha_point) {
			$new_base['gacha_point'] = $user['gacha_point'] + $gacha_point;
		}
		
		// 経験値更新
		$exp = $this->af->get('exp');
		if ($exp) {
			$new_base['exp'] = $exp;
		}
		
		//ポイント管理サーバ
		$game_transaction_id = $this->af->get('game_transaction_id');
		if (strlen($game_transaction_id) > 0) {
			if (!$point_m->isTransactionAvailable($game_transaction_id, $user_id)) {
				if (!$point_m->isTransactionExists($game_transaction_id, $user_id)) {
					// 指定されたトランザクションが存在しないまたは他のユーザーに紐付いている
					error_log('ERROR:' . __FILE__ . ':' . __LINE__);
					return 'admin_error_500';
				} else {
					// 指定されたトランザクションは使用済み（ポイント管理サーバからの出力を受信済み）
					error_log('ERROR:' . __FILE__ . ':' . __LINE__);
					return 'admin_error_500';
				}
			}
			
			$point_type = $this->af->get('point_type');
			switch ($point_type) {
				case 'gamebonus': // サービス付与
					$service_count = $this->af->get('item_count');
					if ($service_count) {
						$point_output = $point_m->gamebonus($game_transaction_id, $user_id, $service_count, $remote_addr, $action);
						list($payment, $service) = $point_m->convertPointOutputToPaymentService($point_output);

						if (is_numeric($service)) {
							$new_base['service_point'] = $service;
						}
					}
				
					break;
			
				case 'consume': // 消費
					$item_count = $this->af->get('item_count');
					if ($item_count) {
						$point_output = $point_m->consume($game_transaction_id, $user_id, $item_count, $remote_addr, $action);
						list($payment, $service) = $point_m->convertPointOutputToPaymentService($point_output);
					
						if (is_numeric($payment)) {
							$new_base['medal'] = $payment;
						}
			
						if (is_numeric($service)) {
							$new_base['service_point'] = $service;
						}
					}
				
					break;
				
				default:
					error_log('ERROR:' . __FILE__ . ':' . __LINE__);
					return 'admin_error_500';
			}

			if (!is_array($point_output) ||
				!isset($point_output['sts']) ||
				($point_output['sts'] != 'OK')
			) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return 'admin_error_500';
			}

			// トランザクション開始
			$transaction = ($db->db->transCnt == 0); // トランザクション開始するか
			if ($transaction) $db->begin();
			
			$ret = $point_m->updatePreparedTransaction();
			if ($ret !== true) {
				if ($transaction) $db->rollback();
				
				return 'admin_error_500';
			}
			
			$new_base['game_transaction_id'] = $point_m->createTransaction($user_id);
error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($new_base, true));
		}
		
		if (count($new_base) > 0) {
			$ret = $user_m->setUserBase($user_id, $new_base);
		}
		
		// トランザクション完了
//		$db->commit();
		if (isset($transaction) && $transaction) {
			$db->commit();
		}
		
		if ($user_type == 'create') {
			$this->af->setApp('uid',  $user['user_id']);
			$this->af->setApp('uipw', $user['uipw']);
			$this->af->setApp('dmpw', $dmpw);
		}
		
        return 'admin_test_data_exec';
    }
}

?>