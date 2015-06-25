<?php
/**
 *  Admin/Developer/User/View/Userfriend.php
 *
 *  1行を編集できるだけのビュー。
 *  Pp_Action_AdminDeveloperUserViewから呼ばれる。
 *  t_user_baseのカラム数が多くてViewableGridを使用すると表示が横に伸びすぎるので、
 *  ViewableGridは使用せず、1行についての各カラム内容を縦に並べて表示する為に、このビューを作成した。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_view_userfriend view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserViewUserfriend extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$user_m =& $this->backend->getManager('User');
		$friend_m =& $this->backend->getManager('AdminFriend');
		
		$user_id = $this->af->get('id');
		$user_base = $user_m->getUserBase($user_id);
		$friend_list = array();
		$friend_cnt = array();
		
		//フレンドID一覧を取得
		for ($i = 1; $i <= 4; $i++) {
			$friend_list[$i] = $friend_m->getFriendListAd($user_id, $i);
			$friend_cnt[$i] = count($friend_list[$i]);
			//error_log("$i=".print_r($friend_list[$i],true));
		}
		
		$this->af->setApp('base', $user_base);
		$this->af->setApp('friend_list', $friend_list);
		$this->af->setApp('friend_cnt',  $friend_cnt);
		
		parent::preforward();
    }
}
?>