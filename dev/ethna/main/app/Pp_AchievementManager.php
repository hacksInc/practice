<?php
/**
 *  Pp_AchievementManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_AchievementManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AchievementManager extends Ethna_AppManager
{
	// 参照カラム名テーブル
	var $COLUMN_TABLE = array(
		 1 => 'btl_paralyzer',
		 2 => 'btl_eliminator',
		 3 => 'btl_decomposer',
		 4 => array(
			600 => 'btl_clear_10min',
			540 => 'btl_clear_9min',
			480 => 'btl_clear_8min',
			420 => 'btl_clear_7min',
			360 => 'btl_clear_6min',
			300 => 'btl_clear_5min'
		 ),
		 5 => 'btl_persuasion',
		 6 => 'btl_reprimand',
		 7 => 'btl_warning',
		 8 => 'mis_support_tsunemori',
		 9 => 'mis_support_kougami',
		10 => 'mis_support_ginoza',
		11 => 'mis_support_masaoka',
		12 => 'mis_support_kagari',
		13 => 'mis_support_kunizuka',
		14 => 'btl_clear_best',
		15 => 'btl_clear_normal',
		16 => 'login',
		17 => 'best_clear',
		18 => 'normal_clear',
		19 => array(
			600 => 'clear_10min',
			540 => 'clear_9min',
			480 => 'clear_8min',
			420 => 'clear_7min',
			360 => 'clear_6min',
			300 => 'clear_5min'
		),
		20 => 'paralyzer',
		21 => 'eliminator',
		22 => 'decomposer',
		23 => 'persuasion',
		24 => 'reprimand',
		25 => 'warning'
	);


	// 親クラスのコンストラクタで取得されないDBのインスタンス
	protected $db_m_r = null;

	/**
	 * DBのインスタンスを生成
	 *
	 * @return null
	 */
	function set_db()
	{
		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
	}

	/**
	 * 勲章条件マスタ一覧を取得する
	 * 
	 * @param
	 *
	 * @return
	 */
	function getMasterAchievementConditionListAssoc()
	{
		// memcacheから取得してみる
		$cache_key = "achievement_condition_list_assoc";
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 60 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// キャッシュから取得できなければDBから取得
		$this->set_db();

		$sql = "SELECT ach_id, c.* FROM m_achievement_condition as c";
		$data = $this->db_m_r->db->GetAssoc( $sql );

		// 取得したデータをキャッシュする
		if( !empty( $data ))
		{
			$cache_m->set( $cache_key, $data );
		}
		return $data;
	}

	/**
	 * 勲章条件マスタの中のグループ別最低ランクの情報のリストを取得
	 * 
	 * @param
	 *
	 * @return
	 */
	function getMasterAchievementConditionLowestRankListAssoc()
	{
		// memcacheから取得してみる
		$cache_key = "achievement_condition_lowest_rank_list_assoc";
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// キャッシュから取得できなければDBから取得
		$this->set_db();

		$sql = "SELECT ach_id, c.* FROM m_achievement_condition as c WHERE rank = 1";
		$data = $this->db_m_r->db->GetAssoc( $sql );

		// 取得したデータをキャッシュする
		if( !empty( $data ))
		{
			$cache_m->set( $cache_key, $data );
		}
		return $data;
	}

	/**
	 * 指定の勲章グループに所属する勲章条件マスタを取得
	 * 
	 * @param グループIDの配列
	 *
	 * @return
	 */
	function getMasterAchievementConditionByGroupId( $ach_group_ids )
	{
		$this->set_db();

		$temp = array();
		foreach( $ach_group_ids as $ach_group_id )
		{
			$temp[] = '?';
		}
		$sql = "SELECT * FROM m_achievement_condition "
			 . "WHERE ach_group_id IN ( ".implode( ',', $temp )." )";
		return $this->db_m_r->GetAll( $sql, $ach_group_ids );
	}

	/**
	 * 指定の勲章グループに所属する勲章条件マスタを取得
	 * 
	 * @param グループIDの配列
	 *
	 * @return
	 */
	function getMasterAchievementConditionByGroupIdAssoc( $ach_group_ids )
	{
		$this->set_db();

		$temp = array();
		foreach( $ach_group_ids as $ach_group_id )
		{
			$temp[] = '?';
		}
		$sql = "SELECT ach_id, c.* FROM m_achievement_condition as c "
			 . "WHERE ach_group_id IN ( ".implode( ',', $temp )." ) "
			 . "ORDER BY ach_group_id, rank";
		return $this->db_m_r->db->GetAssoc( $sql, $ach_group_ids );
	}


	/**
	 * 勲章グループマスタ一覧を取得する
	 * 
	 * @param
	 *
	 * @return
	 */
	function getMasterAchievementGroupListAssoc()
	{
		// memcacheから取得してみる
		$cache_key = "achievement_group_list_assoc";
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 60 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$this->set_db();

		$sql = "SELECT ach_group_id, g.* FROM m_achievement_group as g "
			 . "WHERE ( date_start IS NULL OR date_start = '0000-00-00 00:00:00' OR date_start <= NOW()) AND ( date_end IS NULL OR date_start = '0000-00-00 00:00:00' OR NOW() < date_end )";
		$data = $this->db_m_r->db->GetAssoc( $sql );

		// 取得したデータをキャッシュする
		if( !empty( $data ))
		{
			$cache_m->set( $cache_key, $data );
		}
		return $data;
	}

	/**
	 * 解放済みの勲章グループを取得
	 * 
	 * @param $got_ach_ids 獲得済みの勲章IDの配列
	 *
	 * @return
	 */
	function getReleasedAchGroup( $got_ach_ids )
	{
		// キャッシュから取得できなければDBから取得
		$this->set_db();

		if( empty( $got_ach_ids ))
		{
			$sql = "SELECT * FROM m_achievement_group WHERE release_ach_id = 0";
			$data = $this->db_m_r->GetAll( $sql );
		}
		else
		{
			$where_in = array();
			foreach( $got_ach_ids as $ach_id )
			{
				$where_in[] = '?';
			}
			$sql = "SELECT * FROM m_achievement_group "
				 . "WHERE release_ach_id IN ( 0, ".implode( ',', $where_in )." )";
			$data = $this->db_m_r->GetAll( $sql, $got_ach_ids );
		}

		return $data;
	}


	/**
	 * 指定の勲章IDから解放される勲章IDを取得
	 * 
	 * @param
	 *
	 * @return
	 */
	function getReleaseAchId( $ach_id )
	{
		// memcacheから取得してみる
		$cache_key = "release_ach_id__".$ach_id;
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// キャッシュから取得できなければDBから取得
		$this->set_db();

		$param = array( $ach_id );
		$sql = "SELECT ac.ach_id FROM m_achievement_group as ag, m_achievement_condition as ac "
			 . "WHERE ag.release_ach_id = ? AND ag.ach_group_id = ac.ach_group_id AND ac.rank = 1";
		$data = $this->db_m_r->GetOne( $sql, $param );

		// 取得したデータをキャッシュする
		$cache_m->set( $cache_key, $data );

		return $data;
	}

	/**
	 * 次に獲得できる勲章IDの一覧を取得
	 * 
	 * @param
	 *
	 * @return
	 */
	function getNextAchIds( $got_ach_ids )
	{
		//-----------------------------------------------------------
		//	現在解放されている勲章グループを取得
		//-----------------------------------------------------------
		$released_ach_group = $this->getReleasedAchGroup( $got_ach_ids );	// 現在解放されているグループを取得

		//-----------------------------------------------------------
		//	解放済みの勲章から未獲得の勲章IDを取得
		//-----------------------------------------------------------
		$group_ids = array();
		foreach( $released_ach_group as $row )
		{
			$group_ids[] = $row['ach_group_id'];
		}
		$ach_group_cond = $this->getMasterAchievementConditionByGroupId( $group_ids );	// グループに所属する全勲章条件マスタ
		$temp = array();
		foreach( $ach_group_cond as $row )
		{
			if( isset( $temp[$row['ach_group_id']] ))
			{
				if( $temp[$row['ach_group_id']] > $row['ach_id'] )
				{
					$temp[$row['ach_group_id']] = $row['ach_id'];
				}
				else
				{
				}
			}
			else if( !in_array( $row['ach_id'], $got_ach_ids ))
			{
				$temp[$row['ach_group_id']] = $row['ach_id'];
			}
		}
		$group_ach_ids = array_values( $temp );		// 勲章IDの配列にする

		$buff = array();
		$buff2 = array();
		foreach( $group_ach_ids as $v )
		{
			$buff[] = $v;
		}
		foreach( $got_ach_ids as $v )
		{
			$buff2[] = $v;
		}

		// 獲得済みの勲章IDを除外
		$ach_ids = array_diff( $group_ach_ids, $got_ach_ids );		// 未獲得の勲章IDの配列

		return $ach_ids;
	}


	/**
	 * 指定の勲章IDの次のランクの勲章条件マスタ情報を取得する
	 * 
	 * @param $ach_id 勲章ID
	 *
	 * @return array:勲章条件マスタ情報（空配列の場合は（最大ランクなので）次はない）| null:エラー
	 */
	function getMasterAchievementConditionNextRank( $ach_id )
	{
		// memcacheから取得してみる
		$cache_key = "achievement_condition__".$ach_id;
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// キャッシュから取得できなければDBから取得
		$this->set_db();

		$param = array( $ach_id );
		$sql = "SELECT * FROM m_achievement_condition "
			 . "WHERE ( ach_group_id, rank ) = "
			 . "( SELECT ach_group_id, ( rank + 1 ) FROM m_achievement_condition WHERE ach_id = ? )";
		$data = $this->db_m_r->GetRow( $sql, $param );

		// 取得したデータをキャッシュする
		if( $data !== false )	// ※空配列はキャッシュするよ！
		{
			$cache_m->set( $cache_key, $data );
		}
		return $data;
	}

	/**
	 * 各勲章グループの次の獲得すべき勲章IDのリストを取得する
	 * 
	 * @param $user_ach_rank ユーザーが獲得済の勲章情報
	 *
	 * @return array:勲章条件マスタ情報（空配列の場合は（最大ランクなので）次はない）| null:エラー
	 */
	function getAchievementNextTargetList( $user_ach_rank )
	{
		// 勲章条件マスタを取得
		$cond_master = $this->getMasterAchievementConditionListAssoc();
		if( empty( $cond_master ))
		{	// 取得エラー
			return null;
		}

		// 勲章グループマスタを取得
		$group_master = $this->getMasterAchievementGroupListAssoc();
		if( empty( $group_master ))
		{	// 取得エラー
			return null;
		}

		// 各勲章グループの最低ランクの勲章情報リストを取得
		$lowest_master = $this->getMasterAchievementConditionLowestRankListAssoc();
		if( empty( $lowest_master ))
		{	// 取得エラー
			return null;
		}

		// ユーザーの勲章獲得情報からデータを生成
		$buff = array();
		if( !empty( $user_ach_rank ))
		{
			foreach( $user_ach_rank as $v )
			{
				$group = $cond_master[$v['ach_id']]['ach_group_id'];
				if( !array_key_exists( $group, $group_master ))
				{	// 取得したグループマスタ情報にグループがない場合は公開期間外と考える
					continue;
				}

				if( !array_key_exists( $group, $buff ))
				{	// まだ値が設定されていない
					$buff[$group] = array(
						'ach_id' => $v['ach_id'],
						'rank' => $cond_master[$v['ach_id']]['rank'],
						'cond_value' => $cond_master[$v['ach_id']]['cond_value']
					);
				}
				else
				{	// 値を設定済み
					if( $buff[$group]['rank'] < $cond_master[$v['ach_id']]['rank'] )
					{	// バッファ内のランク以上のものを獲得していたら更新
						$buff[$group]['ach_id'] = $v['ach_id'];
						$buff[$group]['rank'] = $cond_master[$v['ach_id']]['rank'];
						$buff[$group]['cond_value'] = $cond_master[$v['ach_id']]['cond_value'];
					}
				}
			}

			// 獲得している勲章の次のランク情報に差し替える
			foreach( $buff as $group => $v )
			{
				$next = $this->getMasterAchievementConditionNextRank( $v['ach_id'] );
				if( $next === false )
				{	// 取得エラー
					return 'error_500';
				}
				if( empty( $next ))
				{	// 次のランクがない
					$buff[$group] = array();	// 表示しないので情報を空にする
				}
				else
				{	// 次のランクがあるなら差し替える
					$buff[$group]['ach_id'] = $next['ach_id'];
					$buff[$group]['rank'] = $next['rank'];
					$buff[$group]['cond_value'] = $next['cond_value'];
				}
			}
		}

		// 返却データを各勲章グループの最低ランク情報で初期化
		$now_timestamp = $_SERVER['REQUEST_TIME'];
		$achieve_medal = array();
		foreach( $lowest_master as $ach_id => $v )
		{
			$group = $v['ach_group_id'];
			if( !array_key_exists( $group, $group_master ))
			{	// 取得したグループマスタ情報にグループがない場合は公開期間外と考える
				continue;
			}
			if( empty( $group_master['date_end'] ))
			{	// 期限なし
				$date_end = '9999-12-31 23:59:59';
				$remain = -1;
			}
			else
			{	// 期限あり
				$date_end = $group_master['date_end'];
				$remain = strtotime( $date_end ) - $now_timestamp;
			}
			$achieve_medal[$group] = array(
				'ach_id' => $ach_id,
				'ach_group_id' => $group,
				'category' => $group_master[$group]['category'],
				'condition' => $v['cond_value'],
				'date_end' => $date_end,
				'remain' => $remain
			);
		}

		// 返却データに作成したデータを上書きする
		if( !empty( $buff ))
		{
			foreach( $buff as $group => $v )
			{
				if( empty( $v ))
				{	// 最高ランクの勲章まで獲得したので表示しない
					unset( $achieve_medal[$group] );	// 要素を削除
				}
				else
				{	// 勲章情報を差し替え
					$achieve_medal[$group]['ach_id'] = $v['ach_id'];
					$achieve_medal[$group]['condition'] = $v['cond_value'];
				}
			}
		}

		return $achieve_medal;
	}

	/**
	 * 指定の日時以降に獲得した勲章の一覧を取得
	 * 
	 * @param $pp_id サイコパスID
	 * @param $view_datetime 基準日時
	 *
	 * @return array 獲得勲章一覧情報 | null:エラー
	 */
	function getAchievementNewComplete( $pp_id, $view_datetime, $dns = "db_r" )
	{
		$param = array( $pp_id, $view_datetime );
		$sql = "SELECT * FROM ut_user_achievement_rank "
			 . "WHERE pp_id = ? AND date_created > ?";
		return $this->$dns->GetAll( $sql, $param );
	}
}
?>
