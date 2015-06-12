<?php
/**
 *	Api/User/Boot.php
 *	ゲーム起動時通信
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  api_user_boot Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiUserBoot extends Pp_ApiActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
	);

	/**
	 *  Form input value convert filter : sample
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	/*
	function _filter_sample($value)
	{
		//  convert to upper case.
		return strtoupper($value);
	}
	*/
}

/**
 *  api_user_boot action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiUserBoot extends Pp_ApiActionClass
{
	// OS種別
	const OS_TYPE_UNKNOWN     = 0;
	const OS_TYPE_IOS         = 1;
	const OS_TYPE_ANDROID     = 2;
	const OS_TYPE_IOS_ANDROID = 3;

	// DBインスタンス
	private $db_cmn = null;
	private $db_logex = null;

	/**
	 *  preprocess of api_user_boot Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		if ($this->af->validate() > 0) {
			return 'error_400';
		}

		return null;
	}

	/**
	 *  api_user_boot action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$db =& $this->backend->getDB();
		$db_cmn =& $this->backend->getDB( "cmn" );
		$db_logex =& $this->backend->getDB( "logex" );

		// クライアントから送信されてきたデータを取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		$api_transaction_id = $this->getApiTransactionId();
		$device_info = $this->af->get( 'device_info' );
		/*
		if( empty( $pp_id ))
		{
			$pp_id = 916150486;
			$pp_id = 912010614;
		}
		*/
		// マネージャのインスタンスを取得
		$user_m =& $this->backend->getManager( 'User' );
		$character_m =& $this->backend->getManager( 'Character' );
		$present_m =& $this->backend->getManager( 'Present' );
		$item_m =& $this->backend->getManager( 'Item' );
		$photo_m =& $this->backend->getManager( 'Photo' );
		$portal_user_m =& $this->backend->getManager( 'PortalUser' );
		$achievement_m =& $this->backend->getManager( 'Achievement' );
		$logdata_m =& $this->backend->getManager( 'Logdata' );
		$news_m =& $this->backend->getManager( 'News' );
		$kpi_m =& $this->backend->getManager( 'Kpi' );
		$photo_gacha_m =& $this->backend->getManager( 'PhotoGacha' );

		//--------------------------------------------------------------------------
		//	定時ストレスケア処理を実行
		//--------------------------------------------------------------------------
		$res = $character_m->stressCare( $pp_id, $api_transaction_id );
		if( Ethna::isError( $res ))
		{
			$this->backend->logger->log( LOG_ERR, 'fixed stress care error.' );
			$this->af->setApp( 'status_detail_code', SDC_FIXED_STRESS_CARE_ERROR, true );
			return 'error_500';
		}

		//--------------------------------------------------------------------------
		//	情報を取得
		//--------------------------------------------------------------------------
		if( empty( $pp_id ))
		{	// サイコパスIDがない
			$this->backend->logger->log( LOG_ERR, 'Empty pp_id.' );
			$this->af->setApp( 'status_detail_code', SDC_USER_NONEXISTENCE, true );
			return 'error_500';
		}

		// ユーザー情報を取得
		$ub = $user_m->getUserBase( $pp_id );
		if( empty( $ub ))
		{
			$this->backend->logger->log( LOG_ERR, "UserBase not found: pp_id=$pp_id" );
			$this->af->setApp( 'status_detail_code', SDC_USER_NONEXISTENCE, true );
			return 'error_500';
		}

		$ug = $user_m->getUserGame( $pp_id );
		if( empty( $ug ))
		{
			$this->backend->logger->log( LOG_ERR, "UserGame not found: pp_id=$pp_id" );
			$this->af->setApp( 'status_detail_code', SDC_USER_NONEXISTENCE, true );
			return 'error_500';
		}

		$ui = $user_m->getUserIngame( $pp_id );
		if( is_null( $ui ) || ( $ui === false ))
		{
			$this->backend->logger->log( LOG_ERR, "UserIngame not found: pp_id=$pp_id" );
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		$uc = $character_m->getUserCharacter( $pp_id );
		if( empty( $uc ))
		{
			$this->backend->logger->log( LOG_ERR, "UserCharacter not found: pp_id=$pp_id" );
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// フォト情報を取得
		$photo = $photo_m->getUserPhotoByType( $pp_id, 'all' );
		if( is_null( $photo ) || ( $photo === false ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'UserPhoto not found.' );
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		if( empty( $photo ))
		{	// 所有フォトなし
			$photo_master = null;
		}
		else
		{	// 所有フォトあり
			$temp = array();
			foreach( $photo as $v )
			{
				$temp[] = $v['photo_id'];
			}

			// 所有しているフォトのマスタ情報を取得
			$photo_master = $photo_m->getMasterPhotoByPhotoIdsEx( $temp );
			if( empty( $photo_master ))
			{	// 取得エラー
				$this->backend->logger->log( LOG_ERR, 'PhotoMaster not found.' );
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'error_500';
			}
		}

		// 所有アイテム情報を取得
		$item = $item_m->getUserItemList( $pp_id );
		if( is_null( $item ) || Ethna::isError( $item ))
		{
			$this->backend->logger->log( LOG_ERR, 'UserItem not found.' );
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// チュートリアル情報を取得
		$tutorial = $user_m->getUserTutorial( $pp_id );
		if( empty( $tutorial ))
		{
			$this->backend->logger->log( LOG_ERR, 'Tutorial not found.' );
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// ポータルのユーザー情報を取得
		$portal_user = $portal_user_m->getUserBase( $pp_id );
		if( empty( $portal_user ))
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'PortalUser not found.' );
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// 勲章グループマスタを取得
		$group_master = $achievement_m->getMasterAchievementGroupListAssoc();
		if( empty( $group_master ))
		{
			$this->backend->logger->log( LOG_ERR, 'Not found group_master.' );
			return 'error_500';
		}

		// 勲章条件マスタを取得
		$group_ids = array_keys( $group_master );
		$cond_master = $achievement_m->getMasterAchievementConditionByGroupIdAssoc( $group_ids );
		if( empty( $cond_master ))
		{
			$this->backend->logger->log( LOG_ERR, 'Not found cond_master.' );
			return 'error_500';
		}

		// メインバナーリストの一覧を取得
		$homebanner_list = $news_m->getCurrentHomeBannerListForApiResponse(
			Pp_NewsManager::HOME_BANNER_DISP_STS_NORMAL, null, $ub['device_type']
		);
		if( !empty( $homebanner_list ))
		{
			foreach( $homebanner_list as $index => $key )
			{	// カンマ区切りの文字列を配列に変換
				$homebanner_list[$index]['banner_attribute_value'] = explode( ',', $homebanner_list[$index]['banner_attribute_value'] );
			}
		}

		// クリア済みミッションIDの取得
		$mission_ids = $user_m->getClearedMissionIdList( $pp_id );
		if( is_null( $mission_ids ))
		{	// 取得エラー
			return 'error_500';
		}

		// 解放済みガチャを取得
		$gacha = $photo_gacha_m->getMasterPhotoGachaAvailable( $mission_ids );

		//--------------------------------------------------------------------------
		//	ゲーム側BANチェック
		//--------------------------------------------------------------------------
		if( !empty( $ub['ban_limit'] ))
		{	// アクセス禁止期間が設定されている
			if( strtotime( $ub['ban_limit'] ) > time())
			{	// まだだ、まだ終わらんよ！
				$this->af->setApp( 'status_detail_code', SDC_USER_ACCESS_BAN, true );	// というわけで番場蛮
				return 'error_500';
			}
			// もう許してやるか
		}

		//--------------------------------------------------------------------------
		//	アクティブなプレゼント配布の処理
		//--------------------------------------------------------------------------
		//プレゼント配布の処理
		//アクティブなプレゼント配布データを取得
		$present_mng = $present_m->getPresentMngListTerm( );
		//error_log(print_r($present_mng,true));
		//データがあれば
		if (count($present_mng) > 0) {

            // 既に配布済一覧を前もってDBへ問い合わせをし複数回クエリを呼ばないようにする
			$present_mng_ids = array();
			foreach ($present_mng as $pval)
			{
				$present_mng_ids[] = $pval['present_mng_id'];
			}

			$tmp_presented_list = $present_m->getUserPresentmngids($pp_id, $present_mng_ids);
			$presented_list = array();
			foreach ($tmp_presented_list as $presented)
			{
				// 後々中身は使わないので、boolean値を代入
				$presented_list[ $presented['present_mng_id'] ] = true;
			}
			unset($tmp_presented_list);

            foreach($present_mng as $pval) {
				//error_log(print_r($pval,true));
				//既に配布済みかチェック
				$presented = (array_key_exists($pval['present_mng_id'], $presented_list)) ? $presented_list[ $pval['present_mng_id'] ] : null;
				//配布した形跡がなければ配布する
				if (empty($presented)) {
                    if (($pval['target_type'] == Pp_PresentManager::TARGET_TYPE_ALL)
                     || ($pval['target_type'] == Pp_PresentManager::TARGET_TYPE_PPID && $pval['pp_id'] === $pp_id)
                     || ($pval['target_type'] == Pp_PresentManager::TARGET_TYPE_TERM && $pval['access_date_start'] <= $ug['last_login'] && $ug['last_login'] <= $pval['access_date_end'])){
						// 勲章獲得報酬をプレゼントBOXへ
						$columns = array(
							'comment_id'       => $pval['comment_id'],
							'present_mng_id'   => $pval['present_mng_id'],
							'present_category' => $pval['present_category'],
							'present_value'    => $pval['present_value'],
							'num'              => $pval['num']
						);

						// トランザクション開始
						$db_cmn->begin();

						// プレゼント配布
						$present_id = $present_m->setUserPresent($pp_id, 0, $columns);
						if( Ethna::isError( $present_id ))
						{	// 更新エラー
							$error_detail = "setUserPresent() error!: pp_id=$pp_id";
							throw new Exception( $error_detail, SDC_DB_ERROR );
						}

						//受け取った人数+1
						$ret = $present_m->incPresentMngCnt($pval['present_mng_id']);
						if (!$ret || Ethna::isError($ret)) {
							$db_cmn->rollback();
							error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':error_500:[' . $pp_id .']');
							$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
							return 'error_500';
						}

						// トランザクション完了
						$db_cmn->commit();
					}
				}
			}
		}

		//--------------------------------------------------------------------------
		//	送信用に取得データを加工する
		//--------------------------------------------------------------------------
		// ダミーデータ
		$view_info = 1;

		// 次の定時ストレスケアの情報
		$next_stress_care = $user_m->getNextFixedStressCare();

		// 最後に同行したサポートキャラクターID
		$last_support_chara_id = ( empty( $ui['accompany_character_id'] )) ? 0 : $ui['accompany_character_id'];

		// DBトランザクション開始
		$db->begin();
		$db_cmn->begin();
		$db_logex->begin();

		try
		{
			//--------------------------------------------------------------------------
			//	DBの更新
			//--------------------------------------------------------------------------
			// ut_user_gameのログイン日時を更新
			$ret = $user_m->updateUserGameLogin( $pp_id );
			if( $ret !== true )
			{	// 更新エラー
				$err_msg = '';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			// 端末情報の更新
			if(( $device_info['ua'] == self::OS_TYPE_IOS )||( $device_info['ua'] == self::OS_TYPE_ANDROID ))
			{
				$ret = $user_m->updateUserDeviceInfo( $pp_id, $device_info );
				if( $ret !== true )
				{	// 更新エラー
					$err_msg = 'error: updateUserDeviceInfo( '.$pp_id.' )';
					$err_code = SDC_DB_ERROR;
					throw new Exception( $err_msg, $err_code );
				}
			}

			//--------------------------------------------------------------------------
			//	KPI,ログの記録
			//--------------------------------------------------------------------------
			// ユーザーログイン履歴
			$ret = $logdata_m->logUserLogin( $pp_id, $device_info['ua'] );
			if( $ret !== true )
			{	// 更新エラー
				$err_msg = 'error: logUserLogin( '.$pp_id.', '.$device_info['ua'].' )';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			//--------------------------------------------------------------------------
			//	レイドイベント報酬処理
			//--------------------------------------------------------------------------
			$ret = $this->_addRaidReward_20150331( $pp_id );
			if ( $ret !== true )
			{	// 更新エラー
				$err_msg = 'error: _addRaidReward_20150331( '.$pp_id.' )';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}

			//--------------------------------------------------------------------------
			//	勲章獲得チェック（ログイン日時を更新した後で実行すること！）
			//--------------------------------------------------------------------------
			// 獲得できる勲章の一覧を取得
			$ach_rank = $user_m->getUserAchievementRank( $pp_id );
			if( is_null( $ach_rank )||( $ach_rank === false ))
			{	// 取得エラー
				$err_msg = 'error: getUserAchievementRank( '.$pp_id.' )';
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
			}
			$temp = $achievement_m->getAchievementNextTargetList( $ach_rank );
			$user_medal = array();
			foreach( $temp as $v )
			{
				$user_medal[] = $v['ach_id'];	// 勲章IDだけの配列を作る
			}

			$ach_count_diff = $user_m->getUserAchievementCountBaseDiff( $pp_id, "db" );		// 最新のユーザー実績情報を取得
			$get_ach_ids = $this->_checkAchievement( $cond_master, $ach_count_diff, $ach_rank, $achievement_m );
			if( !empty( $get_ach_ids ))
			{	// 新規獲得あり
				$ach_present_ids = array();
				foreach( $get_ach_ids as $ach_id )
				{
					// 獲得したことにより新しく解放される勲章があるか？
					$release_ach_ids = $achievement_m->getReleaseAchId( $ach_id );
					if( Ethna::isError( $release_ach_ids ))
					{
						$err_msg = "getReleaseAchId() error!: pp_id={$pp_id}, ach_id={$ach_id}";
						$err_code = SDC_DB_ERROR;
						throw new Exception( $err_msg, $err_code );
					}
					if( !empty( $release_ach_ids ))
					{	// 新しく解放される勲章があるよ
						foreach( $release_ach_ids as $row )
						{
							$res = $user_m->insertUserAchievementBaseCount( $pp_id, $row['ach_id'] );
							if( $res !== true )
							{
								$err_msg = "insertUserAchievementBaseCount() error!: pp_id={$pp_id}, release_ach_id=".$row['ach_id'];
								$err_code = SDC_DB_ERROR;
								throw new Exception( $err_msg, $err_code );
							}
						}
					}

					// 同じグループの次のランクの勲章があるか？
					$next_achieve = $achievement_m->getMasterAchievementConditionNextRank( $ach_id );
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
					$res = $user_m->insertUserAchievementRank( $pp_id, $ach_id );
					if( $res !== true )
					{	// 追加エラー
						$err_msg = "insertUserAchievementRank() error!: pp_id=$pp_id, ach_id=$ach_id";
						$err_code = SDC_DB_ERROR;
						throw new Exception( $err_msg, $err_code );
					}

					// 勲章獲得報酬をプレゼントBOXへ
					$columns = array(
						'comment_id'       => Pp_PresentManager::COMMENT_ACHIEVEMENT,		// 勲章獲得報酬っす！
						'present_category' => $cond_master[$ach_id]['reward_category'],	// 報酬のカテゴリ
						'present_value'    => $cond_master[$ach_id]['reward_id'],		// ブツのID
						'num'              => $cond_master[$ach_id]['reward_num']		// 配布数
					);
					$present_id = $present_m->setUserPresent(
						$pp_id,
						0,
						$columns
					);
					if( Ethna::isError( $present_id ))
					{	// 更新エラー
						$error_detail = "setUserPresent() error!: pp_id=$pp_id, ach_id=$ach_id";
						throw new Exception( $error_detail, SDC_DB_ERROR );
					}
					$ach_present_ids[$ach_id] = $present_id;

					// 勲章獲得報酬のプレゼント情報を記録
					$columns = array(
						'pp_id' => $pp_id,
						'api_transaction_id' => $api_transaction_id,				// トランザクションID
						'processing_type' => 'C01',									// 処理コード
						'present_id' => $present_id,								// プレゼントID
						'present_category' => $cond_master[$ach_id]['reward_category'],	// 配布物カテゴリ
						'present_value' => $cond_master[$ach_id]['reward_id'],		// 配布物ID
						'num' => $cond_master[$ach_id]['reward_num'],				// 配布数
						'status' => Pp_PresentManager::STATUS_NEW,					// ステータス
						'comment_id' => Pp_PresentManager::COMMENT_ACHIEVEMENT		// 配布コメント
					);
					$res = $logdata_m->logPresent( $columns );
				}
			}

			// 各勲章グループの次の獲得すべき勲章IDのリストを取得
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
			if( empty( $ug['last_achievement_view'] ) || $ug['last_achievement_view'] == '0000-00-00 00:00:00' )
			{	// 一度も参照していない
				$view_timestamp = strtotime( $ug['date_created'] );	// レコード作成日時で代用する
			}
			else
			{	// 過去に参照したことがある
				$view_timestamp = $ug['last_achievement_view'];		// 参照日時をセット
			}
			$temp = $achievement_m->getAchievementNewComplete( $pp_id, $view_timestamp, "db" );
			if( $temp === false )
			{
				$err_msg = 'getAchievementNewComplete(): pp_id='.$pp_id.', date='.$ug['last_achievement_view'];
				$err_code = SDC_DB_ERROR;
				throw new Exception( $err_msg, $err_code );
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
				$next = $achievement_m->getMasterAchievementConditionNextRank( $ach_id );
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

			// プレゼントBOX情報を取得
			$ret = $present_m->deleteMaxOverUserPresent( $pp_id );
			if(( $ret === false )||( Ethna::isError( $ret )))
			{
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'error_500';
			}
			$ret = $present_m->deleteExpiredUserPresent( $pp_id );
			if( Ethna::isError( $ret ))
			{
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'error_500';
			}
			$present = $present_m->getUserPresentList( $pp_id );
			if( is_null( $present ) || ( $present === false ))
			{
				$this->backend->logger->log( LOG_ERR, 'UserPresent not found.' );
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				return 'error_500';
			}

			//--------------------------------------------------------------------------
			//	クライアントへの送信用データを作成
			//--------------------------------------------------------------------------
			// ユーザー情報
			$user_base = array(
				'name' => $ub['name'],								// ユーザー名
				'gender' => $portal_user['sex'],					// 性別
				'crime_coef' => $ug['crime_coef'],					// 犯罪係数
				'body_coef' => $ug['body_coef'],					// 身体係数
				'intelli_coef' => $ug['intelli_coef'],				// 知能係数
				'mental_coef' => $ug['mental_coef'],				// 心的係数
				'ex_stress_care' => $ug['ex_stress_care'],			// 臨時ストレスケア回数
				'last_support_chara_id' => $last_support_chara_id,	// 最後にサポートとして帯同したキャラID
				'date_next_stress_care' => strftime( "%Y-%m-%d %H:%M:%S", $next_stress_care['next_timestamp'] ),			// 次のストレスケア時間
				'rest_stress_care_sec' => ( $next_stress_care['next_timestamp'] - $next_stress_care['base_timestamp'] ),	// ストレスケアに必要な残り秒
				'base_stress_care_date' => strftime( "%Y-%m-%d %H:%M:%S", $next_stress_care['base_timestamp'] ),			// ストレスケアに必要な残り秒の基点日時
				'last_stress_care_index' => (( $next_stress_care['index'] + 3 ) & 0x03 ),									// 最後の実行定時ストレスケアのインデックス
				'view_info' => $view_info,							// INFOを表示するか（１日の最初のアクセス時のみ）
				'age_verification' => $ub['age_verification'],		// 年齢認証
				'ma_purchased' => $ub['ma_purchase'],				// 月間購入金額
				'ma_purchased_max' => $ub['ma_purchase_max'],		// 月間購入金額上限
				'mission_id' => $ug['mission_id']					// 進行中のミッションID
			);

			// サポートキャラ情報
			$support_character = $this->_getSendDataUserCharacter( $uc );

			// フォト所有情報
			$user_photo = $this->_getSendDataUserPhoto( $photo, $photo_master );

			// プレゼントボックス情報
			$user_box = $this->_getSendDataUserBox( $present, $present_m->COMMENT_ID_OPTIONS );

			// 所有アイテム
			$user_item = $this->_getSendDataUserItem( $item );

			// チュートリアル情報
			$user_tutorial = array( 'tutorial' => $tutorial['flag'] );


			$gacha_list = array();
			if( !empty( $gacha ))
			{
				foreach( $gacha as $v )
				{
					$gacha_list[] = array(
						'gacha_id' => $v['gacha_id'],	// ガチャID
						'stage_id' => $v['stage_id'],	// 対象ステージID
						'type' => $v['type'],			// ガチャ種別
						'price' => $v['price']			// 価格（使用するフォトフィルムの数）
					);
				}
			}

			$this->af->setApp( 'user_base', $user_base, true );
			$this->af->setApp( 'support_character', $support_character, true );
			$this->af->setApp( 'user_photo', $user_photo, true );
			$this->af->setApp( 'homebanner_list', $homebanner_list, true );
			$this->af->setApp( 'user_box', $user_box, true );
			$this->af->setApp( 'user_item', $user_item, true );
			$this->af->setApp( 'user_tutorial', $user_tutorial, true );
			$this->af->setApp( 'portal_point', $portal_user['point'], true );
			$this->af->setApp( 'user_medal', $user_medal, true );
			$this->af->setApp( 'achieve_medal', $achieve_medal, true );
			$this->af->setApp( 'complete_medal', $complete_medal, true );
			$this->af->setApp( 'new_medal', $new_medal, true );
			$this->af->setApp( 'gacha_list', $gacha_list, true );

			$db_logex->commit();
			$db_cmn->commit();
			$db->commit();
		}
		catch( Exception $e )
		{	// 例外発生
			$db_logex->rollback();
			$db_cmn->rollback();
			$db->rollback();		// 更新をロールバックする
			return 'error_500';
		}
		
		// KPI処理
		$uym = date( "ym", strtotime( $ub['date_created'] ) );
		if ( $device_info['ua'] == self::OS_TYPE_IOS ) {
			$kpi_m->log( "Apple-ppp-dau", 3, 1, time(), $pp_id, "", "", "" );
			$kpi_m->log( "Apple-ppp-" . $uym . "_install_user_mau", 3, 1, time(), $pp_id, "", "", "" );
		} else {
			$kpi_m->log( "Google-ppp-dau", 3, 1, time(), $pp_id, "", "", "" );
			$kpi_m->log( "Google-ppp-" . $uym . "_install_user_mau", 3, 1, time(), $pp_id, "", "", "" );
		}

		return 'api_json_encrypt';
	}

	//====================================================================================================
	//====================================================================================================
	//====================================================================================================

	/*
		送信用ユーザーキャラクター情報の生成
	*/
	private function _getSendDataUserCharacter( $user_character )
	{
		if( empty( $user_character ))
		{	// サポートキャラがいない
			return array();
		}

		$data = array();
		foreach( $user_character as $v )
		{
			$data[] = array(
				'character_id' => ( int )$v['character_id'],	// キャラクターID
				'crime_coef' => ( int )$v['crime_coef'],		// 犯罪係数
				'body_coef' => ( int )$v['body_coef'],			// 身体係数
				'intelli_coef' => ( int )$v['intelli_coef'],	// 知能係数
				'mental_coef' => ( int )$v['mental_coef'],		// 心的係数
				'ex_stress_care' => ( int )$v['ex_stress_care']	// 臨時ストレスケア回数
			);
		}
		return $data;
	}

	/*
		送信用ユーザー所有フォト情報の生成
	*/
	private function _getSendDataUserPhoto( $photo, $photo_master )
	{
		if( empty( $photo ))
		{	// 所有フォトがなし
			return array();
		}

		$data = array();
		foreach( $photo as $v )
		{
			$data[] = array(
				'photo_id' => ( int )$v['photo_id'],		// フォトID
				'photo_lv' => ( int )$v['photo_lv'],		// フォトLV
			);
		}
		return $data;
	}

	/*
		送信用ユーザー所有アイテム情報の生成
	*/
	private function _getSendDataUserItem( $item )
	{
		if( empty( $item ))
		{	// 所有アイテムなし
			return array();
		}

		$data = array();
		foreach( $item as $k => $v )
		{
			$data[] = array(
				'item_id' => $k,
				'item_num' => $v['num']
			);
		}
		return $data;
	}

	/*
		送信用ユーザーBOX情報の生成
	*/
	private function _getSendDataUserBox( $present, $comment_list )
	{
		if( empty( $present ))
		{	// プレゼントBOXが空なら空の配列を返す
			return array();
		}

		$data = array();
		foreach( $present as $v )
		{
			$data[] = array(
				'present_id' => ( int )$v['present_id'],
				'comment' => $comment_list[$v['comment_id']],
				'present_category' => ( int )$v['present_category'],
				'present_value' => ( int )$v['present_value'],		// 64bitなんでbigintの値をintにキャストしても問題なし
				'num' => ( int )$v['num'],
				'date_created' => $v['date_created'],
			);
		}
		return $data;
	}
	
	/**
	 * レイド報酬獲得処理
	 */
	function _addRaidReward_20150331 ( $pp_id )
	{
		// 期日以降は処理しない
		if ( "2015-04-07 23:59:59" < date( "Y-m-d H:i:s" ) ) return true;
		
		$flag_m =& $this->backend->getManager( "Flag" );
		$logdata_m =& $this->backend->getManager( "Logdata" );
		$present_m =& $this->backend->getManager( "Present" );
		$raid_m =& $this->backend->getManager( "Raid" );
		$user_m =& $this->backend->getManager( "User" );
		
		$reward = array(
			1	=> array( "border" => 315840,  "present_category" => Pp_PresentManager::CATEGORY_PHOTO, "present_value" => 3700300000, "num" => 1, "flag_id" => Pp_FlagManager::USER_FLAG_RAID_REWARD_20150331, "value" => 1, "memo" => "レイド報酬第1段階まで取得" ),
			2	=> array( "border" => 800000,  "present_category" => Pp_PresentManager::CATEGORY_PHOTO, "present_value" => 3700200000, "num" => 1, "flag_id" => Pp_FlagManager::USER_FLAG_RAID_REWARD_20150331, "value" => 2, "memo" => "レイド報酬第2段階まで取得" ),
			3	=> array( "border" => 1200000, "present_category" => Pp_PresentManager::CATEGORY_PHOTO, "present_value" => 3700100000, "num" => 1, "flag_id" => Pp_FlagManager::USER_FLAG_RAID_REWARD_20150331, "value" => 3, "memo" => "レイド報酬第3段階まで取得" ),
			4	=> array( "border" => 1600000, "present_category" => Pp_PresentManager::CATEGORY_PHOTO, "present_value" => 3700200000, "num" => 1, "flag_id" => Pp_FlagManager::USER_FLAG_RAID_REWARD_20150331, "value" => 4, "memo" => "レイド報酬第4段階まで取得" ),
			5	=> array( "border" => 2000000, "present_category" => Pp_PresentManager::CATEGORY_PHOTO, "present_value" => 3700100000, "num" => 1, "flag_id" => Pp_FlagManager::USER_FLAG_RAID_REWARD_20150331, "value" => 5, "memo" => "レイド報酬第5段階まで取得" ),
		);
		
		$flag = $user_m->getUserFlagList( $pp_id, "db" );
		$raid = $raid_m->getRaidTotal( 1 );
		
		foreach ( $reward as $key => $row ) {
			// 総執行数が満たない場合は終了
			if ( $raid < $row['border'] ) break;
			
			// 既に獲得済みならスキップ
			if ( $row['value'] <= $flag[$row['flag_id']]['value'] ) continue;
			
			// プレゼント配布
			$columns = array(
				"comment_id"		=> Pp_PresentManager::COMMENT_EVENT,
				"present_category"	=> $row['present_category'],
				"present_value"		=> $row['present_value'],
				"num"				=> $row['num'],
			);
			$present_id = $present_m->setUserPresent( $pp_id, Pp_PresentManager::ID_NEW_PRESENT, $columns );

			if ( !$present_id || Ethna::isError( $present_id ) ) {
				return false;
			}
			
			$columns = array(
				'pp_id'					=> $pp_id,
				'api_transaction_id'	=> '',			// トランザクションID
				'processing_type'		=> 'C06',								// 処理コード
				'present_id'			=> $present_id,							// プレゼントID
				'present_category'		=> $row['present_category'],	// 配布物カテゴリ
				'present_value'			=> $row['present_value'],			// 配布物ID
				'num'					=> $row['num'],							// 配布数
				'status'				=> Pp_PresentManager::STATUS_NEW,				// ステータス
				'comment_id'			=> Pp_PresentManager::COMMENT_EVENT	// 配布コメント
			);
			$res = $logdata_m->logPresent( $columns );
			if ( !$res || Ethna::isError( $res ) ) {
				return false;
			}
			
			// フラッグ情報追加
			$result = $user_m->insertUserFlag( $pp_id, $row['flag_id'], $row['value'], $row['memo'] );
			if ( !$res || Ethna::isError( $res ) ) {
				return false;
			}
		}
		
		return true;
	}

	/**
	 * 新規に獲得した勲章の一覧を取得
	 *
	 * @param array $ach_cond_master 勲章条件マスタ
	 * @param array $ach_count_diff ユーザー実績差分情報
	 * @param array $ach_rank ユーザー勲章情報
	 * @param array $achievement_m Pp_AchievementManagerのインスタンス
	 *
	 * @return 更新結果
	 */
	private function _checkAchievement( $ach_cond_master, $ach_count_diff, $ach_rank, $achievement_m )
	{
		// 獲得済みの勲章IDを配列にまとめる
		$temp = array();
		if( !empty( $ach_rank ))
		{
			foreach( $ach_rank as $v )
			{
				$temp[] = $v['ach_id'];
			}
		}

		$get_ach_ids = array();	// 新規獲得勲章
		foreach( $ach_cond_master as $ach_id => $cond )
		{
			if( in_array( $ach_id, $temp ))
			{	// 既に取得済み
				continue;
			}

			if( !isset( $ach_count_diff[$ach_id] ))
			{	// まだ解放されていないもの
				continue;
			}

			// で、どうよ？条件満たしてる？
			if( $ach_count_diff[$ach_id] >= $cond['cond_value'] )
			{	// 条件を満たしていたら新規獲得
				$get_ach_ids[] = $ach_id;
			}
		}
		return $get_ach_ids;
	}
}
