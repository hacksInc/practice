<?php
/**
 *	Api/User/Age/Verification.php
 *	年齢による金額上限確認
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  api_user_age_verification Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiUserAgeVerification extends Pp_ApiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'verification' => array(
			// Form definition
			'type'        => VAR_TYPE_INT,    // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 0,               // Minimum value
			'max'         => 3,            // Maximum value
		),
    );
}

/**
 *  api_user_age_verification action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiUserAgeVerification extends Pp_ApiActionClass
{
    /**
     *  preprocess of api_user_age_verification Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        if ($this->af->validate() > 0) {
            return 'error_400';
        }
		
        return null;
    }

    /**
     *  api_user_age_verification action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$pp_id = $this->getAuthenticatedBasicAuth('user');
		
		$verification = $this->af->get( 'verification' );
		
		$user_m =& $this->backend->getManager( 'User' );
		
        $user_base = $user_m->getUserBase( $pp_id );
		
		//エラー
		if ( !$user_base || Ethna::isError( $user_base ) ) {
			$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
			return 'error_500';
		}
		
		$columns = array(
			"age_verification"	=> $verification,
			"ma_purchase_max"	=> $user_m->ma_purchase_max[$verification],
		);
		
		$ret = $user_m->updateUserBase( $pp_id, $columns );
		
		if ( !$ret || Ethna::isError( $ret ) ) {
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}
		
		$this->af->setApp( 'user_verification', $columns, true );

		return 'api_json_encrypt';
    }
}
