<?php
/**
 *  Inapi/Raid/System/Check.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_InapiActionClass.php';

/**
 *  inapi_raid_system_check Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */

class Pp_Form_InapiRaidSystemCheck extends Pp_InapiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'user_id'
    );
}

/**
 *  Inapi_raid_system_check_ action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_InapiRaidSystemCheck extends Pp_InapiActionClass
{
	/**
	 *  preprocess of inapi_raid_system_check Action.
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
			$this->af->setApp( 'result', 0, true );
			return 'inapi_json';
		}
	    return null;
	}

	/**
	 *  inapi_raid_system_check action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$user_id = $this->af->get( 'user_id' );

		// マネージャのインスタンスを取得
		$user_m =& $this->backend->getManager( 'User' );

		//-------------------------------------------------------------
		//	メンテナンスチェック
		//-------------------------------------------------------------
		$user = ( empty( $user_id ) === true ) ? null : $user_m->getUserBase( $user_id );
		if(( empty( $user ) === true )||( $user['attribute'] != 21 ))
		{	// BTFユーザーでなければメンテナンスチェックを行う
			$gmctrl = $user_m->getGameCtrl();
			if( empty( $gmctrl ) === true )
			{	// 取得エラー
				$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
				$this->af->setApp( 'result', 0, true );
				return 'inapi_json';
			}
			else
			{
				if(( $gmctrl['status'] != Pp_UserManager::GAME_CTRL_STATUS_RUNNING )||
				   ( $gmctrl['raid_status'] != Pp_UserManager::GAME_CTRL_STATUS_RUNNING ))
				{	// メンテナンス中
					$this->af->setApp( 'status_detail_code', SDC_HTTP_503_SERVICE_UNAVAILABLE, true );
					$this->af->setApp( 'result', 0, true );
					return 'inapi_json';
				}
			}
		}

		//-------------------------------------------------------------
		//	nodejsに返すパラメータをセット
		//-------------------------------------------------------------
		$this->af->setApp( 'result', 1, true );

		return 'inapi_json';
	}
}
?>
