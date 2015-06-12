<?php
/**
 *  Pp_AssetbundleManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

define( 'ASSET_BUNDLE_INACTIVE', 0 );	// 無効なAssetBundle
define( 'ASSET_BUNDLE_ACTIVE', 1 ); 	// 有効なAssetBundle

/**
 *  Pp_AssetbundleManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AssetbundleManager extends Ethna_AppManager
{
	protected $db_m_r = null;

	/**
	 * DBのインスタンスを生成
	 *
	 * @return null
	 */
	private function set_db()
	{
		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
	}

	/**
	 *  Assetbundle管理情報を取得する
	 *
	 *  @access private
	 *  @param  string  $date       対象日時
	 *  @param  string  $dir        ディレクトリ
	 *  @param  string  $file_name  ファイル名
	 *  @param  string  $version    ヴァージョン
	 *  @return array  Assetbundle管理情報
	 */
	function getAssetBundle( $date, $dir = '', $file_name = '', $version = 1 )
	{
		$this->set_db();

		$param = array( $version, ASSET_BUNDLE_ACTIVE );
		$sql = "SELECT * FROM m_asset_bundle WHERE version >= ? AND active_flg = ? ";
		if( !empty( $date ))
		{
			$param[] = $date;
			$param[] = $date;
			$sql .= "AND date_start <= ? AND ? <= date_end ";
		}
		if( !empty( $dir ))
		{
			$param[] = $dir;
			$sql .= "AND dir = ? ";
		}
		if( !empty( $file_name ))
		{
			$param[] = $file_name;
			$sql .= "AND file_name = ? ";
		}
		$data = $this->db_m_r->GetAll( $sql, $param );

		return $data;
	}
	
	/**
	 * CSV作成
	 *
	 * @param array $tilte
	 * @param array $data
	 * @param string $log_name
	 * @return boolean|string
	 */
	public function createCsv($tilte, $data, $log_name)
	{
	
		$datetime = new DateTime();
		$file_name = $log_name .'_' . $datetime->format('Ymd') . '_' . mt_rand();
	
		if (!$fp=@fopen(BASE . '/tmp/asset_bundle_csv/' . $file_name, 'a'))
		{
			return false;
		}
	
		// タイトル書き込み
		fwrite($fp, mb_convert_encoding(implode(',', $tilte), "Shift-JIS", "UTF-8") . "\r\n");
	
		// データ書き込み
		foreach ($data as $key => $item)
		{
			$_item = array();
	
			foreach ($item as $value)
			{
				$_item[] = $value;
			}
	
			fwrite($fp, mb_convert_encoding(implode(',', $_item), "Shift-JIS", "UTF-8") . "\r\n");
		}
	
		fclose($fp);
	
		return $file_name;
	}
}
?>
