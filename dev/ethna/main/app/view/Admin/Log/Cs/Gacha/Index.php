<?php
/**
 *  Admin/Log/Cs/Gacha/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_gacha_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsGachaIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */

	function preforward()
	{

		$this->af->setApp('create_file_path', 'admin/log/cs/gacha');
		$this->af->setApp('dialog_url', 'admin/log/cs/gacha/info');
		$this->af->setApp('dialog_title', 'ガチャ情報詳細');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
