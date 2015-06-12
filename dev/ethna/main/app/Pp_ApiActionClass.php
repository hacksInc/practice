<?php
// vim: foldmethod=marker
/**
 *  Pp_ApiActionClass.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

// {{{ Pp_ApiActionClass
/**
 *  api action execution class
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_ApiActionClass extends Ethna_ActionClass
{
	/** 認証必須か */
	protected $must_authenticate = true;

	/** 認証済みのBASIC認証情報 */
	protected $authenticated_basic_auth = array('user' => null, 'password' => null);
	
	/**
	 * 固定ユニット番号
	 * 
	 * 必ず特定のユニットへ接続したい場合、
	 * 派生クラス（各アクション）でこの値をオーバーライドする
	 */
	protected $fixed_unit = null;

	
	/**
	 *  authenticate before executing action.
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (null if no errors. false if we have something wrong.)
	 */
	function authenticate()
	{
//error_log( "test_00" );
		$parent_ret = parent::authenticate();
		if ($parent_ret) {
			return $parent_ret;
		}
		
		// ヘッダに含まれる環境情報をチェック
		$headers = getAllheaders();
		
		// cryptパラメータが存在し、商用環境でなく且つ値がfalse（文字列）のときのみ、レスポンスの暗号化を解除
		if ( isset( $headers['jmeter'] ) && $this->config->get( "is_test_site" ) == 1 ) {
			$this->af->setApp( "api_crypt_flg", false );
		} else {
			$this->af->setApp( "api_crypt_flg", true );
		}
		
		/* ↓↓↓↓↓ ここで設定してもsetFormVars()では参照できない。というのも、ここに来た時には既にsetFormVars()は実行済みなのです。
		// base64パラメータが存在し、商用環境でなく且つ値がfalse（文字列）のときのみ、Authorizationパラメータのbase64decodeを無視
		if ( isset( $headers['base64'] ) && $headers['base64'] == 'false' && $this->config->get( "is_test_site" ) == 1 ) {
			$this->af->setApp( "auth_base64_flg", false );
		} else {
			$this->af->setApp( "auth_base64_flg", true );
		}
		/* ↑↑↑↑↑ なのでsetFormVars()に移動させます。
		*/
		
//error_log( "test_01" );
		
	// 2014/12/18 Y.Koiwa : ユニットチェックテスト
	//$ret = $this->authenticateUnit();
	//error_log( "authenticateUnit(): $ret" );

	// 2014/12/18 Y.Koiwa : API開発用にとりあえずチェック関係を全て無視するようにしておく
	// 開発が進んだら外す
//	$unit_m = $this->backend->getManager('Unit');
//	$unit_m->resetUnit(1);
//	return;
	// ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

		// ユニットチェック
		// マネージャが未生成の段階でチェックしないと以後のDB接続に支障があるのでここで行う
		$ret = $this->authenticateUnit();
		if ($ret) {
			return $ret;
		}

//error_log( "test_02" );

		// サーバメンテナンスチェック
		// 認証とは少し違う処理の気もするが、ここで実行した方が簡単なので…
		if ($this->config->get('maintenance') == 1) {//無理やりメンテに入れるように残しておく
			$this->af->setApp('status_detail_code', SDC_HTTP_503_SERVICE_UNAVAILABLE, true);
			return 'error_503';
		}

		// ユーザ認証
		$user_m =& $this->backend->getManager('User');
		
		$tmp_user = $this->af->getRequestedBasicAuth('user');
		$tmp_password = $this->af->getRequestedBasicAuth('password');
		
		// 特定ユーザーのみメンテナンス突破
		/*
		if ( $this->config->get( 'maintenance' ) == 2 && !$this->isBreakthrough( $tmp_user ) ) {//無理やりメンテに入れるように残しておく
			// ただしportal/api/checkだけはAction側でチェックしてるので、ここでは対象外
			if ( $this->backend->ctl->getCurrentActionName() != "portal_api_check" ) {
				$this->af->setApp('status_detail_code', SDC_HTTP_503_SERVICE_UNAVAILABLE, true);
				return 'error_503';
			} else {
				// portal_api_checkではpp_idがafに入るのでこっちで再比較
				$af_pp_id = $this->af->get( "id" );
				
				if ( !$this->isBreakthrough( $af_pp_id ) ) {
					$this->af->setApp('status_detail_code', SDC_HTTP_503_SERVICE_UNAVAILABLE, true);
					return 'error_503';
				}
			}
		}

		
		if( $tmp_user == 915694803 )
		{
			$this->must_authenticate = false;
		}
		*/
//error_log( "$tmp_user:$tmp_password" );
		
		// base64:falseの場合、パスワードのチェックは外す（jmeter試験対策）
		if ( $this->af->getApp( "auth_base64_flg" ) ) {
			if ((strlen($tmp_user) > 0) && (strlen($tmp_password) > 0) && 
					$user_m->isValidInstallPassword( $tmp_user, $tmp_password )
			) {
				$is_valid_uipw = true;
			} else {
				$is_valid_uipw = false;
			}
		} else {
			$tmp_base = $user_m->getUserBase( $tmp_user );
			
			if ( isset( $tmp_base['pp_id'] ) ) {
				$is_valid_uipw = true;
			} else {
				$is_valid_uipw = false;
			}
		}
		
		// サーバメンテナンスチェック(DB)
		$gm_ctrl = $user_m->getGameCtrl();
		$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        // BTF（メンテ突破フラグ）対応
        $action = $this->backend->ctl->getCurrentActionName();
        $breakthrough_flag = $this->af->get('btf');
        if (($gm_ctrl['btf'] != 1) || ($action != 'api_user_create') || (strcmp($breakthrough_flag, '1') !== 0)) {

            // 特定アカウントのmigrateの場合はメンテナンスチェックしない
            $action = $this->backend->ctl->getCurrentActionName();
            if (($action != 'api_user_migrate') || !is_debug20140617_account($this->af->get('account'), $this->af->get('dmpw'))) {

                //データがあれば
                if (!empty($gm_ctrl)) {
                    //稼働中以外の時
                    if ($gm_ctrl['status'] != Pp_UserManager::GAME_CTRL_STATUS_RUNNING) {
                        //メンテナンス前
                        if ($gm_ctrl['status'] == Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE_BEFORE) {
                            //メンテナンス時間になった
                            if ($gm_ctrl['date_start'] <= $now && $gm_ctrl['date_end'] > $now) {
                                $gm_ctrl['status'] = Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE;
                                $ret = $user_m->updGameCtrl($gm_ctrl);
                                if (!$ret || Ethna::isError($ret)) {
                                    $this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
                                    return 'error_500';
                                }
                            }
                        }
                        //メンテナンス中
                        if ($gm_ctrl['status'] == Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE) {
                            //メンテナンス時間が終わった
                            if ($gm_ctrl['date_end'] <= $now) {
                                $gm_ctrl['status'] = Pp_UserManager::GAME_CTRL_STATUS_RUNNING;
                                $ret = $user_m->updGameCtrl($gm_ctrl);
                                if (!$ret || Ethna::isError($ret)) {
                                    $this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
                                    return 'error_500';
                                }
                            } else if (!$is_valid_uipw) {
                                //自社スタッフでなければメンテで返す
                                $this->af->setApp('status_detail_code', SDC_HTTP_503_SERVICE_UNAVAILABLE, true);
                                return 'error_503';
                            } else {
                                $userbase = $user_m->getUserBase($tmp_user);
                                if (!$userbase || Ethna::isError($userbase)) {
                                    $this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
                                    return 'error_500';
                                }
                                //自社スタッフでなければメンテで返す
                                if ($userbase['attribute'] != Pp_UserManager::USER_ATTRIBUTE_STAFF) {
                                    $this->af->setApp('status_detail_code', SDC_HTTP_503_SERVICE_UNAVAILABLE, true);
                                    return 'error_503';
                                }
                            }
                        }
                    }
                }
            }
        } // BTF（メンテ突破フラグ）対応の閉じ括弧

		// クライアント側アプリの各種バージョンチェック
		$ret = $this->authenticateApp();
		if ($ret) {
			return $ret;
		}

		// ユーザ認証が必要か確認
		if (!$this->must_authenticate) {
			return;
		}
		
		// ユーザ認証成功の場合
		if ($is_valid_uipw) {
			$this->authenticated_basic_auth = array(
				'user' => $tmp_user,
				'password' => $tmp_password,
			);
			
			//BANチェック
			$userbase = $user_m->getUserBase( $tmp_user );
			if ( $userbase === false )
			{
				$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
				return 'error_500';
			}
			if ( !is_null( $userbase['ban_limit'] ) && date( "Y-m-d H:i:s" ) < $userbase['ban_limit'] ) {
				$this->af->setApp('status_detail_code', SDC_USER_ACCESS_BAN, true);
				return 'error_500';
			}
			
			// ユーザー属性
			$this->config->set( "is_debug_user", (($userbase['attr'] == 10) ? 0 : 1));

			// ポータル用に一部値を登録
			$this->af->setApp( "url", $this->config->get( "url" ) );
			$this->af->setApp( "domain", $this->config->get( "domain" ) );
			
			// OK
			return;
		}
		
		// 一部APIはユーザーIDとパスワードが存在しないため、returnする
		$action = $this->backend->ctl->getCurrentActionName();
		switch ( $action ) {
			case "portal_api_check":
			case "portal_api_convert":
			case "portal_api_initialize":
			case "portal_api_update":
				return;
				break;
		}
		
		// 負荷テスト対応
		// 負荷テスト終了後はコメントアウトすること
/*
		if (is_stress_test()) {
			if (strlen($tmp_user) > 0) {
				// 何らかのパスワードが登録済みのユーザーだったら、BASIC認証リクエストでのパスワードが不正でもユーザ認証成功させる
				// getUserBaseは、ここに来る前に既にisValidUipw内で呼ばれている為、問題ない（ここでgetUserBaseを呼んでも、DBを参照するタイミングには影響ない）
				$stress_user = $user_m->getUserBase($tmp_user);
				if (is_array($stress_user) && isset($stress_user['uipw_hash']) && (strlen($stress_user['uipw_hash']) > 0)) {
					$this->authenticated_basic_auth = array(
						'user' => $tmp_user,
						'password' => $tmp_password,
					);

					$this->logger->log(LOG_INFO, 'Authentication passed for stress test.');
					return;
				}
			}
		}
*/
		
		// ここに来るのはユーザ認証失敗した場合
		error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':error_401:[' . $tmp_user .']');
		$this->af->setApp('status_detail_code', SDC_HTTP_401_UNAUTHORIZED, true);
		return 'error_401';
	}
	
	/**
	 * ユニットの自動振り分け処理
	 * 
	 * @return int ユニット番号
	 */
	protected function authenticateUnit()
	{
		// マネージャのインスタンスを取得
		$unit_m = $this->backend->getManager('Unit');

		// 実行アクション名を取得
		$action = $this->backend->ctl->getCurrentActionName();

//error_log( $action );

//		if( $action === 'api_user_create' )
		if ( $action === 'portal_api_initialize' || $action === 'portal_api_check' || $action === 'portal_api_convert' ) // 20141229黒澤：ユーザー作成はポータル側のイニシャライズAPIで行う
		{	// ユーザー新規作成
			$unit = $unit_m->getAllocatableUnit();
			if( is_null( $unit ))
			{	// 割り当て先のユニットがない
				$this->backend->logger->log( LOG_WARNING, 'can not allocate unit.' );
				return 'error_400';
			}
		}
		else if( $action === 'api_user_migrate' )
		{	// ユーザー引き継ぎ
			$migrate_id = $this->af->get( 'account' );	// 引き継ぎIDを取得
			if( strlen( $migrate_id ) == 0 )
			{	// 引き継ぎIDがない
				$this->backend->logger->log( LOG_WARNING, 'migrate_id required.' );
				return 'error_400';
			}

			$unit = $unit_m->getUnitByMigrateId( $migrate_id );
			if( !is_numeric( $unit ))
			{	// 謎のユニット？
				$this->backend->logger->log( LOG_WARNING, 'Unit not found.' );
				$this->af->setApp( 'status_detail_code', SDC_USER_MIGRATE, true );
				return 'error_500';
			}

		}
		else if( $this->fixed_unit )
		{	// ユニット固定
			$unit = $this->fixed_unit;
			$this->backend->logger->log( LOG_WARNING, "Fixed unit. [$unit]" );
		}
		else
		{	// ユーザーの所属ユニットを取得
			// サイコパスIDを取得
//			$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
			$pp_id = $this->af->getRequestedBasicAuth( 'user' );
//error_log( "pp_id:$pp_id" );
			if( !is_numeric( $pp_id ))
			{	// 謎のサイコパスID
				$this->backend->logger->log( LOG_WARNING, 'authenticateUnit failed.' );
				return 'error_400';
			}

			// ユーザーが所属するユニットを取得
			$unit = $unit_m->cacheGetUnitByPpId( $pp_id );
			if( !is_numeric( $unit ))
			{	// 謎のユニット？
				$this->backend->logger->log( LOG_WARNING, 'Unit not found.' );
				return 'error_500';
			}
		}

		// 取得したユニットに設定
		$unit_m->resetUnit( $unit );
	}
	
	/**
	 * クライアント側アプリの各種バージョンの認可処理
	 */
	protected function authenticateApp()
	{
		$headers = getallheaders();
		
		//バージョンをDBから取得してくるように変えた
		$date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$app_ver = $this->backend->getManager('Client')->getLatestAppVer($date);
		$res_ver = $this->backend->getManager('Client')->getLatestResVer($date);
		//万が一、空だったらconfigから取得する　よってconfigの値も合わせておくようにする
		if (empty($app_ver['app_ver'])) $app_ver['app_ver'] = $this->config->get('appver');
		if (empty($res_ver['res_ver'])) $res_ver['res_ver'] = $this->config->get('rscver');
		$vers = array(
			'appver' => $app_ver['app_ver'],
			'rscver' => $res_ver['res_ver'],
		);

		$request_arr = array(); // $request_arr = array('appver' => クライアントからリクエストされたx-psycho-appver, 'rscver' => クライアントからリクエストされたx-psycho-rscver)
		
		if (isset($headers['x-psycho-appver']) && !isset($headers['x-psycho-rscver'])) { // アプリバージョンがある＆リソースバージョンがない場合
			// チェックしない
			$this->backend->logger->log(LOG_INFO,
				'Version check skipped. x-psycho-appver=[' . $headers['x-psycho-appver'] . ']'
			);
		} else foreach (array(
			'appver' => 'x-psycho-appver',
			'rscver' => 'x-psycho-rscver',
		) as $config_name => $request_header_name) {
			if (isset($headers[$request_header_name]))
				$request_value = $headers[$request_header_name];
			else
				$request_value = 0;
		//	$server_value  = $this->config->get($config_name);
			$server_value  = $vers[$config_name];
			
			$request_arr[$config_name] = $request_value;

			$valid = 1;
			if ($request_value && $server_value) {
				if ($config_name == 'appver') {
					// アプリバージョンのチェック
					// ※アプリバージョンは、クライアント側の値がサーバ側の値より大きくてもOK
					if ($request_value < $server_value) {
						$valid = 0;
					}
				} else {
					// リソースバージョンのチェック
					if ($request_value != $server_value) {
						$valid = 0;
					}
				}
			}
			
			$this->backend->logger->log(LOG_INFO,
				$config_name . 
				': request=[' . $request_value . 
				'] server=[' . $server_value . 
				'] valid=[' . $valid . ']'
			);
			
			if (!$valid) {
				$this->af->setApp('status_detail_code', ($config_name == 'appver' ? SDC_APPVER_NOT_LATEST : SDC_RSCVER_NOT_LATEST));
				return 'error_400';
			}
		}
		
		// 2014/01/21不具合対応
		if (isset($request_arr['appver']) && isset($request_arr['rscver']) && 
			($request_arr['appver'] == 2) && ($request_arr['rscver'] == 0)
		) {
			$action = $this->backend->ctl->getCurrentActionName();
			if (($action == 'api_user_boot') || 
			    ($action == 'api_quest_start') ||
			    ($action == 'api_quest_helper') ||
			    ($action == 'api_friend_list') ||
			    ($action == 'api_friend_search') ||
			    ($action == 'api_user_present_list') ||
			    ($action == 'api_monster_book') ||
			    ($action == 'api_shop_gacha_exec') ||
			    ($action == 'api_shop_gacha_list') ||
			    ($action == 'api_user_serial') ||
			    ($action == 'api_user_achievement')
			) {
				$this->backend->logger->log(LOG_WARNING, 
					'Broken rscver. appver=[%d] rscver=[%d] action=[%s]',
					$request_arr['appver'], $request_arr['rscver'], $action
				);
				
				$this->af->setApp('status_detail_code', SDC_RSCVER_NOT_LATEST);
				return 'error_400';
			}
		}
		
//DEBUG
//[jugmon@kaiajmja-011 log]$ tail -f jugmon_ethna.log | grep 810544345
//Jan 21 18:22:33 wbiajmja-028 Pp[27492]: INFO: Pp_Logger.log(/app/Pp_Controller.php:343): user 810544345: action_name=[api_user_present_list]
//Jan 21 18:22:33 wbiajmja-028 Pp[27492]: INFO: Pp_Logger.log(/app/Pp_ApiActionForm.php:454): user 810544345: Input Json:{"offset":0,"limit":100}
//Jan 21 18:22:33 wbiajmja-028 Pp[27492]: INFO: Pp_Logger.log(/app/Pp_ApiActionClass.php:272): user 810544345: appver: request=[2] server=[1] valid=[1]
//Jan 21 18:22:33 wbiajmja-028 Pp[27492]: INFO: Pp_Logger.log(/app/Pp_ApiActionClass.php:272): user 810544345: rscver: request=[0] server=[5] valid=[1]
//Jan 21 18:22:33 wbiajmja-028 Pp[27492]: INFO: Pp_Logger.log(/app/Pp_ApiActionForm.php:512): user 810544345: Authorization header exists.
//Jan 21 18:22:33 wbiajmja-028 Pp[27492]: INFO: Pp_Logger.log(/app/view/Api/Json/Encrypt.php:61): user 810544345: Output Json:{"user_present":[],"appver":1,"rscver":1,"maintenance":0,"status_detail_code":2000}
		
	}
	
	/**
	 *  Preparation for executing action. (Form input check, etc.)
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (null if no errors. false if we have something wrong.)
	 */
	function prepare()
	{
		return parent::prepare();
	}

	/**
	 *  execute action.
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (we does not forward if returns null.)
	 */
	function perform()
	{
		return parent::perform();
	}
	
	/**
	 * 認証済みのBASIC認証情報を取得
	 * 
	 * @param string $colname 取得したいカラム名（'user' or 'password'）
	 * @return string
	 */
	function getAuthenticatedBasicAuth($colname)
	{
		return $this->authenticated_basic_auth[$colname];
	}

	/**
	 * クライアントから送信されてくるAPIトランザクションIDを取得する
	 */
	function getApiTransactionId()
	{
		$headers = getallheaders();
		//return $headers['api-transaction-id'];
		if( isset( $headers['api-transaction-id'] ))
		{
			return $headers['api-transaction-id'];
		}
		else
		{
			return 'dummy_transaction_id';
		}
	}
	
	/**
	 * アクセス元のIPアドレスを取得する
	 * 
	 * Webサーバがリバースプロキシだった場合への対応としてHTTP_X_FORWARDED_FORも参照する。
	 */
