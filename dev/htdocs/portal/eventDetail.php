<?php
/***************************************************
		home.php, ホーム画面処理
		create: 2014-10-19
		update: 
***************************************************/
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/user.php';
require_once dirname(__FILE__) . '/api/lib/serial.php';

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

//	パラメータチェック
if ( empty( $user_id ) ) {
    require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

$serial_id = @intval( $_GET['serial_id'] );

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

//	POST された id から、会員情報を取得
$user_info = getUserInfo( $db, $user_id );

// 開催中のイベント情報を取得
// とりあえずサイコパスる冬のみ
$event = getSerialMaster( $db, $serial_id );

$event['description'] = html_entity_decode( $event['description'] );

// DB接続終了
$db->closeDB();

require_once dirname(__FILE__) . "/template/eventDetail.html"
?>