<?php
/**
 *  Admin/Announce/Loginbonus/Content/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_loginbonus_content_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceLoginbonusContentIndex extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$loginbonus_m =& $this->backend->getManager('AdminLoginbonus');

		$loginbonus = $loginbonus_m->getLoginbonusAll();
	//	$this->af->setApp('loginbonus', $loginbonus);
		$this->af->setApp('loginbonus', array_reverse($loginbonus));
		
		//現在のログインボーナス
		$now_date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$today = substr($now_date, 0, 10);//YYYY-mm-dd のみ抽出
		$lb = $loginbonus_m->getLoginbonus($today);
		$this->af->setApp('lb', $lb);
		
		//IDの最大値を求める
		$max_id = 0;
		foreach($loginbonus as $val) {
			if ($max_id < $val['login_bonus_id'])
				$max_id = $val['login_bonus_id'];
		}
		$this->af->setApp('max_id', $max_id+1);
		
		parent::preforward();
    }
}
?>