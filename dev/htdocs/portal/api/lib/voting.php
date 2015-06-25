<?php
require_once dirname(__FILE__) . '/../sub/sub.php';

/***************************************************
		関数
***************************************************/
/*
 * 投票集計結果取得
 */
function getVotingReportList ( $db, $voting_id )
{
    $bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM ct_portal_voting_report WHERE voting_id = ? ORDER BY point DESC;";
    
	$db->setSql_str( $sql );
	$bind_param	= $db->addBind( $bind_param, "i", $voting_id );
    
	$result = $db->exeQuery($bind_param);
	
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
 * 投票実行
 */
function execVoting ( $db, $user_id, $voting_id, $item_id, $point )
{
	$bind_param	= $db->initBindParam();
	
	$sql = "INSERT INTO ct_portal_voting( voting_id, pp_id, item_id, point, date_created ) VALUES( ?, ?, ?, ?, NOW() );";
	
	$db->setSql_str($sql);
	
	$bind_param = $db->addBind($bind_param, "i", $voting_id);
	$bind_param = $db->addBind($bind_param, "i", $user_id);
	$bind_param = $db->addBind($bind_param, "i", $item_id);
	$bind_param = $db->addBind($bind_param, "i", $point);
	
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
	if ($count <= 0) {
		$db->rollback();
		return false;
	}
	
	// ポイント減少
	if ( !insUserPoint_cave( $db, $user_id, 2, $point * -1 ) ) {
		return false;
	}
	
	$db->closeStmt($result);
	
	return true;
}

/**
 * 投票マスタ取得
 */
function getMasterVotingList ( $db, $voting_id )
{
	$bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM m_portal_voting WHERE voting_id = ?;";
    
	$db->setSql_str( $sql );
	$bind_param	= $db->addBind( $bind_param, "i", $voting_id );
    
	$result = $db->exeQuery($bind_param);
	
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
 * 投票マスタ取得
 */
function getMasterVoting ( $db, $voting_id, $item_id )
{
	$bind_param	= $db->initBindParam();
    
	$sql = "SELECT * FROM m_portal_voting WHERE voting_id = ? AND item_id = ?;";
    
	$db->setSql_str( $sql );
	
	$bind_param	= $db->addBind( $bind_param, "i", $voting_id );
	$bind_param	= $db->addBind( $bind_param, "i", $item_id );
    
	$result = $db->exeQuery($bind_param);
	
    // 全件取得
    // あとでadodbに変えよう……
    $row = $db->exeFetch( $result );
    
	$db->closeStmt($result);
	
	return $row;
}

/**
 * 投票残り時間
 */
function getTimeLimit ( $time_limit )
{
	$left = $time_limit - time();
	
	if ( $left <= 0 ) return "投票受付終了しました。";
	
	$str = "";
	
	if ( $left >= 3600 * 24 )	$str .= floor( $left / ( 3600 * 24 ) ) . "日";
	if ( $left >= 3600 )		$str .= floor( ( $left % ( 3600 * 24 ) ) / 3600 ) . "時間";
								$str .= floor( ( $left % 3600 ) / 60 ) . "分";
	
	return $str;
}
?>