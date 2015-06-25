<?php
/**
 *  Admin/Announce/Help/Detail/History.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pager.php';
require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_help_detail_history view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHelpDetailHistory extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_help_detail_create_exec' => null,
	);

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$help_m =& $this->backend->getManager('AdminHelp');
		$page = $this->af->getPageFromPageID();

		$limit = 20;
		$offset = $limit * $page;

		$list_all = $help_m->getHelpDetailList(0, 100, true);
		$list = array_slice($list_all, $offset, $limit);
		$num = count($list_all);

		$options = array(
			'mode'        => 'Sliding',
			'delta'       => 4,
			'importQuery' => false,
			'totalItems'  => $num,
			'perPage'     => $limit,
		);

		$pager =& Pager::factory($options);
		$links = $pager->getLinks();

		// テンプレート変数にアサイン
		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);

		$this->af->setApp('form_template', $this->af->form_template);
		$this->af->setApp('category_list', $this->af->getCategoryList());

		$this->af->setAppNe('pager', $links);
	}
}
