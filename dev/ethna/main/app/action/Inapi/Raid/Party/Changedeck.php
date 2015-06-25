<?php
/**
 *  Inapi/Raid/Party/Changedeck.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_changedeck Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyChangedeck extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'user_id',
		'active_team_id'
    );
}

/**
 *  Inapi_raid_party_changesetting action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyChangedeck extends Pp_InapiActionClass
{
	/**
	 *  preprocess of api_raid_party_changedeck Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{	// バリデートエラー
			$this->af->setApp( 'error_detail', 'validate error.', true );
			$this->af->setApp( 'status_detail_code', SDC_INAPI_ARGUMENT_ERROR, true );
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
	    return null;
	}

	/**
	 *  api_raid_party_changedeck action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// nodejsサーバーからのパラメータを取得
		$user_id = $this->af->get( 'user_id' );
		$active_team_id = $this->af->get( 'active_team_id' );

		// マネージャのインスタンスを取得
		$monster_m =& $this->backend->getManager( 'Monster' );
		$user_m =& $this->backend->getManager( 'User' );
		$team_m =& $this->backend->getManager( 'Team' );

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		try
		{
			// アクティブチーム情報を更新
			$ret = $user_m->setUserBase( $user_id, array( 'active_team_id' => $active_team_id ));
			if( $ret !== true )
			{
				$error_detail = "setUserBase( ".$user_id.", array( ".$active_team_id." ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}

			/*
			// アクティブチームのリーダーモンスター情報を取得
			$leader_list = $monster_m->getActiveLeaderList( array( $user_id ));
			if( empty( $leader_list ) === true )
			{	// リーダーモンスターを取得できない
				$error_detail = "getActiveLeaderList( array( ".$user_id." ))";
				$detail_code = SDC_INAPI_DB_ERROR;
				throw new Exception( $error_detail, $detail_code );
			}
			*/

			// デッキ情報を取得
			$active_team_list = $team_m->getUsersActiveTeamList( array( $user_id ));
			$deck_info = array();
			foreach( $active_team_list[$user_id] as $i => $v )
			{
				$deck_info[] = array(
					'position'   => ( int )$v['position'] - 1,	// ポジションはDBの1~5ではなく0~4に直す
					'leader'     => ( int )$v['leader_flg'],
					'monster_id' => ( int )$v['monster_id'],
					'unique_id'  => $v['user_monster_id'],
					'exp'        => ( int )$v['exp'],
					'lv'         => ( int )$v['lv'],
					'skill_lv'   => ( int )$v['skill_lv'],
					'badge_num'  => ( int )$v['badge_num'],
					'badges'     => $v['badges']
				);
				if( $v['leader_flg'] == 1 )
				{	// リーダーモンスター
					$leader_mons_id = ( int )$v['monster_id'];
					$leader_mons_lv = ( int )$v['lv'];
				}
			}

			// フレンドポジションを取得
			$friend_pos_info = $team_m->getUsersActiveTeamFriendPos( array( $user_id ));

			//-------------------------------------------------------------
			//	nodejsに返すパラメータをセット
			//-------------------------------------------------------------
			$this->af->setApp( 'user_id', $user_id, true );
			$this->af->setApp( 'leader_mons_id', $leader_mons_id, true );
			$this->af->setApp( 'leader_mons_lv', $leader_mons_lv, true );
			$this->af->setApp( 'deckInfoList', $deck_info, true );
			$this->af->setApp( 'friend_pos', (( int )$friend_pos_info[$user_id]['position'] - 1 ), true );
			$this->af->setApp( 'result', 1, true );

			$db->commit();		// 問題がなければコミットしてトランザクション終了
		}
		catch( Exception $e )
		{
			$db->rollback();	// エラーなのでロールバックする
			$detail_code = $e->getCode();
			if( empty( $detail_code ) === true )
			{	// コードが設定されていない時は適当な値で返す
				$detail_code = SDC_RAID_ERROR;
			}
			$this->af->setApp( 'status_detail_code', $detail_code, true );
			$this->af->setApp( 'error_detail', $e->getMessage(), true );
			$this->af->setApp( 'result', 0, true );
		}

		return 'inapi_json';
	}
}
?>
