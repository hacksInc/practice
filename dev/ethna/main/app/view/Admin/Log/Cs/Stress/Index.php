<?php
/**
 *  Admin/Log/Cs/Stress/Index.php
 *
 *  @author     {$author}
 *  @package    Jm
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_stress_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Jm
 */
class Pp_View_AdminLogCsStressIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$this->af->setApp('create_file_path', 'admin/log/cs/stress');
		$this->af->setApp('dialog_url', 'admin/log/cs/stress/info');
		$this->af->setApp('dialog_title', 'ストレスケア履歴詳細');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
