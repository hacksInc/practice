<?php
/**
 *  Admin/Log/Cs/Achievement/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_achievement_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsAchievementIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$this->af->setApp('create_file_path', 'admin/log/cs/achievement');
		$this->af->setApp('dialog_url', 'admin/log/cs/achievement/info');
		$this->af->setApp('dialog_title', '勲章付与情報詳細');

		$this->af->setApp('form_template', $this->af->form_template);
	}
}
