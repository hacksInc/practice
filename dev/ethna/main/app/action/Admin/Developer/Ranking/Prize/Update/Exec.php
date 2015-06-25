<?php
/**
 *  Admin/Developer/Ranking/Prize/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../Pp_Form_AdminDeveloperRanking.php';

/**
 *  admin_developer_ranking_prize_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingPrizeUpdateExec extends Pp_Form_AdminDeveloperRanking
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
	var $form = array(
		'id',
		'ranking_id',
		'distribute_start',
		'distribute_end',
		'prize_type',
		'prize_id',
		'lv',
		'number',
		'status',
	);
}

/**
 *  admin_developer_ranking_prize_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingPrizeUpdateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_ranking_prize_update_exec Action.
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
			return 'admin_developer_ranking_prize_update_input';
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }

    /**
     *  admin_developer_ranking_prize_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$ranking_prize_m =& $this->backend->getManager( 'AdminRankingPrize' );

		$columns = array(
			'id' => $this->af->get( 'id' ),
			'ranking_id' => $this->af->get( 'ranking_id' ),
			'distribute_start' => $this->af->get( 'distribute_start' ),
			'distribute_end' => $this->af->get( 'distribute_end' ),
			'prize_type' => $this->af->get( 'prize_type' ),
			'prize_id' => $this->af->get( 'prize_id' ),
			'lv' => $this->af->get( 'lv' ),
			'number' => $this->af->get( 'number' ),
			'status' => $this->af->get( 'status' ),
		);

		// 無駄な入力はデフォルト値に戻しておく
		switch( intval( $columns['prize_type'] ))
		{
			// アイテム・バッジ・素材
			case Pp_AdminRankingPrizeManager::PRIZE_TYPE_ITEM:
			case Pp_AdminRankingPrizeManager::PRIZE_TYPE_BADGE:
			case Pp_AdminRankingPrizeManager::PRIZE_TYPE_MATERIAL:
				$columns['lv'] = 0;			// LVは不要
				break;

			// 合成メダル・マジカルメダル
			case Pp_AdminRankingPrizeManager::PRIZE_TYPE_COIN:
			case Pp_AdminRankingPrizeManager::PRIZE_TYPE_MEDAL:
				$columns['prize_id'] = 0;	// アイテムIDは不要
				$columns['lv'] = 0;			// LVは不要
				break;

			// モンスター
			default:
		}

		$ret = $ranking_prize_m->updateRankingPrize( $columns );
		if( !$ret || Ethna::isError( $ret ))
		{
			return 'admin_error_500';
		}
		$this->af->setApp( 'ranking_id', $this->af->get( 'ranking_id' ));
		return 'admin_developer_ranking_prize_update_exec';
    }
}

?>