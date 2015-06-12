<?php
/**
 *  Admin/Api/Digest.php
 *
 *  HTTPダイジェスト認証を行なう。
 *  管理画面の内でデプロイ機能は社内サーバ(web03)に置く関係で、
 *  社内サーバでも認証を行なえるようにする為の対応。
 *  @see http://php.net/manual/ja/features.http-auth.php
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_api_digest Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminApiDigest extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  admin_api_digest action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminApiDigest extends Pp_AdminActionClass
{
	protected $must_login = false;
	
	/**
     *  preprocess of admin_api_digest Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
/*
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }
*/

    /**
     *  admin_api_digest action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
header('HTTP/1.0 500 Internal Server Error');
exit;
/*		
		$admin_m =& $this->backend->getManager('Admin');

		$realm = Pp_AdminManager::HTTP_DIGEST_REALM;

		if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$realm.
				   '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
			die('Authorization Required');
		}

		// PHP_AUTH_DIGEST 変数を精査する
		if (!($data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
			!($user = $admin_m->getAdminUser($data['username']))
		) {
			header('HTTP/1.1 401 Unauthorized');
			die('Authorization Failed');
		}

		// 有効なレスポンスを生成する
		$A1 = $admin_m->getValidAdminDigestA1($data['username']);
		$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
		$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

		if ($data['response'] != $valid_response) {
			header('HTTP/1.1 401 Unauthorized');
			die('Authorization Failed');
		}

		// OK, 有効なユーザー名とパスワードだ
		$this->af->setApp('user', $user);
		
        return 'admin_api_digest';
*/
    }

	// http auth ヘッダをパースする関数
/*
	protected function http_digest_parse($txt)
	{
		// データが失われている場合への対応
		$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));

		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

		foreach ($matches as $m) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}

		return $needed_parts ? false : $data;
	}
*/
}

?>