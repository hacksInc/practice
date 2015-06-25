<?php
/**
 *  Portal/Votingresult.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  portal_votingresult Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_PortalExecvoting extends Pp_PortalWebViewActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'item_id'	=> array(
			"type"		=> VAR_TYPE_STRING,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		),
		'item_id2'	=> array(
			"type"		=> VAR_TYPE_STRING,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		),
		'point'		=> array(
			"type"		=> VAR_TYPE_INT,
			"required"	=> true,
			"min"		=> 1,
			"max"		=> null,
		),
    );
}

/**
 *  portal_votingresult action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_PortalExecvoting extends Pp_PortalWebViewActionClass
{
    /**
     *  preprocess of portal_votingresult Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
		if ($this->af->validate() > 0) {
			return 'portal_error_default';
		}
		
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$item_id_1 = $this->af->get( "item_id" );
		$item_id_2 = $this->af->get( "item_id2" );
		$point = $this->af->get( "point" );
		
		$user_m =& $this->backend->getManager( "User" );
		$pvoting_m =& $this->backend->getManager( "PortalVoting" );
		
		if ( Pp_PortalVotingManager::IS_CLOSE ) {
			return 'portal_votingresult';
		}
		
		if ( Pp_PortalVotingManager::VOTING_END < date( "Y-m-d H:i:s" ) ) {
			return 'portal_votingsum';
		}
		
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		
		if ( $voting['point'] < $point ) {
			return 'portal_error_default';
		}
		
		// 名前から数値に変換
		$m_voting = $pvoting_m->getMasterVotingList();
		
		$m_voting_n = array();
		foreach ( $m_voting as $row ) {
			$m_voting_n[$row['item_name']] = $row;
		}
		
		$this->af->set( "item_id", $m_voting_n[$item_id_1]['item_id'] );
		$this->af->set( "item_id2", $m_voting_n[$item_id_2]['item_id'] );
		
		return null;
    }

    /**
     *  portal_votingresult action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$item_id_1 = $this->af->get( "item_id" );
		$item_id_2 = $this->af->get( "item_id2" );
		$point = $this->af->get( "point" );
		
		$pvoting_m =& $this->backend->getManager( "PortalVoting" );
		
		if ( Ethna::isError( $pvoting_m->execVoting( $pp_id, Pp_PortalVotingManager::VOTING_ID, $item_id_1, $item_id_2, $point ) ) ) {
			return 'portal_error_default';
		}
		
        return 'portal_execvoting';
    }
}
?>