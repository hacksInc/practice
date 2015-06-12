<?php
/**
 *  Admin/Announce/Help/Detail/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_help_detail_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpDetailUpdateExec extends Pp_Form_AdminAnnounceHelpDetail
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'help_id',
		'priority',
		'category_id',
		'title',
		'body',
		'confirm_uniq_picture',
		'picture' => array('required' => false),
		'picture_uploaded',
	);
}

/**
 *  admin_announce_help_detail_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpDetailUpdateExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_help_detail_update_exec Action.
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
	 *  admin_announce_help_detail_update_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$help_m =& $this->backend->getManager('AdminHelp');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}
		$help_id = $this->af->get('help_id');

		$picture_filename = $this->af->getApp('picture_filename');

		$row = $help_m->getHelpDetail($help_id);
		if (!$row) {
			return 'admin_error_500';
		}

		$picture_data = null;
		if ($picture_filename) {
			$picture_data = 'data:image/png;base64,' . base64_encode(file_get_contents($picture_filename));
		}

		$columns = array(
			'help_id'                => $help_id,
			'priority'               => $this->af->get('priority'),
			'category_id'            => $this->af->get('category_id'),
			'title'                  => $this->af->get('title'),
			'body'                   => $this->af->get('body'),
		);

		if ($picture_filename) {
			$columns['picture'] = "1";
		}

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// DBへ登録
		$ret = $help_m->updateHelpDetail($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}

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
		}

		// ログ
		$log_columns = $columns;
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

		return 'admin_announce_help_detail_update_exec';
	}
}
