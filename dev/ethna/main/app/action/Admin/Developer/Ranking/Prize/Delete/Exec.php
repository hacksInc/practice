<?php
/**
 *  Admin/Developer/Ranking/Prize/Delete/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../Pp_Form_AdminDeveloperRanking.php';

/**
 *  admin_developer_ranking_prize_delete_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingPrizeDeleteExec extends Pp_Form_AdminDeveloperRanking
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'id',
		'ranking_id',
    );
}

/**
 *  admin_developer_ranking_prize_delete_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingPrizeDeleteExec extends Pp_AdminActionClass
{
    /**
     *  admin_developer_ranking_prize_delete_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
	function perform()
	{
		$ranking_prize_m =& $this->backend->getManager( 'AdminRankingPrize' );
		$id = $this->af->get( 'id' );
		$this->af->setApp( 'ranking_id', $this->af->get( 'ranking_id' ));

		$ret = $ranking_prize_m->deleteRankingPrize( $id );
		if( !$ret || Ethna::isError( $ret ))
		{
			return 'admin_error_500';
		}
		return 'admin_developer_ranking_prize_delete_exec';
    }
}

?>