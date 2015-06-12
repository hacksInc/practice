<?php
/**
 *  Admin/Kpi/Shop/Sum/Select.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_shop_sum_select view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiShopSumSelect extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$duration_type = $this->af->get('duration_type');

		$admin_m =& $this->backend->getManager('Admin');
		
		switch ($duration_type) {
			case Pp_AdminManager::DURATION_TYPE_DAILY:
				$start_default = $this->getYesterdayYmd();
				$end_default   = $start_default;
				break;

			case Pp_AdminManager::DURATION_TYPE_MONTHLY:
				$start_default = $this->getLastMonthYm();
				$end_default   = $start_default;
				break;
		}

		$this->af->setApp('start_default', $start_default);
		$this->af->setApp('end_default',   $end_default);
    }
}

?>
