<?php
/**
 * クライアント側アプリ用APIへのアクセスのみ許可するエントリポイント 
 * 
 * このファイルはドキュメントルート直下に実体ファイルとして設置する事
 * （ドキュメントルート外に設置してシンボリックリンクで指す構成にはしない事）
 * @see http://www.ethna.jp/ethna-document-dev_guide-app-limitentrypoint.html
 */

$ver_border = 10010;

// かなり強引だが、バージョン情報が古い場合はすべて旧ポータルフォルダのファイルを使う
$header = getAllheaders();

// X-Psycho-Appverがあった場合（API用）
if ( ( isset( $header['X-Psycho-Appver'] ) && $header['X-Psycho-Appver'] <= $ver_border ) || 
	( isset( $header['x-psycho-appver'] ) && $header['x-psycho-appver'] <= $ver_border ) ) {
	$action = $_REQUEST['_action'];
	
	require_once dirname(__FILE__) . "/../" . $action;
	exit;
}

// x-cave-appverもしくはX-Jugmon-Appverがあればjson_decodeしてチェック（WebView用）
if ( isset( $header['x-cave-appver'] ) ) { // まずはx-cave-appverを取得する
    $header['x-param'] = $header['x-cave-appver'];
} elseif ( isset( $header['X-Jugmon-Appver'] ) ) { // ない場合はX-Jugmon-Appver
    $header['x-param'] = $header['X-Jugmon-Appver'];
} elseif ( isset( $header['x-jugmon-appver'] ) ) { // なぜか全部小文字のケースもある
    $header['x-param'] = $header['x-jugmon-appver'];
}

if ( isset( $header['x-param'] ) ) {
//	$header['x-param'] = json_decode( $header['x-param'], 1 );
	
	if ( $header['x-param'] <= $ver_border ) {
		$action = $_REQUEST['_action'];
//error_log( print_r( $_REQUEST ) );
		require_once dirname(__FILE__) . "/../" . $action;
		exit;
	}
}

// entry-ini.php は予め管理画面(admin_program_entry_ini_update_input)から作成しておくこと
require_once 'entry-ini.php';

// Ethnaコントローラをロードする
function pp_load_controller()
{
	// HTTPリクエストヘッダからクライアントバージョンを取得
	$headers = getallheaders();
	if (isset($headers['x-psycho-appver']) && 
		preg_match("/^[0-9]{1,10}$/", $headers['x-psycho-appver']) // 整数値かチェック
	) {
//		error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . $headers['X-Jugmon-Appver']);
		$client_ver = $headers['x-psycho-appver'];
	} else {
		// クライアントバージョンが通知されなかった場合は現行バージョンとして扱う
		$client_ver = PP_CURRENT_VER;
	}

	if (PP_CURRENT_VER >= $client_ver) { // クライアントバージョンが現行バージョン以下の場合
		$dir = 'main';
	} else if (PP_REVIEW_VER && (PP_REVIEW_VER >= $client_ver)) { // レビューバージョンが現在有効で、クライアントバージョンがレビューバージョン以下の場合
		$dir = 'review';
	} else {
		header('HTTP/1.0 403 Forbidden');
		exit;
	}
	
	error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . $dir . "(client_ver:" . $client_ver . ", action:" . $_REQUEST['_action'] . ")");

	// 選択されたバージョンのコントローラを読み込む
	require_once dirname(__FILE__) . '/../../ethna/' . $dir . '/app/Pp_PortalController.php';
}

pp_load_controller();

Pp_PortalController::main('Pp_PortalController', array( 'portal_*' ), 'undef');
?>