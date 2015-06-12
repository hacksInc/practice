<?php
/**
 *	Pp_PortalWebViewActionForm.php
 *	ポータルウェブビュー用のアクションフォーム制御
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Blowfish.class.php';

/**
 *  Pp_PortalWebViewActionForm class.
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_PortalWebViewActionForm extends Pp_ApiActionForm
{
	/**
	 * setRequestedBasicAuthの処理のみ変更する（WebViewは$_REQUEST['c']が存在しないため、ApiActionClassの処理だとおかしくなる）
	 * ホントは根っこを変えるべきなんだろうけど、期間もないので継承先で対処
	 */

	/**
	 * HTTPリクエストに含まれるBASIC認証ユーザー名またはパスワードを取得
	 * 
	 * 正しいユーザー名，パスワードかどうかは、この関数では検証しないので注意。
	 * なぜか $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] が存在しないので、
	 * 自前で処理を記述した。
	 * @param string $colname 取得したいカラム名（'user' or 'password'）
	 * @return string
	 */
	function getRequestedBasicAuth($colname)
	{
		static $auth = null;

		if ($auth === null) {
			$headers = getallheaders();
			if (isset($headers['Authorization'])) {
				list($user, $password) = explode(':', 
					base64_decode(substr($headers['Authorization'], 6)), 2 // 6 == strlen('Basic ')
				);
				$this->logger->log(LOG_INFO, 'Authorization header exists.');
				$auth = array(
					'user' => $user,
					'password' => $password,
				);
			} elseif ( isset( $headers['authorization'] ) ) { // 端末依存の小文字対策
				list($user, $password) = explode(':', 
					base64_decode(substr($headers['authorization'], 6)), 2 // 6 == strlen('Basic ')
				);
				$this->logger->log(LOG_INFO, 'Authorization header exists.');
				$auth = array(
					'user' => $user,
					'password' => $password,
				);
			} else {
				if ( $this->backend->config->get( "is_test_site" ) == 1 ) {
					$auth = array(
						'user' => 915694803, 
						'password' => 'b833a427b974bddcf3ea66188d80f4537e97cd2e',
					);
				} else {
					$this->logger->log(LOG_INFO, 'No Authorization header.');
					$auth = array(
						'user' => null, 
						'password' => null
					);
				}
			}
		}
		
//error_log( "auth:" . print_r( $auth, 1 ) );

		return $auth[$colname];
	}
	
}
?>
