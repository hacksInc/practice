<?php
/**
 *  Admin/Developer/Ranking/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_ranking_update_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperRankingUpdateConfirm extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_ranking_update_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$ranking_m =& $this->backend->getManager( 'AdminRanking' );

		$target_type = $this->af->get( 'target_type' );
		$this->af->setApp( 'target_type_options', $ranking_m->TARGET_TYPE_OPTIONS );
		$this->af->setApp( 'ranking_id', $this->af->get( 'ranking_id' ));
		$this->af->setApp( 'title', $this->af->get( 'title' ));
		$this->af->setApp( 'subtitle', $this->af->get( 'subtitle' ));
		$this->af->setApp( 'target_type', $target_type );
		$this->af->setApp( 'target_type_str', $ranking_m->TARGET_TYPE_OPTIONS[$target_type] );
		$this->af->setApp( 'targets', $this->af->get( 'targets' ));
		$this->af->setApp( 'processing_type', $this->af->get( 'processing_type' ));
		$this->af->setApp( 'clear_target_dungeon_rank3', $this->af->get( 'clear_target_dungeon_rank3' ));
		$this->af->setApp( 'clear_target_dungeon_rank4', $this->af->get( 'clear_target_dungeon_rank4' ));
		$this->af->setApp( 'threshold', $this->af->get( 'threshold' ));
		$this->af->setApp( 'view_higher', $this->af->get( 'view_higher' ));
		$this->af->setApp( 'view_lower', $this->af->get( 'view_lower' ));
		$this->af->setApp( 'view_ranking_top', $this->af->get( 'view_ranking_top' ));
		$this->af->setApp( 'date_start', $this->af->get( 'date_start' ));
		$this->af->setApp( 'date_end', $this->af->get( 'date_end' ));
		$this->af->setApp( 'banner_url', $this->af->get( 'banner_url' ));
		$this->af->setApp( 'url', $this->af->get( 'url' ));
    }
}

?>