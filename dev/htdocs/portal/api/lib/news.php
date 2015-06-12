<?php
require_once dirname(__FILE__) . '/../sub/sub.php';

/**
 * ニュースを取得
 */
function getNews ( $db, $user_id, $offset, $limit )
{
    //	バインドパラメータ初期化
    $bind_param	= $db->initBindParam();
    
    //	SQL文生成
    $sql = "SELECT news.id, news.disp_date, news.news_title, news.news_text,
                CASE COALESCE(ar_read.news_id, '') WHEN '' THEN '0' ELSE '1' END AS ar_flag,
                CASE COALESCE(new_news.id, '') WHEN '' THEN '0' ELSE CASE COALESCE(ar_read.news_id, '') WHEN '' THEN '1' ELSE '0' END END AS new_flag
            FROM news
                LEFT JOIN (SELECT news_id FROM news_read WHERE news_read.user_id = ?) AS ar_read
                ON news.id = ar_read.news_id
                LEFT JOIN (SELECT id FROM news WHERE DATE_ADD(NOW(), INTERVAL -1 DAY) <= regist_date) AS new_news
                ON news.id = new_news.id
            WHERE news.news_kbn = ? AND news.del_flg = ? AND CAST(news.disp_date AS DATE) <= CURRENT_DATE() AND news.open_date <= NOW() 
            ORDER BY news.regist_date DESC;";
    
    //	SQLをセット
    $db->setSql_str($sql);
    $bind_param	= $db->addBind($bind_param, "i", $user_id);
    $bind_param	= $db->addBind($bind_param, "s", NEWS_KBN_NEWS);
    $bind_param	= $db->addBind($bind_param, "s", DEL_FLG_NODELETE);
//    $bind_param	= $db->addBind($bind_param, "i", $offset);
//    $bind_param	= $db->addBind($bind_param, "i", $limit);
    
    //	SQL実行＆結果取得
    $result		= $db->exeQuery($bind_param);
    
    // 全件取得
    // あとでadodbに変えよう……
    $news_data = array();
    while ( $row = $db->exeFetch( $result ) ) {
    	array_push( $news_data, $row );
    }
    
    $db->closeStmt( $result );
    
    return $news_data;
}

/**
 * ニュース閲覧時のポイント獲得
 */
function readNews ( $db, $user_id, $news_id )
{
	//　既読テーブルに追加
	$bind_param	= $db->initBindParam();
	$sql		= "INSERT INTO news_read(user_ID, news_id) VALUES(?, ?);";
	$db->setSql_str($sql);
	$bind_param = $db->addBind($bind_param, "i", $user_id);
	$bind_param = $db->addBind($bind_param, "i", $news_id);
	$ar_result		= $db->exeQuery($bind_param);
	$ar_count		= $db->getAffectedRows($ar_result);
	if ($ar_count <= 0) {
		$db->rollback();
        return false;
	} else {
		//	ポイントの処理
		$pointResult = insUserPoint($db, $user_id, POINT_KBN_NEWS);
		if ($pointResult == NEWS_STATUS_SYSTEM_ERR){
			$db->rollback();
			return false;
		}
        
		//	コミット
		$db->commit();
	}
    
    return true;
}

/**
 * ニュースを一件取得
 */
function getNewsOnce ( $db, $user_id, $news_id )
{
    //	バインドパラメータ初期化
    $bind_param	= $db->initBindParam();
    
    //	SQL文生成
    $sql = "SELECT news.id, news.disp_date, news.news_title, news.news_text,
                CASE COALESCE(ar_read.news_id, '') WHEN '' THEN '0' ELSE '1' END AS ar_flag,
                CASE COALESCE(new_news.id, '') WHEN '' THEN '0' ELSE CASE COALESCE(ar_read.news_id, '') WHEN '' THEN '1' ELSE '0' END END AS new_flag
            FROM news
                LEFT JOIN (SELECT news_id FROM news_read WHERE news_read.user_id = ?) AS ar_read
                ON news.id = ar_read.news_id
                LEFT JOIN (SELECT id FROM news WHERE DATE_ADD(NOW(), INTERVAL -1 DAY) <= regist_date) AS new_news
                ON news.id = new_news.id
            WHERE news.news_kbn = ? AND news.id = ? AND news.del_flg = ? AND CAST(news.disp_date AS DATE) <= CURRENT_DATE() AND news.open_date <= NOW();";
    
    //	SQLをセット
    $db->setSql_str($sql);
    $bind_param	= $db->addBind($bind_param, "i", $user_id);
    $bind_param	= $db->addBind($bind_param, "s", NEWS_KBN_NEWS);
    $bind_param	= $db->addBind($bind_param, "i", $news_id);
    $bind_param	= $db->addBind($bind_param, "s", DEL_FLG_NODELETE);
    
    //	SQL実行＆結果取得
    $result		= $db->exeQuery($bind_param);
    
    $row = $db->exeFetch( $result );
    
    $db->closeStmt( $result );
    
    return $row;
}

/**
 * twitter_tmp から最新のテキストを取得
 * return	String		twitter_txt
 */
function getTwitterTmp($db) {
	$bind_param	= $db->initBindParam();
	$sql		= <<<EOT
SELECT twitter_text 
  FROM twitter_tmp
 ORDER BY regist_date DESC
 LIMIT 1;
EOT;
	$db->setSql_str($sql);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getRows($result);
	if ($count <= 0) {
		$db->closeStmt($result);
		return "";
	}
	$row = $db->exeFetch($result);
	$db->closeStmt($result);
	return $row['twitter_text'];
}
/**
 * twitter_tmp から最新のテキストを取得
 * return	String		twitter_txt
 */
function getNewsTopic($db) {
	$bind_param	= $db->initBindParam();
	$sql		= <<<EOT
SELECT disp_date, news_title, news_text
  FROM news
 WHERE news_kbn = ?
 AND del_flg = ?
 AND open_date <= ?
 ORDER BY disp_date DESC
 LIMIT 1;
EOT;
	$db->setSql_str($sql);
	$bind_param	= $db->addBind($bind_param, "s", NEWS_KBN_TOPIC);
	$bind_param	= $db->addBind($bind_param, "s", DEL_FLG_NODELETE);
	$bind_param	= $db->addBind($bind_param, "s", date('YmdHis'));
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getRows($result);
	if ($count <= 0) {
		$db->closeStmt($result);
		return "";
	}
	$row = $db->exeFetch($result);
	$db->closeStmt($result);
	return $row;
}
?>