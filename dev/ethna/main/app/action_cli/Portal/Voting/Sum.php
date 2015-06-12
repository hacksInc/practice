<?php
/**
 *  Portal/Voting/Sum.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_PortalBaseManager.php';

/**
 *  portal_voting_sum action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_PortalVotingSum extends Pp_ActionClass
{
	function authenticate ()
	{
	}
	
    /**
     *  preprocess of portal_voting_sum Action.
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
     *  portal_voting_sum action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$pvoting_m =& $this->backend->getManager( "PortalVoting" );
		
		$pvoting_m->sumVoting();
		
        return null;
    }
}

?>
