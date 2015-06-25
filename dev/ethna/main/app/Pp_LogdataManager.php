<?php
/**
 *  Pp_LogDataManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_LogDataManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_LogDataManager extends Ethna_AppManager
{
	protected $db_logex = null;

	private function set_db()
	{
		if( is_null( $this->db_logex ))
		{
			$this->db_logex =& $this->backend->getDB( 'logex' );
		}
	}

	/**
	 * ユーザーログイン履歴の記録
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $device_type 端末種別（0:謎のOS, 1:iOS, 2:Android）
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logUserLogin( $pp_id, $device_type )
	{
		$this->set_db();

		$param = array( $device_type, $pp_id, strftime( "%Y-%m-%d %H:%M:%S", $_SERVER['REQUEST_TIME'] ));
		$sql = "INSERT INTO log_user_login( device_type, pp_id, date_login, date_created ) "
			. "VALUES( ?, ?, ?, NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logUserLogin() error!: pp_id=".$pp_id.", device_type=".$device_type );
			return false;
		}
		return true;
	}

	/**
	 * フォト獲得履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		processing_type:処理タイプ
	 *		photo_id:獲得フォトID
	 *		photo_lv:獲得後フォトLV
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logPhoto( $columns )
	{
		$this->set_db();

		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_photo( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logPhoto() error!" );
			return false;
		}
		return true;
	}

	/**
	 * ガチャ実行履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		gacha_id:ガチャID
	 *		type:ガチャ種別
	 *		photo_id:獲得フォトID
	 *		photo_lv:獲得後フォトLV
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logPhotoGacha( $columns )
	{
		$this->set_db();

		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_photo_gacha( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logPhotoGacha() error!" );
			return false;
		}
		return true;
	}

	/**
	 * ユーザーステージ情報変動履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		karanomori_report:唐之杜通信発生回数
	 *		karanomori_report_prev:唐之杜通信発生回数（変動前）
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logStage( $columns )
	{
		$this->set_db();

		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_stage( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logStage() error!" );
			return false;
		}
		return true;
	}

	/**
	 * ユーザーエリア情報変動履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		area_id:エリアID
	 *		area_stress:エリアストレス値
	 *		area_stress_prev:エリアストレス値（変動前）
	 *		status:エリアステータス
	 *		status_prev:エリアステータス（変動前）
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logArea( $columns )
	{
		$this->set_db();

		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_area( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logArea() error!" );
			return false;
		}
		return true;
	}

	/**
	 * InGame開始履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		play_id:プレイID
	 *		mission_id:ミッションID
	 *		accompany_character_id:同行サポートキャラクターID
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logIngameStart( $columns )
	{
		$this->set_db();

		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_ingame_start( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logIngameStart() error!" );
			return false;
		}
		return true;
	}

	/**
	 * InGame開始履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		play_id:プレイID
	 *		mission_id:ミッションID
	 *		result_type:ミッション結果種別
	 *		status:ミッション終了ステータス
	 *		zone:プレイヤーが最後にいたゾーン番号
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logIngameResult( $columns )
	{
		$this->set_db();

		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_ingame_result( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logIngameResult() error!" );
			return false;
		}
		return true;
	}

	/**
	 * キャラクターパラメータ変動履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		character_id:キャラクターID
	 *		processing_type:処理タイプ
	 *		crime_coef:犯罪係数
	 *		crime_coef_prev:犯罪係数（変更前）
	 *		body_coef:身体係数
	 *		body_coef_prev:身体係数（変更前）
	 *		intelli_coef:知能係数
	 *		intelli_coef_prev:知能係数（変更前）
	 *		mental_coef:心的係数
	 *		mental_coef_prev:心的係数（変更前）
	 *		ex_stress_care:臨時ストレスケア回数
	 *		ex_stress_care_prev:臨時ストレスケア回数（変更前）
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logCharacter( $columns )
	{
		$this->set_db();

		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_character( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logCharacter() error!" );
			return false;
		}
		return true;
	}

	/**
	 * アイテム履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		item_id:アイテムID
	 *		processing_type:処理タイプ
	 *		device_type:端末種別
	 *		count:付与・使用数
	 *		num:所持数
	 *		num_prev:所持数（変更前）
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logItem( $columns )
	{
		$this->set_db();

		//error_log( 'columns = '.print_r($columns, 1) );
		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_item( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logItem() error!" );
			return false;
		}
		return true;
	}

	/**
	 * プレゼントBOX情報履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		processing_type:処理タイプ
	 *		present_id:プレゼントID
	 *		present_category:プレゼントカテゴリ
	 *		present_value:配布物ID
	 *		num:配布数
	 *		status:ステータス
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logPresent( $columns )
	{
		$this->set_db();

		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_present( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logPresent() error!" );
			return false;
		}
		return true;
	}

	/**
	 * 課金アイテム購入履歴の記録
	 *
	 * @param columns 記録データの連想配列
	 *		pp_id:サイコパスID
	 *		api_transaction_id:トランザクションID
	 *		sell_id:購入商品ID
	 *		price:購入価格
	 *
	 * @return boolean true:正常終了 | false:記録エラー
	 */
	function logAccounting( $columns )
	{
		$this->set_db();

		$param = array();
		$column_name = array();
		foreach( $columns as $column => $val )
		{
			$param[] = $val;
			$column_name[] = $column;
			$str_values[] = '?';
		}
		$sql = "INSERT INTO log_accounting( ".implode( ',', $column_name ).", date_created ) "
			. "VALUES( ".implode( ',', $str_values ).", NOW())";
		$res = $this->db_logex->execute( $sql, $param );
		if( !$res )
		{	// 記録エラー
			$this->backend->logger->log( LOG_ERR, "logAccounting() error!" );
			return false;
		}
		return true;
	}
}
