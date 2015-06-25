<?php
/**
 *	Admin/Developer/Gacha/Banner/Index.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *	admin_developer_gacha_banner_index view implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_View_AdminDeveloperGachaBannerIndex extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_gacha_banner_create_exec' => null,
	);

	/**
	 *	preprocess before forwarding.
	 *
	 *	@access public
	 */
	function preforward()
	{
		$shop_m =& $this->backend->getManager('AdminShop');
		
		$date_draw_start = date('Y-m-d', $_SERVER['REQUEST_TIME']) . ' 00:00:00';
		
		$list = $shop_m->getGachaListForAdmin();
		
		if ($list) foreach ($list as $i => $row) {
			if ($row['date_start'] > date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])) {
				$status = 'waiting';
			} else if ($row['date_end'] > date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])) {
				$status = 'active';
			} else {
				$status = null;
			}
			
			$list[$i]['status'] = $status;

			$gacha_id = $row['gacha_id'];
//			$list[$i]['gacha_order_info'] = $shop_m->getGachaOrderInfo($gacha_id);
//			$list[$i]['gacha_cnt_today'] = $shop_m->countGachaDrawListDate($gacha_id, $date_draw_start);

			// ガチャのトランザクション情報があるかチェック
			$is_exists = false;		// トランザクション情報があるかどうか
			$is_broken = false;		// 必要なトランザクション情報が全て揃っていなければ false
			$info = $shop_m->getGachaListInfo( $gacha_id );
			if(( is_array( $info ) === true )&&( count( $info ) > 0 ))
			{	// 管理情報
				$list[$i]['transaction_info']['date_created'] = $info['date_created'];
				$is_exists = true;
			}
			if( $shop_m->isGachaCategoryInfoExists( $gacha_id ) === false )
			{	// 一次抽選テーブル
				$is_broken = true;
			}
			if( $shop_m->isGachaItemInfoExists( $gacha_id ) === false )
			{	// 二次抽選テーブル
				$is_broken = true;
			}

			$list[$i]['extra_gacha']['is_exists'] = $shop_m->isGachaExtraType( $gacha_id );
			if( $list[$i]['extra_gacha']['is_exists'] === true )
			{	// おまけガチャあり
				// トランザクションテーブルがあるかをチェック
				$info = $shop_m->getGachaExtraListInfo( $gacha_id );
				if( is_array( $info ) === false || count( $info ) === 0 )
				{	// 管理情報
					$is_broken = true;
				}
				if( $shop_m->isGachaExtraCategoryInfoExists( $gacha_id ) === false )
				{	// 一次抽選テーブル
					$is_broken = true;
				}
				if( $shop_m->isGachaExtraItemInfoExists( $gacha_id ) === false )
				{	// 二次抽選テーブル
					$is_broken = true;
				}
			}
			$list[$i]['transaction_info']['is_exists'] = $is_exists;
			$list[$i]['transaction_info']['is_broken'] = $is_broken;
		}
		
		$this->af->setApp('list', $list);
//		$this->af->setAppNe('list', $list);
		$this->af->setApp('form_template', $this->af->form_template);
	}
}

?>