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
		
		// $oauth_token, $oauth_secret には空文字列が入る
		$oauth_token = $user_request->get_parameter('oauth_token');
		$oauth_token_secret = $user_request->get_parameter('oauth_token_secret');
		$oauth_signature = $user_request->get_parameter('oauth_signature');
		//APP_ID が取得できる
		$oauth_consumer_key = $user_request->get_parameter('oauth_consumer_key');
	error_log("[Noah]oauth_token=$oauth_token");
	error_log("[Noah]oauth_token_secret=$oauth_token_secret");
	error_log("[Noah]oauth_signature=$oauth_signature");
	error_log("[Noah]oauth_consumer_key=$oauth_consumer_key");
		
		$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		// 各アプリのアプリID とシークレットキーを設定する
		// 複数のアプリのコールバックを受ける場合は上述の$oauth_consumer_key で切り替える
		// アプリID・シークレットキーはiOS・Androidで変わる
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
		正常にpoints を付与する処理が完了した場合にはHTTP ステータス200 を応答してください。
		同一ユーザーへの重複付与となるために付与を行わなかった場合には225 、
		再試行が必要ないエラーが発生した場合には403 を応答してください。
		それ以外のステータスが返った場合にはリワード通知の再試行を行います。
		*/
		// OAuth の署名が正しければポイントを付与する
		if ($signature_valid)
		{
	error_log("[Noah]signature_valid=true");
			// Noah サーバーからのリクエストパラメーターを受け取る。
			$action_id      = $_GET['action_id'];
			$user_id        = $_GET['guid'];
			$points         = $_GET['points'];
			$user_action_id = $_GET['user_action_id'];
			$offer_name     = $_GET['offer_name'];
			$app_name       = $_GET['app_name'];
			$vc_id          = $_GET['vc_id'];
			// 以下、ポイント付与の処理
			// $guid のユーザーに$points を付与する処理を記述する。
			$db =& $this->backend->getDB();
			// 重複付与チェック
			$param = array($user_id, $action_id);
			$sql = "SELECT * FROM t_user_rewards_ad_noah WHERE user_id = ? AND action_id = ?";
			$ret = $db->GetRow($sql, $param);
			// 既に付与済み
			if (empty($ret) == false) {
				header('HTTP/1.0 225 Unassigned');
				error_log("[Noah]$user_id HTTP:225 $action_id");
				exit;
			}
			// トランザクション開始
			$db->begin();
			// プレゼント付与
			$present_m = $this->backend->getManager('Present');
			// プレゼントのデータをセット
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
			//プレゼントを贈る
			$ret = $present_m->setUserPresent(Pp_PresentManager::USERID_FROM_ADMIN, Pp_PresentManager::ID_NEW_PRESENT, $present);
			if (!$ret || Ethna::isError($ret)) {
				$db->rollback();
				header('HTTP/1.0 500 Internal Server Error');
				error_log("[Noah]$user_id HTTP:500 setUserPresent");
				exit;
			}
			// 登録する
			$data = array(
				'action_id'      => $action_id,
				'points'         => $points,
				'user_action_id' => $user_action_id,
				'offer_name'     => $offer_name,
				'app_name'       => $app_name,
				'vc_id'          => $vc_id,
			);
			// INSERT実行
			$param = array($user_id, $data['action_id'], $data['points'], $data['user_action_id'], $data['offer_name'], $data['app_name'], $data['vc_id']);
			$sql = "INSERT INTO t_user_rewards_ad_noah(user_id, action_id, points, user_action_id, offer_name, app_name, vc_id, date_created)"
				 . " VALUES(?,?,?,?,?,?,?,NOW())";
			// DBエラー
			if (!$db->execute($sql, $param)) {
				$db->rollback();
				header('HTTP/1.0 500 Internal Server Error');
				error_log("[Noah]$user_id HTTP:500 setUserRewardsAdNoah");
				exit;
			}
			// トランザクション完了
			$db->commit();
		}
		else	// OAuth の署名が正しくなければコールバックを拒否します。
		{
	error_log("[Noah]signature_valid=false");
			// このコールバックを拒否する場合にはHTTP ステータス403 を返す。
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