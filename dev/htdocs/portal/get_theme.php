<?php
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/user.php';
require_once dirname(__FILE__) . '/api/lib/theme.php';

/***************************************************
		引数
***************************************************/
$theme_id = isset($_GET['theme_id']) ? $_GET['theme_id'] : null;

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

//	パラメータチェック
if ( empty($user_id) || empty($theme_id) ) {
    // エラーページ入れよう
    require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

//	排他制御
synchronizedSPUser( $db, $user_id );

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
    require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

// 既に獲得済みならエラー
if ( $theme['lock_flg'] == LOCK_FLG_UNLOCK ) {
	$db->closeDB();
    require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

//	ポイントのチェック
$point = getPointBalance( $db, $user_id );
if ( $point < 0 ) {
	$db->closeDB();
    require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

if ( $point < $theme['use_point'] ) {
	$db->closeDB();
	require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

//	ポイントの使用
if ( insUserPoint_cave( $db, $user_id, CALK_KBN_OUT, $theme['use_point'] * -1 ) === false ) {
	$db->rollback();
	$db->closeDB();
	require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

//	ユーザーテーマの登録
if ( insUserTheme( $db, $user_id, $theme_id ) <= 0 ) {
	$db->rollback();
	$db->closeDB();
	require_once dirname(__FILE__) . "/template/error.html";
	exit;
}

//	コミット
$db->commit();

//	POST された id から、会員情報を取得
$user_info = getUserInfo( $db, $user_id );

$db->closeDB();

require_once dirname(__FILE__) . "/template/get_theme.html";
?>