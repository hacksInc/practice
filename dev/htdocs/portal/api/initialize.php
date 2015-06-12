<?php
require_once dirname(__FILE__) . "/sub/sub.php";
require_once dirname(__FILE__) . "/lib/user.php";
require_once dirname(__FILE__) . "/lib/theme.php";
require_once dirname(__FILE__) . "/sub/status_code.php";

// HTTPボディの取得
$c = json_decode( stripslashes( $_POST['c'] ), 1 );

$nickname = $c['nickname'];
$ruby     = $c['ruby'];
$sex      = $c['sex'];
$uuid     = $c['uuid'];
$ua       = $c['ua'];

//	パラメータチェック
if ( empty( $nickname ) || empty( $ruby ) || empty( $sex ) || empty( $uuid ) ) {
    $code = STATUS_DETAIL_CODE_USER_INFO_NO_MATCH;
    $response = array(
        "status_detail_code" => STATUS_DETAIL_CODE_USER_INFO_NO_MATCH,
    );
} else {
    // ユーザーデータ作成
    //	データベースアクセス部品の初期化
    $db = new db();

    //	データベースにコネクト
    $db->connectDB();
    
    //	ユーザーの登録
    $citizen_id = createCitizenID( $db );
    $password = substr( md5( uniqid( rand(), 1 ) ), 0, 12 );
    
    $user_id = insSPUser( $db, $nickname, $ruby, $password, null, $uuid, $ua, '1', null, $sex, $citizen_id );
    if ( $user_id < 0 ) {
    	$db->rollback();
        
        $code = STATUS_DETAIL_CODE_DB_ERROR;
        $response = array(
            "status_detail_code" => STATUS_DETAIL_CODE_DB_ERROR,
        );
    } else {
        //	ユーザーテーマの登録
        if ( insUserTheme( $db, $user_id, $sex, CURRENT_FLG_CURRENT ) < 0 ) {
            $db->rollback();
            
            $code = STATUS_DETAIL_CODE_DB_ERROR;
            $response = array(
                "status_detail_code" => STATUS_DETAIL_CODE_DB_ERROR,
            );
        } else {
            // iOSのみ、初回50pt進呈
            if ( $ua == 1 ) {
                if ( insUserPoint_cave( $db, $user_id, CALK_KBN_IN, 50 ) === false ) {
                    $db->rollback();
            
                    $code = STATUS_DETAIL_CODE_DB_ERROR;
                    $response = array(
                        "status_detail_code" => STATUS_DETAIL_CODE_DB_ERROR,
                    );
                    sendJson( $code, $response );
                    exit;
                }
            }
            
            //	コミット
            $db->commit();
            
            // theme_idは登録情報から取得
            if ( $sex == 1 ) $theme_id = 1;
            else             $theme_id = 2;
            
            $code = STATUS_DETAIL_CODE_SUCCESS;
            $response = array(
                "status_detail_code" => STATUS_DETAIL_CODE_SUCCESS,
                "id"                 => intval( $user_id ),
                "pass"               => $password,
                "theme_id"           => $theme_id,
            );
        }
    }
    
    $db->closeDB();
}

sendJson( $code, $response );
?>