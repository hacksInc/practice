<?php
require_once dirname(__FILE__) . "/sub/sub.php";
require_once dirname(__FILE__) . "/lib/user.php";
require_once dirname(__FILE__) . "/lib/theme.php";
require_once dirname(__FILE__) . "/sub/status_code.php";

// HTTPボディの取得
$c = json_decode( stripslashes( $_POST['c'] ), 1 );

$user_id  = $c['id'];
$password = $c['pass'];
$uuid     = $c['uuid'];
$ua       = $c['ua'];

//	データベースアクセス部品の初期化
$db = new db();
    
//	データベースにコネクト
$db->connectDB();

// ユーザー情報を検索
$user = getOldUser( $db, $user_id, $password );

if ( empty( $user ) ) {
    $code = STATUS_DETAIL_CODE_USER_INFO_NO_MATCH;
    $response = array(
        "status_detail_code" => STATUS_DETAIL_CODE_USER_INFO_NO_MATCH,
    );
} else {
    // パスワードを生成
    $new_password = substr( md5( uniqid( rand(), 1 ) ), 0, 12 );
    
    if ( !convertOldUser( $db, $new_password, $uuid, $user_id, $password, $ua ) ) {
        $code = STATUS_DETAIL_CODE_USER_INFO_NO_MATCH;
        $response = array(
            "status_detail_code" => STATUS_DETAIL_CODE_USER_INFO_NO_MATCH,
        );
    } else {
        $db->commit();
        
        $theme_id = getCurrentThemeId( $db, $user['ID'] );
        
        $code = STATUS_DETAIL_CODE_SUCCESS;
        $response = array(
            "status_detail_code" => STATUS_DETAIL_CODE_SUCCESS,
            "id"                 => intval( $user['ID'] ),
            "pass"               => $new_password,
            "theme_id"           => $theme_id,
        );
    }
}
$db->closeDB();

sendJson( $code, $response );
?>