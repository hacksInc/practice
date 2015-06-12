<?php
/**
 *  Pp_AdminRankingPrizeManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_AdminRankingPrizeManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminRankingPrizeManager extends Ethna_AppManager
{
	const PRIZE_TYPE_ITEM     = 1;
	const PRIZE_TYPE_MONSTER  = 2;
	const PRIZE_TYPE_COIN     = 3;
	const PRIZE_TYPE_MEDAL    = 4;
	const PRIZE_TYPE_BADGE    = 5;
	const PRIZE_TYPE_MATERIAL = 6;

	const PRIZE_STATUS_NON   = 0;
	const PRIZE_STATUS_BUSY  = 1;
	const PRIZE_STATUS_STOP  = 2;

	// 賞品タイプ名
	var $PRIZE_TYPE_OPTIONS = array(
		self::PRIZE_TYPE_ITEM    => '通常アイテム',
		self::PRIZE_TYPE_MONSTER => 'モンスター',
		self::PRIZE_TYPE_COIN    => '合成メダル',
		self::PRIZE_TYPE_MEDAL   => 'マジカルメダル',
	);

	// 賞品配布状態
	var $PRIZE_STATUS_OPTIONS = array(
		self::PRIZE_STATUS_NON   => '未配布',
		self::PRIZE_STATUS_BUSY  => '配布中',
		self::PRIZE_STATUS_STOP  => '配布中止'
	);

	// アップロード・ダウンロードで入出力対象となるカラム名
	var $LOAD_PRIZE_COLUMNS = array(
		'distribute_start', 'distribute_end',
		'prize_type', 'prize_id', 'lv', 'number'
	);

	/*
	 * DB接続
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn = null;
	protected $db_cmn_r = null;

	/**
	 *	指定ランキングIDの賞品配布データを取得する
	 *
	 *	@return array ランキングマスター情報
	 */
	function getRankingPrizeForRankingId( $ranking_id )
	{
		$param = array( $ranking_id );
		$sql = "SELECT * FROM t_ranking_prize WHERE ranking_id = ?";
		return $this->db_r->GetAll( $sql, $param );
	}

	/**
	 *	指定IDの賞品配布データを取得する
	 *
	 *	@return array ランキングマスター情報
	 */
	function getRankingPrizeForId( $id )
	{
		$param = array( $id );
		$sql = "SELECT * FROM t_ranking_prize WHERE id = ?";
		return $this->db_r->GetRow( $sql, $param );
	}

	/**
	 *	未配布の賞品配布対象データの取得
	 */
	function getRankingPrizeNondistribution( $ranking_id )
	{
		$param = array( $ranking_id, self::PRIZE_STATUS_NON );
		$sql = "SELECT * FROM t_ranking_prize WHERE ranking_id = ? AND status = ?";
		return $this->db_r->GetAll( $sql, $param );
	}

	/**
	 *	指定の賞品IDの配布情報を取得する
	 */
	function getRankingPrizeDistributionForPrizeId( $prize_id )
	{
		if (!$this->db_cmn_r) {
			$this->db_cmn_r =& $this->backend->getDB('cmn_r');
		}
		$param = array( $prize_id );
		$sql = "SELECT * FROM t_ranking_prize_distribution WHERE prize_id = ?";
		return $this->db_cmn_r->GetAll( $sql, $param );
	}

	/**
	 *	賞品配布データの新規追加
	 */
	function insertRankingPrize( $columns )
	{
		$param = array(
			$columns['ranking_id'],
			$columns['distribute_start'],
			$columns['distribute_end'],
			$columns['prize_type'],
			$columns['prize_id'],
			$columns['lv'],
			$columns['number'],
			self::PRIZE_STATUS_NON
		);
		$sql = "INSERT INTO t_ranking_prize( ranking_id, distribute_start, distribute_end, prize_type, prize_id, lv, number, status, date_created ) "
			 . "VALUES( ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 *	賞品配布データの更新
	 */
	function updateRankingPrize( $columns )
	{
		$param = array(
			$columns['distribute_start'],
			$columns['distribute_end'],
			$columns['prize_type'],
			$columns['prize_id'],
			$columns['lv'],
			$columns['number'],
			$columns['status'],
			$columns['id']
		);
		$sql = "UPDATE t_ranking_prize "
			 . "SET distribute_start = ?, distribute_end = ?, prize_type = ?, prize_id = ?, lv = ?, number = ?, status = ? "
			 . "WHERE id = ? ";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 *	賞品配布データの削除
	 */
	function deleteRankingPrize( $id )
	{
		$param = array( $id );
		$sql = "DELETE FROM t_ranking_prize WHERE id = ?";
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 *	賞品配布管理情報の登録
	 *
	 *	@param int $prize_id 賞品ID
	 *	@param int $present_id プレゼント管理ID
	 *
	 *	@return true:正常終了 !true:エラー
	 */
	function insertRankingPrizeDistribution( $prize_id, $present_id, $unit )
	{
		if (!$this->db_cmn) {
			$this->db_cmn =& $this->backend->getDB('cmn');
		}
		$param = array( $prize_id, $unit, $present_id );
		$sql = "INSERT INTO t_ranking_prize_distribution( prize_id, unit, present_id, date_created ) "
			 . "VALUES( ?, ?, ?, NOW())";
		if( !$this->db_cmn->execute( $sql, $param )) 
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 *	賞品配布を中止設定にする
	 *
	 *	@param int $info 配布情報
	 *
	 *	@return true:正常終了 !true:エラー
	 */
	function abortRankingPrizeDistributionMulti( $distribute_ids )
	{
		if (!$this->db_cmn) {
			$this->db_cmn =& $this->backend->getDB('cmn');
		}
		foreach( $distribute_ids as $distribute_id )
		{
			$param[] = $distribute_id;
			$where_distribute_id_in[] = '?';
		}
		$sql = "UPDATE t_ranking_prize_distribution SET status = 1 "
			 . "WHERE prize_distribute_id IN (".implode( ',', $where_distribute_id_in ).")";
		if( !$this->db_cmn->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_cmn->db->ErrorNo(), $this->db->db_cmn->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}
}
