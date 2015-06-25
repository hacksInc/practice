<?php
/**
 *  Resource/Event/Score.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  resource_event_score view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceEventScore extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$pvoting_m =& $this->backend->getManager( "PortalVoting" );
		$raid_m =& $this->backend->getManager( "Raid" );
		
		$raid = $raid_m->getRaidTotal( 1 );
		
		$time_limit = $pvoting_m->getTimeLimit( strtotime( "2015-04-07 15:00:00" ) );
		
		$this->af->setApp( "raid", $raid );
		$this->af->setApp( "time_limit", $time_limit );
    }
}
?>