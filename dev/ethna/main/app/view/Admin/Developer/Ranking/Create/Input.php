<?php
/**
 *  Admin/Developer/Ranking/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_ranking_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperRankingCreateInput extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$ranking_m =& $this->backend->getManager( 'AdminRanking' );

		$this->af->setApp( 'target_type_options', $ranking_m->TARGET_TYPE_OPTIONS );
    }
}

?>
