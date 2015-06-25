<?php
require_once dirname(__FILE__) . '/../sub/sub.php';

/***************************************************
		関数
***************************************************/
/**
 * 指定のシリアルを獲得しているか
 */
function issetEventSerial ( $db, $user_id, $serial_id )
{
	$bind_param	= $db->initBindParam();
    
	$sql = "SELECT serial_id FROM ct_event_serial WHERE user_id = ? AND serial_id = ?;";
    
	$db->setSql_str( $sql );
	$bind_param	= $db->addBind( $bind_param, "i", $user_id );
    $bind_param	= $db->addBind( $bind_param, "i", $serial_id );
    
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getRows($result);
    
	if ($count > 0) {
		$db->closeStmt($result);
		return true;
	}
    
	$db->closeStmt($result);
	return false;
}

/**
 * ユニーク性のあるコードが使用されていないかチェック
 */
function isUsedEventSerial ( $db, $serial_id )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT serial FROM ct_event_serial WHERE serial_id = ?;";
    
	$db->setSql_str( $sql );
    $bind_param	= $db->addBind( $bind_param, "i", $serial_id );
    
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getRows($result);
    
	if ($count > 0) {
		$db->closeStmt($result);
		return true;
	}
    
	$db->closeStmt($result);
	return false;
}

/**
 * シリアル情報登録
 */
function setEventSerial ( $db, $user_id, $serial_id )
{
	$bind_param	= $db->initBindParam();
    
	$sql = "INSERT INTO ct_event_serial( user_id, serial_id, date_created ) VALUES( ?, ?, NOW() );";
	$db->setSql_str($sql);
    
	$bind_param	= $db->addBind($bind_param, "i", $user_id);
    $bind_param	= $db->addBind($bind_param, "i", $serial_id);
    
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
    
	if ($count <= 0) {
		$db->rollback();
		return false;
	}
	$db->closeStmt($result);
	return true;
}

/**
 * ユーザーが獲得しているシリアルコードの情報を取得
 */
function getUserEventSerialList ( $db, $user_id, $offset = 0, $limit = 10 )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM ct_event_serial WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?;";
    
	$db->setSql_str( $sql );
	$bind_param	= $db->addBind( $bind_param, "i", $user_id );
    $bind_param	= $db->addBind( $bind_param, "i", $offset );
    $bind_param	= $db->addBind( $bind_param, "i", $limit );
    
	$result		= $db->exeQuery($bind_param);
    
    // 全件取得
    // あとでadodbに変えよう……
    $list = array();
    while ( $row = $db->exeFetch( $result ) ) {
    	$list[$row['serial_id']] = $row;
    }
    
	$db->closeStmt($result);
	return $list;
}

/**
 * ユーザーが入力済みの、指定したシリアルIDのリストを取得
 */
function getUserEventSerialListBySerialIdArray ( $db, $user_id, $serial_id_array )
{
    $bind_param	= $db->initBindParam();
    
    $bind_param	= $db->addBind( $bind_param, "i", $user_id );
    
    // bind_param用に数を調整
    $bind_target = "";
    foreach ( $serial_id_array as $row ) {
        $bind_param	= $db->addBind( $bind_param, "i", $row );
        $bind_target .= "?,";
    }
    
	$sql = sprintf( "SELECT * FROM ct_event_serial WHERE user_id = ? AND serial_id IN (%s);", substr( $bind_target, 0, strlen( $bind_target ) - 1 ) );
    
	$db->setSql_str( $sql );
    
	$result		= $db->exeQuery($bind_param);
	
    // 全件取得
    // あとでadodbに変えよう……
    $list = array();
    while ( $row = $db->exeFetch( $result ) ) {
    	$list[$row['serial_id']] = $row;
    }
    
	$db->closeStmt($result);
    
	return $list;
}

/**
 * シリアルのマスター情報を入力したコードから取得
 */
function getSerialMaster ( $db, $serial_id )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM m_event_serial WHERE serial_id = ?;";
    
	$db->setSql_str( $sql );
	$bind_param	= $db->addBind( $bind_param, "i", $serial_id );
    
	$result		= $db->exeQuery($bind_param);
	$row		= $db->exeFetch($result);
    
	$db->closeStmt($result);
	return $row;
}

/**
 * シリアルのマスター情報を入力したコードから取得
 */
function getSerialMasterByCode ( $db, $code )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM m_event_serial WHERE code = ?;";
    
	$db->setSql_str( $sql );
	$bind_param	= $db->addBind( $bind_param, "s", $code );
    
	$result		= $db->exeQuery($bind_param);
	$row		= $db->exeFetch($result);
    
	$db->closeStmt($result);
	return $row;
}

/**
 * シリアルのマスター情報リストを引数のserial_id_arrayから取得
 */
function getSerialMasterListByIdArray ( $db, $serial_id_array )
{
    $bind_param	= $db->initBindParam();
    
    // bind_param用に数を調整
    $bind_target = "";
    foreach ( $serial_id_array as $row ) {
        $bind_param	= $db->addBind( $bind_param, "i", $row );
        $bind_target .= "?,";
    }
    
	$sql = sprintf( "SELECT * FROM m_event_serial WHERE serial_id IN (%s);", substr( $bind_target, 0, strlen( $bind_target ) - 1 ) );
    
	$db->setSql_str( $sql );
    
	$result		= $db->exeQuery($bind_param);
	
    // 全件取得
    // あとでadodbに変えよう……
    $list = array();
    while ( $row = $db->exeFetch( $result ) ) {
    	array_push( $list, $row );
    }
    
	$db->closeStmt($result);
	return $list;
}

/**
 * シリアルのマスター情報リストを引数のevent_id_arrayから取得
 */
function getSerialMasterListByEventIdArray ( $db, $event_id_array )
{
    $bind_param	= $db->initBindParam();
    
    // bind_param用に数を調整
    $bind_target = "";
    foreach ( $event_id_array as $row ) {
        $bind_param	= $db->addBind( $bind_param, "i", $row );
        $bind_target .= "?,";
    }
    
	$sql = sprintf( "SELECT * FROM m_event_serial WHERE event_id IN (%s);", substr( $bind_target, 0, strlen( $bind_target ) - 1 ) );
    
	$db->setSql_str( $sql );
    
	$result		= $db->exeQuery($bind_param);
	
    // 全件取得
    // あとでadodbに変えよう……
    $list = array();
    while ( $row = $db->exeFetch( $result ) ) {
    	$list[$row['serial_id']] = $row;
    }
    
	$db->closeStmt($result);
	return $list;
}

/**
 * 指定のイベントID関連のシリアルマスタを取得
 */
function getSerialMasterListByEventId ( $db, $event_id )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM m_event_serial WHERE event_id = ?;";
    
	$db->setSql_str( $sql );
	$bind_param	= $db->addBind( $bind_param, "i", $event_id );
    
	$result		= $db->exeQuery($bind_param);
    
    // 全件取得
    // あとでadodbに変えよう……
    $list = array();
    while ( $row = $db->exeFetch( $result ) ) {
    	array_push( $list, $row );
    }
    
	$db->closeStmt($result);
	return $list;
}
?>