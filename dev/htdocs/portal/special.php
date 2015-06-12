<?php
// 共通関数群を読み込む
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . "/api/lib/user.php";
require_once dirname(__FILE__) . "/api/lib/theme.php";
require_once dirname(__FILE__) . '/api/sub/status_code.php';

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

// 排他処理
// いらない気がする……
//synchronizedSPUser($db, $user_id);

//	POST された id から、会員情報を取得
$user_info = getUserInfo( $db, $user_id );
if ( empty( $user_info ) ) {
	$db->closeDB();
    
    require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

$theme = htmlspecialchars( json_encode( getThemeInfo( $db, $user_id ) ) );

$db->closeDB();

$next_require_pt = GET_THEME_REQUIRE_POINT;

header( "HTTP/1.0 200 OK" );
require_once dirname(__FILE__) . "/template/special.html"
?>