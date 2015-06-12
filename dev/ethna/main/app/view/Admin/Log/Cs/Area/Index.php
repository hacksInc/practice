<?php
/**
 *  Admin/Log/Cs/Area/Index.php
 *
 *  @author     {$author}
 *  @package    Jm
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_area_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Jm
 */
class Pp_View_AdminLogCsAreaIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$this->af->setApp('create_file_path', 'admin/log/cs/area');
		$this->af->setApp('dialog_url', 'admin/log/cs/area/info');
		$this->af->setApp('dialog_title', '課金アイテム購入情報詳細');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
