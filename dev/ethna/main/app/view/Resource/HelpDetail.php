<?php
/**
 *  Resource/HelpDetail.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  resource_helpDetail view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceHelpDetail extends Pp_ViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$help_m =& $this->backend->getManager('Help');

		$help_id = $this->af->get('help_id');

		$row = $help_m->getHelpDetail($help_id);
		if (!$row) {
			return 'admin_error_500';
		}

		// テンプレート変数アサイン
		$this->af->setApp('row', $row);
		$this->af->setAppNe('title', $row['title']);
		$this->af->setAppNe('body', $row['body']);

		$this->af->setApp('form_template', $this->af->form_template);
		$this->af->setApp('mtime', $help_m->getImageDirMtime());
	}
}
