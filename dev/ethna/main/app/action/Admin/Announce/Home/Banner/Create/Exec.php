<?php
/**
 *  Admin/Announce/Home/Banner/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_home_banner_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHomeBannerCreateExec extends Pp_Form_AdminAnnounceHomeBanner
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
		'confirm_uniq',
		'banner_image' => array('required' => false),
		'hbanner_id'   => array('required' => false),
		'img_id'       => array('required' => false),
	);
}

/**
 *  admin_announce_home_banner_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHomeBannerCreateExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_home_banner_create_exec Action.
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

		$confirm_uniq = $this->af->get('confirm_uniq');
		$banner_filename = $this->af->getAdminTmpBannerFilename($confirm_uniq);

		if (!(file_exists($banner_filename) && (filesize($banner_filename) > 0))) {
			return 'admin_error_500';
		}

		$this->af->setApp('banner_filename', $banner_filename);
	}

	/**
	 *  admin_announce_home_banner_create_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$news_m =& $this->backend->getManager('AdminNews');
		$admin_m =& $this->backend->getManager('Admin');

		$banner_filename = $this->af->getApp('banner_filename');
		$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));

		$columns = array(
			'ua'                     => $this->af->get('ua'),
			'type'                   => $this->af->get('type'),
			'pri'                    => $this->af->get('pri'),
			'memo'                   => $this->af->get('memo'),
			'url_ja'                 => $this->af->get('url_ja'),
			'banner_attribute'       => $this->af->get('banner_attribute'),
			'date_start'             => $this->af->get('date_start'),
			'date_end'               => $this->af->get('date_end'),
			'account_reg'            => $this->session->get('lid'),
		);

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// DBへ登録
		$ret = $news_m->insertHomeBanner($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}

		$img_id     = $news_m->getLastInsertHomeBannerId('img_id');
		$hbanner_id = $news_m->getLastInsertHomeBannerId('hbanner_id');

		// ファイルを登録
		if ($banner_filename) {
			$banner_dest = $news_m->getHomeBannerPath($img_id);
			umask(0002);

			// ディレクトリ作成
			Pp_Util::mkdir(dirname($banner_dest));

			if (!rename($banner_filename, $banner_dest)) {
				$db->rollback();
				return 'admin_error_500';
			}
		}

		// トランザクション完了
		$db->commit();

		// ログ
		$log_columns = $columns;
		$log_columns['hbanner_id'] = $hbanner_id;
		$log_columns['img_id'] = $img_id;
		$log_columns['banner_data'] = $banner_data;

		$admin_m->addAdminOperationLog('/announce/home', 'banner_log',
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $log_columns)
		);

		return 'admin_announce_home_banner_create_exec';
	}
}