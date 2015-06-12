<?php
/**
 *  Admin/Announce/News/Content/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_news_content_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceNewsContentCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_news_content_create_exec' => null,
	);

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		//		$content_id = $this->af->get('content_id');

		// 表示終了日時デフォルト値の設定
		//		if ($content_id) {
		//			// 「複製」の場合は現在
		//			$date_end = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		//		} else {
		// 入力日時から7日後の終わり時間
		$date_end = date('Y-m-d', $_SERVER['REQUEST_TIME'] + (86400 * 7)) . ' 14:59:59';
		//		}

		$this->af->setApp('date_end', $date_end);
	}
}

