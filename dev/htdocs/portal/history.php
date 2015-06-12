<?php
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/user.php';
require_once dirname(__FILE__) . '/api/lib/event.php';
require_once dirname(__FILE__) . '/api/lib/serial.php';

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

//	会員情報を取得
$user_info = getUserInfo( $db, $user_id );

// 今はサイコパスる冬のみに特化
$event = getEventMasterListAll( $db );

// serial_numが0のもの（シリアルが不要なもの）はそもそも載せない
$event_id_array = array();
foreach ( $event as $event_id => $row ) {
    if ( $row['serial_num'] == 0 ) {
        unset( $event[$event_id] );
    } else {
        $event_id_array[] = $event_id;
    }
}

$event_serial = getSerialMasterListByEventIdArray( $db, $event_id_array );

$serial_id_array = array();
foreach ( $event_serial as $row ) {
    $serial_id_array[] = $row['serial_id'];
}

// クリア情報を取得
$user_serial = getUserEventSerialListBySerialIdArray( $db, $user_id, $serial_id_array );

// DB接続終了
$db->closeDB();

// クリア状態を計算
// serial_numからクリア数を引いて、0以下になればクリア（普通は0で止まるけど念のため）
$clear = 0;
foreach ( $user_serial as $row ) {
    $event[$event_serial[$row['serial_id']]['event_id']]['serial_num']--;
    
    if ( !isset( $event[$event_serial[$row['serial_id']]['event_id']]['date_clear'] ) ||
         $event[$event_serial[$row['serial_id']]['event_id']]['date_clear'] < $row['date_created']
    ) $event[$event_serial[$row['serial_id']]['event_id']]['date_clear'] = $row['date_created'];
    
    if ( $event[$event_serial[$row['serial_id']]['event_id']]['serial_num'] <= 0 ) $clear = 1;
}

require_once dirname(__FILE__) . "/template/history.html";
?>