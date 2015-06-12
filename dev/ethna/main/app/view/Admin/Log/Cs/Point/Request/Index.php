<?php
/**
 *  Admin/Log/Cs/Point/Request/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_point_request_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsPointRequestIndex extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$search_date_from = $this->af->get('search_date_from');
		$search_date_to   = $this->af->get('search_date_to');
		$search_type      = $this->af->get('search_type');
		
		$search_date_max = date('Y-m-d H:i', $_SERVER['REQUEST_TIME'] - 60) . ':00';
//		$search_date_max = date('Y-m-d H', $_SERVER['REQUEST_TIME'] - 3600) . ':00:00';
		$search_date_min = date('Y-m-d', strtotime($search_date_max) - 86400 * 30) . ' 00:00:00';
		
		$search_date_from_1 = $search_date_min;
		$search_date_to_1   = $search_date_max;
		$search_date_from_2 = $search_date_min;
		$search_date_to_2   = $search_date_max;
		
		if ($search_type == Pp_LogdataViewPointManager::SEARCH_TYPE_USER_ID) {
			$search_date_from_1 = $search_date_from;
			$search_date_to_1   = $search_date_to;
		} else if ($search_type == Pp_LogdataViewPointManager::SEARCH_TYPE_STS_NG) {
			$search_date_from_2 = $search_date_from;
			$search_date_to_2   = $search_date_to;
		}

		$this->af->setApp('search_date_from_1', $search_date_from_1);
		$this->af->setApp('search_date_to_1',   $search_date_to_1);
		$this->af->setApp('search_date_from_2', $search_date_from_2);
		$this->af->setApp('search_date_to_2',   $search_date_to_2);
    }
}
