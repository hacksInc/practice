<?php
/**
 *  Admin/Log/Cs/Accounting/Index.php
 *
 *  @author     {$author}
 *  @package    Jm
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_accounting_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Jm
 */
class Pp_View_AdminLogCsAccountingIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$this->af->setApp('create_file_path', 'admin/log/cs/accounting');
		$this->af->setApp('dialog_url', 'admin/log/cs/accounting/info');
		$this->af->setApp('dialog_title', '課金アイテム購入情報詳細');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
