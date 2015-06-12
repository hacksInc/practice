<?php
require_once dirname(__FILE__) . '/api/sub/sub.php';
require_once dirname(__FILE__) . '/api/lib/user.php';
require_once dirname(__FILE__) . '/api/lib/voting.php';

if ( WEB_MAINTENANCE == 1 ) {
    require_once ( dirname(__FILE__) . '/template/maintenance.html');
    exit;
}

/***************************************************
		処理
***************************************************/
// ヘッダのユーザー情報を取得
$user_id = $header['UserId'];

//	データベースアクセス部品の初期化
$db = new db();

//	データベースにコネクト
$db->connectDB();

//	会員情報を取得
$user_info = getUserInfo( $db, $user_id );

// 投票情報を取得
$m_voting = getMasterVotingList( $db, 1 );
$r_voting = getVotingReportList( $db, 1 );

// 項目はアトランダムに
$list = array();
$assoc = array();
$cnt = count( $m_voting );
for ( $i = 0; $i < $cnt; $i++ ) {
	$key = array_rand( $m_voting );
	
	$list[] = $m_voting[$key];
	$assoc[$m_voting[$key]['item_id']] = $m_voting[$key];
	
	unset( $m_voting[$key] );
}

// ランキング用の文字列生成
$rank_str = array(
	"1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th", "11th", "12th", "13th", "14th", "15th"
);

// 結果に対して順位文字列を入れておく（同率対応）
$rank = 0;
$rank_skip = 0;
foreach ( $r_voting as $key => $row ) {
	$r_voting[$key]['rank_str'] = $rank_str[$rank];
	
	if ( isset( $r_voting[$key + 1] ) && $row['point'] > $r_voting[$key + 1]['point'] ) {
		$rank += ( $rank_skip + 1 );
		$rank_skip = 0;
	} else {
		$rank_skip++;
	}
}

$db->closeDB();

require_once dirname(__FILE__) . "/template/votingresult2.html"
?>