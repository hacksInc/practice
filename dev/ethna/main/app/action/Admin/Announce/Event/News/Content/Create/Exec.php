<?php
/**
 *  Admin/Announce/Event/News/Content/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_event_news_content_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceEventNewsContentCreateExec extends Pp_Form_AdminAnnounceEventNewsContent
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'ua',
		'priority',
		'date_disp',
		'body',
		'date_start',
		'date_end',
		'banner_image',
		'confirm_uniq',
		'banner_uploaded',
    );
}

/**
 *  admin_announce_event_news_content_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceEventNewsContentCreateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_event_news_content_create_exec Action.
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
			$confirm_uniq = $this->af->get('confirm_uniq');
			$banner_filename = $this->af->getAdminTmpBannerFilename($confirm_uniq);
			
			if (!(file_exists($banner_filename) && (filesize($banner_filename) > 0))) {
				return 'admin_error_500';
			}
			
			$this->af->setApp('banner_filename', $banner_filename);
		}
    }

    /**
     *  admin_announce_event_news_content_create_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$news_m =& $this->backend->getManager('AdminNews');
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');

		$ua              = $this->af->get('ua');
		$priority        = $this->af->get('priority');
		$date_disp       = $this->af->get('date_disp');
		$body            = $this->af->get('body');
		$date_start      = $this->af->get('date_start');
		$date_end        = $this->af->get('date_end');
		
		$banner_filename = $this->af->getApp('banner_filename');
		
		$banner = null;
		$banner_data = null;
		if ($banner_filename) {
			$banner = 1;
			$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));
		} else {
			$banner = 0;
		}
		
		$columns = array(
			'ua'         => $ua,
			'priority'   => $priority,
			'date_disp'  => $date_disp,
			'body'       => $body,
			'date_start' => $date_start,
			'date_end'   => $date_end,
			'banner'     => $banner,
			'account_reg' => $this->session->get('lid'),
		);
		
		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// DBへ登録
		$ret = $news_m->insertEventNewsContent($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}
		
		$content_id = $news_m->getLastInsertEventNewsContentId();
		
		// ファイルを登録
		if ($banner_filename) {
			$banner_dest = $news_m->getEventNewsBannerPath($content_id);
			umask(0002);
			if (!rename($banner_filename, $banner_dest)) {
				$db->rollback();
				return 'admin_error_500';
			}
		}
			
		// トランザクション完了
		$db->commit();
		
		// ログ
		$log_columns = $columns;
		$log_columns['content_id'] = $content_id;
		if ($banner_data) {
			$log_columns['banner_data'] = $banner_data;
		}

		$admin_m->addAdminOperationLog('/announce/event_news', 'content_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $log_columns)
		);

        return 'admin_announce_event_news_content_create_exec';
    }
}

?>