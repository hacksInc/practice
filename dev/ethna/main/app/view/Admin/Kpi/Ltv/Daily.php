<?php
/**
 *  Admin/Kpi/Ltv/Daily.php
 *
 *  @author	 {$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_ltv_daily view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminKpiLtvDaily extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	public function preforward()
	{
		$this->af->setApp('create_file_path', '/admin/kpi/ltv');
		$this->af->setApp('form_template', $this->af->form_template);
	}
}
