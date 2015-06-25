<?php
require_once dirname(__FILE__) . '/../sub/sub.php';

/***************************************************
		関数
***************************************************/
/**
 * ユーザー情報を取得
 * 
 * citizen_idはCITIZEN_ID_DEFAULT+user_idで求めるものとする。
 * 新規リリースにともなってBASEも変更する
 */
function getUserInfo ( $db, $user_id ) {
    $bind_param	= $db->initBindParam();
    
    $sql = "SELECT user_name, user_name_en, email, sex_kbn, ? AS code_name, sp_user.ID + ? AS citizen_id, 
            COALESCE(point, 0) AS point, theme_id, theme_name, COALESCE(login_cnt, 0) AS login_cnt, user_agent 
            FROM sp_user
            LEFT JOIN (SELECT user_ID, SUM(point) AS point FROM user_point WHERE user_ID = ? GROUP BY user_ID) AS user_point
            ON sp_user.ID = user_point.user_ID
            LEFT JOIN user_theme ON sp_user.ID = user_theme.user_ID AND user_theme.current_flg = ?
            LEFT JOIN theme ON user_theme.theme_id = theme.ID AND theme.disp_flg = ?
            LEFT JOIN (SELECT user_ID, COUNT(ID) AS login_cnt FROM login_history WHERE user_ID = ? GROUP BY user_ID) AS login_history
            ON sp_user.ID = login_history.user_ID
            WHERE sp_user.ID = ?;";

	$db->setSql_str($sql);
	$bind_param = $db->addBind( $bind_param, "s", CODE_NAME_DEFAULT );
    $bind_param	= $db->addBind( $bind_param, "i", CITIZEN_ID_DEFAULT );
	$bind_param	= $db->addBind( $bind_param, "i", $user_id );
	$bind_param = $db->addBind( $bind_param, "s", CURRENT_FLG_CURRENT );
	$bind_param = $db->addBind( $bind_param, "s", DISP_FLG_DISP );
	$bind_param	= $db->addBind( $bind_param, "i", $user_id );
	$bind_param	= $db->addBind( $bind_param, "i", $user_id );
	$result		= $db->exeQuery( $bind_param );
    
	$row = $db->exeFetch( $result );
	$db->closeStmt( $result );
    
	return $row;
}

/**
 * 旧サイコパスIDからユーザーを検索
 */
function getOldUser ( $db, $email, $password )
{
    $bind_param	= $db->initBindParam();
    
    $sql = "SELECT * FROM sp_user WHERE email = ? AND password = ?;";
    
    $db->setSql_str( $sql );
    
	$bind_param	= $db->addBind( $bind_param, "s", $email );
    $bind_param	= $db->addBind( $bind_param, "s", getEncryptedPassword( $password ) );
    
	$result = $db->exeQuery($bind_param);
    
    return $db->exeFetch( $result );
}

/**
 * 旧ユーザーを新形式にコンバート
 */
function convertOldUser ( $db, $new_password, $uuid, $email, $password, $ua )
{
    $bind_param	= $db->initBindParam();
    
    $sql = "UPDATE sp_user SET new_password = ?, uuid = ?, update_date = NOW() WHERE email = ? AND password = ? AND user_agent = ?";
    
    $db->setSql_str( $sql );
    
    $bind_param	= $db->addBind( $bind_param, "s", getEncryptedPassword( $new_password ) );
    $bind_param	= $db->addBind( $bind_param, "s", $uuid );
    $bind_param	= $db->addBind( $bind_param, "s", $email );
    $bind_param	= $db->addBind( $bind_param, "s", getEncryptedPassword( $password ) );
    $bind_param	= $db->addBind( $bind_param, "i", $ua );
    
    $result		= $db->exeQuery( $bind_param );
    $count		= $db->getAffectedRows($result);
    
    if ( $count <= 0 ) {
        $db->rollback();
        return false;
    }
    
    $db->closeStmt($result);
    
    return true;
}

/**
 * citizen_id の生成
 * return	string		生成した citizen_id
 */
function createCitizenID($db) {
	$bind_param	= $db->initBindParam();
	$sql		= "SELECT MAX(CAST(citizen_id AS unsigned)) AS citizen_id FROM sp_user;";
	$db->setSql_str($sql);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getRows($result);
	
	if ($count <= 0) {
		$db->closeStmt($resutl);
		return CITIZEN_ID_DEFAULT;
	}
    
    $row = $db->exeFetch($result);
    
	if (empty($row['citizen_id'])) {
		$db->closeStmt($resutl);
		return CITIZEN_ID_DEFAULT;
	} 
	$db->closeStmt($result);
	
	return $row['citizen_id'] + 1;
}
/**
 * ユーザーの登録
 * return	int			登録した user_id（エラーの場合、-1）
 */
function insSPUser($db, $user_name, $user_name_en, $password, $email, $uuid, $ua, $sns_kbn, $sns_id, $sex_kbn, $citizen_id) {
	$bind_param	= $db->initBindParam();
	$sql		= "INSERT INTO sp_user(user_name, user_name_en, password, new_password, email, uuid, user_agent, sns_kbn, sns_id, sex_kbn, citizen_id, del_flg, update_date) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp());";
	$db->setSql_str($sql);
	$bind_param	= $db->addBind($bind_param, "s", $user_name);
	$bind_param	= $db->addBind($bind_param, "s", $user_name_en);
	$bind_param	= $db->addBind($bind_param, "s", getEncryptedPassword($password));
    $bind_param	= $db->addBind($bind_param, "s", getEncryptedPassword($password));
	$bind_param	= $db->addBind($bind_param, "s", $email);
    $bind_param	= $db->addBind($bind_param, "s", $uuid);
    $bind_param	= $db->addBind($bind_param, "i", $ua);
	$bind_param	= $db->addBind($bind_param, "s", $sns_kbn);
	$bind_param	= $db->addBind($bind_param, "s", $sns_id);
	$bind_param	= $db->addBind($bind_param, "s", $sex_kbn);
	$bind_param	= $db->addBind($bind_param, "s", $citizen_id);
	$bind_param	= $db->addBind($bind_param, "s", DEL_FLG_NODELETE);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
	
	if ($count <= 0) {
		$db->rollback();
		return -1;
	}
	$user_id = $db->getInsertID();
	$db->closeStmt($result);
	return $user_id;
}
/**
 * user_id から最終ログイン日時を取得
 * return	boolean		現在日のログイン履歴が存在しない場合、true
 */
function checkLoginHistory($db, $user_id) {
	$bind_param	= $db->initBindParam();
	$sql		= <<<EOT
SELECT sp_user.ID
  FROM sp_user JOIN login_history ON sp_user.ID = login_history.user_ID 
                                 AND login_history.regist_date >= CURRENT_DATE
 WHERE sp_user.ID = ?
EOT;
	$db->setSql_str($sql);
	$bind_param	= $db->addBind($bind_param, "i", $user_id);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getRows($result);
	if ($count > 0) {
		$db->closeStmt($result);
		return FALSE;
	}
	$db->closeStmt($result);
	return TRUE;
}

/**
 * ログイン履歴登録
 * return	int			登録した login_history_id（エラーの場合、-1）
 */
function insLoginHistory($db, $user_id) {
	$bind_param	= $db->initBindParam();
	$sql		= "INSERT INTO login_history(user_ID) VALUES(?);";
	$db->setSql_str($sql);
	$bind_param	= $db->addBind($bind_param, "s", $user_id);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
	if ($count <= 0) {
		$db->rollback();
		return -1;
	}
	$login_history_id = $db->getInsertID();
	$db->closeStmt($result);
	return $login_history_id;
}
?>