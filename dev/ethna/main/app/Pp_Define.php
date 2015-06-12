<?php
/**
 *  Pp_Define.php
 *
 *  @package   Pp
 *
 *  $Id$
 */

// アプリで定義する定数（エラーコード除く）
define("CITIZEN_ID_DEFAULT"					, 315647592);//	市民ID のデフォルト値
define("CODE_NAME_DEFAULT"					, "COMMISA");//	コードネームのデフォルト値（固定値）

// MCRYPTエンコードキー
define( "CRYPT_KEY",    "psurmq01kr87fsno");

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
define( "CURRENT_FLG_NOCURRENT",			0 );
define( "CURRENT_FLG_CURRENT",				1 );
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
		定数（ポータル用）
***************************************************/
define( "PORTAL_LOGIN_POINT",				10 );	// ログインポイント
define( "PORTAL_ENC_KEY",					"AKHGHIFODVAWWQGEPYWQ" );	// 暗号化キー
?>