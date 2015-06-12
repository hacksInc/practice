<?php
require_once dirname(__FILE__) . '/../sub/sub.php';

/**
 * 選択中のテーマIDを取得
 */
function getCurrentThemeId ( $db, $user_id )
{
    $bind_param	= $db->initBindParam();
    
    $sql = "SELECT theme_id FROM user_theme WHERE user_ID = ? AND current_flg = ?;";
    $db->setSql_str($sql);
    $bind_param	= $db->addBind($bind_param, "s", $user_id);
    $bind_param	= $db->addBind($bind_param, "s", CURRENT_FLG_CURRENT);
    $result		= $db->exeQuery($bind_param);
    
    $row = $db->exeFetch($result);
    
    if ( is_null( $row ) ) return -1;
    
    $db->closeStmt($result);
    
    return intval( $row['theme_id'] );
}

/**
 * ユーザーのテーマ情報の取得
 */
function getThemeInfo ( $db, $user_id )
{
    $bind_param	= $db->initBindParam();
    
    $sql = "SELECT theme.ID AS theme_id, theme.theme_name, theme.use_point, 
            CASE WHEN user_theme.theme_id IS NOT NULL THEN ? ELSE ? END AS lock_flg, 
            COALESCE(current_flg, ?) AS selected_flg
            FROM theme 
            JOIN sp_user ON sp_user.ID = ? 
            LEFT JOIN user_theme ON theme.ID = user_theme.theme_id AND sp_user.ID = user_theme.user_ID;";
    $db->setSql_str($sql);
    $bind_param	= $db->addBind($bind_param, "s", LOCK_FLG_UNLOCK);
    $bind_param	= $db->addBind($bind_param, "s", LOCK_FLG_LOCK);
    $bind_param	= $db->addBind($bind_param, "s", CURRENT_FLG_NOCURRENT);
    $bind_param	= $db->addBind($bind_param, "s", $user_id);
    $result		= $db->exeQuery($bind_param);
    $count		= $db->getRows($result);
    if ( $count > 0 ) {
        $theme_data = array();
        while ($row = $db->exeFetch($result)) {
            array_push($theme_data, $row);
        }
    }
    
    $db->closeStmt($result);
    
    return $theme_data;
}

/**
 * ユーザーテーマの登録
 * return	int			登録した user_theme_id（エラーの場合、-1）
 */
function insUserTheme($db, $user_id, $theme_id,$current=CURRENT_FLG_NOCURRENT) {
	$bind_param	= $db->initBindParam();
	$sql		= "INSERT INTO user_theme(user_ID, theme_id, current_flg) VALUES(?, ?, ?);";
	$db->setSql_str($sql);
	$bind_param = $db->addBind($bind_param, "i", $user_id);
	$bind_param = $db->addBind($bind_param, "i", $theme_id);
	$bind_param = $db->addBind($bind_param, "s", $current);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
	if ($count <= 0) {
		$db->rollback();
		return -1;
	}
	$user_theme_id = $db->getInsertID();
	$db->closeStmt($result);
	return $user_theme_id;
}

/**
 * 既存の選択中のテーマを、非選択状態に更新
 * return	int		更新行数
 */
function updOldSelectedUserTheme($db, $user_id, $theme_id) {
	$bind_param	= $db->initBindParam();
	$sql		= <<<EOT
UPDATE user_theme SET current_flg = ? WHERE user_ID = ? AND theme_id != ? AND current_flg = ?;
EOT;
	$db->setSql_str($sql);
	$bind_param	= $db->addBind($bind_param, "s", CURRENT_FLG_NOCURRENT);
	$bind_param	= $db->addBind($bind_param, "i", $user_id);
	$bind_param	= $db->addBind($bind_param, "i", $theme_id);
	$bind_param	= $db->addBind($bind_param, "s", CURRENT_FLG_CURRENT);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
	$db->closeStmt($result);
	return $count;
}
/**
 * 選択したテーマを、選択中状態に更新
 * return	int		更新行数
 */
function updNewSelectedUserTheme($db, $user_id, $theme_id) {
	$bind_param	= $db->initBindParam();
	$sql		= <<<EOT
UPDATE user_theme SET current_flg = ? WHERE user_ID = ? AND theme_id = ? AND current_flg = ?;
EOT;
	$db->setSql_str($sql);
	$bind_param	= $db->addBind($bind_param, "s", CURRENT_FLG_CURRENT);
	$bind_param	= $db->addBind($bind_param, "i", $user_id);
	$bind_param	= $db->addBind($bind_param, "i", $theme_id);
	$bind_param	= $db->addBind($bind_param, "s", CURRENT_FLG_NOCURRENT);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
	$db->closeStmt($result);
	return $count;
}
?>
