<?php
/**
 *  Admin/Log/Cs/Mission/Index.php
 *
 *  @author     {$author}
 *  @package    Jm
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_mission_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Jm
 */
class Pp_View_AdminLogCsMissionIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$this->af->setApp('create_file_path', 'admin/log/cs/mission');
		$this->af->setApp('dialog_url', 'admin/log/cs/mission/info');
		$this->af->setApp('dialog_title', 'キャラクター履歴詳細');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
