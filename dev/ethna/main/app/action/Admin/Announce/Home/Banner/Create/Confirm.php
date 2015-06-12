<?php
/**
 *  Admin/Announce/Home/Banner/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_home_banner_create_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHomeBannerCreateConfirm extends Pp_Form_AdminAnnounceHomeBanner
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'ua',
		'type',
		'pri',
		'memo',
		'url_ja',
		'banner_attribute',
		'date_start',
		'date_end',
		'banner_image',
	);
}

/**
 *  admin_announce_home_banner_create_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHomeBannerCreateConfirm extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_home_banner_create_confirm Action.
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
	 *  admin_announce_home_banner_create_confirm action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$news_m =& $this->backend->getManager('AdminNews');

		$banner_image = $this->af->get('banner_image');

		$confirm_uniq = uniqid();

		if (!$banner_image || ($banner_image['error'] != UPLOAD_ERR_OK) || ($banner_image['size'] <= 0)) {
			$this->af->ae->add(null, "画像ファイルが不正です。", E_ERROR_DEFAULT);
			return 'admin_error_500';
		}
		// 画像ファイルを、次の完了画面まで保持するためのテンポラリのパスへ設置
		$banner_filename = $this->af->getAdminTmpBannerFilename($confirm_uniq);
		umask(0002);
		move_uploaded_file($banner_image['tmp_name'], $banner_filename);

		// 画像のData URLスキーム表記を作成
		$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));

		// テンプレート変数アサイン
		$this->af->setApp('confirm_uniq', $confirm_uniq);
		$this->af->setApp('banner_data',  $banner_data);

		return 'admin_announce_home_banner_create_confirm';
	}
}
