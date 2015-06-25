<?php
/**
 *  Api/User/Get.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  api_user_get Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiUserGet extends Pp_ApiActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
	);

	
	// ���N���C�A���g����T�C�R�p�XID�ȊO�̈����������Ă���ꍇ�͂��̋L�q�ɂ���
	//var $form = array(
	//  'c'
	//);
	// ��'c'���������Í�������������ɂȂ��Ă���APp_ApiActionForm�̃N���X���Ŏ����I��
	//   �W�J����܂��B�W�J���ꂽ�����̎擾���@��perform()���ɋL�q�B
}

/**
 *  api_user_get action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiUserGet extends Pp_ApiActionClass
{
	/**
	 *  preprocess of api_user_get Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
	function prepare()
	{
		error_log( "BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBb" );

		if( $this->af->validate() > 0 )
		{
			return 'error_400';
		}
		return null;
	}

	/**
	 *  api_user_get action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// �N���C�A���g���瑗�M����Ă���T�C�R�p�XID���擾
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );	// �T�C�R�p�XID�̓w�b�_�[�ɂ������đ����Ă���̂ł��̋L�q�Ŏ擾����B

		$pp_id = 910000001;		// �l���擾�ł������Ƃɂ���
		error_log( "pp_id = $pp_id" );

		// �N���C�A���g���瑗���Ă��������i'c'�̓W�J��̃f�[�^�j���擾������@
		// �������Ƃ��Ă� map_id �� area_id �������Ă��Ă���ꍇ
		// $map_id = $this->af->get( 'map_id' );
		// $area_id = $this->af->get( 'area_id' );

		// �}�l�[�W���̃C���X�^���X���擾
		$user_m =& $this->backend->getManager( 'User' );	// Pp_UserManager�N���X�̃C���X�^���X
		$photo_m =& $this->backend->getManager( 'Photo' );	// Pp_PhotoManager�N���X�̃C���X�^���X

		print_r( $photo_m->getMasterPhotoCount());

		// ut_user_base�̏����擾
		$user_base = $user_m->getUserBase( $pp_id );		// ���\�b�h�Ăяo��
		if( is_null( $user_base ) === true )
		{	// �擾�G���[
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		// ut_user_game�̏����擾
		$user_game = $user_m->getUserGame( $pp_id );		// ���\�b�h�Ăяo��
		if( is_null( $user_game ) === true )
		{	// �擾�G���[(500�G���[�ŕԂ�)
			$this->af->setApp( 'status_detail_code', SDC_DB_ERROR, true );
			return 'error_500';
		}

		print_r( $user_base );
		print_r( $user_game );

		// �擾�����f�[�^���N���C�A���g�ɕԂ�
		$this->af->setApp( 'user_base', $user_base, true );
		$this->af->setApp( 'user_game', $user_game, true );

		// �����̂Ƃ���d�l�͂܂����܂��Ă���܂��񂪁AAPI�̕��ŃN���C�A���g���]�ތ`����
		//   �f�[�^�����H���A�߂�l�Ƃ��ĕԂ��悤�ɂȂ�܂��B

		return 'api_json_encrypt';
	}

	/* ���̏������ƈȉ��̂悤��JSON�ɂȂ��ăN���C�A���g�ɓn����܂� 

		{
			"user_base": {
				"pp_id": "*****",
				"pu_id": "*****",
				"name": "*****",
				"device_type": "*****",
				"attr": "*****",
				"migrate_id": "*****",
				"migrate_pw_hash": "*****",
				"install_pw_hash": "*****",
				"ban_limit": "*****",
				"date_created": "*****",
				"date_modified": "*****"
			},
			"user_game": {
				"pp_id": "*****",
				"crime_coef": "*****",
				"body_coef": "*****",
				"intelli_coef": "*****",
				"mental_coef": "*****",
				"date_created": "*****",
				"date_modified": "*****"
			}
		}

	*/
}
?>