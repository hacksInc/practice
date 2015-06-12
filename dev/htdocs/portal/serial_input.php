<?php
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/user.php';
require_once dirname(__FILE__) . '/api/lib/serial.php';

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

// イベントIDを取得
$serial_id = @intval( $_GET['serial_id'] );

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

// イベント情報を取得（なければエラー）
$event = getSerialMaster( $db, $serial_id );

if ( !$event ) {
    $db->closeDB();
    $error_string = "イベントIDが不正です。";
    require_once dirname(__FILE__) . "/template/error.html";
    exit;
}

//	会員情報を取得
$user_info = getUserInfo( $db, $user_id );

$db->closeDB();

// キャッシュ制御のテスト
header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );
header( 'Last-Modified: '.gmdate( 'D, d M Y H:i:s' ).' GMT' );

// HTTP/1.1
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', FALSE );

// HTTP/1.0
header( 'Pragma: no-cache' );
require_once dirname(__FILE__) . "/template/serial_input.html";
?>