<?php
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/user.php';
require_once dirname(__FILE__) . '/api/lib/voting.php';

if ( WEB_MAINTENANCE == 1 ) {
    require_once ( dirname(__FILE__) . '/template/maintenance.html');
    exit;
}

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

// クエリ取得
$item_id = intval( $_REQUEST['item_id'] );
$point = intval( $_REQUEST['point'] );

if ( $point <= 0 ) {
	require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

//	会員情報を取得
$user_info = getUserInfo( $db, $user_id );

if ( $user_info['point'] < $point ) {
	$db->closeDB();
	require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

// 投票
if ( !execVoting( $db, $user_id, 1, $item_id, $point ) ) {
	$db->closeDB();
	require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

$db->commit();

//	会員情報を再取得（ポイント表示用）
$user_info = getUserInfo( $db, $user_id );

$m_voting = getMasterVoting( $db, 1, $item_id );

$db->closeDB();

require_once dirname(__FILE__) . "/template/execvoting.html"
?>