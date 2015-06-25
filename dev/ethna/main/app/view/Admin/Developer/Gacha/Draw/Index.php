<?php
/**
 *  Admin/Developer/Gacha/Draw/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pager.php';
require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_draw_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaDrawIndex extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		
		$gacha_id        = $this->af->get('gacha_id');
		$date_draw_start = $this->af->get('date_draw_start');
		$date_draw_end   = $this->af->get('date_draw_end');
		$page            = $this->af->getPageFromPageID();
		
		$limit = 100;
		$offset = $limit * $page;

		$shop_m->queryLogGachaDrawList($gacha_id, $date_draw_start, $date_draw_end, $offset, $limit);
		$gacha_draw_list = array();
		while ($row = $shop_m->fetchLogGachaDrawList()) {
			$gacha_draw_list[] = $shop_m->convertLogGachaDrawListRow($row);
		}
		
		$gacha_draw_list_header = array_values($shop_m->getLogGachaDrawListLabels());
		
		$total_number_of_monsters = $shop_m->countLogGachaDrawList($gacha_id, $date_draw_start, $date_draw_end);
		
		$options = array(
			'mode'        => 'Sliding',
			'delta'       => 4,
			'importQuery' => false,
			'extraVars'   => compact('gacha_id', 'date_draw_start', 'date_draw_end'),
			'totalItems'  => $total_number_of_monsters,
			'perPage'     => $limit,
		);
		
		$pager =& Pager::factory($options);
		$links = $pager->getLinks();
		
		$this->af->setApp('gacha_draw_list',          $gacha_draw_list);
		$this->af->setApp('gacha_draw_list_header',   $gacha_draw_list_header);
		$this->af->setApp('total_number_of_monsters', $total_number_of_monsters);
		$this->af->setApp('form_template',            $this->af->form_template);
		$this->af->setAppNe('pager', $links);
    }
}