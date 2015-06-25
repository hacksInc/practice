<?php
/**
 *  Admin/Kpi/User/Count/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_user_count_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiUserCountView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$date_created_from = $this->af->get('date_created_from');
		$date_created_to   = $this->af->get('date_created_to');
		
		$admin_m =& $this->backend->getManager('Admin');
		
		$cnt = $admin_m->countCreatedDbCmnUser($date_created_from, $date_created_to);
		
		$this->af->setApp('cnt', $cnt);
    }
}

?>