<?php
/**
 *  Admin/Developer/Gacha/Banner/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_banner_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaBannerCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_developer_gacha_banner_create_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		$user_m =& $this->backend->getManager('User');
		
		$row = $this->af->getApp('row');
		
		if (!$row || !is_array($row)) { // 新規の場合（複製ではない場合）
			$row = array(
				'type'       => Pp_ShopManager::GACHA_TYPE_BRONZE,
				'sort_list'  => 1,
				'price'      => 300,
				'banner_type' => Pp_ShopManager::GACHA_BANNER_TYPE_NONE,
				'banner_url' => '',
				'ua'         => Pp_UserManager::OS_IPHONE_ANDROID,
				'width'      => 620,
				'height'     => 888,
				'position_x' => 0,
				'position_y' => 0,
				'date_start' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
				'date_end'   => '9999-12-31 23:59:59',
			);
			
			$this->af->setApp('row', $row);
		}
    }
}

?>