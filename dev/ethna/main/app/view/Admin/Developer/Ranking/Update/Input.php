<?php
/**
 *  Admin/Developer/Ranking/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_ranking_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperRankingUpdateInput extends Pp_AdminViewClass
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
		$ranking_id = $this->af->get('ranking_id');

		// ランキングマスターの取得
		$master = $ranking_m->getMasterRanking( $ranking_id );

		$this->af->setApp( 'master', $master );
		$this->af->setApp( 'target_type_options', $ranking_m->TARGET_TYPE_OPTIONS );
    }
}

?>