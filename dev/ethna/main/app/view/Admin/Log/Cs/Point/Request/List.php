<?php
/**
 *  Admin/Log/Cs/Point/Request/List.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pager.php';
require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_point_request_list view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsPointRequestList extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$search_type = $this->af->get('search_type');
		
		$extra_vars = array(
			'search_type'      => $search_type,
			'search_date_from' => $this->af->get('search_date_from'),
			'search_date_to'   => $this->af->get('search_date_to'),
		);
		
		if ($search_type == Pp_LogdataViewPointManager::SEARCH_TYPE_USER_ID) {
			$extra_vars['search_user_id'] = $this->af->get('search_user_id');
		}
		
		$point_log_count = $this->af->getApp('point_log_count');
		
		$options = array(
			'mode'        => 'Sliding',
			'delta'       => 4,
			'importQuery' => false,
			'extraVars'   => $extra_vars,
			'totalItems'  => $point_log_count,
			'perPage'     => Pp_Action_AdminLogCsPointRequestList::MAX_PAGE_DATA_COUNT,
		);
		
		$pager =& Pager::factory($options);
		$links = $pager->getLinks();
		
		$this->af->setAppNe('pager', $links);
    }
}

?>