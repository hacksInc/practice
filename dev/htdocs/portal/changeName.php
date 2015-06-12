<?php
// 共通関数群を読み込む
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . "/api/lib/user.php";
require_once dirname(__FILE__) . '/api/sub/status_code.php';

if ( WEB_MAINTENANCE == 1 ) {
    require_once ( dirname(__FILE__) . '/template/maintenance.html');
    exit;
}

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

$db->closeDB();

header( "HTTP/1.0 200 OK" );
require_once dirname(__FILE__) . "/template/changeName.html";
?>