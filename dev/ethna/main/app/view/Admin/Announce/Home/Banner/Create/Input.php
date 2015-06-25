<?php
/**
 *  Admin/Announce/Home/Banner/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_home_banner_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHomeBannerCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_home_banner_create_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$row = array(
			'ua'         => Pp_UserManager::OS_IPHONE_ANDROID,
			'pri'        => 99,
			'date_start' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
//			'date_end'   => '9999-12-31 23:59:59',
			'date_end'   => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + 86400 * 7),
		);

		$this->af->setApp('row', $row);
    }
}

?>