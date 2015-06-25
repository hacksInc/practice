<?php
/**
 *  Api/Noah.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_NoahActionClass.php';
/**
 *  api_noah Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiNoah extends Pp_ActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'guid',
		'action_id',
		'points',
		'user_action_id',
		'offer_name',
		'app_name',
		'vc_id',
	);
}

/**
 *  api_noah action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiNoah extends Pp_NoahActionClass
{
	/**
	 *  preprocess of api_noah Action.
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
	 *  api_noah action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		/*
		$action_id      = $this->af->get('action_id');
		$guid           = $this->af->get('guid');
		$points         = $this->af->get('points');
		$user_action_id = $this->af->get('user_action_id');
		*/

		require_once 'Oauth/OAuth.php';
		require_once 'HTTP/Request.php';
		
		$user_request = OAuthRequest::from_request(null, null, null);
		
		// $oauth_token, $oauth_secret �ɂ͋󕶎��񂪓���
		$oauth_token = $user_request->get_parameter('oauth_token');
		$oauth_token_secret = $user_request->get_parameter('oauth_token_secret');
		$oauth_signature = $user_request->get_parameter('oauth_signature');
		//APP_ID ���擾�ł���
		$oauth_consumer_key = $user_request->get_parameter('oauth_consumer_key');
	error_log("[Noah]oauth_token=$oauth_token");
	error_log("[Noah]oauth_token_secret=$oauth_token_secret");
	error_log("[Noah]oauth_signature=$oauth_signature");
	error_log("[Noah]oauth_consumer_key=$oauth_consumer_key");
		
		$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		// �e�A�v���̃A�v��ID �ƃV�[�N���b�g�L�[��ݒ肷��
		// �����̃A�v���̃R�[���o�b�N���󂯂�ꍇ�͏�q��$oauth_consumer_key �Ő؂�ւ���
		// �A�v��ID�E�V�[�N���b�g�L�[��iOS�EAndroid�ŕς��
		if ($oauth_consumer_key == 'APP_756537bfd09df258') //iOS
			$oauth_consumer = new OAuthConsumer( 'APP_756537bfd09df258', 'KEY_141537bfd09df2f8');
		if ($oauth_consumer_key == 'APP_852537bfd72d2ba7') //Android
			$oauth_consumer = new OAuthConsumer( 'APP_852537bfd72d2ba7', 'KEY_065537bfd72d2bfa');
		$access_token = new OAuthToken($oauth_token, $oauth_token_secret);
		$base_string = $user_request->get_signature_base_string();
	error_log("[Noah]access_token=[$access_token]");
	error_log("[Noah]oauth_consumer=[$oauth_consumer]");
	error_log("[Noah]base_string=[$base_string]");
		
		$signature_valid = $signature_method->check_signature(	$user_request,
																$oauth_consumer,
																$access_token,
																$oauth_signature);
		/*
		�����points ��t�^���鏈�������������ꍇ�ɂ�HTTP �X�e�[�^�X200 ���������Ă��������B
		���ꃆ�[�U�[�ւ̏d���t�^�ƂȂ邽�߂ɕt�^���s��Ȃ������ꍇ�ɂ�225 �A
		�Ď��s���K�v�Ȃ��G���[�����������ꍇ�ɂ�403 ���������Ă��������B
		����ȊO�̃X�e�[�^�X���Ԃ����ꍇ�ɂ̓����[�h�ʒm�̍Ď��s���s���܂��B
		*/
		// OAuth �̏�������������΃|�C���g��t�^����
		if ($signature_valid)
		{
	error_log("[Noah]signature_valid=true");
			// Noah �T�[�o�[����̃��N�G�X�g�p�����[�^�[���󂯎��B
			$action_id      = $_GET['action_id'];
			$user_id        = $_GET['guid'];
			$points         = $_GET['points'];
			$user_action_id = $_GET['user_action_id'];
			$offer_name     = $_GET['offer_name'];
			$app_name       = $_GET['app_name'];
			$vc_id          = $_GET['vc_id'];
			// �ȉ��A�|�C���g�t�^�̏���
			// $guid �̃��[�U�[��$points ��t�^���鏈�����L�q����B
			$db =& $this->backend->getDB();
			// �d���t�^�`�F�b�N
			$param = array($user_id, $action_id);
			$sql = "SELECT * FROM t_user_rewards_ad_noah WHERE user_id = ? AND action_id = ?";
			$ret = $db->GetRow($sql, $param);
			// ���ɕt�^�ς�
			if (empty($ret) == false) {
				header('HTTP/1.0 225 Unassigned');
				error_log("[Noah]$user_id HTTP:225 $action_id");
				exit;
			}
			// �g�����U�N�V�����J�n
			$db->begin();
			// �v���[���g�t�^
			$present_m = $this->backend->getManager('Present');
			// �v���[���g�̃f�[�^���Z�b�g
			$present = array(
						'user_id_to'   => $user_id,
						'comment_id'   => Pp_PresentManager::COMMENT_INSTALL,
						'comment'      => '',
						'type'         => Pp_PresentManager::TYPE_MAGICAL_MEDAL,
						'item_id'      => 0,
						'lv'           => 0,
						'badge_expand' => 0,
						'badges'       => '',
						'lv'           => 0,
						'number'       => $points,
					);
			//�v���[���g�𑡂�
			$ret = $present_m->setUserPresent(Pp_PresentManager::USERID_FROM_ADMIN, Pp_PresentManager::ID_NEW_PRESENT, $present);
			if (!$ret || Ethna::isError($ret)) {
				$db->rollback();
				header('HTTP/1.0 500 Internal Server Error');
				error_log("[Noah]$user_id HTTP:500 setUserPresent");
				exit;
			}
			// �o�^����
			$data = array(
				'action_id'      => $action_id,
				'points'         => $points,
				'user_action_id' => $user_action_id,
				'offer_name'     => $offer_name,
				'app_name'       => $app_name,
				'vc_id'          => $vc_id,
			);
			// INSERT���s
			$param = array($user_id, $data['action_id'], $data['points'], $data['user_action_id'], $data['offer_name'], $data['app_name'], $data['vc_id']);
			$sql = "INSERT INTO t_user_rewards_ad_noah(user_id, action_id, points, user_action_id, offer_name, app_name, vc_id, date_created)"
				 . " VALUES(?,?,?,?,?,?,?,NOW())";
			// DB�G���[
			if (!$db->execute($sql, $param)) {
				$db->rollback();
				header('HTTP/1.0 500 Internal Server Error');
				error_log("[Noah]$user_id HTTP:500 setUserRewardsAdNoah");
				exit;
			}
			// �g�����U�N�V��������
			$db->commit();
		}
		else	// OAuth �̏������������Ȃ���΃R�[���o�b�N�����ۂ��܂��B
		{
	error_log("[Noah]signature_valid=false");
			// ���̃R�[���o�b�N�����ۂ���ꍇ�ɂ�HTTP �X�e�[�^�X403 ��Ԃ��B
			$user_id = $_GET['guid'];
			header('HTTP/1.0 403 Forbidden');
			error_log("[Noah]$user_id HTTP:403");
			exit;
		}
		error_log("[Noah]$user_id HTTP:200 OK");
		exit;
	}
}

?>