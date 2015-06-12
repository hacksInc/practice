<?php
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/news.php';
require_once dirname(__FILE__) . '/api/lib/user.php';

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

// ニュースを取得
$news = getNews( $db, $user_id, 0, 20 );

//	会員情報を取得
$user_info = getUserInfo( $db, $user_id );

$db->closeDB();

require_once dirname(__FILE__) . "/template/news.html";
?>