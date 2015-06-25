<?php
/**
 *  Admin/Announce/Loginbonus/Content/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_loginbonus_content_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceLoginbonusContentCreateInput extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$present_m =& $this->backend->getManager('Present');

		$date_start = date('Y-m-d', $_SERVER['REQUEST_TIME']+86400);
		$date_end = date('Y-m-d', $_SERVER['REQUEST_TIME']+86400*31);

		$this->af->setApp('date_start', $date_start);
		$this->af->setApp('date_end', $date_end);
		
		$this->af->setApp('dist_type_options', $present_m->DIST_TYPE_OPTIONS);
		
		parent::preforward();
    }
}
?>