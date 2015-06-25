<?php
/**
 *  Admin/Announce/Help/Detail/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_help_detail_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpDetailCreateExec extends Pp_Form_AdminAnnounceHelpDetail
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'priority',
		'category_id',
		'title',
		'body',
		'picture' => array('required' => false),
		'confirm_uniq_picture',
		'picture_no' => array('required' => false),
	);
}

/**
 *  admin_announce_help_detail_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpDetailCreateExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_help_detail_create_exec Action.
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
		// 画像
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
	 *  admin_announce_help_detail_create_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$help_m =& $this->backend->getManager('AdminHelp');
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');

		$picture_no = $this->af->get('picture_no');

		$picture_filename = $this->af->getApp('picture_filename');
		if ($picture_filename)
		{
			$picture_data = 'data:image/png;base64,' . base64_encode(file_get_contents($picture_filename));
		}
		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}

		if ($picture_filename || $picture_no) {
			$columns['picture'] = "1";
		}

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		$ret = $help_m->insertHelpDetail($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}

		$help_id = $help_m->getLastInsertHelpId();

		// ファイルを登録
		if ($picture_filename) {
			$picture_dest = $help_m->getHelpDetailPicturePath($help_id);
			umask(0002);

			// ディレクトリ作成
			Pp_Util::mkdir(dirname($picture_dest));

			if (!rename($picture_filename, $picture_dest)) {
				$db->rollback();
				return 'admin_error_500';
			}
		} elseif ($picture_no) {
			$picture_dest_old = $help_m->getHelpDetailPicturePath($picture_no);
			$picture_dest = $help_m->getHelpDetailPicturePath($help_id);
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
		$log_columns['help_id'] = $help_id;
		if ($picture_data)
		{
			$log_columns['picture_data'] = $picture_data;
		}
		$admin_m->addAdminOperationLog('/announce/help', 'detail_log',
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $log_columns)
		);

		// トランザクション完了
		$db->commit();

		return 'admin_announce_help_detail_create_exec';
	}
}
