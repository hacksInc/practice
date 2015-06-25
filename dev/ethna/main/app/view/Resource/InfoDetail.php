<?php
/**
 *  Resource/InfoDetail.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  resource_infoDetail view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceInfoDetail extends Pp_ViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$news_m =& $this->backend->getManager('News');

		$content_id = $this->af->get('content_id');

		$row = $news_m->getNewsContent($content_id);
		if (!$row) {
			return 'admin_error_500';
		}

		// テンプレート変数アサイン
		$this->af->setApp('row', $row);
		$this->af->setAppNe('title', $row['title']);
		$this->af->setAppNe('abridge', $row['abridge']);
		$this->af->setAppNe('body', $row['body']);

		$this->af->setApp('form_template', $this->af->form_template);
		$this->af->setApp('mtime', $news_m->getImageDirMtime());
	}
}

