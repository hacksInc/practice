<?php
/***************************************************
		home.php, ホーム画面処理
		create: 2014-10-19
		update: 
***************************************************/
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/user.php';
require_once dirname(__FILE__) . '/api/lib/news.php';

if ( WEB_MAINTENANCE == 1 ) {
    require_once ( dirname(__FILE__) . '/template/maintenance.html');
    exit;
}

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

error_log( $user_id );

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

//	Twitter 取得
$twitter_txt = getTwitterTmp( $db );

// News Topic 取得
$news_topic= getNewsTopic( $db );

// ログイン履歴チェック
$point_get = 0;
if ( checkLoginHistory( $db, $user_id ) ) {
    synchronizedSPUser( $db, $user_id );
    
	// ログイン履歴に登録
	if (insLoginHistory($db, $user_id) < 0) {
		$db->rollback();
		$db->closeDB();
        require_once dirname(__FILE__) . "/template/error.html";
		exit;
	}
	
	//	ポイント付与
	if (insUserPoint($db, $user_id, POINT_KBN_LOGIN) < 0) {
		$db->rollback();
		$db->closeDB();
		require_once dirname(__FILE__) . "/template/error.html";
		exit;
	}
	
	//	コミット
	$db->commit();
    
    $point_get = 1;
	
	$date = date( "Y-m-d H:i:s" );
	if ( "2015-03-10 00:00:00" <= $date && $date < "2015-03-14 00:00:00" ) {
		$point = 100;
	} else {
    	$point = LOGIN_POINT;
	}
}

//	POST された id から、会員情報を取得
$user_info = getUserInfo( $db, $user_id );

// DB接続終了
$db->closeDB();

require_once dirname(__FILE__) . "/template/index.html";
?>