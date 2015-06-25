<?php
/**
 *	Api/Tutorial/Set.php
 *	チュートリアル情報の設定
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_tutorial_set Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiTutorialSet extends Pp_ApiActionForm
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
		'tutorial_id' => array(
			// Form definition
			'type'        => VAR_TYPE_INT, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => null,           // Maximum value
		),
	);
}

/**
 *	api_tutorial_set action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiTutorialSet extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_tutorial_set Action.
	 *
	 *	@access public
	 *	@return string	  forward name(null: success.
	 *								  false: in case you want to exit.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{
			return 'error_400';
		}
		
		return null;
	}

	/**
	 *	api_tutorial_set action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );

		$tutorial_id = $this->af->get( "tutorial_id" );
		
		$user_m =& $this->backend->getManager( "User" );
		
		$result = $user_m->updateUserTutorial( $pp_id, $tutorial_id );
		
		if ( !$result || Ethna::isError( $result ) ) {
			return 'error_500';
		}
		
		$tutorial = $user_m->getUserTutorial( $pp_id, "db" );
		
		$this->af->setApp( "user_tutorial", array( "tutorial" => $tutorial['flag'] ) );
		
		return 'api_json_encrypt';
	}
}
