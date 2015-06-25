<?php
/**
 *  Admin/Announce/News/Content/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_news_content_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceNewsContentCreateExec extends Pp_Form_AdminAnnounceNewsContent
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'priority',
//		'date_disp',
		'date_disp' => array('required' => false, 'custom' => null),
		'title',
		'abridge',
		'body',
		'banner' => array('required' => false),
		'picture' => array('required' => false),
		'date_start',
		'date_end',
		'confirm_uniq_banner',
		'confirm_uniq_picture',
		'banner_no' => array('required' => false),
		'picture_no' => array('required' => false),
    );
}

/**
 *  admin_announce_news_content_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceNewsContentCreateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_news_content_create_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */

    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
		$banner_filename = null;
		$confirm_uniq_banner = $this->af->get('confirm_uniq_banner');
		// バナー画像
		if ($confirm_uniq_banner)
		{
			$banner_filename = $this->af->getAdminTmpFilename($confirm_uniq_banner);
			if (!(file_exists($banner_filename) && (filesize($banner_filename) > 0)))
			{
				return 'admin_error_500';
			}
			$this->af->setApp('banner_filename', $banner_filename);
		}

		// 全文用画像
		$picture_filename = null;
		$confirm_uniq_picture = $this->af->get('confirm_uniq_picture');
		if ($confirm_uniq_picture)
		{
			$picture_filename = $this->af->getAdminTmpFilename($confirm_uniq_picture);
			if (!(file_exists($picture_filename) && (filesize($picture_filename) > 0)))
			{
				return 'admin_error_500';
			}
			$this->af->setApp('picture_filename', $picture_filename);
		}
    }

    /**
     *  admin_announce_news_content_create_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$news_m =& $this->backend->getManager('AdminNews');
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');

		$banner_no = $this->af->get('banner_no');
		$picture_no = $this->af->get('picture_no');

		$banner_filename = $this->af->getApp('banner_filename');
		if ($banner_filename)
		{
			$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));
		}
		$picture_filename = $this->af->getApp('picture_filename');
		if ($picture_filename)
		{
			$picture_data = 'data:image/png;base64,' . base64_encode(file_get_contents($picture_filename));
		}
		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}

		$columns['lang'] = $this->af->getLang0();
		$columns['ua'] = $this->af->getUa0();

		$columns['date_disp'] = $this->af->get('date_start');

		if ($banner_filename || $banner_no) {
			$columns['banner'] = "1";
		}
		if ($picture_filename || $picture_no) {
			$columns['picture'] = "1";
		}
		// 仮挿入
		$columns['test_flag'] = 0;

		$ua0 = $this->af->getUa0();
		if ($ua0 == 0) {
			$ua_array = array(Pp_UserManager::OS_IPHONE, Pp_UserManager::OS_ANDROID);
		} else {
			$ua_array = array($ua0);
		}

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		$ret = $news_m->insertNewsContent($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}

		$content_id = $news_m->getLastInsertContentId();

		// ファイルを登録
		if ($banner_filename) {
			$banner_dest = $news_m->getNewsContentBannerPath($content_id);
			umask(0002);

			// ディレクトリ作成
			Pp_Util::mkdir(dirname($banner_dest));

			if (!rename($banner_filename, $banner_dest)) {
				$db->rollback();
				return 'admin_error_500';
			}
		} elseif ($banner_no) {
			$banner_dest_old = $news_m->getNewsContentBannerPath($banner_no);
			$banner_dest = $news_m->getNewsContentBannerPath($content_id);
			umask(0002);

			// ディレクトリ作成
			Pp_Util::mkdir(dirname($banner_dest));

			if (!copy($banner_dest_old, $banner_dest)) {
				$db->rollback();
				return 'admin_error_500';
			}
		}
		if ($picture_filename) {
			$picture_dest = $news_m->getNewsContentPicturePath($content_id);
			umask(0002);

			// ディレクトリ作成
			Pp_Util::mkdir(dirname($picture_dest));

			if (!rename($picture_filename, $picture_dest)) {
				$db->rollback();
				return 'admin_error_500';
			}
		} elseif ($picture_no) {
			$picture_dest_old = $news_m->getNewsContentPicturePath($picture_no);
			$picture_dest = $news_m->getNewsContentPicturePath($content_id);
			umask(0002);

			// ディレクトリ作成
			Pp_Util::mkdir(dirname($picture_dest));

			if (!copy($picture_dest_old, $picture_dest)) {
				$db->rollback();
				return 'admin_error_500';
			}
		}

		// ログ
		$log_columns = $columns;
		$log_columns['content_id'] = $content_id;
		if ($banner_data)
		{
			$log_columns['banner_data'] = $banner_data;
		}
		if ($picture_data)
		{
			$log_columns['picture_data'] = $picture_data;
		}
		$admin_m->addAdminOperationLog('/announce/news', 'content_log',
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $log_columns)
		);

		// トランザクション完了
		$db->commit();

        return 'admin_announce_news_content_create_exec';
    }
}

?>
