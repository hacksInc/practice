<?php
/**
 *  Admin/Announce/Message/Tips/History.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pager.php';
require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_message_tips_history view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceMessageTipsHistory extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_message_tips_create_exec' => null,
	);

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$message_m =& $this->backend->getManager('AdminMessage');
		$lang = $this->af->getLang();
		$ua = $this->af->getUa();
		$page = $this->af->getPageFromPageID();

		$limit = 20;
		$offset = $limit * $page;
		
		$list_all = $message_m->getMessageTipsList(0, 100);
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
		
		$this->af->setAppNe('pager', $links);
    }
}
