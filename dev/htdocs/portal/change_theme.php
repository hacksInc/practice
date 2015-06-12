<?php
// 共通関数群を読み込む
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . "/api/lib/user.php";
require_once dirname(__FILE__) . "/api/lib/theme.php";

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

$theme_id = intval( $_GET['theme_id'] );

//	パラメータチェック
if ( empty($user_id) || empty($theme_id) ) {
    require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

// テーマ情報を取得
$themeArray = getThemeInfo( $db, $user_id );

// 該当のテーマ情報を取得
// assoc形式で取れればこんなんやらずに済むんだがな……
$hit = false;
foreach ( $themeArray as $theme ) {
    if ( $theme['theme_id'] == $theme_id ) {
        $hit = true;
        break;
    }
}

// 該当しないテーマIDはエラー
if ( !$hit ) {
	$db->closeDB();
    header( "HTTP/1.0 200 OK" );
    exit;
}

// 獲得してないテーマはエラー
if ( $theme['lock_flg'] == LOCK_FLG_LOCK ) {
	$db->closeDB();
    header( "HTTP/1.0 200 OK" );
    exit;
}

//	既存の選択中テーマを、非選択に更新
if ( updOldSelectedUserTheme($db, $user_id, $theme_id) <= 0) {
	$db->rollback();
	$db->closeDB();
	header( "HTTP/1.0 200 OK" );
    exit;
}

//	ユーザーテーマの登録
if (updNewSelectedUserTheme($db, $user_id, $theme_id) <= 0) {
	$db->rollback();
	$db->closeDB();
	header( "HTTP/1.0 200 OK" );
    exit;
}

//	コミット
$db->commit();

//	POST された id から、会員情報を取得
$user_info = getUserInfo( $db, $user_id );
if ( empty( $user_info ) ) {
	$db->closeDB();
    
    header( "HTTP/1.0 404 NOT FOUND" );
	return;
}

// テーマ切り替え
$db->closeDB();

header( "HTTP/1.0 200 OK" );
require_once dirname(__FILE__) . "/template/change_theme.html"
?>