<?php
/**
 *  Admin/Announce/News/Content/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_news_content_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceNewsContentUpdateConfirm extends Pp_Form_AdminAnnounceNewsContent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'content_id',
		'priority',
		'date_disp',
		'title',
		'abridge',
		'body',
		'banner' => array('required' => false),
		'picture' => array('required' => false),
		'date_start',
		'date_end',
	);
}

/**
 *  admin_announce_news_content_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceNewsContentUpdateConfirm extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_news_content_update_confirm Action.
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
	 *  admin_announce_news_content_update_confirm action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$news_m =& $this->backend->getManager('AdminNews');
		$content_id   = $this->af->get('content_id');

		// アナウンスバナー
		$banner = $this->af->get('banner');
		$confirm_uniq_banner = uniqid();
		$banner_uploaded = false; // バナー画像がアップロードされたか
		$banner_filename = null;  // アップロードされたバナー画像ファイルのサーバ内パス
		$banner_data = null;      // バナー画像のData URLスキーム表記
		if ($banner && ($banner['error'] == UPLOAD_ERR_OK) && ($banner['size'] > 0)) {
			$banner_uploaded = true;
			$banner_filename = $this->af->getAdminTmpFilename($confirm_uniq_banner);
			umask(0002);
			move_uploaded_file($banner['tmp_name'], $banner_filename);
			$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));
		}

		// アナウンス全文用画像
		$picture = $this->af->get('picture');
		$confirm_uniq_picture = uniqid();
		$picture_uploaded = false; // バナー画像がアップロードされたか
		$picture_filename = null;  // アップロードされたバナー画像ファイルのサーバ内パス
		$picture_data = null;      // バナー画像のData URLスキーム表記
		if ($picture && ($picture['error'] == UPLOAD_ERR_OK) && ($picture['size'] > 0)) {
			$picture_uploaded = true;
			$picture_filename = $this->af->getAdminTmpFilename($confirm_uniq_picture);
			umask(0002);
			move_uploaded_file($picture['tmp_name'], $picture_filename);
			$picture_data = 'data:image/png;base64,' . base64_encode(file_get_contents($picture_filename));
		}

		$row = $news_m->getNewsContent($content_id);
		if (!$row) {
			return 'admin_error_500';
		}

		// テンプレート変数アサイン
		$this->af->setApp('confirm_uniq_banner', $confirm_uniq_banner);
		$this->af->setApp('banner_uploaded', $banner_uploaded ? 1 : 0);
		$this->af->setApp('banner_data', $banner_data);
		$this->af->setApp('confirm_uniq_picture', $confirm_uniq_picture);
		$this->af->setApp('picture_uploaded', $picture_uploaded ? 1 : 0);
		$this->af->setApp('picture_data', $picture_data);
		$this->af->setApp('row', $row);

		return 'admin_announce_news_content_update_confirm';
	}
}
