<?php
/**
 *  Admin/Announce/Help/Detail/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_help_detail_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpDetailUpdateConfirm extends Pp_Form_AdminAnnounceHelpDetail
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
		'picture' => array('required' => false),
	);
}

/**
 *  admin_announce_help_detail_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpDetailUpdateConfirm extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_help_detail_update_confirm Action.
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
	 *  admin_announce_help_detail_update_confirm action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$help_m =& $this->backend->getManager('AdminHelp');
		$help_id   = $this->af->get('help_id');

		// ヘルプ画像
		$picture = $this->af->get('picture');
		$confirm_uniq_picture = uniqid();
		$picture_uploaded = false; // ヘルプ画像がアップロードされたか
		$picture_filename = null;  // アップロードされたヘルプ画像ファイルのサーバ内パス
		$picture_data = null;      // ヘルプ画像のData URLスキーム表記
		if ($picture && ($picture['error'] == UPLOAD_ERR_OK) && ($picture['size'] > 0)) {
			$picture_uploaded = true;
			$picture_filename = $this->af->getAdminTmpFilename($confirm_uniq_picture);
			umask(0002);
			move_uploaded_file($picture['tmp_name'], $picture_filename);
			$picture_data = 'data:image/png;base64,' . base64_encode(file_get_contents($picture_filename));
		}

		$row = $help_m->getHelpDetail($help_id);
		if (!$row) {
			return 'admin_error_500';
		}

		// テンプレート変数アサイン
		$this->af->setApp('category_list',  $this->af->getCategoryList());
		$this->af->setApp('confirm_uniq_picture', $confirm_uniq_picture);
		$this->af->setApp('picture_uploaded', $picture_uploaded ? 1 : 0);
		$this->af->setApp('picture_data', $picture_data);
		$this->af->setApp('row', $row);

		return 'admin_announce_help_detail_update_confirm';
	}
}

