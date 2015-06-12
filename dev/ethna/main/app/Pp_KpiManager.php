<?php
/**
 *  Pp_KpiManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 *  @see [開発用KPI] http://dev-kpi.cave.co.jp/application/customize.php
 */

//require_once($_SERVER["DOCUMENT_ROOT"]."/kpi_tool/kpi_config.php");
require_once BASE . '/www/kpi_tool/kpi_config.php';

/**
 *  Pp_KpiManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_KpiManager extends Ethna_AppManager
{
	/**
	 * ログバッファ
	 * @var array $log_buffer = array(array(arg0, arg1, ...), array(arg0, arg1, ...), ...)
	 */
	protected $log_buffer = array();

	/**
	 * User-Agent種別からKPIタグ用プラットフォーム部分の文字列を取得する
	 * 
	 * @param int $ua User-Agent種別
	 * @return string|false KPIタグ用プラットフォーム部分の文字列（エラー時はfalse）
	 */
	function getPlatformByUa($ua)
	{
		$user_m = $this->backend->getManager('User');
		
		$map = array(
			Pp_UserManager::OS_IPHONE  => 'Apple',
			Pp_UserManager::OS_ANDROID => 'Google',
		);

		if (!isset($map[$ua])) {
			return false;
		}
		
		return $map[$ua];
	}

	/**
	 * ユーザーIDからKPIタグ用プラットフォーム部分の文字列を取得する
	 * 
	 * @param int $user_id
	 * @return string|false KPIタグ用プラットフォーム部分の文字列（エラー時はfalse）
	 */
	function getPlatform($user_id)
	{
		$user_m = $this->backend->getManager('User');

		$base = $user_m->getUserBase($user_id);
		if (!is_array($base) || !isset($base['ua'])) {
			return false;
		}
		
		return $this->getPlatformByUa($base['ua']);
	}
	
	/**
	 * 記録する
	 * 
	 * この関数ではまだ最終的な記録先へは出力せず、バッファリングするのみ
	 * 引数は可変個数。kpi_set関数と同じ順番で以下の様に指定すること。
	 * 第一引数：タグ名
	 * 第二引数：タグタイプ
	 * 第三引数：カウント数
	 * 第四引数：日付
	 * 第五引数：ユーザID
	 * 第六引数：金額
	 * 第七引数：レベル
	 * 第八引数：ステージ
	 * 
	 * ●タグ送信時の日時指定について
	 * タグ送信時に日時をしてする場合は,UNIXタイムを入力した形で送信する。
	 * 例 ) 
	 * // 2012/5/10 13:24
	 * kpi_set("cave-tagtest-pv_test1",1,1,"1336623840","","","","");
	 * ※日時指定の場合はタイプ1のみ有効
	 */
	function log()
	{
		// バッファに保存
		$this->log_buffer[] = func_get_args();
	}
	
	/**
	 * フラッシュ
	 * 
	 * バッファリングされたログをまとめて出力する。
	 * Pp_Plugin_Filter_TrackingのpostFilterから呼ぶようにしてあるので、
	 * 各アクションやマネージャから個別に呼ぶ必要は無い。
	 */
	function flush()
	{
		while ($log = array_shift($this->log_buffer)) {
			$this->_flush($log);
		}
	}
	
	protected function _flush($log)
	{
		kpi_set(
			isset($log[0]) ? $log[0] : null,
			isset($log[1]) ? $log[1] : null,
			isset($log[2]) ? $log[2] : null,
			isset($log[3]) ? $log[3] : null,
			isset($log[4]) ? $log[4] : null,
			isset($log[5]) ? $log[5] : null,
			isset($log[6]) ? $log[6] : null,
			isset($log[7]) ? $log[7] : null
		);
		
		$this->backend->logger->log(LOG_DEBUG, 
			'KPI flushed. args=[' . var_export($log, true) . ']'
		);
	}
}
?>
