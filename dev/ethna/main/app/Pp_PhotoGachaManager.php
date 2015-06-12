<?php
/**
 *	Pp_PhotoGachaManager.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	Pp_PhotoGachaManager
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_PhotoGachaManager extends Ethna_AppManager
{
	// コンストラクタで取得されないDBのインスタンス
	protected $db_m_r = null;	// マスターデータDB（Slave）
	protected $db_cmn = null;	// 共通データDB（Master）
	protected $db_cmn_r = null;	// 共通データDB（Slave）

	//================================================================================================
	//		m_photo_gacha に関する処理
	//================================================================================================
	/**
	 * フォトガチャマスタを取得
	 *
	 * @param int $gacha_id ガチャID
	 *
	 * @return null|array null:取得エラー, array:フォトガチャマスタ情報
	 */
	function getMasterPhotoGacha( $gacha_id )
	{
		// キャッシュから取得してみる
		$cache_key = "getMasterPhotoGacha_$gacha_id";	// キャッシュキー
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
		$param = array( $gacha_id );
		$sql = "SELECT * FROM m_photo_gacha WHERE gacha_id = ?";
		$data = $this->db_m_r->GetRow( $sql, $param );

		if( $data !== false )
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * 利用可能なフォトガチャマスタの一覧を取得
	 *
	 * @param array $mission_ids 取得対象解放IDの配列
	 *
	 * @return null|array null:取得エラー, array:フォトガチャマスタ情報の配列
	 */
	function getMasterPhotoGachaAvailable( $mission_ids = array())
	{
		/*
		if( empty( $mission_ids ))
		{	// 取得対象なし
			return array();
		}
		*/

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		$sql = "SELECT * FROM m_photo_gacha WHERE ( mission_id IS NULL ";
		if( empty( $mission_ids ))
		{
			$param = null;
		}
		else
		{	// 取得対象なし
			$param = array();
			$mission_id_in = array();
			foreach( $mission_ids as $mission_id )
			{
				$param[] = $mission_id;
				$mission_id_in[] = '?';
			}
			$sql .= "OR mission_id IN ( ".implode( ',', $mission_id_in )." )";
		}
		$sql .= ") AND date_start <= NOW() AND NOW() < date_end ORDER BY sort_no";

		$data = $this->db_m_r->GetAll( $sql, $param );

		return $data;

		//【MySQLトリビア】
		// MySQL(4.1以降？)ではIS (NOT) NULL検索でNULL値にインデックスが効くそうです（へぇへぇへぇ…）
	}

	/**
	 * 全てのフォトガチャマスタの一覧を取得
	 *
	 * @return null|array null:取得エラー, array:フォトガチャマスタ情報の配列
	 */
	function getMasterPhotoGachaAll()
	{
		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		$sql = "SELECT * FROM m_photo_gacha";

		$data = $this->db_m_r->GetAll( $sql );

		return $data;
	}


	//================================================================================================
	//		m_photo_gacha_lineup に関する処理
	//================================================================================================
	/**
	 * フォトガチャラインナップ情報を取得
	 *
	 * @param int $gacha_id ガチャID
	 *
	 * @return null|array null:取得エラー, array:フォトガチャラインナップ情報
	 */
	function getMasterPhotoGachaLineup( $gacha_id )
	{
		// キャッシュから取得してみる
		$cache_key = "getMasterPhotoGachaLineup_$gacha_id";	// キャッシュキー
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
		$param = array( $gacha_id );
		$sql = "SELECT * FROM m_photo_gacha_lineup WHERE gacha_id = ?";
		$data = $this->db_m_r->GetAll( $sql, $param );

		if( $data !== false )
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * フォトガチャラインナップ情報を取得（取得データのキーがフォトID版）
	 *
	 * @param int $gacha_id ガチャID
	 *
	 * @return null|array null:取得エラー, array:フォトガチャラインナップ情報
	 */
	function getMasterPhotoGachaLineupEx( $gacha_id )
	{
		// キャッシュから取得してみる
		$cache_key = "getMasterPhotoGachaLineupEx_$gacha_id";	// キャッシュキー
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$data = $this->getMasterPhotoGachaLineup( $gacha_id );
		if( $data !== false )
		{	// 正常に取得できた
			if( !empty( $data ))
			{
				// 配列のキーをphoto_idにする
				$temp = array();
				foreach( $data as $v )
				{
					$temp[$v['photo_id']] = $v;
				}
				$data = $temp;
			}

			// キャッシュにセット
			$cache_m->set( $cache_key, $data );
		}
		return $data;
	}

	//================================================================================================
	//		ct_photo_gacha に関する処理
	//================================================================================================
	/**
	 * フォトガチャ管理情報の取得
	 *
	 * @param int $gacha_id ガチャID
	 *
	 * @return array フォトガチャ管理情報
	 */
	function getPhotoGacha( $gacha_id )
	{
		if( is_null( $this->db_cmn_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_cmn_r =& $this->backend->getDB( 'cmn_r' );
		}

		$param = array( $gacha_id );
		$sql = "SELECT * FROM ct_photo_gacha WHERE gacha_id = ? ";
		$data = $this->db_cmn_r->GetRow( $sql, $param );

		return $data;
	}

	/**
	 * フォトガチャ管理情報の更新
	 *
	 * @param int $gacha_id ガチャID
	 * @param array $columns 更新するカラムとデータの配列（カラム名 => データ）
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし):false | Ethna_Errorオブジェクト:更新エラー
	 */
	function updatePhotoGacha( $gacha_id, $columns )
	{
		if( is_null( $this->db_cmn ))
		{	// インスタンスを取得していないなら取得
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}

		// 主キーが更新されるといかんので更新内容から削除
		unset( $columns['gacha_id'] );

		// DB更新
		$str_set = array();
		$param = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = ?";
			$param[] = $v;
		}
		$param = $gacha_id;

		$sql = "UPDATE ct_photo_gacha SET ".implode( ',', $str_set )." WHERE gacha_id = ?";
		if( !$this->db_cmn->execute( $sql, $param ))
		{	// 更新エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 更新された行数をチェック
		if( $this->db_cmn->db->affected_rows() == 0 )
		{
			return false;
		}

		return true;
	}

	/**
	 * フォトガチャ管理情報のカウンターを進める
	 *
	 * @param int $gacha_id ガチャID
	 *
	 * @return true:正常終了(更新あり) | false:正常終了(更新なし):false | Ethna_Errorオブジェクト:更新エラー
	 */
	function incPhotoGachaDropCount( $gacha_id )
	{
		if( is_null( $this->db_cmn ))
		{	// インスタンスを取得していないなら取得
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}

		$param = array( $gacha_id );
		$sql = "UPDATE ct_photo_gacha SET drop_count = drop_count + 1 WHERE gacha_id = ?";
		if( !$this->db_cmn->execute( $sql, $param ))
		{	// 更新エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		// 更新された行数をチェック
		if( $this->db_cmn->db->affected_rows() == 0 )
		{
			return false;
		}

		return true;
	}

	//================================================================================================
	//		ct_photo_gacha_box に関する処理
	//================================================================================================
	/**
	 * フォトガチャBOX情報を取得
	 *
	 * @param int $gacha_id ガチャID
	 *
	 * @return array フォトガチャBOX情報
	 */
	function getPhotoGachaBox( $gacha_id, $not_empty = false )
	{
		if( is_null( $this->db_cmn_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_cmn_r =& $this->backend->getDB( 'cmn_r' );
		}

		$param = array( $gacha_id );
		$sql = "SELECT * FROM ct_photo_gacha_box WHERE gacha_id = ?";

		return $this->db_cmn_r->GetAll( $sql, $param );
	}

	/**
	 * 指定ガチャIDのウエイトの合計を取得
	 *
	 * @param int $gacha_id ガチャID
	 *
	 * @return 合計ウエイト
	 */
	function getPhotoGachaBoxTotalWeight( $gacha_id )
	{
		// キャッシュから取得してみる
		$cache_key = "getPhotoGachaBoxTotalWeight__$gacha_id";	// キャッシュキー
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		if( is_null( $this->db_cmn_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_cmn_r =& $this->backend->getDB( 'cmn_r' );
		}
		$param = array( $gacha_id );
		$sql = "SELECT SUM( weight ) as weight FROM ct_photo_gacha_box WHERE gacha_id = ?";
		$weight = $this->db_cmn_r->GetOne( $sql, $param );

		// キャッシュにセット
		$cache_m->set( $cache_key, $weight );

		return $weight;
	}

	/**
	 * フォトガチャBOX情報のガチャカウンターを加算
	 *
	 * @param int $gacha_id ガチャID
	 * @param int $photo_id フォトID
	 *
	 * @return true:正常終了, Errorオブジェクト:エラー
	 */
	function incPhotoGachaBoxCount( $gacha_id, $photo_id )
	{
		if( is_null( $this->db_cmn ))
		{	// インスタンスを取得していないなら取得
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}

		$param = array( $gacha_id, $photo_id );
		$sql = "UPDATE ct_photo_gacha_box SET gacha_cnt = gacha_cnt + 1 WHERE gacha_id = ? AND photo_id = ?";
		if( !$this->db_cmn->execute( $sql, $param ))
		{	// 削除エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return true;
	}
}
?>
