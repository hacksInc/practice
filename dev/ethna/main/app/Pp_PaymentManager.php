<?php
/**
 *	Pp_PaymentManager.php
 *	課金管理マネージャ
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_PaymentManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PaymentManager extends Ethna_AppManager
{
	private $db_cmn = null;
	private $db_cmn_r = null;
	
	private $curl_opts;	// cURLオプション設定
	
	/**
	 * サーバへのリクエスト
	 *
	 *  @access private
	 *  @param  string  $url  接続先
	 *  @param  array  $post  送信パラメータ
	 *  @return array  取得結果
	 */
	private function _request ( $url, $post )
	{
		// cURL設定
		$this->curl_opts = array(
			CURLOPT_POST			=> true,
			CURLOPT_CONNECTTIMEOUT	=> 10,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_BINARYTRANSFER	=> true,	// PHP5.1.3以降だと不要らしいけど、一応残しておく
			CURLOPT_TIMEOUT			=> 60,
		);

		// 初期化
		$ch = curl_init();

		// オプションを設定
		$this->curl_opts[CURLOPT_POSTFIELDS] = $post;//http_build_query( $params, null, '&' );
		$this->curl_opts[CURLOPT_URL] = $url;

		if ( $this->config->get( 'is_test_site' ) ) {
			// 開発だった場合はSSL証明書のチェックを厳密に行わない
			$this->curl_opts[CURLOPT_SSL_VERIFYPEER] = false;
			$this->curl_opts[CURLOPT_SSL_VERIFYHOST] = false;
		}

		// 'Expect: 100-continue'を無効にする。
		// CURLはこのヘッダをサポートしないサーバだとtimeoutするまで処理を中断(2秒程度)する為
		if ( isset( $this->curl_opts[CURLOPT_HTTPHEADER] ) ) {
			$existing_headers = $this->curl_opts[CURLOPT_HTTPHEADER];
			$existing_headers[] = 'Expect:';
			$this->curl_opts[CURLOPT_HTTPHEADER] = $existing_headers;
		} else {
			$this->curl_opts[CURLOPT_HTTPHEADER] = array('Expect:');
		}

		// 設定を反映
		curl_setopt_array( $ch, $this->curl_opts );

		// 実行
		$result = curl_exec( $ch );

		if ( $result === false ) {
			// 実行エラー
			throw new CurlException( curl_errno( $ch ) . ':' . curl_error( $ch ) );
		}

		// 終了
		curl_close( $ch );

		return json_decode( $result, true );
	}
	
	/**
	 * iOSのレシート検証
	 */
	function validateReceiptiOS ( $receipt, $m_sell, &$receipt_result = null )
	{
		$post = json_encode( array( "receipt-data" => $receipt ) );
		
//error_log( print_r( $post, 1 ) );
		
		// まずは本番サーバーに問い合わせる
		$response = $this->_request( "https://buy.itunes.apple.com/verifyReceipt", $post );
		
		if ( $response['status'] == 21007 ) {
			// テスト環境のレシートは、テストサーバーに再度問い合わせ
			$response = $this->_request( "https://sandbox.itunes.apple.com/verifyReceipt", $post );
		}
		
error_log( print_r( $response, 1 ) );
		
		if ( $response['status'] != 0 ) {
			return Ethna::raiseError( "RESPONSE STATUS ERROR FILE[%s] LINE[%d]", E_USER_ERROR, 
				__FILE__, __LINE__ );
		}
		
		// プロダクトIDが異なる場合はエラー
		if ( $response['receipt']['product_id'] != $m_sell['product_id'] ) {
error_log( $response['receipt']['product_id'] . ":" . $m_sell['product_id'] );
			return Ethna::raiseError( "PRODUCT ID ERROR FILE[%s] LINE[%d]", E_USER_ERROR, 
				__FILE__, __LINE__ );
		}
		
		// 登録済みのレシートか検証
		// ……はアクション側でやってるので、ここではスルー
		
		$receipt_result = $response;
		
		return true;
	}
	
	/**
	 * Android（GooglePlay）のレシート検証
	 *
	 * @param string $receipt レシート情報（JSON）
	 * @param string $signature シグネチャ
	 * @param bool|object 処理結果
	 */
	function validateReceiptAndroid ( $receipt, $signature )
	{
		// GooglePlay公開鍵を取得
		$rsa_key = $this->config->get( 'google_play_public_key' );
		
		// PEM形式に変更（こうしないとopenssl_pkey_get_publicで抽出できない）
		$cert =	"-----BEGIN PUBLIC KEY-----" . PHP_EOL . 
				chunk_split( $rsa_key, 64, PHP_EOL ) .
				"-----END PUBLIC KEY-----";
		
		// 公開鍵から証明書データを取得
		$public_key_id = openssl_pkey_get_public( $cert );
		
		// 署名をデコードする
		$signature = base64_decode( $signature );
		
		$result = openssl_verify( $receipt, $signature, $public_key_id, OPENSSL_ALGO_SHA1 );
		
		// 証明書データを解放
		openssl_free_key( $public_key_id );
		
		switch ( $result ) {
			case 1:	// 正常
				$return = true;
				break;
				
			case 0:	// 失敗
				$return = false;
				break;
				
			default:	// エラー
				$return = Ethna::raiseError( "OPENSSL VERIFY ERROR FILE[%s] LINE[%d]", E_USER_ERROR, 
					__FILE__, __LINE__ );
				break;
		}
		
		return $return;
	}
	
	/**
	 * トランザクションIDからレシート情報を取得
	 *
	 * @param string $transaction_id トランザクションID
	 * @param string $dsn DSN名
	 * @return array レシート情報
	 */
	function getPaymentHistoryiOS ( $transaction_id, $dsn = "db_cmn_r" )
	{
		// DSN指定が間違ってたらエラー
		if ( !in_array( $dsn, array( "db_cmn", "db_cmn_r" ) ) ) {
			return Ethna::raiseError( "DSN NAME ERROR FILE[%s] LINE[%d]", E_USER_ERROR, 
					__FILE__, __LINE__ );
		}
		
		if ( is_null( $this->$dsn ) ) {
			$this->$dsn =& $this->backend->getDB( str_replace( "db_", "", $dsn ) );
		}
		
		$param = array( $transaction_id );
		$sql = "SELECT * FROM ct_payment_history_ios WHERE transaction_id = ?";
		
		return $this->$dsn->getRow( $sql, $param );
	}
	
	/**
	 * オーダーIDからレシート情報を取得
	 *
	 * @param string $order_id オーダーID
	 * @param string $dsn DSN名
	 * @return array レシート情報
	 */
	function getPaymentHistoryAndroid ( $order_id, $dsn = "db_cmn_r" )
	{
		// DSN指定が間違ってたらエラー
		if ( !in_array( $dsn, array( "db_cmn", "db_cmn_r" ) ) ) {
			return Ethna::raiseError( "DSN NAME ERROR FILE[%s] LINE[%d]", E_USER_ERROR, 
					__FILE__, __LINE__ );
		}
		
		if ( is_null( $this->$dsn ) ) {
			$this->$dsn =& $this->backend->getDB( str_replace( "db_", "", $dsn ) );
		}
		
		$param = array( $order_id );
		$sql = "SELECT * FROM ct_payment_history_android WHERE order_id = ?";
		
		return $this->$dsn->getRow( $sql, $param );
	}
	
	/**
	 * レシート情報の記録（iOS）
	 *
	 * @param string $transaction_id 検証済みレシートのトランザクションID
	 * @param int $pp_id サイコパスID
	 * @param string $receipt レシート情報（JSON）
	 * @return bool 処理結果
	 */
	function insertPaymentHistoryiOS ( $transaction_id, $pp_id, $receipt )
	{
		if ( is_null( $this->db_cmn ) ) {
			$this->$dsn =& $this->backend->getDB( "db_cmn" );
		}
		
		$param = array( $transaction_id, $pp_id, $receipt );
		$sql = "INSERT INTO ct_payment_history_ios( transaction_id, pp_id, receipt, date_created ) VALUES( ?, ?, ?, NOW() )";
		
		if ( !$this->db_cmn->execute( $sql, $param ) ) {
			Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * レシート情報の記録（Android）
	 *
	 * @param string $order_id レシートのオーダーID
	 * @param int $pp_id サイコパスID
	 * @param string $receipt レシート情報（JSON）
	 * @param string $signature シグネチャ
	 * @return bool 処理結果
	 */
	function insertPaymentHistoryAndroid ( $order_id, $pp_id, $receipt, $signature )
	{
		if ( is_null( $this->db_cmn ) ) {
			$this->$dsn =& $this->backend->getDB( "db_cmn" );
		}
		
		$param = array( $order_id, $pp_id, $receipt, $signature );
		$sql = "INSERT INTO ct_payment_history_android( order_id, pp_id, receipt, signature, date_created ) VALUES( ?, ?, ?, ?, NOW() )";
		
		if ( !$this->db_cmn->execute( $sql, $param ) ) {
			Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__ );
			return false;
		}
		
		return true;
	}
}
?>