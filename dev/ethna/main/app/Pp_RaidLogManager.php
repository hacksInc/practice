<?php
/**
 *  Pp_RaidLogManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */
require_once 'Pp_LogdataManager.php';

/**
 *  Pp_RaidLogManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RaidLogManager extends Pp_LogDataManager
{
	// プレイヤーアクション
	const ACTION_NONE              = 0;		// アクションなし（欠番扱い）
	const ACTION_DISCONNECT        = 1;		// 回線切断
	const ACTION_LOGIN_CREATE      = 10;	// パーティ作成による入室
	const ACTION_LOGIN_AUTO        = 11;	// 自動入室
	const ACTION_LOGIN_SEARCH      = 12;	// 検索機能経由による入室
	const ACTION_LOGIN_FRIEND      = 13;	// フレンド戦歴経由の入室
	const ACTION_LEAVE_SELF        = 20;	// 自主退室
	const ACTION_LEAVE_FORCE       = 21;	// マスター権限による強制退室
	const ACTION_LEAVE_AUTO        = 22;	// 時間経過の自動離脱判定による退室
	const ACTION_LEAVE_MIGRATE     = 23;	// 引き継ぎによる強制退室
	const ACTION_LOBBY_STAND_BY    = 30;	// 準備完了ボタン押下
	const ACTION_LOBBY_START       = 31;	// パーティマスターの出発ボタン押下
	const ACTION_LOBBY_SALLY       = 33;	// メンバー参戦
	const ACTION_LOBBY_SALLY_AGAIN = 34;	// 再出撃
	const ACTION_CONTINUE_MEDAL1   = 40;	// コンティニュー（マジカルメダル1pt消費）
	const ACTION_CONTINUE_MEDAL3   = 41;	// コンティニュー（マジカルメダル3pt消費）
	const ACTION_CONTINUE_POINT1   = 42;	// コンティニュー（レイドポイント1pt消費）
	const ACTION_CONTINUE_POINT3   = 43;	// コンティニュー（レイドポイント3pt消費）

	// ランキングポイント種別
	const RANKING_POINT_TYPE_BASE   = 1;
	const RANKING_POINT_TYPE_DAMAGE = 2;

	/**
	 * パーティメンバーアクションログの記録
	 * 
	 * @param array $columns
	 * 
	 * @return boolean
	 */
	function trackingPartyMemberAction( $columns )
	{
		if( isset( $columns['date_log'] ) === false )
		{
			$columns['date_log'] = date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] );
		}
		return $this->insertLogRaidPartyMemberAction( $columns );
	}

	/**
	 * ランキングポイントログの記録
	 * 
	 * @param array $columns
	 * 
	 * @return boolean
	 */
	function trackingRankingPoint( $columns )
	{
		if( isset( $columns['date_log'] ) === false )
		{
			$columns['date_log'] = date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] );
		}
		return $this->insertLogRaidRankingPoint( $columns );
	}
}
?>
