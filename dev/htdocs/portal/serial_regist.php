<?php
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/user.php';
require_once dirname(__FILE__) . '/api/lib/serial.php';

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

// QRコードに含まれるクエリストリングを取得
$sc = $_GET['sc'];

// 英数字以外が含まれていたらNG
if ( !preg_match( '/^[a-zA-Z0-9]+$/', $sc ) ) {
    $error_string = "シリアルコードに不正な文字が含まれています。";
    require_once dirname(__FILE__) . "/template/error.html";
    exit;
}

// 大文字小文字を問わない（DBにはすべて小文字のデータが入っている）
$sc = strtolower( $sc );

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

// マスターからコードの情報を取得
$serial = getSerialMasterByCode( $db, $sc );

// コード情報が存在しなければエラー
if ( !$serial ) {
    $db->closeDB();
    $error_string = "不正なコードです。";
    require_once dirname(__FILE__) . "/template/error.html";
    exit;
}

// 期限が切れているコードはNG
if ( date( "Y-m-d H:i:s" ) < $serial['date_open'] || $serial['date_close'] < date( "Y-m-d H:i:s" ) ) {
    $db->closeDB();
    $error_string = "コードの有効期間外です。";
    require_once dirname(__FILE__) . "/template/error.html";
    exit;
}

// ユニーク性のあるコードで、既に他のユーザーが入力していたらエラー
if ( $serial['is_unique'] == 1 ) {
    if ( isUsedEventSerial( $db, $serial['serial_id'] ) ) {
        $db->closeDB();
        $error_string = "このコードは既に使用されています。";
        require_once dirname(__FILE__) . "/template/error.html";
        exit;
    }
}

// 入力済みのコードは、登録処理をスキップ
if ( issetEventSerial( $db, $user_id, $serial['serial_id'] ) ) {
    $is_used = true;
} else {
    $is_used = false;
    
    if ( !setEventSerial( $db, $user_id, $serial['serial_id'] ) ) {
        $db->closeDB();
        require_once dirname(__FILE__) . "/template/error.html";
        exit;
    }
    
    $db->commit();
}

//	会員情報を取得
$user_info = getUserInfo( $db, $user_id );

$db->closeDB();

require_once dirname(__FILE__) . "/template/serial_regist.html";
?>