<?php
/**
 *  Admin/Log/Cs/Photo/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_photo_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsPhotoIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */

	function preforward()
	{

		$this->af->setApp('create_file_path', 'admin/log/cs/photo');
		$this->af->setApp('dialog_url', 'admin/log/cs/photo/info');
		$this->af->setApp('dialog_title', 'フォト取得情報詳細');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
