<?php
/**
 *  Admin/Announce/News/Content/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_news_content_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceNewsContentUpdateExec extends Pp_Form_AdminAnnounceNewsContent
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
		'date_start',
		'date_end',
		'confirm_uniq_banner',
		'banner' => array('required' => false),
		'banner_uploaded',
		'confirm_uniq_picture',
		'picture' => array('required' => false),
		'picture_uploaded',
    );
}

/**
 *  admin_announce_news_content_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceNewsContentUpdateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_news_content_update_exec Action.
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

		$banner_uploaded = $this->af->get('banner_uploaded');
		if ($banner_uploaded) {
			$confirm_uniq_banner = $this->af->get('confirm_uniq_banner');
			$banner_filename = $this->af->getAdminTmpFilename($confirm_uniq_banner);

			if (!(file_exists($banner_filename) && (filesize($banner_filename) > 0))) {
				return 'admin_error_500';
			}

			$this->af->setApp('banner_filename', $banner_filename);
		}

		$picture_uploaded = $this->af->get('picture_uploaded');
		if ($picture_uploaded) {
			$confirm_uniq_picture = $this->af->get('confirm_uniq_picture');
			$picture_filename = $this->af->getAdminTmpFilename($confirm_uniq_picture);

			if (!(file_exists($picture_filename) && (filesize($picture_filename) > 0))) {
				return 'admin_error_500';
			}

			$this->af->setApp('picture_filename', $picture_filename);
		}
    }

    /**
     *  admin_announce_news_content_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$news_m =& $this->backend->getManager('AdminNews');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}
		$content_id = $this->af->get('content_id');

		$banner_filename = $this->af->getApp('banner_filename');
		$picture_filename = $this->af->getApp('picture_filename');

		$row = $news_m->getNewsContent($content_id);
		if (!$row) {
			return 'admin_error_500';
		}

		$banner_data = null;
		if ($banner_filename) {
			$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));
		}
		$picture_data = null;
		if ($picture_filename) {
			$picture_data = 'data:image/png;base64,' . base64_encode(file_get_contents($picture_filename));
		}

		$columns = array(
			'content_id'             => $content_id,
			'priority'               => $this->af->get('priority'),
			'title'                  => $this->af->get('title'),
			'abridge'                => $this->af->get('abridge'),
			'body'                   => $this->af->get('body'),
			'date_disp'              => $this->af->get('date_disp'),
			'date_start'             => $this->af->get('date_start'),
			'date_end'               => $this->af->get('date_end'),
		);

		if ($banner_filename) {
			$columns['banner'] = "1";
		}
		if ($picture_filename) {
			$columns['picture'] = "1";
		}

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// DBへ登録
		$ret = $news_m->updateNewsContent($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}

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
		}

		// ログ
		$log_columns = $columns;
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

        return 'admin_announce_news_content_update_exec';
    }
}

?>
