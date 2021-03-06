<?php
/**
 *  Admin/Kpi/User/Mission/Index.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_user_mission_index view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminKpiUserMissionIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	public function preforward()
	{
		$this->af->setApp('create_file_path', '/admin/kpi/user/mission');
		$this->af->setApp('form_template', $this->af->form_template);
	}
}