/*
	static function getRemoteAddr()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
			list($remote_addr, $trash) = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'], 2);
			return $remote_addr;
		} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return false;
	}
*/
	
	/**
	 * 端末情報関連のアクションを実行する
	 */
	function performDeviceInfo()
	{
		// 引数取得
		$content = $this->af->get('device_info');
		if (strlen($content) === 0) {
			// OK
			return;
		}

		$user_id = $this->getAuthenticatedBasicAuth('user');
		$ua = null;
		if (!$user_id) {
			$action = $this->backend->ctl->getCurrentActionName();
			if (($action == 'api_user_create') ||
				($action == 'api_user_migrate')
			) {
				$user_id = $this->af->getApp('uid');
				$ua = $this->af->get('ua');
			}
		}
		
		$client_m = $this->backend->getManager('Client');
		
		// DBへ保存する
		$ret = $client_m->setUserDeviceInfoContent($user_id, null, $content, $ua);
		$device_info_ok = ($ret === true) ? 1 : 0;
		
		// 戻り値をセット
		$this->af->setApp('device_info_ok', $device_info_ok, true);
	}
	
	/**
	 * メンテ突破ユーザー設定
	 */
	function isBreakthrough ( $pp_id )
	{
		$user_list = array(
			13594,		// 樋口
			14311,		// 川野
//			15471,		// 黒澤
			76363,		// 米原
			77289,		// 検証端末（iPhone5S）
			77312,		// 検証端末（Galaxy）
			916926157,	// 小倉
			914454408,	// 阿部
			911709300,	// 森
			
			916831126,
			913880398,
			911314810,
			913563333,
			910712031,
			910688041,
			918863930,
			914260845,
			912084365,
		);
		
		// ユーザーIDがない場合は、そもそも突破させない
		if ( is_null( $pp_id ) ) return false;
		
		if ( !in_array( $pp_id, $user_list ) ) return false;
		
		return true;
	}
}
?>
