<?php
/***************************************************
		sub.php, 共通部品
		create: 2014-10-15, Y.Sai
		update:
***************************************************/
require_once( dirname(__FILE__) . '/_json_common.php');
require_once( dirname(__FILE__) . '/const.php');
require_once( dirname(__FILE__) . '/../../class/db.php');
require_once( dirname(__FILE__) . '/status_code.php');

/////////////////////////
// ヘッダー情報取得
/////////////////////////
$header = getAllHeaders();

// .htaccessによるbasic認証がなくなったので、PHP側で制御する
if ( !isset( $header['Authorization'] ) && !isset( $header['authorization'] ) ) {
error_log( "auth:no auth header" );
	header( "HTTP/1.0 401 Unauthorized" );
	exit;
} else {
	if ( isset( $header['Authorization'] ) ) {
		list($__user, $__password) = explode(':', 
			base64_decode( substr( $header['Authorization'], 6 ) ), 2 // 6 == strlen('Basic ')
		);
	} elseif ( isset( $header['authorization'] ) ) {
		list($__user, $__password) = explode(':', 
			base64_decode( substr( $header['authorization'], 6 ) ), 2 // 6 == strlen('Basic ')
		);
	}
	
	if ( $__user != 'psyapp' || $__password != 'um5cynpH7aff9eynsh4O' ) {
error_log( "auth:" . $__user . ":" . $__password );
		header( "HTTP/1.0 401 Unauthorized" );
		exit;
	}
}

if ( isset( $header['x-cave-appver'] ) ) { // まずはx-cave-appverを取得する
    $header['x-param'] = $header['x-cave-appver'];
} elseif ( isset( $header['X-Jugmon-Appver'] ) ) { // ない場合はX-Jugmon-Appver
    $header['x-param'] = $header['X-Jugmon-Appver'];
} elseif ( isset( $header['x-jugmon-appver'] ) ) { // なぜか全部小文字のケースもある
    $header['x-param'] = $header['x-jugmon-appver'];
}

$header['x-param'] = json_decode( $header['x-param'], 1 );
$header['UserId'] = $header['x-param']['user_id'];
$header['Appver'] = $header['x-param']['app_ver'];

// もし$_GET['ustr']が存在したら、そちらで置き換える。
// セキュリティ的にはアレだが、せめてエンコードをかけておく
if ( strlen( $_GET['ustr'] ) > 0 ) {
    $ustr = cryptDecode( $_GET['ustr'] );
    
//    error_log( print_r( $ustr, 1 ) );
    
    if ( isset( $ustr['user_id'] ) ) {
        $header['UserId'] = $ustr['user_id'];
    }
}

$header['UserIdEncode'] = cryptEncode(
    array(
        "user_id" => $header['UserId'],
    )
);

//error_log( print_r( $header, 1 ) );

// セッションの有効期限を設定
// とりあえず四日
//ini_set( 'session.gc_maxlifetime', 60 * 60 * 24 * 4 );

/***************************************************
	セッションスタートとユーザーID チェック
***************************************************/
//	セッションスタート
/*session_start();

$need_session_check = TRUE;
if (!empty($login)) {
	$need_session_check = FALSE;
}

error_log( print_r( $_SESSION, 1 ) );

//	login.php 以外から呼ばれており、かつセッションにユーザーID がなければ、終了
if ($need_session_check && !isset($_SESSION['user_id'])) {
	$json = array('status'=>SESSION_ERR);
	echoJson($json);
	exit;
}*/

/***************************************************
		パスワードの暗号化関数
***************************************************/
/**
 * password の暗号化
 * return	string		生成した password
 */
function getEncryptedPassword($password) {
	$key = md5(ENC_KEY);
	$td  = mcrypt_module_open('des', '', 'ecb', '');
	$key = substr($key, 0, mcrypt_enc_get_key_size($td));
	$iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	if (mcrypt_generic_init($td, $key, $iv) < 0) {
		$msg = "エラーが発生しました";
	}
	$crypt_pass = base64_encode(mcrypt_generic($td, $password));
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);

	return $crypt_pass;
}

/***************************************************
		セッシsョンに関連する関数
***************************************************/
/**
 * セッションへのユーザーID の登録
 */
function setSessionUserID($user_id) {
	session_regenerate_id();
	$_SESSION['user_id'] = $user_id;
	return;
}
/**
 * セッションからのユーザーID の取得
 * return	string		user_id
 */
function getSessionUserID() {
	if (!isset($_SESSION['user_id'])) {
		return null;
	}
	return $_SESSION['user_id'];
}
/**
 * セッションからのユーザーID の破棄
 */
function clearSessionUserID() {
	//	セッション変数のクリア
	$_SESSION = array();
	
	//	クッキーの破棄
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
	
	//	セッションクリア
	session_destroy();
}

/***************************************************
		ポイントに関連する関数
***************************************************/
/**
 * 排他制御
 */
function synchronizedSPUser($db, $user_id) {
	$bind_param	= $db->initBindParam();
	$sql		= <<<EOT
SELECT ID
  FROM sp_user
 WHERE ID = ?
   FOR UPDATE;
EOT;
	$db->setSql_str($sql);
	$bind_param	= $db->addBind($bind_param, "i", $user_id);
	$result		= $db->exeQuery($bind_param);
	$db->closeStmt($result);
	return;
}
/**
 * ポイント残高の取得
 * return	int		ポイント残高（取得できなかった場合、-1）
 */
