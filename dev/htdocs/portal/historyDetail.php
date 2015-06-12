<?php
/***************************************************
		home.php, ホーム画面処理
		create: 2014-10-19
		update: 
***************************************************/
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/user.php';
require_once dirname(__FILE__) . '/api/lib/event.php';
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

$event_id = @intval( $_GET['event_id'] );

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

//	POST された id から、会員情報を取得
$user_info = getUserInfo( $db, $user_id );

// 開催中のイベント情報を取得
// とりあえずサイコパスる冬のみ
$event = getEventMaster( $db, $event_id );

// 存在しないイベントはエラー
if ( !$event ) {
    $db->closeDB();
    require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

$event_serial = getSerialMasterListByEventId( $db, $event_id );

$serial_id_array = array();
foreach ( $event_serial as $row ) {
    $serial_id_array[] = $row['serial_id'];
}

// クリア情報を取得
$serial =  getUserEventSerialListBySerialIdArray( $db, $user_id, $serial_id_array );

// DB接続終了
$db->closeDB();

// もしクリア条件を満たしていなかったらエラー
if ( count( $serial ) < $event['serial_num'] ) {
    $error_string = "イベントクリア条件を満たしていません。";
    require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

require_once dirname(__FILE__) . "/template/historyDetail.html"
?>