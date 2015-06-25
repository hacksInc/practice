<?php
/**
 *  Admin/Kpi/Item/Use/Index.php
 *
 *  @author	 {$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_item_use_index view implementation.
 *
 *  @author	 {$author}
 *  @access	 public
 *  @package	Pp
 */
class Pp_View_AdminKpiItemUseIndex extends Pp_AdminViewClass
{

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	public function preforward()
	{
		$this->af->setApp('create_file_path', '/admin/kpi/item/use');
		$this->af->setApp('form_template', $this->af->form_template);
	}
}
