<?php
/**
 *	Api/Shop/List.php
 *	ショップ・商品一覧取得
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

/**
 *	api_shop_list Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_ApiShopList extends Pp_ApiActionForm
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
	);
}

/**
 *	api_shop_list action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_ApiShopList extends Pp_ApiActionClass
{
	/**
	 *	preprocess of api_shop_list Action.
	 *
	 *	@access public
	 *	@return string	  forward name(null: success.
	 *								  false: in case you want to exit.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{
			return 'error_400';
		}
		return null;
	}

	/**
	 *	api_shop_list action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$shop_m =& $this->backend->getManager( "Shop" );
		$user_m =& $this->backend->getManager( "User" );
		
		$user = $user_m->getUserBase( $pp_id );
		
		$m_shop = $shop_m->getOpenMasterShopList( $user['device_type'] );
		$m_sell = $shop_m->getOpenMasterSellList();
		
		// 整形
		$shop_list = array();
		foreach ( $m_shop as $key => $shop ) {
			$shop_list[$key] = array(
				"shop_id"		=> $shop['shop_id'],
				"shop_name"		=> $shop['name_ja'],
			);
			
			foreach ( $m_sell[$shop['shop_id']] as $sell ) {
				$shop_list[$key]['sell_list'][] = array(
					"sell_id"		=> $sell['sell_id'],
					"price"			=> $sell['price'],
					"item_id"		=> $sell['item_id'],
					"item_num"		=> $sell['num'],
					"sort_no"		=> $sell['sort_no'],
					"product_id"	=> $sell['product_id'],
				);
			}
		}
		
//error_log( print_r( $shop_list, 1 ) );
		
		$this->af->setApp( "shop_list", $shop_list, true );

		return 'api_json_encrypt';
	}
}
