<?php
/**
 *	Api/Achievement/List.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_achievement_list Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiAchievementList extends Pp_ApiActionForm
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
		'c'
	);
}

/**
 *	api_achievement_list action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiAchievementList extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_achievement_list Action.
	 *
	 *	@access public
	 *	@return string	  forward name(null: success.
	 *								  false: in case you want to exit.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{
			return 'error_400';
		}
		return null;
	}

	/**
	 *	api_achievement_list action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );			// サイコパスID
		$api_transaction_id = $this->getApiTransactionId();				// トランザクションID

		//$pp_id = 915694803;
		//$api_transaction_id = time();

		// マネージャのインスタンスを取得
		$user_m =& $this->backend->getManager( 'User' );
		$ach_m =& $this->backend->getManager( 'Achievement' );
		$trans_m =& $this->backend->getManager( 'Transaction' );
		$present_m =& $this->backend->getManager( 'Present' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );

		// ここでログインのチェックを行う（当日再ログインはスルーされる）
		$ret = $user_m->updateUserGameLogin( $pp_id, false );
		if( $ret !== true )
		{	// 更新エラー
			$this->backend->logger->log( LOG_ERR, 'error: updateUserGameLogin( '.$pp_id.', false )' );
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// 多重処理防止チェック
		$json = $trans_m->getResultJson( $api_transaction_id );
		if( !empty( $json ))
		{	// 既に一度処理している
			$this->backend->logger->log( LOG_INFO, 'Found api_transaction_id.' );
			$temp = json_decode( $json, true );
			foreach( $temp as $k => $v )
			{
				$this->af->setApp( $k, $v, true );
			}
			return 'api_json_encrypt';
		}

		// ユーザーゲーム情報を取得
		$user_game = $user_m->getUserGame( $pp_id );
		if( empty( $user_game ))
		{
			$this->backend->logger->log( LOG_ERR, 'Not found user_game. pp_id='.$pp_id );
			return 'error_500';
		}

		// 勲章グループマスタを取得
		$group_master = $ach_m->getMasterAchievementGroupListAssoc();
		if( empty( $group_master ))
		{
			$this->backend->logger->log( LOG_ERR, 'Not found group_master.' );
			return 'error_500';
		}

		// 勲章条件マスタを取得
		//error_log( 'group_master: '.print_r( $group_master, true ) );
		$group_ids = array_keys( $group_master );
		//error_log( 'group_ids: '.print_r( $group_ids, true ) );
		$cond_master = $ach_m->getMasterAchievementConditionByGroupIdAssoc( $group_ids );
		if( empty( $cond_master ))
		{
			$this->backend->logger->log( LOG_ERR, 'Not found cond_master.' );
			return 'error_500';
		}
		//error_log( 'cond_master: '.print_r( $cond_master, true ) );

		// ユーザー実績差分情報を取得
		$ach_count_diff = $user_m->getUserAchievementCountBaseDiff( $pp_id );

		// ユーザー勲章獲得情報を取得
		$ach_rank = $user_m->getUserAchievementRank( $pp_id );
		if(( is_null( $ach_rank ))||( $ach_rank === false ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'getUserAchievementRank(): pp_id='.$pp_id );
			return 'error_500';
		}

		// ログイン回数の勲章を新規獲得していないかを判定
		$get_ach_ids = $this->_checkAchievement( $ach_rank, $cond_master, $ach_count_diff );

		// DBトランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			if( !empty( $get_ach_ids ))
			{
				foreach( $get_ach_ids as $get_ach_id )
				{
					// 獲得したことにより新しく解放される勲章があるか？
					$release_ach_id = $ach_m->getReleaseAchId( $get_ach_id );
					if( Ethna::isError( $release_ach_id ))
					{
						$err_msg = "getReleaseAchId() error!: pp_id={$pp_id}, get_ach_id={$get_ach_id}";
						$err_code = SDC_DB_ERROR;
						throw new Exception( $err_msg, $err_code );
					}
					if( !empty( $release_ach_id ))
					{	// 新しく解放される勲章があるよ
						$res = $user_m->insertUserAchievementBaseCount( $pp_id, $release_ach_id );
						if( $res !== true )
						{
							$err_msg = "insertUserAchievementBaseCount() error!: pp_id={$pp_id}, release_ach_id={$release_ach_id}";
							$err_code = SDC_DB_ERROR;
							throw new Exception( $err_msg, $err_code );
						}
					}

					// 同じグループの次のランクの勲章があるか？
					$next_achieve = $ach_m->getMasterAchievementConditionNextRank( $get_ach_id );
					if( !empty( $next_achieve ))
					{	// あるなら次のランクを解放
						$res = $user_m->insertUserAchievementBaseCount( $pp_id, $next_achieve['ach_id'] );
						if( $res !== true )
						{
							$err_msg = "insertUserAchievementBaseCount() error!: pp_id={$pp_id}, next_ach_id=".$next_achieve['ach_id'];
							$err_code = SDC_DB_ERROR;
							throw new Exception( $err_msg, $err_code );
						}
					}

					// ユーザー勲章情報を追加
					$res = $user_m->insertUserAchievementRank( $pp_id, $get_ach_id );
					if( $res !== true )
					{	// 追加エラー
						$err_msg = "insertUserAchievementRank() error!: pp_id=$pp_id, get_ach_id=$get_ach_id";
						$err_code = SDC_DB_ERROR;
						throw new Exception( $err_msg, $err_code );
					}

					// 勲章獲得報酬をプレゼントBOXへ
					$columns = array(
						'comment_id'       => Pp_PresentManager::COMMENT_ACHIEVEMENT,		// 勲章獲得報酬っす！
						'present_category' => $cond_master[$get_ach_id]['reward_category'],	// 報酬のカテゴリ
						'present_value'    => $cond_master[$get_ach_id]['reward_id'],		// ブツのID
						'num'              => $cond_master[$get_ach_id]['reward_num']		// 配布数
					);
					$present_id = $present_m->setUserPresent(
						$pp_id,
						0,
						$columns
					);
					if( Ethna::isError( $present_id ))
					{	// 更新エラー
						$error_detail = "setUserPresent() error!: pp_id=$pp_id, get_ach_id=$get_ach_id";
						throw new Exception( $error_detail, SDC_DB_ERROR );
					}

					// 勲章獲得報酬のプレゼント情報を記録
					$columns = array(
						'pp_id' => $pp_id,
						'api_transaction_id' => $api_transaction_id,				// トランザクションID
						'processing_type' => 'C01',									// 処理コード
						'present_id' => $present_id,								// プレゼントID
						'present_category' => $cond_master[$get_ach_id]['reward_category'],	// 配布物カテゴリ
						'present_value' => $cond_master[$get_ach_id]['reward_id'],	// 配布物ID
						'num' => $cond_master[$get_ach_id]['reward_num'],			// 配布数
						'status' => Pp_PresentManager::STATUS_NEW,					// ステータス
						'comment_id' => Pp_PresentManager::COMMENT_ACHIEVEMENT		// 配布コメント
					);
					$res = $logdata_m->logPresent( $columns );
				}
			}

			// 各勲章グループの次の獲得すべき勲章IDのリストを再取得
			$achieve_medal = array();
			$ach_count_diff = $user_m->getUserAchievementCountBaseDiff( $pp_id, "db" );		// 最新のユーザー実績情報を取得
			if( !empty( $ach_count_diff ))
			{
				$now_timestamp = $_SERVER['REQUEST_TIME'];
				foreach( $ach_count_diff as $ach_id => $count )
				{
					if( !isset( $cond_master[$ach_id] ))
					{
						continue;
					}
					$cond = $cond_master[$ach_id];
					$group_id = $cond['ach_group_id'];
					$group = $group_master[$group_id];

					if(( empty( $group['date_end'] ))||( $group['date_end'] == '0000-00-00 00:00:00' ))
					{	// 期限なし
						$date_end = '9999-12-31 23:59:59';
						$remain = -1;
					}
					else
					{	// 期限あり
						$date_end = $group['date_end'];
						$remain = strtotime( $date_end ) - $now_timestamp;
					}

					$achieve_medal[] = array(
						'ach_id' => $ach_id,
						'ach_group_id' => $group_id,
						'category' => $group['category'],
						'condition' => $cond['cond_value'],
						'limit_time' => $date_end,
						'remain' => $remain,
						'achieve_count' => $ach_count_diff[$ach_id],
						'next_ach_id' => 0	// ここは０固定でセット
					);
				}
			}

			// 最後に参照勲章情報を取得した日時以降に取得した勲章の一覧を取得
			if( empty( $user_game['last_achievement_view'] ) || $user_game['last_achievement_view'] == '0000-00-00 00:00:00' )
			{	// 一度も参照していない
				$view_timestamp = strtotime( $user_game['date_created'] );	// レコード作成日時で代用する
			}
			else
			{	// 過去に参照したことがある
				$view_timestamp = $user_game['last_achievement_view'];		// 参照日時をセット
			}
			$temp = $ach_m->getAchievementNewComplete( $pp_id, $view_timestamp, "db" );
			if( $temp === false )
			{
				$this->backend->logger->log( LOG_ERR, 'getAchievementNewComplete(): pp_id='.$pp_id.', date='.$user_game['last_achievement_view'] );
				return 'error_500';
			}
			$complete_medal = array();
			foreach( $temp as $v )
			{
				$ach_id = $v['ach_id'];
				if( !isset( $cond_master[$ach_id] ))
				{
					continue;
				}
				$cond_data = $cond_master[$ach_id];
				$group_data = $group_master[$cond_data['ach_group_id']];
				$next = $ach_m->getMasterAchievementConditionNextRank( $ach_id );
				$complete_medal[] = array(
					'ach_id' => $ach_id,
					'ach_group_id' => $cond_data['ach_group_id'],
					'category' => $group_data['category'],
					'condition' => $cond_data['cond_value'],
					'next_ach_id' => (( empty( $next )) ? 0 : $next['ach_id'] )
				);
			}

			$new_medal = array();

			// 参照した日時を更新
			$columns = array( 'last_achievement_view' => strftime( "%Y-%m-%d %H:%M:%S", $_SERVER['REQUEST_TIME'] ));
			$ret = $user_m->updateUserGame( $pp_id, $columns );
			if( $ret !== true )
			{	// 更新エラー
				$err_msg = 'updateUserGame()';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// 最新のプレゼントBOX情報を取得
			$ret = $present_m->deleteMaxOverUserPresent( $pp_id );
			if(( $ret === false )||( Ethna::isError( $ret )))
			{
				$err_msg = 'deleteMaxOverUserPresent()';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}
			$ret = $present_m->deleteExpiredUserPresent( $pp_id );
			if( Ethna::isError( $ret ))
			{
				$err_msg = 'deleteExpiredUserPresent()';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}
			$present_list = $present_m->getUserPresentList( $pp_id );
			if( is_null( $present_list ) || ( $present_list === false ))
			{
				$err_msg = 'getUserPresentList( '.$pp_id.' )';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}
			$user_box = $present_m->convertUserBox( $present_list );

			// 処理結果をトランザクション情報として記録する
			$buff = array();
			$buff['achieve_medal'] = $achieve_medal;
			$buff['complete_medal'] = $complete_medal;
			$buff['new_medal'] = $new_medal;
			$buff['user_box'] = $user_box;
			$result_json = json_encode( $buff );		// JSON文字列にする
			$res = $trans_m->registTransaction( $pp_id, $api_transaction_id, $result_json );
			if( $res !== true )
			{	// 記録エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			$this->af->setApp( 'achieve_medal', $achieve_medal, true );
			$this->af->setApp( 'complete_medal', $complete_medal, true );
			$this->af->setApp( 'new_medal', $new_medal, true );
			$this->af->setApp( 'user_box', $user_box, true );

			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollback();		// 更新をロールバックする
			return 'error_500';
		}

		return 'api_json_encrypt';
	}


	function _checkAchievement( $ach_rank, $cond_master, $ach_count_diff )
	{
		$checked = array();

		// 獲得済みの勲章IDを配列にまとめる
		$temp = array();
		if( !empty( $ach_rank ))
		{
			foreach( $ach_rank as $v )
			{
				$temp[] = $v['ach_id'];
			}
		}

		$get_ach_ids = array();
		foreach( $cond_master as $ach_id => $cond )
		{
			error_log( 'ach_id : '.$ach_id );
			if( $cond['type'] != '16' )
			{	// ログイン関係以外の勲章はチェックしない
				error_log( 'skip1' );
				continue;
			}
			if( in_array( $cond['ach_group_id'], $checked ))
			{	// 今回のチェックで獲得済みのグループ
				error_log( 'skip2' );
				continue;
			}
			if( in_array( $ach_id, $temp ))
			{	// 既に取得済み
				error_log( 'skip3' );
				continue;
			}
			if(( $cond['rank'] > 1 )&&( !isset( $ach_count_diff[$ach_id] )))
			{	// まだ解放されていないもの
				error_log( 'skip4' );
				continue;
			}
			// で、どうよ？条件満たしてる？
			if( $ach_count_diff[$ach_id] >= $cond['cond_value'] )
			{	// 条件を満たしていたら新規獲得
				error_log( 'new_get_ach_id : '.$ach_id );
				$get_ach_ids[] = $ach_id;
				$checked[] = $cond['ach_group_id'];
			}
		}
		return $get_ach_ids;
	}
}
