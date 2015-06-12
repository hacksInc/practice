<?php
/**
 *  Admin/Log/Cs/Purchase/Index.php
 *
 *  @author     {$author}
 *  @package    Jm
 *  @version    $Id$
 */

require_once 'Jm_AdminViewClass.php';

/**
 *  admin_log_cs_purchase_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Jm
 */
class Jm_View_AdminLogCsPurchaseIndex extends Jm_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$this->af->setApp('create_file_path', '/admin/log/cs/purchase');
		$this->af->setApp('dialog_url', '/admin/log/cs/purchase/info');
		$this->af->setApp('dialog_title', '課金アイテム購入情報詳細');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
