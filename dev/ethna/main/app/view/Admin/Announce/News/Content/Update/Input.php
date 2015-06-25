<?php
/**
 *  Admin/Announce/News/Content/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_news_content_update_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceNewsContentUpdateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_news_content_update_exec' => null,
	);

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$news_m =& $this->backend->getManager('AdminNews');

		$this->af->setApp('form_template', $this->af->form_template);
		$this->af->setApp('mtime', $news_m->getHomeBannerDirMtime());
		$this->af->setAppNe('title', $this->af->get('title'));
		$this->af->setAppNe('body',  $this->af->get('body'));
	}
}

