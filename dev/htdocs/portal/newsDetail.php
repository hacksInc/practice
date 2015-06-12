<?php
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/news.php';
require_once dirname(__FILE__) . '/api/lib/user.php';

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

$news_id = $_GET['id'];

if ( empty( $user_id ) || empty( $news_id ) ) {
    require_once dirname(__FILE__) . "/template/error.html";
    exit;
}

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

// ニュースを取得
$news = getNewsOnce( $db, $user_id, $news_id );

// もし未読のニュースならポイント追加処理
$point_get = 0;
if ( $news['ar_flag'] == 0 ) {
    synchronizedSPUser( $db, $user_id );
    
    if ( !readNews( $db, $user_id, $news_id ) ) {
        require_once dirname(__FILE__) . "/template/error.html";
        exit;
    }
    
    $point_get = 1;
    $point = GET_NEWS_POINT;
}

//	会員情報を取得
$user_info = getUserInfo( $db, $user_id );

$db->closeDB();

// ニュース情報を解析して、指定タグをリンクに変換
// タグは暫定で{link_a url="xxx"}txt{/link_a}
$news['news_text'] = html_entity_decode( $news['news_text'] );
$pattern = '/<linka url={(.*?)}>(.*?)<\/linka>/misu';
preg_match_all( $pattern, $news['news_text'], $matches );
//error_log( mb_convert_encoding( $news['news_text'], "sjis", "utf-8") );
//error_log( html_entity_decode ( $news['news_text'] ) );
//error_log( print_r( $matches, 1 ) );

// 該当の文字列を置き換える
$replace = array();
$link = array();
$text = array();
foreach ( $matches as $key => $rows ) {
    foreach ( $rows as $key2 => $row ) {
        switch ( $key ) {
            case 0: // リンク対象文字列全体
                $replace[$key2] = $row;
                break;
                
            case 1: // URL
                $link[$key2] = $row;
                break;
                
            case 2: // 文字列
                $text[$key2] = $row;
                break;
        }
    }
}

foreach ( $replace as $key => $row ) {
    $news['news_text'] = str_replace( $row, '<a onClick="Unity.call(\'' . $link[$key] . '\');">' . $text[$key] . '</a>', $news['news_text'] );
}

require_once dirname(__FILE__) . "/template/newsDetail.html";
?>