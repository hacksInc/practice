<?php
//	ログインURL
define("LOGIN_URL",	"1");

//	メールアドレス
//define("FROM_MAIL_ADDRESS",	"noreply@psychopass_join.com");
define("FROM_MAIL_ADDRESS",	"root@ptpyco.cave.co.jp");

//	暗号化キー
define("ENC_KEY",	"AKHGHIFODVAWWQGEPYWQ");

// MCRYPTエンコードキー
define( "CRYPT_KEY",    "psurmq01kr87fsno");

// アプリバージョン
define( "APP_VER", 100 );
define( "APP_VER_IOS", 100 );
define( "APP_VER_ANDROID", 10009 );

// リソースバージョン
define( "RSC_VER", 100 );
// メンテナンス中フラグ
define( "MAINTENANCE", 0 );
define( "WEB_MAINTENANCE", 0 );

// サーバードメイン
define( "SERVER_DOMAIN", "dev.psycho-pass.cave.co.jp" );

/***************************************************
		定数（区分、フラグなど）
***************************************************/
//	SNS区分
define("SNS_KBN_EMAIL"						, "1");
define("SNS_KBN_TWITTER"					, "2");
define("SNS_KBN_FACEBOOK"					, "3");
//	男女区分
define("SEX_KBN_MALE"						, "1");
define("SEX_KBN_FEMALE"						, "2");
//	削除フラグ
define("DEL_FLG_NODELETE"					, "0");
define("DEL_FLG_DELETE"						, "1");
//	選択中テーマフラグ
define("CURRENT_FLG_NOCURRENT"				, "0");
define("CURRENT_FLG_CURRENT"				, "1");
// 表示フラグ（？）
define("DISP_FLG_DISP"				        , "1");
//	ロックフラグ
define("LOCK_FLG_UNLOCK"					, "0");
define("LOCK_FLG_LOCK"						, "1");
//	ポイント計算区分
define("CALK_KBN_IN"						, "1");
define("CALK_KBN_OUT"						, "2");
//	ポイント区分
define("POINT_KBN_LOGIN"					, "1");
define("POINT_KBN_NEWS"						, "2");
define("POINT_KBN_GET_THEME"				, "3");

define("NEWS_KBN_TOPIC"						, "1");
define("NEWS_KBN_NEWS"						, "2");

/***************************************************
		定数（デフォルト値など）
***************************************************/
//	セッションエラー
define("SESSION_ERR"						, "90");
//	市民ID のデフォルト値
//define("CITIZEN_ID_DEFAULT"					, "394111060");
define("CITIZEN_ID_DEFAULT"					, 315647592);
//	コードネームのデフォルト値（固定値）
define("CODE_NAME_DEFAULT"					, "COMMISA");
//	男女区分による、テーマID のデフォルト値
define("THEME_ID_DEFAULT_MALE"				, "1");
define("THEME_ID_DEFAULT_FEMALE"			, "2");
//	ログイン時の取得ポイント
define("LOGIN_POINT"						, 10);
//	新規ニュース取得時の取得ポイント
define("GET_NEWS_POINT"						, 5);
//	テーマ取得時必要ポイント
define("GET_THEME_REQUIRE_POINT"			, 200);
//	次回テーマ取得時必要ポイントの基本数
define("NEXT_REQUIRE_POINT_BASE"			, 500);
?>