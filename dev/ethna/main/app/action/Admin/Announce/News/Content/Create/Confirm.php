<?php
/**
 *  Admin/Announce/News/Content/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_news_content_create_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceNewsContentCreateConfirm extends Pp_Form_AdminAnnounceNewsContent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'priority',
		//		'date_disp',
		'title',
		'abridge',
		'body',
		'banner',
		'picture',
		'date_start',
		'date_end',
		'banner_no' => array('required' => false),
		'picture_no' => array('required' => false),
	);
}

/**
 *  admin_announce_news_content_create_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceNewsContentCreateConfirm extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_news_content_create_confirm Action.
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
	 *  admin_announce_news_content_create_confirm action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		// アナウンスバナー
		$banner = $this->af->get('banner');
		$banner_data = null;
		$confirm_uniq_banner = null;
		if ($banner['tmp_name'])
		{
			if ($banner['error'] != UPLOAD_ERR_OK || $banner['size'] <= 0)
			{
				$this->af->ae->add(null, "バナー画像ファイルが不正です。", E_ERROR_DEFAULT);
				return 'admin_error_500';
			}
			$confirm_uniq_banner = uniqid();
			$banner_filename = $this->af->getAdminTmpFilename($confirm_uniq_banner);
			umask(0002);
			move_uploaded_file($banner['tmp_name'], $banner_filename);
			$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));
		}
		// アナウンス全文用画像
		$picture = $this->af->get('picture');
		$picture_data = null;
		$confirm_uniq_picture = null;
		if ($picture['tmp_name'])
		{
			if ($picture['error'] != UPLOAD_ERR_OK || $picture['size'] <= 0)
			{
				$this->af->ae->add(null, "全文用画像ファイルが不正です。", E_ERROR_DEFAULT);
				return 'admin_error_500';
			}
			$confirm_uniq_picture = uniqid();
			$picture_filename = $this->af->getAdminTmpFilename($confirm_uniq_picture);
			umask(0002);
			move_uploaded_file($picture['tmp_name'], $picture_filename);
			$picture_data = 'data:image/png;base64,' . base64_encode(file_get_contents($picture_filename));
		}

		// テンプレート変数アサイン
		$this->af->setApp('confirm_uniq_banner', $confirm_uniq_banner);
		$this->af->setApp('banner_data',  $banner_data);
		$this->af->setApp('banner_no',  $this->af->get('banner_no'));
		$this->af->setApp('confirm_uniq_picture', $confirm_uniq_picture);
		$this->af->setApp('picture_data',  $picture_data);
		$this->af->setApp('picture_no',  $this->af->get('picture_no'));

		return 'admin_announce_news_content_create_confirm';
	}
}
