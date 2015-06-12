<?php
/**
 *	Pp_PhotoManager.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	Pp_PhotoManager
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_PhotoManager extends Ethna_AppManager
{
	// 最大フォトLV
	const PHOTO_LV_MAX = 999;

	// コンストラクタで取得されないDBのインスタンス
	protected $db_m_r = null;	// マスターデータDB

	//================================================================================================
	//		m_photo に関する処理
	//================================================================================================
	/**
	 * フォト総数の取得
	 *
	 * @param int $type フォト種別（指定がない場合は全種別）
	 *
	 * @return int:指定種別のフォト総数 | null:取得エラー
	 */
	function getMasterPhotoCount( $type = 'all' )
	{
		$cache_key = "getMasterPhotoCount_$type";
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

		// 取得できない場合はDBから取得
		if( $type != 'all' )
		{	// フォト種別指定あり
			$param = array( $type );
			$sql = "SELECT COUNT(photo_id) FROM m_photo WHERE type = ?";
			$data = $this->db_m_r->GetOne( $sql, $param );
		}
		else
		{	// フォト種別指定なし
			$sql = "SELECT COUNT(photo_id) FROM m_photo";
			$data = $this->db_m_r->GetOne( $sql );
		}
		if( $data !== false )
		{	// 正常に取得できたならキャッシュへ
			$cache_m->set( $cache_key, $data );
		}
		return $data;
	}

	/**
	 * フォトマスタ情報の取得（フォト種別指定版）
	 *
	 * @param int $type フォト種別（指定がない場合は全種別）
	 *
	 * @return array:指定種別のフォトマスタ情報の配列 | null:取得エラー
	 */
	function getMasterPhotoByType( $type = 'all' )
	{
		// キャッシュから取得してみる
		$cache_key = "getMasterPhotoByType_$type";	// キャッシュキー
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

		// 取得できない場合はDBから取得
		if( $type != 'all' )
		{	// フォト種別指定あり
			$param = array( $type );
			$sql = "SELECT * FROM m_photo WHERE type = ? ORDER BY photo_id";
			$data = $this->db_m_r->GetAll( $sql, $param );
		}
		else
		{	// フォト種別指定なし
			$sql = "SELECT * FROM m_photo ORDER BY photo_id";
			$data = $this->db_m_r->GetAll( $sql );
		}
		if( $data !== false )
		{	// 正常に取得できたならキャッシュへ
			$cache_m->set( $cache_key, $data );
		}
		return $data;
	}

	/**
	 * フォトマスタ情報の取得（フォト種別指定＆取得データのキーがphoto_id版）
	 *
	 * @param int $type フォト種別（指定がない場合は全種別）
	 *
	 * @return array:指定種別のフォトマスタ情報の配列 | null:取得エラー
	 */
	function getMasterPhotoByTypeEx( $type = 'all' )
	{
		// キャッシュから取得してみる
		$cache_key = "getMasterPhotoByTypeEx_$type";	// キャッシュキー
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if( $cache_data && !Ethna::isError( $cache_data ))
		{	// キャッシュから取得できた
			return $cache_data;
		}

		// とりあえずデータを取得
		$data = $this->getMasterPhotoByType( $type );

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

	/**
	 * フォトマスタ情報の取得（フォトID指定版）
	 *
	 * @param int $photo_id フォトID（指定なしor'all'の場合は全フォトを取得）
	 *
	 * @return array:フォトマスタ情報 | null:取得エラー
	 */
	function getMasterPhotoByPhotoId( $photo_id = 'all' )
	{
		// キャッシュから取得してみる
		$cache_key = "getMasterPhotoByPhotoId_$photo_id";	// キャッシュキー
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

		// 取得できない場合はDBから取得
		if( $photo_id != 'all' )
		{	// フォトID指定あり
			$param = array( $photo_id );
			$sql = "SELECT * FROM m_photo WHERE photo_id = ?";
			$data = $this->db_m_r->GetRow( $sql, $param );
		}
		else
		{	// フォト種別指定なし
			$sql = "SELECT * FROM m_photo ORDER BY photo_id";
			$data = $this->db_m_r->GetAll( $sql );
		}
		if( $data !== false )
		{	// 正常に取得できたならキャッシュへ
			$cache_m->set( $cache_key, $data );
		}
		return $data;
	}

	/**
	 * フォトマスタ情報の取得（フォトID複数指定版）
	 *
	 * @param array フォトIDの配列
	 *
	 * @return array:フォトマスタ情報の配列 | null:取得エラー
	 */
	function getMasterPhotoByPhotoIds( $photo_ids )
	{
		if( !is_array( $photo_ids ) || empty( $photo_ids ))
		{	// 引数エラー
			return null;
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		$where_in = array();
		for( $i = 0; $i < count( $photo_ids ); $i++ )
		{
			$where_in[] = '?';
		}

		$sql = "SELECT * FROM m_photo WHERE photo_id IN ( ".implode( ',', $photo_ids )." ) ORDER BY photo_id";
		return $this->db_m_r->GetAll( $sql );
	}

	/**
	 * フォトマスタ情報の取得（フォトID複数指定＆取得データのキーがphoto_id版）
	 *
	 * @param array フォトIDの配列
	 *
	 * @return array:フォトマスタ情報の配列 | null:取得エラー
	 */
	function getMasterPhotoByPhotoIdsEx( $photo_ids )
	{
		// とりあえずデータを取得
		$data = $this->getMasterPhotoByPhotoIds( $photo_ids );
		if( empty( $data ))
		{	// 取得エラー、もしくは該当データなし
			return $data;
		}

		// 配列のキーをphoto_idにする
		$new_data = array();
		foreach( $data as $v )
		{
			$new_data[$v['photo_id']] = $v;
		}

		return $new_data;
	}

	//================================================================================================
	//		ut_user_photo に関する処理
	//================================================================================================
	/**
	 * フォト所有情報の取得
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $photo_id フォトID
	 * @param boolean $from_master true:マスタから取得, false:スレーブから取得
	 *
	 * @return array:フォトマスタ情報の配列 | null:取得エラー
	 */
	function getUserPhoto( $pp_id, $photo_id, $from_master = false )
	{
		$db = ( $from_master === true ) ? $this->db : $this->db_r;
		$param = array( $pp_id, $photo_id );
		$sql = "SELECT * FROM ut_user_photo WHERE pp_id = ? AND photo_id = ?";
		return $db->GetRow( $sql, $param );
	}

	/**
	 * フォト所有情報の取得（複数件）
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $photo_ids フォトID配列
	 * @param boolean $from_master true:マスタから取得, false:スレーブから取得
	 *
	 * @return array:フォトマスタ情報の配列 | null:取得エラー
	 */
	function getUserPhotoByPhotoIds ( $pp_id, $photo_ids, $dsn = "db" )
	{
		if ( count( $photo_ids ) == 0 ) return array();

		$target = array();
		$param = array();
		foreach ( $photo_ids as $id ) {
			$target[] = "?";
			$param[] = $id;
		}

		$param[] = $pp_id;

		$sql = "SELECT * FROM ut_user_photo"
			. " WHERE photo_id IN ( %s ) AND pp_id = ?";

		return $this->$dsn->GetAll( sprintf( $sql, implode( ",", $target ) ), $param );
	}

	/**
	 * フォト所有情報の取得（フォト種別指定）
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $type フォト種別（指定がない場合は全種別）
	 *
	 * @return array:フォト所有情報の配列 | null:取得エラー
	 */
	function getUserPhotoByType( $pp_id, $type = 'all' )
	{
		// マスターデータの取得
		$master = $this->getMasterPhotoByType( $type );
		if( $master === false )
		{	// 取得エラー
			return null;
		}

		// ユーザーが所有するフォトのうち、指定の種別のフォトの情報を取得
		$param = array( $pp_id );
		$where_in_photo_id = array();
		foreach( $master as $row )
		{
			$param[] = $row['photo_id'];
			$where_in_photo_id[] = '?';
		}
		$sql = "SELECT * FROM ut_user_photo "
			 . "WHERE pp_id = ? AND photo_id IN ( ".implode( ',', $where_in_photo_id )." ) "
			 . "ORDER BY photo_id";

		return $this->db_r->GetAll( $sql, $param );
	}

	/**
	 * 獲得したフォトをフォト所有情報に追加
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $photo_ids 追加するフォトIDの配列
	 *
	 * @return boolean true:正常終了, false:エラー
	 */
	function addUserPhoto( $pp_id, $photo_ids )
	{
		// 同一処理のものは先にやっておく
		$param = array( $pp_id, 0, self::PHOTO_LV_MAX, self::PHOTO_LV_MAX );
		$sql = "INSERT INTO ut_user_photo( pp_id, photo_id, date_created ) "
			 . "VALUES( ?, ?, NOW()) "
			 . "ON DUPLICATE KEY UPDATE photo_lv = IF(( photo_lv < ? ), ( photo_lv + 1 ), ? )";

		// 各フォトIDに対して実行
		foreach( $photo_ids as $photo_id )
		{
			$param[1] = $photo_id;
			if( !$this->db->execute( $sql, $param ))
			{	// エラー
				return false;
			}
		}
		return true;
	}

	/**
	 * 指定のフォトIDの中でフォトLVが最大のフォト所有情報を取得
	 *
	 * @param array $photo_ids チェック対象のフォトIDの配列（指定がない場合は全所有フォトが対象）
	 *
	 * @return array:フォト所有情報の配列 | null:取得エラー
	 */
	function getUserPhotoMaxLvByPhotoIds( $pp_id, $photo_ids = null )
	{
		$param = array( $pp_id );
		$sql = "SELECT * FROM ut_user_photo WHERE pp_id = ? ";
		if( !empty( $photo_ids ))
		{	// フォトID指定あり
			$where_in = array();
			foreach( $photo_ids as $photo_id )
			{
				$where_in[] = '?';
				$param[] = $photo_id;
			}
			$sql .= "AND photo_id IN ( ".implode( ',', $where_in )." ) ";
		}
		$param[] = self::PHOTO_LV_MAX;
		$sql .= "AND photo_lv = ?";

		return $this->db_r->GetAll( $sql, $param );
	}

	/**
	 * 渡されたut_user_photoのデータをmodify_photo形式に変換する
	 */
	function convertModifyPhoto ( $user_photo )
	{
		$modify_photo = array();

		foreach ( $user_photo as $key => $row ) {
			$modify_photo[] = array(
				"photo_id"	=> intval( $row['photo_id'] ),
				"photo_lv"	=> intval( $row['photo_lv'] ),
			);
		}

		return $modify_photo;
	}
}
?>
