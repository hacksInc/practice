<?php
require_once dirname(__FILE__) . "/sub/sub.php";
require_once dirname(__FILE__) . "/lib/user.php";
require_once dirname(__FILE__) . "/sub/status_code.php";

// KPI
//require_once($_SERVER["DOCUMENT_ROOT"]."/kpi_tool/kpi_config.php");
//kpi_set("Google-ppp-install",2,1,"","","","","");

// HTTPボディの取得
$c = json_decode( stripcslashes( $_POST['c'] ), 1 );

$app_ver = $c['appver'];
$user_id = $c['id'];
$password = $c['pass'];

if ( $app_ver < APP_VER ) {// アプリバージョンチェック
	$code = STATUS_DETAIL_CODE_APP_VER_NO_MATCH;
	$response = array(
		"status_detail_code" => STATUS_DETAIL_CODE_APP_VER_NO_MATCH,
	);
} elseif ( MAINTENANCE == 1 ) {    // メンテナンス中
    $code = STATUS_DETAIL_CODE_MAINTENANCE;
    $response = array(
        "status_detail_code" => STATUS_DETAIL_CODE_MAINTENANCE,
    );
} else {
    $code = STATUS_DETAIL_CODE_SUCCESS;
    $response = array(
        "status_detail_code" => STATUS_DETAIL_CODE_SUCCESS,
    );
}

// KPI取得
//kpi_set( "Apple-ppp-dau", 3, 1, time(), $user_id, "", "", "" );

if ( $user_id != "" && $password != "" ) {
    //	データベースアクセス部品の初期化
    $db = new db();

    //	データベースにコネクト
    $db->connectDB();

    //	POST された id から、会員情報を取得
    $user_info = getUserInfo( $db, $user_id );
    
	// アプリバージョンチェック（UAごと）
	switch ( $user_info['user_agent'] ) {
		case 1;
			if ( $app_ver < APP_VER_IOS ) {    // アプリバージョンチェック
				$code = STATUS_DETAIL_CODE_APP_VER_NO_MATCH;
				$response = array(
					"status_detail_code" => STATUS_DETAIL_CODE_APP_VER_NO_MATCH,
				);
			}
			break;
			
		default;
			if ( $app_ver < APP_VER_ANDROID ) {    // アプリバージョンチェック
				$code = STATUS_DETAIL_CODE_APP_VER_NO_MATCH;
				$response = array(
					"status_detail_code" => STATUS_DETAIL_CODE_APP_VER_NO_MATCH,
				);
			}
			break;
	}
	
	if ( $code == STATUS_DETAIL_CODE_SUCCESS ) {
    	$response['theme_id'] = intval( $user_info['theme_id'] );
	}
} else {
    // テーマIDを含めて返す。とりあえず-1
    $response['theme_id'] = -1;
}

sendJson( $code, $response );
?>