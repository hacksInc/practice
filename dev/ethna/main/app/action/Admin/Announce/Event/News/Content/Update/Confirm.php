<?php
/**
 *  Admin/Announce/Event/News/Content/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_event_news_content_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceEventNewsContentUpdateConfirm extends Pp_Form_AdminAnnounceEventNewsContent
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'content_id',
		'ua',
		'priority',
		'date_disp',
		'body',
		'date_start',
		'date_end',
		'banner_image',
		'banner_disabled',
    );
}

/**
 *  admin_announce_event_news_content_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceEventNewsContentUpdateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_event_news_content_update_confirm Action.
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
     *  admin_announce_event_news_content_update_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$news_m =& $this->backend->getManager('AdminNews');
		
		$content_id   = $this->af->get('content_id');
		$banner_image = $this->af->get('banner_image');
		
		$confirm_uniq = uniqid();
		
		$banner_uploaded = false; // バナー画像がアップロードされたか
		$banner_filename = null;  // アップロードされたバナー画像ファイルのサーバ内パス
		$banner_data = null;      // バナー画像のData URLスキーム表記
		if ($banner_image && ($banner_image['error'] == UPLOAD_ERR_OK) && ($banner_image['size'] > 0)) {
			$banner_uploaded = true;
			$banner_filename = $this->af->getAdminTmpBannerFilename($confirm_uniq);
			umask(0002);
			move_uploaded_file($banner_image['tmp_name'], $banner_filename);
			$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));
		}
		
		$row = $news_m->getEventNewsContent($content_id);
		if (!$row) {
			return 'admin_error_500';
		}

		// テンプレート変数アサイン
		$this->af->setApp('confirm_uniq',    $confirm_uniq);
		$this->af->setApp('banner_uploaded', $banner_uploaded ? 1 : 0);
		$this->af->setApp('banner_data',     $banner_data);
		$this->af->setApp('row',             $row);
		
        return 'admin_announce_event_news_content_update_confirm';
    }
}

?>