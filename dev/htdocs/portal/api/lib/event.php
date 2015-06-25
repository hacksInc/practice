<?php
require_once dirname(__FILE__) . '/../sub/sub.php';

/***************************************************
		関数
***************************************************/
/*
 * 開催中のイベント一覧を取得
 */
function getOpenEventMasterList ( $db )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM m_event_detail WHERE open_at <= ? AND close_at >= ?;";
    
	$db->setSql_str( $sql );
	$bind_param	= $db->addBind( $bind_param, "s", date( "Y-m-d H:i:s" ) );
    $bind_param	= $db->addBind( $bind_param, "s", date( "Y-m-d H:i:s" ) );
    
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

/*
 * 開催中のイベントを取得
 */
function getOpenEventMaster ( $db, $event_id )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM m_event_detail WHERE event_id = ? AND open_at <= ? AND close_at >= ?;";
    
	$db->setSql_str( $sql );
    $bind_param	= $db->addBind( $bind_param, "i", $event_id );
	$bind_param	= $db->addBind( $bind_param, "s", date( "Y-m-d H:i:s" ) );
    $bind_param	= $db->addBind( $bind_param, "s", date( "Y-m-d H:i:s" ) );
    
	$result		= $db->exeQuery($bind_param);
	$row = $db->exeFetch( $result );
    
	$db->closeStmt($result);
	return $row;
}

/*
 * 全イベントを取得
 */
function getEventMasterListAll ( $db )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM m_event_detail;";
    
	$db->setSql_str( $sql );
    
	$result		= $db->exeQuery($bind_param);
	
    // 全件取得
    // あとでadodbに変えよう……
    $list = array();
    while ( $row = $db->exeFetch( $result ) ) {
        $list[$row['event_id']] = $row;
    }
    
	$db->closeStmt($result);
	return $list;
}

/**
 * 時期に関わらずマスター情報を取得
 */
function getEventMaster ( $db, $event_id )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM m_event_detail WHERE event_id = ?;";
    
	$db->setSql_str( $sql );
    $bind_param	= $db->addBind( $bind_param, "i", $event_id );
    
	$result		= $db->exeQuery($bind_param);
	$row = $db->exeFetch( $result );
    
	$db->closeStmt($result);
	return $row;
}
?>