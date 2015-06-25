<?php
/**
 *  Admin/Log/Cs/Item/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_item_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsItemIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$this->af->setApp('create_file_path', 'admin/log/cs/item');
		$this->af->setApp('dialog_url', 'admin/log/cs/item/info');
		$this->af->setApp('dialog_title', 'アイテム情報詳細');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