function getPointBalance($db, $user_id) {
	$bind_param	= $db->initBindParam();
	$sql		= <<<EOT
SELECT user_ID, COALESCE(SUM(point), 0) AS point
  FROM sp_user LEFT JOIN user_point ON sp_user.ID = user_point.user_ID
 WHERE sp_user.ID = ?
 GROUP BY 1;
EOT;
	$db->setSql_str($sql);
	$bind_param	= $db->addBind($bind_param, "i", $user_id);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getRows($result);
	if ($count <= 0) {
		$db->closeStmt($result);
		return -1;
	}
	$row = $db->exeFetch($result);
	$db->closeStmt($result);
	return $row['point'];
}
/**
 * ポイントの使用
 * return	int			登録した user_point_id（エラーの場合、-1）
 */
function insUserPoint($db, $user_id, $point_kb) {
	//	ポイントの算出
	$calc_kbn	= CALK_KBN_IN;
	$point	= 0;
	if ($point_kb == POINT_KBN_LOGIN) {
		$calc_kbn	= CALK_KBN_IN;

// 20150310～0313まで、ログインは100pt
		$date = date( "Y-m-d H:i:s" );
		if ( "2015-03-10 00:00:00" <= $date && $date < "2015-03-14 00:00:00" ) {
			$point = 100;
		} else {
			$point = LOGIN_POINT;
		}
	} else if ($point_kb == POINT_KBN_NEWS) {
		$calc_kbn	= CALK_KBN_IN;
		$point = GET_NEWS_POINT;
	} else if ($point_kb == POINT_KBN_GET_THEME) {
		$calc_kbn	= CALK_KBN_OUT;
		$point = GET_THEME_REQUIRE_POINT * -1;
	} else {
		return -1;
	}
	
	//	ユーザーポイントの登録
	$bind_param	= $db->initBindParam();
	$sql		= "INSERT INTO user_point(user_ID, calc_kbn, point) VALUES(?, ?, ?);";
	$db->setSql_str($sql);
	$bind_param = $db->addBind($bind_param, "i", $user_id);
	$bind_param = $db->addBind($bind_param, "s", $calc_kbn);
	$bind_param = $db->addBind($bind_param, "i", $point);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
	
	if ($count <= 0) {
		$db->rollback();
		return -1;
	}

	
	$user_point_id = $db->getInsertID();
	$db->closeStmt($result);
	return $user_point_id;
}

/**
 * ポイントの使用。自社開発版。
 * return	int			登録した user_point_id（エラーの場合false。===で比較すること）
 */
function insUserPoint_cave ( $db, $user_id, $calc_kbn, $point )
{
	//	ユーザーポイントの登録
	$bind_param	= $db->initBindParam();
	$sql		= "INSERT INTO user_point(user_ID, calc_kbn, point) VALUES(?, ?, ?);";
	$db->setSql_str($sql);
	$bind_param = $db->addBind($bind_param, "i", $user_id);
	$bind_param = $db->addBind($bind_param, "s", $calc_kbn);
	$bind_param = $db->addBind($bind_param, "i", $point);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
	
	if ($count <= 0) {
		$db->rollback();
		return false;
	}

	
	$user_point_id = $db->getInsertID();
	$db->closeStmt($result);
	return $user_point_id;
}

function echoJson($json) {
	echo $_GET['callback'] . "(" . json_encode($json). ")";
}

/**
 * HTTPステータスコードと共にエンコードしたボディを返す
 */
function sendJson ( $code, $response )
{
    switch ( $code ) {
        default:
            header( "HTTP/1.0 200 OK" );
            break;
    }
    
    $response['appver']      = APP_VER;
    $response['rscver']      = RSC_VER;
    $response['maintenance'] = MAINTENANCE;
    
    echo json_encode( $response );
}

/**
 *  復号
 *
 *  @access public
 *	@param	string	暗号文字列
 *  @return mixed	データ
 */
function cryptDecode($str)
{
	$data	= __hex2bin(substr($str, 1));
	
	$td		= mcrypt_module_open(MCRYPT_BLOWFISH, '', 'ecb', '');
	$key	= substr(CRYPT_KEY, 0, mcrypt_enc_get_key_size($td));
	$iv		= substr($data, 0, mcrypt_enc_get_iv_size($td));
	$data	= substr($data, mcrypt_enc_get_iv_size($td));
	
	if (@mcrypt_generic_init($td, $key, $iv) != -1) {
		$c_t	= mdecrypt_generic($td, $data);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		
		return @unserialize($c_t);
	} else {
		error_log("mcrypt init failed.");
		return "";
	}
}

/**
 *  暗号化(ObjectをserializeしてBlowfishで暗号化)
 *
 *  @access public
 *	@param	mixed	元データ
 *  @return string  暗号文字列
 */
function cryptEncode($obj)
{
	$str	= serialize($obj);
	
	$td		= mcrypt_module_open(MCRYPT_BLOWFISH, '', 'ecb', '');
	$key	= substr(CRYPT_KEY, 0, mcrypt_enc_get_key_size($td));
	$iv		= mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	
	if (@mcrypt_generic_init($td, $key, $iv) != -1) {
		$c_t	= mcrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		
		return '_'. bin2hex($iv . $c_t);
	} else {
		error_log("mcrypt init failed.");
		return "";
	}
}

/**
 *  16進表記→バイナリ変換
 *
 *  @access public
 *	@param	string	暗号化(?)文字列
 *  @return string  キャラクタID (不正な値の場合はfalse)
 */
function __hex2bin($str)
{
	$data	= "";
	
	for ($i=0; $i<strlen($str); $i+=2) {
		$data	.= chr(hexdec(substr($str, $i, 2)));
	}
	
	return $data;
}
?>