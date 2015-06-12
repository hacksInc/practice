<?php
// 端末情報を取得して振り分けを行うだけ
$_agent = $_SERVER['HTTP_USER_AGENT'];

$model = 'unknown';

// UAの規則性が変態的なので、AndroidかiPhoneかぐらいの括りにする
if ( strpos($_agent, 'Android') !== false ) {
	$model = 'Android';
} elseif (strpos($_agent, 'iPhone OS') !== false) {
	$model = 'iPhone';

} elseif (strpos($_agent, 'iPod') !== false) {
	$model = 'iPhone';

} elseif (strpos($_agent, 'iPad') !== false) {
	// スマホ
	$model = 'iPhone';
} elseif (strpos($_agent, 'Tizen') !== false) {
	$model = 'Tizen';
}

switch ( $model ) {
    case "Android":
//        echo "Android";
        header( "Location: https://play.google.com/store/apps/details?id=jp.co.cave.PSYCHOPASS.PORTAL" );
        break;
    
    case "iPhone":
//        echo "iPhone";
        header( "Location: https://itunes.apple.com/jp/app/psycho-pass-gong-shiapuri/id932269751?mt=8" );
        break;
        
    default:
        echo "非対応機種";
        break;
}
?>