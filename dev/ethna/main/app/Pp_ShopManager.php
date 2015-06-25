<?php
/**
 * Pp_ShopManager.php
 *
 * ショップマネージャ
 * サイコパスゲームでは、ショップは商品の大カテゴリとして扱う
 * 例えばショップ「フォトフィルム」は子に「フォトフィルム*1」「フォトフィルム*3」等を持つ
 *
 * @author 	{$author}
 * @package	Pp
 * @version	$Id$
 */

/**
 * Pp_ShopManager
 *
 * @author 	{$author}
 * @access 	public
 * @package	Pp
 */
class Pp_ShopManager extends Ethna_AppManager
{
	protected $db_m_r = null;
	
    /**
     * オープン中のショップリスト取得
	 * 
	 * 直接SQL結果をキャッシュすると日付が古い可能性があるので、
	 * マスター全件取得（こっちはキャッシュでもよい）後に日付をPHP側で見て判別する
     */
	function getOpenMasterShopList ( $platform )
	{
		$date = date( "Y-m-d H:i:s" );

		$list = $this->getMasterShopList( $platform );

		foreach ( $list as $key => $row )
		{
			if ( $date < $row['date_start'] || $row['date_end'] < $date )
			{
				unset( $list[$key] );
			}
		}

		return $list;
	}
	
	/**
	 * ショップマスターの全件を取得
	 */
	function getMasterShopList ( $platform )
	{
		if ( is_null( $this->db_m_r ) ) {
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}

		// キャッシュが存在していればそれを返す
		$cache_key = "m_shop__" . __FUNCTION__ . "__".$platform;
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ) )
		{	// キャッシュから取得できた
			return $cache_data;
		}

		$param = array( $platform );
		$sql = "SELECT m.shop_id AS id, m.* FROM m_shop m WHERE platform_id = ? ORDER BY shop_id ASC";
		$list = $this->db_m_r->db->getAssoc( $sql, $param );
		if ( Ethna::isError( $list ) )
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db_m_r->db->ErrorNo(), $this->db_m_r->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		if ( count( $list ) > 0 )
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $list );
		}

		return $list;
	}
	
	/**
     * オープン中の販売リスト取得
	 * 
	 * 直接SQL結果をキャッシュすると日付が古い可能性があるので、
	 * マスター全件取得（こっちはキャッシュでもよい）後に日付をPHP側で見て判別する
	 * ついでにshop_id => sell_id形式に直して返す
     */
	function getOpenMasterSellList ()
	{
		$date = date( "Y-m-d H:i:s" );

		$list = $this->getMasterSellList();
		
		$sell_list = array();
		foreach ( $list as $key => $row )
		{
			if ( $date < $row['date_start'] || $row['date_end'] < $date )
			{
				continue;
			}
			
			$sell_list[$row['shop_id']][$row['sell_id']] = $row;
		}

		return $sell_list;
	}
	
	/**
	 * 販売リストマスタを取得
	 * テーブルが分かれているが、同じDB上にあるのでjoinして取り込む
	 */
	function getMasterSellList ()
	{
		if ( is_null( $this->db_m_r ) ) {
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
		
		// キャッシュが存在していればそれを返す
		$cache_key = "m_shop__" . __FUNCTION__;
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_data = $cache_m->get( $cache_key, 3600 );
		if ( $cache_data && !Ethna::isError( $cache_data ) )
		{	// キャッシュから取得できた
			return $cache_data;
		}
		
		$sql =	"SELECT " .
					"sl.shop_id, sl.sell_id, sl.name_ja, sl.price, sl.sort_no, sl.date_start, sl.date_end," .
					"si.item_id, si.num, si.product_id " .
				"FROM " .
					"m_sell_list sl, m_sell_item si " .
				"WHERE " .
					"sl.sell_id = si.sell_id " .
				"ORDER BY sl.sort_no ASC";
		
		$list = $this->db_m_r->db->getArray( $sql );
		if ( Ethna::isError( $list ) )
		{
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db_m_r->db->ErrorNo(), $this->db_m_r->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		if ( count( $list ) > 0 )
		{	// 取得したデータをキャッシュする
			$cache_m->set( $cache_key, $list );
		}

		return $list;
	}
	
	/**
	 * 商品をひとつ取得
	 * 使う箇所限られてるし、キャッシュ見なくてもいい……か？
	 *
	 * @param int $sell_id 商品ID
	 * @return array 商品データ
	 */
	function getMasterSell ( $sell_id )
	{
		if ( is_null( $this->db_m_r ) ) {
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
		
		$param = array( $sell_id );
		$sql =	"SELECT " .
					"si.sell_id AS id, sl.shop_id, sl.sell_id, sl.name_ja, sl.price, sl.sort_no, sl.date_start, sl.date_end," .
					"si.item_id, si.num, si.product_id " .
				"FROM " .
					"m_sell_list sl, m_sell_item si " .
				"WHERE " .
					"sl.sell_id = si.sell_id AND sl.sell_id = ?";
		
		return $this->db_m_r->GetRow( $sql, $param );
	}
}
?>