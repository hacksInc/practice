<?php
/**
 *  Admin/Developer/Ranking/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Pp_Form_AdminDeveloperRanking.php';

/**
 *  admin_developer_ranking_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingUpdateConfirm extends Pp_Form_AdminDeveloperRanking
{
	var $form = array(
		'ranking_id',
		'title' => array(
			'required' => true,
		),
		'subtitle',
		'target_type' => array(
			'required' => true,
		),
		'targets' => array(
			'custom' => 'checkRequiredTargets,checkTargetFormat',
			'filter' => 'space_zentohan,numeric_zentohan,ltrim,rtrim',
		),
		'processing_type' => array(
			'custom' => 'checkRequiredProcessingType',
			'filter' => 'space_zentohan,numeric_zentohan,ltrim,rtrim',
		),
		'clear_target_dungeon_rank3' => array(
			'custom' => 'checkClearTargetDungeonFormat',
			'filter' => 'space_zentohan,numeric_zentohan,ltrim,rtrim',
		),
		'clear_target_dungeon_rank4' => array(
			'custom' => 'checkClearTargetDungeonFormat',
			'filter' => 'space_zentohan,numeric_zentohan,ltrim,rtrim',
		),
		'threshold' => array(
			'required' => true,
			'min' => 1,
		),
		'view_ranking_top' => array(
			'required' => true,
			'min' => 0,
		),
		'view_higher' => array(
			'required' => true,
			'min' => 0,
		),
		'view_lower' => array(
			'required' => true,
			'min' => 0,
		),
		'date_start' => array(
			'required' => true,
			'filter'   => 'space_zentohan,numeric_zentohan,ltrim,rtrim',
		),
		'date_end' => array(
			'required' => true,
			'filter'   => 'space_zentohan,numeric_zentohan,ltrim,rtrim',
			'custom'   => 'checkRankingDatetimeEnd',
		),
		'banner_url' => array(
			'custom'   => 'checkURL, checkHostEnv',
		),
		'url' => array(
			'custom'   => 'checkURL, checkHostEnv',
		),
	);
}

/**
 *  admin_developer_ranking_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingUpdateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_ranking_update_confirm Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			if ($ret == 'admin_error_400') {
				$this->af->setApp( 'ranking_id', $this->af->get( 'ranking_id' ));
				$this->af->setApp( 'title', $this->af->get( 'title' ));
				$this->af->setApp( 'subtitle', $this->af->get( 'subtitle' ));
				$this->af->setApp( 'target_type', $this->af->get( 'target_type' ));
				$this->af->setApp( 'targets', $this->af->get( 'targets' ));
				$this->af->setApp( 'processing_type', $this->af->get( 'processing_type' ));
				$this->af->setApp( 'clear_target_dungeon_rank3', $this->af->get( 'clear_target_dungeon_rank3' ));
				$this->af->setApp( 'clear_target_dungeon_rank4', $this->af->get( 'clear_target_dungeon_rank4' ));
				$this->af->setApp( 'threshold', $this->af->get( 'threshold' ));
				$this->af->setApp( 'view_ranking_top', $this->af->get( 'view_ranking_top' ));
				$this->af->setApp( 'view_higher', $this->af->get( 'view_higher' ));
				$this->af->setApp( 'view_lower', $this->af->get( 'view_lower' ));
				$this->af->setApp( 'date_start', $this->af->get( 'date_start' ));
				$this->af->setApp( 'date_end', $this->af->get( 'date_end' ));
				$this->af->setApp( 'banner_url', $this->af->get( 'banner_url' ));
				$this->af->setApp( 'url', $this->af->get( 'url' ));
				return 'admin_developer_ranking_update_input';
			} else {
				return $ret;
			}
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }

    /**
     *  admin_developer_gacha_ranking_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_ranking_update_confirm';
    }
}

?>