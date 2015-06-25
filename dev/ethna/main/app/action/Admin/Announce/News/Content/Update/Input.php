<?php
/**
 *  Admin/Announce/News/Content/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_news_content_update_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceNewsContentUpdateInput extends Pp_Form_AdminAnnounceNewsContent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'content_id',
	);
}

/**
 *  admin_announce_news_content_update_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceNewsContentUpdateInput extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_news_content_update_input Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
/*
	function prepare()
	{
		// アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
	}
 */

	/**
	 *  admin_announce_news_content_update_input action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$news_m =& $this->backend->getManager('AdminNews');
		$content_id = $this->af->get('content_id');

		$row = $news_m->getNewsContent($content_id);
		if (!$row) {
			return 'admin_error_500';
		}

		// テンプレート変数アサイン
		$this->af->setApp('row', $row);
		$this->af->setAppNe('title', $row['title']);
		$this->af->setAppNe('abridge', $row['abridge']);
		$this->af->setAppNe('body', $row['body']);

		return 'admin_announce_news_content_update_input';
	}
}
