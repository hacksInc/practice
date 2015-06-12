<?php
/**
 *	Api/Mission/List.php
 *	ミッション一覧取得
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_mission_list Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiMissionList extends Pp_ApiActionForm
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
	);
}

/**
 *	api_mission_list action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiMissionList extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_mission_list Action.
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
	 *	api_mission_list action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		// マネージャのインスタンスを取得
		$mission_m =& $this->backend->getManager( 'Mission' );
		$user_m =& $this->backend->getManager( "User" );
		
		// マスターを取得
		$m_stage = $mission_m->getMasterStageList();
		$m_area = $mission_m->getMasterAreaList();
		$m_mission = $mission_m->getMasterMissionList();
		
		// 開催中のイベントエリアマスタを取得
		$m_event_area = $mission_m->getMasterEventAreaListAssoc();
		
		// ユーザーの進捗を取得
		$stage_list = $user_m->getUserStageList( $pp_id, "db" );
		
		// 現在開催中のエリアIDを取得
		$held_area_ids = $mission_m->getHeldAreaIds();
		$area_list = $user_m->getUserAreaListAssoc( $pp_id, $held_area_ids, "db" );
		
		$today = strtotime( strftime( "%Y-%m-%d" ));	// 今日のタイムスタンプ
		$played_daily_area = array();
		$is_release_area = false;						// 新規に解放したエリアがあるか？
		foreach( $m_event_area as $area_id => $row )
		{
			// イベントエリアの情報が作成されているかをチェック
			if( !isset( $area_list[$area_id] ))
			{	// エリア情報がない場合はまだプレイしていない
				$db =& $this->backend->getDB();
				$db->begin();

				$ret = $user_m->releaseNewArea( $pp_id, $area_id, $row['area_stress_def'] );
				if( $ret !== true )
				{
					$db->rollback();
					$this->backend->logger->log( LOG_ERR, "releaseNewArea(): pp_id={$pp_id}, area_id={$area_id}" );
					error_log( 'ERROR:' . __FILE__ . ':' . __LINE__ . ":pp_id={$pp_id}, area_id={$area_id}" );
					$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
					return 'error_500';
				}

				$db->commit();
				$is_release_area = true;
				continue;
			}

			// デイリーエリア（１日１回のみのプレイ）の中で既にプレイしたエリアを除外
			if( $row['type'] != Pp_MissionManager::AREA_TYPE_DAILY )
			{	// デイリーミッション以外はチェックしない
				continue;
			}

			$last_play = strtotime( $area_list[$area_id]['date_last_play'] );	// 最後にプレイした日時
			if( $today > $last_play )
			{	// 今日はまだ未プレイ
				continue;
			}

			// 今日はもうプレイしている
			$played_daily_area[] = $area_id;
		}

		// ユーザーの情報を再取得
		if( $is_release_area == true )
		{	// 新規にユーザーエリア情報が追加されていたら取得し直す
			$area_list = $user_m->getUserAreaListAssoc( $pp_id, $held_area_ids, "db" );
		}
		if( !empty( $played_daily_area ))
		{	// 今日既にプレイ済みのデイリーエリアを削除
			foreach( $played_daily_area as $area_id )
			{
				unset( $area_list[$area_id] );
			}
		}
		$held_mission_master = $mission_m->getMasterMissionListAssocByAreaIds( $held_area_ids );
		$held_mission_ids = array_keys( $held_mission_master );		// 開催中のミッションIDの一覧を取得
		$mission_list = $user_m->getUserMissionList( $pp_id, $held_mission_ids, "db" );

		$game = $user_m->getUserGame( $pp_id );
		
		// エリアストレス平均値
		$area_stress = $mission_m->getAverageAreaStressAssoc( $pp_id );

		// データを整形
		$mission_send = array();
		$user_mission_send = array();
		foreach ( $mission_list as $row ) {
			$area_id = $m_mission[$row['mission_id']]['area_id'];
			if( $m_area[$area_id]['type'] != Pp_MissionManager::AREA_TYPE_KARANOMORI )
			{	// 唐之杜ミッションは除外
				$mission_send[$row['mission_id']] = array(
					"mission_id"	=> $row['mission_id'],
					"area_id"		=> $area_id,
				);
			}

			$user_mission_send[] = array(
				"stage_id"		=> $m_area[$area_id]['stage_id'],
				"area_id"		=> $area_id,
				"mission_id"	=> $row['mission_id'],
				"best_clear"	=> $row['best_clear'],
				"normal_clear"	=> $row['normal_clear'],
				"fail"			=> $row['fail'],
			);
		}
		$mission_send[$game['mission_id']] = array(
			"mission_id"	=> $game['mission_id'],
			"area_id"		=> $m_mission[$game['mission_id']]['area_id'],
		);
		sort( $mission_send );
		
		// データを整形
		$area_send = array();
		foreach ( $area_list as $area_id => $row ) {
			if( $m_area[$area_id]['type'] == Pp_MissionManager::AREA_TYPE_KARANOMORI )
			{	// 唐之杜エリアは除外
				continue;
			}
			$area_send[] = array(
				"area_id"		=> $area_id,
				"stage_id"		=> $m_area[$area_id]['stage_id'],
				"type"			=> $m_area[$area_id]['type'],
				"area_stress"	=> $row['area_stress'],
				"psychohazard"	=> $row['status']
			);
		}

		// データを整形
		$stage_send = array();
		foreach ( $stage_list as $row ) {
			if ( !is_array( $m_stage[$row['stage_id']]['karanomori_find_prob'] ) ) $m_stage[$row['stage_id']]['karanomori_find_prob'] = explode( ",", $m_stage[$row['stage_id']]['karanomori_find_prob'] );
			$avg = isset( $area_stress[$row['stage_id']] ) ? $area_stress[$row['stage_id']] : 0;
			$stage_send[] = array(
				"stage_id"			=> $row['stage_id'],
				"ave_area_stress"	=> $avg,
				"investigate_rate"	=> ( $row['karanomori_report'] <= 0 ) ? 0 : $m_stage[$row['stage_id']]['karanomori_find_prob'][$row['karanomori_report'] - 1],
			);
		}
		
		// 取得したデータをクライアントに返す
		$this->af->setApp( 'stage_list', $stage_send, true );
		$this->af->setApp( 'area_list', $area_send, true );
		$this->af->setApp( 'mission_list', $mission_send, true );
		$this->af->setApp( 'user_mission', $user_mission_send, true );

		return 'api_json_encrypt';
	}
}
