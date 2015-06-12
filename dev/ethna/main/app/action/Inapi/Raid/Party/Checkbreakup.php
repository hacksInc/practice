<?php
/**
 *  Inapi/Raid/Party/Checkbreakup.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';
require_once 'Pp_Form_InapiRaidParty.php';

/**
 *  inapi_raid_party_checkbreakup Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_InapiRaidPartyCheckbreakup extends Pp_Form_InapiRaidParty
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'party_id'
    );
}

/**
 *  Inapi_raid_party_checkbreakup action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidPartyCheckbreakup extends Pp_InapiActionClass
{
	/**
	 *  preprocess of api_raid_party_checkbreakup Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
	    return null;
	}

	/**
	 *  api_raid_party_checkbreakup action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$party_id = $this->af->get( 'party_id' );

		// マネージャのインスタンスを取得
		$raid_quest_m =& $this->backend->getManager( 'RaidQuest' );
		$raid_party_m =& $this->backend->getManager( 'RaidParty' );

		// パーティ情報を取得
		$party = $raid_party_m->getParty( $party_id );

		// 選択中のダンジョンの開催終了時間を取得
		$guerrilla_endtime = $raid_quest_m->getDungeonGuerrillaScheduleTimeEndById( $party['dungeon_id'] );
		if( empty( $guerrilla_endtime ) === true )
		{	// ゲリラ時間外
			$guerrilla_schedules = $raid_quest_m->getMasterGuerrillaScheduleByIds( array( $dungeon_id ));
			if( empty( $guerrilla_schedules ) === false )
			{	// ゲリラ設定があるならゲリラ時間外なのでエラー
				/*
				$error_detail = "outside guerrilla hours.: dungeon_id = ".$dungeon_id;
				$this->af->setApp( 'error_detail', $error_detail, true );
				$this->af->setApp( 'status_detail_code', SDC_INAPI_DUNGEON_OVERTIME, true );
				$this->af->setApp( 'result', 0, true );
				*/
				$this->af->setApp( 'breakup', 0, true );			// 現在のダンジョンは続けられない
				$this->af->setApp( 'end_timestamp', null, true );
				$this->af->setApp( 'result', 1, true );
				return 'inapi_json';
			}

			// ゲリラ設定そのものがない場合は定常開催
			$guerrilla_end_date = $dungeon['date_end'];
		}
		else
		{	// ゲリラ時間内
			$guerrilla_end_date = strftime( "%Y-%m-%d" ).' '.$guerrilla_endtime;
		}
		$end_timestamp = strtotime( $guerrilla_end_date );

		// 他に開催中のダンジョンがあるかをチェック
		$dungeons = $raid_quest_m->getMasterDungeonMixed();
		$breakup = ( empty( $dungeons ) === true ) ? 0 : 1;

		//-------------------------------------------------------------
		//	nodejsに返すパラメータをセット
		//-------------------------------------------------------------
		$this->af->setApp( 'breakup', $breakup, true );
		$this->af->setApp( 'end_timestamp', $end_timestamp, true );
		$this->af->setApp( 'result', 1, true );

		return 'inapi_json';
	}
}
?>
