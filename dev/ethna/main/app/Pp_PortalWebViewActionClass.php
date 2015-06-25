<?php
/**
 *  Pp_ApiActionClass.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once "Pp_ApiActionClass.php";

/**
 *  api action execution class
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_PortalWebViewActionClass extends Pp_ApiActionClass
{
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

		$request_arr = array(); // $request_arr = array('appver' => クライアントからリクエストされたX-Jugmon-Appver, 'rscver' => クライアントからリクエストされたX-Jugmon-Rscver)
		
		// 小文字対応（端末依存で小文字で来るケースがあったため）
		if ( isset( $headers['x-jugmon-appver'] ) ) $headers['X-Jugmon-Appver'] = $headers['x-jugmon-appver'];
		
		// アプリバージョンのチェック（アプリバージョン未定義の際は行わない）
		if ( isset( $headers['X-Jugmon-Appver'] ) && $headers['X-Jugmon-Appver'] < $app_ver['app_ver'] ) {
			$this->af->setApp( 'status_detail_code', SDC_APPVER_NOT_LATEST, true );
			return 'error_400';
		}
	}
}
?>
