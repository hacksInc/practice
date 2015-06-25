<?php
/**
 *  Admin/Kpi/User/Hazard/Level.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_user_hazard_lebel view implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_View_AdminKpiUserHazardLevel extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	public function preforward()
	{
		$this->af->setApp('create_file_path', '/admin/kpi/user/hazard');
		$this->af->setApp('form_template', $this->af->form_template);
	}
}
