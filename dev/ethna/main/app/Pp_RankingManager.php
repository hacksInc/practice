<?php
/**
 *  Pp_RankingManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_RankingManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RankingManager extends Ethna_AppManager
{
	/*
	 * DB接続
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn = null;
	protected $db_cmn_r = null;
	protected $db_logex_r = null;

	const TARGET_TYPE_MONSTER      = 1;	// モンスター
	const TARGET_TYPE_HELPER       = 2;	// 助っ人回数
	const TARGET_TYPE_RAID_RANKING = 3;	// レイドランキングポイント（全ポイント）

	// ターゲットタイプ
	var $TARGET_TYPE_OPTIONS = array(
		self::TARGET_TYPE_MONSTER      => 'モンスター',
		self::TARGET_TYPE_HELPER       => '助っ人回数',
		self::TARGET_TYPE_RAID_RANKING => 'レイドランキングポイント',
	); 

	// クリア情報ビットシフト数
	const DUNGEON_CLEAR_INFO_BITSHIFT_RANK3 = 0;		// 上級
	const DUNGEON_CLEAR_INFO_BITSHIFT_RANK4 = 1;		// 超級

	function truncateAccumuRanking()
	{
	//	$sql = "TRUNCATE TABLE t_ranking_accumu";
		$sql = "DELETE FROM t_ranking_accumu";
		
		// SQLクエリ実行
		if (!$this->db->execute($sql)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		return true;
	}

	function setAccumuRanking($columns)
	{
		// 必要な操作を判別
		$param = array($columns['user_id'],$columns['type'],$columns['rank_row'],$columns['rank_disp'],$columns['name'],$columns['val1'],$columns['val2']);
		$sql = "INSERT INTO t_ranking_accumu(user_id, type, rank_row, rank_disp, name, val1, val2, date_created)"
			 . " VALUES(?, ?, ?, ?, ?, ?, ?, NOW())";
		
		// SQLクエリ実行
		if (!$this->db->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__);
		}
		
		// 影響した行数を確認
		$affected_rows = $this->db->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		
		return true;
	}

	function getAccumuRankingUserRank($type, $user_id)
	{
		$param = array($type, $user_id);
		$sql = "SELECT rank_row FROM t_ranking_accumu"
		     . " WHERE type = ? AND user_id = ?";
		
		return $this->db->GetOne($sql, $param);
	}

	/**
	 * 複数のタイプのラインキングを取得
	 * @param array $types タイプの配列
	 * @param integer $user_id ユーザーID
	 *
	 * @return typeとrank_nowが1組になった配列
	 */
	function getAccumuRankingUserRanks($types, $user_id)
	{
		$param = array();
		$where_type_in = array();

		// types配列から個数分 IN クエリ用配列を作成
		foreach ($types as $type)
		{
			$param[] = $type;
			$where_type_in[] = '?';
		}

		// パラメータ配列にユーザーIDを追加
		$param[] = $user_id;

		// クエリ作成
		$sql = "SELECT type, rank_row FROM t_ranking_accumu"
		     . ' WHERE type IN (' . implode(',', $where_type_in) . ') AND user_id = ?';

		// 実行
		return $this->db->GetAll($sql, $param);
	}

	function getAccumuRankingMaxRank($type)
	{
		$param = array($type);
		$sql = "SELECT rank_row FROM t_ranking_accumu"
		     . " WHERE type = ?"
		     . " ORDER BY rank_row DESC"
		     . " LIMIT 1";
		
		return $this->db->GetRow($sql, $param);
	}

	function getAccumuRankingLimit($type = 1, $offset = 0, $limit = 10)
	{
		$param = array($type, $offset, $limit);
		$sql = "SELECT * FROM t_ranking_accumu"
		     . " WHERE type = ?"
		     . " ORDER BY rank_row"
		     . " LIMIT ?, ?";
		
		return $this->db->GetAll($sql, $param);
	}

	function getCountFromUserbase()
	{
		$sql = "SELECT COUNT(*) FROM t_user_base WHERE tutorial=10";
		
		return $this->db->GetOne($sql);
	}

	function getMonsterTotalRankingFromUserbase($offset = 0, $limit = 100000)
	{
		$param = array($offset, $limit);
		$sql = "SELECT user_id, name, monster_get_total AS val1 FROM t_user_base WHERE tutorial=10"
		     . " ORDER BY monster_get_total DESC, user_id"
		     . " LIMIT ?, ?";
		
		return $this->db->GetAll($sql, $param);
	}

	function getLampTotalRankingFromUserbase($offset = 0, $limit = 100000)
	{
		$param = array($offset, $limit);
		$sql = "SELECT user_id, name, lamp AS val1, game_total AS val2 FROM t_user_base WHERE tutorial=10"
		     . " ORDER BY lamp DESC, game_total DESC, user_id"
		     . " LIMIT ?, ?";
		
		return $this->db->GetAll($sql, $param);
	}

	/**
	 *	開催中のランキングを取得する
	 *
	 *	@return 開催中のランキングマスタ情報
	 */
	function getMasterValidRanking()
	{
		$sql = "SELECT * FROM m_ranking WHERE NOW() BETWEEN date_start AND date_end";
		return $this->db_r->GetAll( $sql );
	}

	/**
	 *	終了したが最終結果が出ていないランキングを取得する
	 *
	 *	@return 集計中のランキングマスタ情報
	 */
	function getMasterNoEndResultRanking()
	{
		// ※【注意】
		// 最終結果が出ていないランキングを取得してIN条件で検索すると
		// 一度も集計をしていないランキングはこの検索に引っかからないよ。
		// というのもt_ranking_infoのレコードは、最初に集計をした時点で
		// 作成されるからである。

		if( empty( $this->db_cmn_r ) === true )
		{
			$this->db_cmn_r =& $this->backend->getDB( 'cmn_r' );
		}
		// 既に最終結果が出ているランキングを取得
		$sql = "SELECT ranking_id FROM t_ranking_info WHERE status = 1";
		$temp = $this->db_cmn_r->GetAll( $sql );

		// NOT IN条件のパラメータ生成
		$param = array();
		foreach( $temp as $v )
		{
			$param[] = $v['ranking_id'];
			$where_ranking_id_not_in[] = '?';
		}

		// NOT INは全表検索になるけど、ランキングマスタの件数なんてたかが知れてるので大丈夫だべ。
		$sql = "SELECT * FROM m_ranking "
			 . "WHERE ranking_id NOT IN ( ".implode( ',', $where_ranking_id_not_in )." ) AND NOW() > date_end";
		return $this->db_r->GetAll( $sql, $param );
	}

	/**
	 *	開催中のランキングを取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *
	 *	@return ランキングマスタ情報
	 */
	function getMasterRanking( $ranking_id )
	{
		$param = array( $ranking_id );
		$sql = "SELECT * FROM m_ranking WHERE ranking_id = ?";
		return $this->db_r->GetRow( $sql, $param );
	}

	/**
	 *	ランキング管理情報を取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *
	 *	@return ランキング管理情報
	 */
	function getRankingInfo( $ranking_id )
	{
		if( empty( $this->db_cmn_r ) === true )
		{
			$this->db_cmn_r =& $this->backend->getDB( 'cmn_r' );
		}
		$param = array( $ranking_id );
		$sql = "SELECT * FROM t_ranking_info WHERE ranking_id = ?";
		return $this->db_cmn_r->GetRow( $sql, $param );
	}

	/**
	 *	ユーザーのbuffer_record_idを取得する
	 *
	 *	@param int $ranking_id
	 *	@param int $buffer_no
	 *	@param int $user_id
	 *
	 *	@return null:ランキング外 !null:バッファレコードID
	 */
	function getRankingBufferRecordId( $ranking_id, $buffer_no, $user_id )
	{
		$param = array( $ranking_id, $buffer_no, $user_id );
		$sql = "SELECT buffer_record_id FROM t_ranking_data "
			 . "WHERE ranking_id = ? AND buffer_no = ? AND user_id = ?";
		return $this->db_r->GetOne( $sql, $param );
	}

	/**
	 *	指定のユーザーIDのランキング集計データを取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *	@param int $user_id ユーザーID
	 *
	 *	@return ランキング集計データ（空の配列の場合はランキング外）
	 */
	function getRankingData( $ranking_id, $user_id )
	{
		if( empty( $user_id ) === true )
		{
			return array();
		}
		$info = $this->getRankingInfo( $ranking_id );
		$param = array( $ranking_id, $info['view_buffer'], $user_id );
		$sql = "SELECT buffer_record_id, rank, user_id, name, score, dungeon_clear_info FROM t_ranking_data "
			 . "WHERE ranking_id = ? AND buffer_no = ? AND user_id = ?";
		return $this->db_r->GetRow( $sql, $param );
	}

	/**
	 *	指定のユーザーID周辺のランキング集計データを取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *	@param int $user_id ユーザーID
	 *
	 *	@return ランキング集計データ（空の配列の場合はランキング外）
	 */
	function getRankingListForUserId( $ranking_id, $user_id = null )
	{
		$master = $this->getMasterRanking( $ranking_id );
		$info = $this->getRankingInfo( $ranking_id );
		if( empty( $user_id ) === true )
		{	// ユーザーIDの指定がなければ全レコードを取得
			$param = array( $ranking_id, $info['view_buffer'] );
			$sql = "SELECT buffer_record_id, rank, user_id, name, score, dungeon_clear_info FROM t_ranking_data "
				 . "WHERE ranking_id = ? AND buffer_no = ? "
				 . "ORDER BY buffer_record_id";
		}
		else
		{	// ユーザーの指定がある場合はそのユーザーの前後○件を取得
			$buffer_record_id = $this->getRankingBufferRecordId( $ranking_id, $info['view_buffer'], $user_id );
			if( empty( $buffer_record_id ) === true )
			{	// 取得できなければランキング外
				return array();
			}

			// 閾値内のレコード数を取得
			$record_count = $this->getThresholdRecordCount( $ranking_id );

			// ランキングにいる
			$higher_shortage = 0;		// 上位表示不足分
			$lower_shortage = 0;		// 下位表示不足分

			// 自分より上位のユーザー数をチェック
			$view_higher = ( int )$master['view_higher'];
			if( $buffer_record_id <= $view_higher )
			{	// 自分より上位の人が表示数に足りない
				$higher_shortage = ( $view_higher + 1 ) - $buffer_record_id;
			}

			// 自分より下位のユーザー数をチェック
			$view_lower = ( int )$master['view_lower'];
			//$lower_count = ( int )$info['record_count'] - $buffer_record_id;	// 自分より下位にいるユーザー数
			$lower_count = ( int )$record_count - $buffer_record_id;	// 自分より下位にいるユーザー数

			if( $lower_count < $view_lower )
			{	// 自分より下位の人が表示数に足りない
				$lower_shortage = $view_lower - $lower_count;
			}

			if(( $higher_shortage > 0 )&&( $lower_shortage === 0 ))
			{	// 上位のユーザー数だけが足りない
				$view_lower += $higher_shortage;	// 不足分を下位の表示数に加算
			}
			else if(( $lower_shortage > 0 )&&( $higher_shortage === 0 ))
			{	// 下位のユーザー数だけが足りない
				$view_higher += $lower_shortage;	// 不足分を上位の表示数に加算
			}

			$param = array(
				$ranking_id, $info['view_buffer'],
				$master['threshold'], 
				$ranking_id, $info['view_buffer'],
				$user_id, ( $view_higher + 1 ),
				$ranking_id, $info['view_buffer'],
				$master['threshold'], 
				$ranking_id, $info['view_buffer'],
				$user_id, $view_lower
			);
			$sql = "SELECT buffer_record_id, rank, user_id, name, score, dungeon_clear_info "
				 . "FROM (( "
				 .   "SELECT buffer_record_id, rank, user_id, name, score, dungeon_clear_info "
				 .   "FROM t_ranking_data "
				 .   "WHERE ranking_id = ? AND buffer_no = ? AND rank <= ? AND buffer_record_id <= ( "
				 .     "SELECT buffer_record_id "
				 .     "FROM t_ranking_data "
				 .     "WHERE ranking_id = ? AND buffer_no = ? AND user_id = ? "
				 .   ") "
				 .   "ORDER BY buffer_record_id DESC "
				 .   "LIMIT 0, ? "
				 . ") UNION ALL ( "
				 .   "SELECT buffer_record_id, rank, user_id, name, score, dungeon_clear_info "
				 .   "FROM t_ranking_data "
				 .   "WHERE ranking_id = ? AND buffer_no = ? AND rank <= ? AND buffer_record_id > ( "
				 .     "SELECT buffer_record_id "
				 .     "FROM t_ranking_data "
				 .     "WHERE ranking_id = ? AND buffer_no = ? AND user_id = ? "
				 .   ") "
				 .   "ORDER BY buffer_record_id ASC "
				 .   "LIMIT 0, ? "
				 . ")) as records ORDER BY buffer_record_id ";

		}
		return $this->db_r->GetAll( $sql, $param );
	}

	/**
	 *	ランキング集計データを取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *	@param boolean $last true:ランキングの末端から false:ランキングの先頭から
	 *
	 *	@return ランキング集計データ（空の配列の場合は未集計）
	 */
	function getRankingList( $ranking_id, $last = false )
	{
		$master = $this->getMasterRanking( $ranking_id );
		$info = $this->getRankingInfo( $ranking_id );
		$view_total = ( int )$master['view_higher'] + ( int )$master['view_lower'] + 1;
		$order = ( $last === false ) ? 'ASC ' : 'DESC ';

		$param = array( $ranking_id, $info['view_buffer'], $master['threshold'], $view_total );
		$sql = "SELECT buffer_record_id, rank, user_id, name, score, dungeon_clear_info FROM t_ranking_data "
			 . "WHERE ranking_id = ? AND buffer_no = ? AND rank <= ? "
			 . "ORDER BY buffer_record_id " . $order
			 . "LIMIT 0, ?";

		$ranking_list = $this->db_r->GetAll( $sql, $param );
		if( $last === true )
		{	// 末端からの場合は逆順で出力されるので戻しておく
			$ranking_list = array_reverse( $ranking_list );
		}
		return $ranking_list;
	}

	/**
	 *	指定の順位までのランキング集計データを取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *	@param int $limit_rank 末尾ランク
	 *
	 *	@return ランキング集計データ（空の配列の場合は未集計）
	 */
	function getRankingListHigherRank( $ranking_id, $limit_rank )
	{
		$info = $this->getRankingInfo( $ranking_id );

		$param = array( $ranking_id, $info['view_buffer'], $limit_rank );
		$sql = "SELECT buffer_record_id, rank, user_id, name, score, dungeon_clear_info FROM t_ranking_data "
			 . "WHERE ranking_id = ? AND buffer_no = ? AND rank <= ? "
			 . "ORDER BY buffer_record_id";
		$ranking_list = $this->db_r->GetAll( $sql, $param );
		return $ranking_list;
	}

	/**
	 *	指定の件数のランキング集計データを取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *	@param int $num 取得件数
	 *
	 *	@return ランキング集計データ（空の配列の場合は未集計）
	 */
	function getRankingListHigherNum( $ranking_id, $num )
	{
		$info = $this->getRankingInfo( $ranking_id );
		$param = array( $ranking_id, $info['view_buffer'], ( int )$num );
		$sql = "SELECT buffer_record_id, rank, user_id, name, score, dungeon_clear_info FROM t_ranking_data "
			 . "WHERE ranking_id = ? AND buffer_no = ? "
			 . "ORDER BY buffer_record_id "
			 . "LIMIT 0, ?";
		$ranking_list = $this->db_r->GetAll( $sql, $param );
		return $ranking_list;
	}

	/**
	 *	ランキング集計データを取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *
	 *	@return ランキング集計データ（空の配列の場合は未集計）
	 */
	function getRankingListAll( $ranking_id )
	{
		$info = $this->getRankingInfo( $ranking_id );

		$param = array( $ranking_id, $info['view_buffer'] );
		$sql = "SELECT buffer_record_id, rank, user_id, name, score, dungeon_clear_info FROM t_ranking_data "
			 . "WHERE ranking_id = ? AND buffer_no = ? "
			 . "ORDER BY buffer_record_id";

		$ranking_list = $this->db_r->GetAll( $sql, $param );

		return $ranking_list;
	}

	/**
	 *	指定順位のユーザーIDを取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *	@param int $rank_begin 取得開始順位
	 *	@param int $rank_end 取得末尾順位
	 *
	 *	@return ランキング集計データ
	 */
	function getRankingUserIdForRank( $ranking_id, $rank_begin, $rank_end = null )
	{
		$info = $this->getRankingInfo( $ranking_id );
		if( empty( $rank_end ) === true )
		{
			$param = array( $ranking_id, $info['view_buffer'], $rank_begin );
			$where = 'rank = ?';
		}
		else
		{
			$param = array( $ranking_id, $info['view_buffer'], $rank_begin, $rank_end );
			$where = 'rank BETWEEN ? AND ?';
		}
		$sql = "SELECT user_id FROM t_ranking_data "
			 . "WHERE ranking_id = ? AND buffer_no = ? AND ".$where;
		return $this->db_r->GetAll( $sql, $param );
	}

	/**
	 *	ランキングの集計
	 *
	 *	@param array $master ランキングマスタ情報
	 *
	 *	@return null:集計エラー !null:ランキング集計結果
	 */
	function countRanking( $master )
	{
		if( !$this->db_logex_r )
		{
			$this->db_logex_r =& $this->backend->getDB( 'logex_r' );
		}

		// ターゲットタイプごとに処理を分ける（タイプが増えてきたらswitch文に変えよう）
		$target_type = intval( $master['target_type'] );
		if( $target_type === self::TARGET_TYPE_MONSTER )
		{	// モンスター
			$point_list = array();		// ターゲット毎の獲得ポイントリスト

			// IN条件のパラメータ生成
			$param = array();
			$targets = explode( ',', $master['targets'] );
			foreach( $targets as $target )
			{
				list( $monster_id, $point ) = explode( '-', $target );

				$param[] = $monster_id;
				$where_target_in[] = '?';

				// ターゲット毎の獲得ポイント
				$point_list[$monster_id] = ( empty( $point ) === true ) ? 0 : ( int )$point;
			}
			$proc_types = explode( ',', $master['processing_type'] );
			foreach( $proc_types as $proc_type )
			{
				$param[] = $proc_type;
				$where_proc_type_in[] = '?';
			}
			$param[] = $master['date_start'];
			$param[] = $master['date_end'];

			// ユーザー別のモンスター別に集計
			$sql = "SELECT user_id, monster_id, COUNT( user_monster_id ) as count FROM log_monster_data "
				 . "WHERE monster_id IN ( ".implode( ',', $where_target_in )." ) "
				 . "AND processing_type IN ( ".implode( ',', $where_proc_type_in )." ) "
				 . "AND date_log BETWEEN ? AND ? "
				 . "GROUP BY user_id, monster_id "
				 . "ORDER BY user_id";

			$buff = $this->db_logex_r->GetAll( $sql, $param );

			// ユーザー単位に集計する
			$buff2 = array();
			foreach( $buff as $v )
			{	// モンスター毎に設定された獲得ポイントを計算してユーザー単位にまとめる
				$score = ( array_key_exists( $v['user_id'], $buff2 ) === true ) ? $buff2[$v['user_id']] : 0;
				$buff2[$v['user_id']] = $score + ( $point_list[$v['monster_id']] * $v['count'] );
			}
			arsort( $buff2, SORT_NUMERIC );	// 合計ポイントの大きい順にソートし直す

			$result = array();
			foreach( $buff2 as $k => $v )
			{
				array_push( $result, array( 'user_id' => $k, 'count' => $v ));
			}
		}
		else if( $target_type === self::TARGET_TYPE_HELPER )
		{	// 助っ人回数
			$param = array( $master['date_start'], $master['date_end'] );
			$sql = "SELECT helper_user_id as user_id, COUNT( helper_user_id ) as count FROM log_quest_data "
				 . "WHERE date_log BETWEEN ? AND ? AND helper_user_id IS NOT NULL "
				 . "GROUP BY helper_user_id "
				 . "ORDER BY count DESC";
			$result = $this->db_logex_r->GetAll( $sql, $param );
		}
		else if( $target_type === self::TARGET_TYPE_RAID_RANKING )
		{	// レイドランキングポイント
			$param = array( $master['date_start'], $master['date_end'] );
			$sql = "SELECT user_id, SUM( point ) as count FROM log_raid_ranking_point "
				 . "WHERE date_log BETWEEN ? AND ? AND type IN ( 1, 2 ) "
				 . "GROUP BY user_id "
				 . "ORDER BY count DESC";
			$result = $this->db_logex_r->GetAll( $sql, $param );

			$user_ids = array();
			$clear_flag = array();
			foreach( $result as $row )
			{
				$user_ids[] = $row['user_id'];
				$clear_flag[$row['user_id']] = array( 'rank3' => 0, 'rank4' => 0 );
			}

			// クリア対象のダンジョンIDを取得
			$dungeon_ids_rank3 = $master['clear_target_dungeon_rank3'];		// 上級
			$dungeon_ids_rank4 = $master['clear_target_dungeon_rank4'];		// 超級
			$count_rank3 = count( explode( ',', $dungeon_ids_rank3 ));
			$count_rank4 = count( explode( ',', $dungeon_ids_rank4 ));

			// ユーザー毎に対象ダンジョンで最終レベルをクリアした記録のレコード数を取得
			$str_user_ids = implode( ',', $user_ids );
			$sql = "SELECT l.user_id, COUNT( l.user_id ) as count "
				 . "FROM log_raid_user_dungeon_clear as l, "
				 . "( SELECT dungeon_id, dungeon_lv FROM m_raid_dungeon_detail "
				 . "  WHERE dungeon_id IN(".$dungeon_ids_rank3.") AND difficulty = 3 AND last_lv = 1 ) as m "
				 . "WHERE l.dungeon_id = m.dungeon_id AND l.difficulty = 3 AND l.dungeon_lv = m.dungeon_lv "
				 . "AND l.user_id IN(".$str_user_ids.") "
				 . "GROUP BY l.user_id";
			$list_rank3 = $this->db_r->GetAll( $sql );

			$sql = "SELECT l.user_id, COUNT( l.user_id ) as count "
				 . "FROM log_raid_user_dungeon_clear as l, "
				 . "( SELECT dungeon_id, dungeon_lv FROM m_raid_dungeon_detail "
				 . "  WHERE dungeon_id IN(".$dungeon_ids_rank4.") AND difficulty = 4 AND last_lv = 1 ) as m "
				 . "WHERE l.dungeon_id = m.dungeon_id AND l.difficulty = 4 AND l.dungeon_lv = m.dungeon_lv "
				 . "AND l.user_id IN(".$str_user_ids.") "
				 . "GROUP BY l.user_id";
			$list_rank4 = $this->db_r->GetAll( $sql );

			// 最終レベルをクリアしたダンジョン数とクリア対象のダンジョン数が同じなら制覇とみなす
			foreach(( array )$list_rank3 as $row )
			{
				if( $row['count'] == $count_rank3 )
				{	// 上級ダンジョン全制覇
					$clear_flag[$row['user_id']]['rank3'] = 1;
				}
			}
			foreach(( array )$list_rank4 as $row )
			{
				if( $row['count'] == $count_rank4 )
				{	// 超級ダンジョン全制覇
					$clear_flag[$row['user_id']]['rank4'] = 1;
				}
			}

			// 制覇クリアフラグビットをセット
			foreach( $result as $k => $v )
			{
				$bits = 0;
				$bits |= ( $clear_flag[$v['user_id']]['rank3'] << self::DUNGEON_CLEAR_INFO_BITSHIFT_RANK3 );
				$bits |= ( $clear_flag[$v['user_id']]['rank4'] << self::DUNGEON_CLEAR_INFO_BITSHIFT_RANK4 );
				$result[$k]['dungeon_clear_info'] = $bits;
			}
		}
		else
		{	// 未定義
			return null;
		}
		return $result;
	}

	/**
	 *	ランキングの集計データの更新
	 *
	 *	@param int $ranking_id ランキングID
	 *  @param int $buffer_no 更新対象のバッファ番号
	 *  @param int $unit 更新対象の(サーバの)ユニット番号
	 *  @param array $data 書き込む集計データ
	 *
	 *	@return null:更新エラー !null:集計データのレコード数
	 */
	function renewRankingData( $ranking_id, $buffer_no, $unit, $data )
	{
		$unit_m = $this->backend->getManager( 'Unit' );

		// 古いデータを削除
		$param = array( $ranking_id, $buffer_no );
		$sql = "DELETE FROM t_ranking_data WHERE ranking_id = ? AND buffer_no = ?";
		$ret = $unit_m->executeForUnit( $unit, $sql, $param, false );
		if( $ret->ErrorNo )
		{
			$this->backend->logger->log( LOG_WARNING,
				'renewRankingData: delele ranking data is failed. '.
				'ranking_id['.$ranking_id.'], unit['.$unit.'], buffer_no['.$buffer_no.']'
			);
			return null;
		}

		// 新しいデータを登録し直す
		$record_count = 0;
		$rank = 0;
		$score = null;

		// クエリ自体は同じなので先に準備しておく
		$sql = "INSERT INTO t_ranking_data( "
			 . "ranking_id, buffer_no, buffer_record_id, rank, user_id, name, score, dungeon_clear_info "
			 . ") VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )";

		// ランキング情報を１レコードずつ登録（ダブルバッファなのでトランザクションは不要）
		foreach( $data as $k => $v )
		{
			$record_count++;

			if(( is_null( $score ) === true )||( $score > ( int )$v['count'] ))
			{	// 前のレコードとスコアが異なる
				$rank = $record_count;			// 順位を変更
				$score = ( int )$v['count'];	// スコアを差し替え
			}

			// クエリに流し込むパラメータをセット
			$clear_info = ( array_key_exists( 'dungeon_clear_info', $v ) === true ) ? $v['dungeon_clear_info'] : null;
			$param = array(
				$ranking_id, $buffer_no, $record_count, $rank, $v['user_id'], $v['name'], $score, $clear_info
			);

			// クエリの実行
			$ret = $unit_m->executeForUnit( $unit, $sql, $param, true );
			if( $ret->ErrorNo )
			{
				$this->backend->logger->log( LOG_WARNING,
					'renewRankingData: insert ranking data is failed. '.
					'ranking_id['.$ranking_id.'], unit['.$unit.'], buffer_no['.$buffer_no.']'
				);
				return null;
			}
		}
		return $record_count;
	}

	/**
	 *	ランキング集計データの削除
	 *
	 *	@param int $ranking_id ランキングID
	 *
	 *	@return true:正常終了 !true:エラー
	 */
	function deleteRankingData( $ranking_id )
	{
		$unit_m = $this->backend->getManager( 'Unit' );

		$param = array( $ranking_id );
		$sql = "DELETE FROM t_ranking_data WHERE ranking_id = ?";

		$unit_info = $unit_m->getUnitInfo();
		$unit_list = array_keys( $unit_info );
		foreach( $unit_list as $v )
		{
			$ret = $unit_m->executeForUnit( $v, $sql, $param, false );
			if( $ret->ErrorNo )
			{
				$this->backend->logger->log( LOG_WARNING,
					'deleteRankingData: delele ranking data is failed. '.
					'ranking_id['.$ranking_id.'], unit['.$v.']'
				);
				return null;
			}
		}
		return true;
	}

	/**
	 *	ランキング管理情報の新規追加
	 *
	 *	@param int $ranking_id ランキングID
	 *  @param int $view_buffer 参照バッファ番号
	 *  @param int $record_count 対象集計データのレコード数
	 *  @param boolean $is_final true:最終結果 false:途中経過
	 *
	 *	@return true:正常終了 !true:エラー
	 */
	function insertRankingInfo( $ranking_id, $view_buffer, $record_count, $is_final )
	{
		if( empty( $this->db_cmn ) === true )
		{
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}
		$param = array( $ranking_id, $view_buffer, $record_count, (( $is_final === true ) ? 1 : 0 ));
		$sql = "INSERT INTO t_ranking_info( ranking_id, view_buffer, record_count, status ) VALUES( ?, ?, ?, ? )";
		if( !$this->db_cmn->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 *	ランキング管理情報の更新
	 *
	 *	@param int $ranking_id ランキングID
	 *  @param int $view_buffer 参照バッファ番号
	 *  @param int $record_count 対象集計データのレコード数
	 *  @param boolean $is_final true:最終結果 false:途中経過
	 *
	 *	@return true:正常終了 !true:エラー
	 */
	function updateRankingInfo( $ranking_id, $view_buffer, $record_count, $is_final  )
	{
		if( empty( $this->db_cmn ) === true )
		{
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}
		$param = array( $view_buffer, $record_count, (( $is_final === true ) ? 1 : 0 ), $ranking_id );
		$sql = "UPDATE t_ranking_info SET view_buffer = ?, record_count = ?, status = ? WHERE ranking_id = ?";
		if( !$this->db_cmn->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 *	ランキング管理情報の削除
	 *
	 *	@param int $ranking_id ランキングID
	 *
	 *	@return true:正常終了 !true:エラー
	 */
	function deleteRankingInfo( $ranking_id )
	{
		if( empty( $this->db_cmn ) === true )
		{
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}
		$param = array( $ranking_id );
		$sql = "DELETE FROM t_ranking_info WHERE ranking_id = ?";
		if( !$this->db_cmn->execute( $sql, $param ))
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 *	ランキング集計結果の閾値内のレコード数を取得する
	 *
	 *	@param int $ranking_id ランキングID
	 *
	 *	@return true:正常終了 !true:エラー
	 */
	function getThresholdRecordCount( $ranking_id )
	{
		$master = $this->getMasterRanking( $ranking_id );
		$info = $this->getRankingInfo( $ranking_id );

		$param = array( $ranking_id, $info['view_buffer'], $master['threshold'] );
		$sql = "SELECT MAX( buffer_record_id ) as record_count FROM t_ranking_data "
			 . "WHERE ranking_id = ? AND buffer_no = ? AND rank < ? ";

		$record_count = $this->db_r->GetOne( $sql, $param );

		return $record_count;
	}

}
