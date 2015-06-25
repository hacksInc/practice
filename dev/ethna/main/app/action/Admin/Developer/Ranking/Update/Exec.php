<?php
/**
 *  Admin/Developer/Ranking/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Pp_Form_AdminDeveloperRanking.php';

/**
 *  admin_developer_ranking_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingUpdateExec extends Pp_Form_AdminDeveloperRanking
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'ranking_id',
		'title',
		'subtitle',
		'target_type',
		'targets',
		'processing_type',
		'clear_target_dungeon_rank3',
		'clear_target_dungeon_rank4',
		'threshold',
		'view_ranking_top',
		'view_higher',
		'view_lower',
		'date_start',
		'date_end',
		'banner_url',
		'url',
    );
}

/**
 *  admin_developer_ranking_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingUpdateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_ranking_update_exec Action.
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
			return 'admin_developer_ranking_update_input';
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }

    /**
     *  admin_developer_ranking_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$ranking_m =& $this->backend->getManager( 'AdminRanking' );
		$target_type = intval( $this->af->get( 'target_type' ));

		if( $target_type === Pp_RankingManager::TARGET_TYPE_HELPER )
		{	// ヘルパー回数 or レイドランキングポイント
			$targets = null;
			$processing_type = null;
			$clear_target_dungeon_rank3 = null;
			$clear_target_dungeon_rank4 = null;
		}
		else if( $target_type === Pp_RankingManager::TARGET_TYPE_RAID_RANKING )
		{
			$targets = null;
			$processing_type = null;

			// カンマ区切り文字列内の空白を除去
			$clear_target_dungeon_rank3 = str_replace( array( ' ', '　' ), '',  $this->af->get( 'clear_target_dungeon_rank3' ));
			$clear_target_dungeon_rank4 = str_replace( array( ' ', '　' ), '',  $this->af->get( 'clear_target_dungeon_rank4' ));

			// 最初や最後が','の場合は削除しておく
			$clear_target_dungeon_rank3 = trim( $clear_target_dungeon_rank3, ',' );
			$clear_target_dungeon_rank4 = trim( $clear_target_dungeon_rank4, ',' );
		}
		else
		{
			$clear_target_dungeon_rank3 = null;
			$clear_target_dungeon_rank4 = null;

			// カンマ区切り文字列内の空白を除去
			$targets = str_replace( array( ' ', '　' ), '',  $this->af->get( 'targets' ));
			$processing_type = str_replace( array( ' ', '　' ), '',  $this->af->get( 'processing_type' ));

			// 最初や最後が','の場合は削除しておく
			$targets = trim( $targets, ',' );
			$processing_type = trim( $processing_type, ',' );
		}

		$columns = array(
			'ranking_id' => $this->af->get( 'ranking_id' ),
			'title' => $this->af->get( 'title' ),
			'subtitle' => $this->af->get( 'subtitle' ),
			'target_type' => $target_type,
			'targets' => $targets,
			'processing_type' => $processing_type,
			'clear_target_dungeon_rank3' => $clear_target_dungeon_rank3,
			'clear_target_dungeon_rank4' => $clear_target_dungeon_rank4,
			'threshold' => $this->af->get( 'threshold' ),
			'view_ranking_top' => $this->af->get( 'view_ranking_top' ),
			'view_higher' => $this->af->get( 'view_higher' ),
			'view_lower' => $this->af->get( 'view_lower' ),
			'date_start' => $this->af->get( 'date_start' ),
			'date_end' => $this->af->get( 'date_end' ),
			'banner_url' => $this->af->get( 'banner_url' ),
			'url' => $this->af->get( 'url' ),
		);

		$ret = $ranking_m->updateMasterRanking( $columns );
		if( !$ret || Ethna::isError( $ret ))
		{
			return 'admin_error_500';
		}
        return 'admin_developer_ranking_update_exec';
    }
}

?>