<?php
/**
 *  Admin/Kpi/World/Quest/Select.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_world_quest_select view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiWorldQuestSelect extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$date_default = $this->getYesterdayYmd();

		$this->af->setApp('date_default', $date_default);
    }
}

?>
