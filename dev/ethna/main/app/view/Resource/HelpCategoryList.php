<?php
/**
 *  Resource/HelpCategoryList.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  resource_helpCategoryList view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceHelpCategoryList extends Pp_ViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$help_m =& $this->backend->getManager('Help');

		$list = $help_m->getHelpCategoryList();

		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);
	}
}

